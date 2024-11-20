<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HSGQ;


class OLTSIDE_HSGQ extends Model
{
    use HasFactory;

    static public function OLT_SIDE_SYSTEMINFO($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $Name       = '';
        $Firmware   = '';
        $Model      = '';
        $RuningTime = '';
        $Hardware   = '';

        try {
                $Name       = $snmp->get('1.3.6.1.2.1.1.5.0'); 
                $Name = trim(str_replace('STRING: ','',$Name));
                $Name = trim(str_replace("\"",'',$Name));
                $html['name'] = $Name;
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
     
        try {
                $Uptime     = $snmp->get(".1.3.6.1.2.1.1.3.0", TRUE);    
                if (strpos($Uptime, "Timeticks") !== false) 
                {
                    $position = strpos($Uptime, ')');
                    if ($position !== false) 
                    { 
                        $html['uptime'] = substr($Uptime, $position + strlen(')'));
                    }
                }
                else
                {
                    $html['uptime'] = $Uptime;
                }
        } 
        catch (\Exception $e) 
        {$html['uptime'] = '';}

        try {
                $Version  = $snmp->get("SNMPv2-MIB::sysDescr.0", TRUE);  
                $Version  = str_replace("STRING: ", "", $Version);
                $Version  = str_replace("\"", "", $Version);
                $html['version'] = $Version;
        } 
        catch (\Exception $e) 
        {$html['version'] = '';}
   
        try {
                $Firmware   = $snmp->get("1.3.6.1.4.1.50224.3.1.1.6.0", TRUE);
                $Firmware   = trim(str_replace('STRING: ','',$Firmware));
                $Firmware   = trim(str_replace('Hex-','',$Firmware));
                $Firmware   = trim(str_replace("\"",'',$Firmware));
                $Firmware   = trim(str_replace(" ",'',$Firmware));
                $html['firmware'] =  self::hexToAscii($Firmware);
        } 
        catch (\Exception $e) 
        {$html['firmware'] = '';}

        try {
                $Hardware   = $snmp->get("1.3.6.1.4.1.50224.3.1.1.7.0", TRUE);    
                $Hardware   = trim(str_replace('STRING: ','',$Hardware));
                $Hardware   = trim(str_replace("\"",'',$Hardware));
                $html['hardware'] = $Hardware;
        } 
        catch (\Exception $e) 
        {$html['hardware'] = '';}


        return $html;
    }

    static public function OLT_SIDE_SWITCHPORTS($ip,$token)
    {
        $html = [];
 
        try {
                $ArrayFirst = self::API('http://'.$ip.'/board?info=pon',$token);
                $dataArray  = json_decode($ArrayFirst, true);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
 
        $F_Online = 0;$F_Offline = 0;
        foreach ($dataArray['data'] as $item) 
        {
            $F_Online  += $item['online'];
            $F_Offline += $item['offline'];
        }
         
        $F_Total  = $F_Online + $F_Offline;

        $html['online']  = $F_Online;
        $html['offline'] = $F_Offline;
        $html['total']   = $F_Total;

        return $html;
    }
     
    static public function OLT_SIDE_PON_CHARTS($ip,$token,$read)
    {
        $html = [];
 
        try {
                $ArrayFirst = self::API('http://'.$ip.'/board?info=pon',$token);
                $dataArray  = json_decode($ArrayFirst, true);

                foreach ($dataArray['data'] as $key => $item) 
                {    
                    $html['PonList'][] = array(
                        'key'      => $item['port_id'],
                        'ifDescr'  => 'EPON0/'.$item['port_id'],
                        'status'   => $item['status'],
                        'value'    => $item['online']+$item['offline']
                    );
                }
        
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
      
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read); 
        try {
                $Desc     = $snmp->walk(".1.3.6.1.4.1.50224.3.2.1.1.3", TRUE);

                foreach ($Desc as $key => $value)
                {
                    $value = trim(str_replace('Hex-STRING: ','',$value));
                    $value = trim(str_replace("\"",'',$value));

                    if (strpos(self::asciiToString($value), 'GE') !== FALSE || strpos(self::asciiToString($value), 'XGE') !== FALSE)
                    {
                        $RPort        = self::Pon__Converter($key);   
                        $ArraySecond  = self::API('http://'.$ip.'/switch_port?form=port_info&port_id='.$RPort,$token);
                        $SecDataArray = json_decode($ArraySecond, true);      
                                        
                        $html['PortList'][] = array(
                            'key'      =>  $key,
                            'ifDescr'  =>  str_replace("\0", "",self::asciiToString($value)), 
                            'status'   =>  $SecDataArray['data']['link_status'] 
                        );
                        
                    }
                }
        } 
        catch (\Exception $e) 
        {}

        return $html;
    }

    static public function OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$token,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        try {
                
                $PonPort = explode('.',$ifindex);
                if( $descr == 'N/A')$descr = ' ';
 
                $data = array(
                    'method' => 'set',
                    'param' => array(
                        'port_id'  => $PonPort[0],
                        'onu_id'   => $PonPort[1],
                        'onu_name' => $descr,
                        'onu_desc' => '',
                        'fec_mode' => 1,
                        'flags'    => 8
                    )
                );
                
                $jsonData = json_encode($data);
                
                $headers = array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonData),
                    "X-Token: {$token}"
                );
                
                $curl = curl_init('http://'.$ip.'/onumgmt?form=config');
                
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                
                $response = curl_exec($curl);
                
                if (curl_errno($curl)) {
                    echo 'Curl error: ' . curl_error($curl);
                }
                
                curl_close($curl);
        
                if(strstr($response,'success') !== false)
                {
                    return 1;
                }
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
     
    }

    static public function OLT_SIDE_ONU_UNINSTALL($ip,$token,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        try {           
                $PonPort = explode('.',$ifindex);

                $Mac = '';$Pon = '';$Port = '';
                $ArraySecond =  self::API('http://'.$ip.'/onu_allow_list?port_id='.$PonPort[0],$token);
                $SecDataArray = json_decode($ArraySecond, true);
                foreach ($SecDataArray['data'] as $item) 
                {
                    if($item['port_id'] == $PonPort[0] && $item['onu_id'] == $PonPort[1])
                    {
                        $Mac = $item['macaddr'];
                    }
                }
               
                if(!empty($Mac))
                {
                    $data = array(
                        'method' => 'delete',
                        'param' => array(
                            'port_id'  => $PonPort[0],
                            'onu_id'   => $PonPort[1],
                            'macaddr' =>  $Mac
                        )
                    );
                    
                    $jsonData = json_encode($data);
                    
                    $headers = array(
                        'Content-Type: application/json',
                        'Content-Length: ' . strlen($jsonData),
                        "X-Token: {$token}"
                    );
                    
                    $curl = curl_init('http://'.$ip.'/onu_allow_list?form=onucfg');
                    
                    curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                    curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
                    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                    
                    $response = curl_exec($curl);
                    
                    if (curl_errno($curl)) 
                    {
                        return response()->json(['error' =>curl_error($curl)]);
                    }
                    
                    curl_close($curl);
                
                    if(strstr($response,'success') !== false)
                    {
                        return 1;
                    }
                }
                return response()->json(['error' => 'Mac Not Found']);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
     
    }

    static public function OLT_SIDE_PON_PARAMETERS($ip,$token)
    {
        $html = [];

        $Global_Online = 0;$Global_Offline = 0;$allOnu = 0;$Global_Total = 0;

        try {           
             $ArraySecond =  self::API('http://'.$ip.'/board?info=pon',$token);
             $SecDataArray = json_decode($ArraySecond, true);

             $Array3     =  self::API('http://'.$ip.'/switch_port?form=portlist_info',$token);
             $SecData3   =  json_decode($Array3, true);  
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }

        foreach ($SecDataArray['data'] as  $keyZ =>  $item) 
        {
            $Global_Online  += $item['online'];
            $Global_Offline += $item['offline'];
            $allOnu = $item['online'] +  $item['offline'];
            $Global_Total += $allOnu;

            $Array2   = self::API('http://'.$ip.'/ponmgmt?form=optical_poninfo&port_id='.$item['port_id'],$token);
            $SecData2 = json_decode($Array2, true);   
             
            $itemZ = [];
            foreach ($SecData3['data'] as $key => $Zitem) 
            {
                if($Zitem['port_id'] == $item['port_id'])
                {        
                    $itemZ['descr']      = $Zitem['port_desc'];
                    $itemZ['admin']      = $Zitem['admin_status'];
                    $itemZ['neg']        = $Zitem['auto_neg'];
                    $itemZ['deuplex']    = $Zitem['duplex'];
                    $itemZ['erate']      = $Zitem['erate'];
                    $itemZ['flag']       = 2048;
                    $itemZ['control']    = $Zitem['flow_ctrl'];
                    $itemZ['irate']      = $Zitem['irate'];
                    $itemZ['mtu']        = $Zitem['mtu'];
                    $itemZ['pvid']       = $Zitem['pvid'];
                    $itemZ['speed']      = $Zitem['speed'];
                }
            } 
             
            $itemX = [];
            $itemX['sfp']               = $SecData2['data']['module_state'];
            $itemX['state']             = $SecData2['data']['portstate'];
            $itemX['admin']             = $item['admin_status'];
            $itemX['pon']               = 'EPON0/'.$item['port_id'];
            $itemX['ifindex']           = $item['port_id'];
            $itemX['temp']              = $SecData2['data']['work_temprature'];
            $itemX['tx']                = $SecData2['data']['transmit_power'];
            $itemX['volt']              = $SecData2['data']['work_voltage'];
            $itemX['curr']              = $SecData2['data']['transmit_bias'];
            $itemX['online']            = $item['online'];
            $itemX['offline']           = $item['offline'];
            $itemX['total']             = $allOnu;
            $itemX['details']           = $itemZ;
            $html['PonList_'.$keyZ]     = $itemX;

        }

        $html['TotalOnline']  = $Global_Online;
        $html['TotalOffline'] = $Global_Offline;
        $html['TotalOnt']     = $Global_Total;

        return $html;
    }
     
    static public function OLT_SIDE_PON_DESCRIPTION($ip,$token,$ifindex,$descr,$admin,$neg,$deuplex,$erate,$flag,$control,$irate,$mtu,$pvid,$speed)
    {
         PrivilegesModel::PrivCheck('Priv_Pon');
     
         $data = array(
            'method' => 'set',
            'param' => array(
                'port_id'  => $ifindex,
                'port_desc' => $descr,
                'admin_status'=> $admin,
                'auto_neg'=> $neg,
                'duplex'=> $deuplex,
                'erate'=> $erate,
                'flags'=> $flag,
                'flow_ctrl'=> $control,
                'irate'=> $irate,
                'mtu'=> $mtu,
                'pvid'=> $pvid,
                'speed'=> $speed,
            )
        );
        
        $jsonData = json_encode($data);
        
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            "X-Token: {$token}"
        );
        
        $curl = curl_init('http://'.$ip.'/switch_port?form=port_info');
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) 
        {
            return response()->json(['error' => curl_error($curl)]);
        }
        
        curl_close($curl);
    
        if(strstr($response,'success') !== false)
        {
            return 1;
        }
        
        return 1;
    }

    static public function OLT_SIDE_PON_TURNOFF($ip,$token,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Pon');

        $data = array(
            'method' => 'set',
            'param' => array(
                'port_id'  => $ifindex,
                'optstate' => 0,
            )
        );
    
        $jsonData = json_encode($data);
        
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            "X-Token: {$token}"
        );
        
        $curl = curl_init('http://'.$ip.'/ponmgmt?form=optstate');
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) {
            return response()->json(['error' => curl_error($curl)]);
        }
        
        curl_close($curl);
    
        if(strstr($response,'success') !== false)
        {
            return 1;
        }
        return flase;
    }

    static public function OLT_SIDE_PON_TURNON($ip,$token,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Pon');

        $data = array(
            'method' => 'set',
            'param' => array(
                'port_id'  => $ifindex,
                'optstate' => 1,
            )
        );
 
        $jsonData = json_encode($data);
        
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            "X-Token: {$token}"
        );
        
        $curl = curl_init('http://'.$ip.'/ponmgmt?form=optstate');
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) {
            return response()->json(['error' => curl_error($curl)]);
        }
        
        curl_close($curl);
    
        if(strstr($response,'success') !== false)
        {
            return 1;
        }
        return flase;
    }

    static public function OLT_SIDE_UPLINKS($ip,$read,$token)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $Desc  = '';

        try {           
                $Desc     = $snmp->walk(".1.3.6.1.4.1.50224.3.2.1.1.3", TRUE);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }

        foreach ($Desc as $key => $value)
        {
            $value = trim(str_replace('Hex-STRING: ','',$value));
            $value = trim(str_replace("\"",'',$value));

            if (strpos(self::asciiToString($value), 'GE') !== FALSE || strpos(self::asciiToString($value), 'XGE') !== FALSE)
            {
                $RPort        = self::Pon__Converter($key);   
                $ArraySecond  = self::API('http://'.$ip.'/switch_port?form=port_info&port_id='.$RPort,$token);
                $SecDataArray = json_decode($ArraySecond, true);      


                if ($SecDataArray['data']['duplex'] == 1) $duplex = 'Full';
                else $duplex = 'Half';

                $item = [];

                try {                        
                        $ArrayDetails  = self::API('http://'.$ip.'/switch_port?form=optical_uplinkinfo&port_id='.$RPort,$token);
                        $SecDetails    = json_decode($ArrayDetails, true);   

                        $item ['rx']                = $SecDetails['data']['receive_power'];
                        $item ['tx']                = $SecDetails['data']['transmit_power'];
                        $item ['temp']              = $SecDetails['data']['work_temprature'];
                        $item ['volt']              = $SecDetails['data']['work_voltage'];
                        $item ['current']           = $SecDetails['data']['transmit_bias'];
                } 
                catch (\Exception $e) 
                {
                    $item ['rx']                = '-';
                    $item ['tx']                = '-';
                    $item ['temp']              = '-';
                    $item ['volt']              = '-';
                    $item ['current']           = '-';
                }

          
                $item ['ifindex']           = $RPort;
                $item ['port']              = str_replace("\0", "",self::asciiToString($value));
                $item ['name']              = $SecDataArray['data']['port_desc'];
                $item ['duplex']            = $duplex;
                $item ['speed']             = $SecDataArray['data']['speed'];
                $item ['admin']             = $SecDataArray['data']['admin_status'];
                $item ['state']             = $SecDataArray['data']['link_status'];

                $item ['negg']              = $SecDataArray['data']['auto_neg'];
                $item ['erate']             = $SecDataArray['data']['erate'];
                $item ['flags']             = 1;
                $item ['control']           = $SecDataArray['data']['flow_ctrl'];
                $item ['irate']             = $SecDataArray['data']['irate'];
                $item ['mtu']               = $SecDataArray['data']['mtu'];
                $item ['pvid']              = $SecDataArray['data']['pvid'];

                $html ['UplinkList_'.$key]  = $item;
            }
        }

        return $html;
    }
       
    static public function OLT_SIDE_UPLINK_DESCRIPTION($ip,$token,$ifindex,$descr,$admin,$neg,$deuplex,$erate,$flag,$control,$irate,$mtu,$pvid,$speed)
    {
         PrivilegesModel::PrivCheck('Priv_Uplink');
 
         $data = array(
            'method' => 'set',
            'param' => array(
                'port_id'  => $ifindex,
                'port_desc' => $descr,
                'admin_status'=> $admin,
                'auto_neg'=> $neg,
                'duplex'=> $deuplex,
                'erate'=> $erate,
                'flags'=> 2048,
                'flow_ctrl'=> $control,
                'irate'=> $irate,
                'mtu'=> $mtu,
                'pvid'=> $pvid,
                'speed'=> $speed,
            )
        );

        $jsonData = json_encode($data);
        
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            "X-Token: {$token}"
        );
        
        $curl = curl_init('http://'.$ip.'/switch_port?form=port_info');
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) {
            return response()->json(['error' => curl_error($curl)]);
        }
        
        curl_close($curl);

        if(strstr($response,'success') !== false)
        {
            return 1;
        }
        return false;
    }

    static public function OLT_SIDE_UPLINK_TURNOFF($ip,$token,$ifindex,$descr,$neg,$deuplex,$erate,$flag,$control,$irate,$mtu,$pvid,$speed)
    {
        PrivilegesModel::PrivCheck('Priv_Uplink');
 
         $data = array(
            'method' => 'set',
            'param' => array(
                'port_id'  => $ifindex,
                'port_desc' => $descr,
                'admin_status'=> 0,
                'auto_neg'=> $neg,
                'duplex'=> $deuplex,
                'erate'=> $erate,
                'flags'=> $flag,
                'flow_ctrl'=> $control,
                'irate'=> $irate,
                'mtu'=> $mtu,
                'pvid'=> $pvid,
                'speed'=> $speed,
            )
        );


        $jsonData = json_encode($data);
        
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            "X-Token: {$token}"
        );
        
        $curl = curl_init('http://'.$ip.'/switch_port?form=port_info');
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) {
            return response()->json(['error' => curl_error($curl)]);
        }
        
        curl_close($curl);
    
        if(strstr($response,'success') !== false)
        {
           return 1;
        }
        return false;
    }

    static public function OLT_SIDE_UPLINK_TURNON($ip,$token,$ifindex,$descr,$neg,$deuplex,$erate,$flag,$control,$irate,$mtu,$pvid,$speed)
    {
        PrivilegesModel::PrivCheck('Priv_Uplink');
 
         $data = array(
            'method' => 'set',
            'param' => array(
                'port_id'  => $ifindex,
                'port_desc' => $descr,
                'admin_status'=> 1,
                'auto_neg'=> $neg,
                'duplex'=> $deuplex,
                'erate'=> $erate,
                'flags'=> $flag,
                'flow_ctrl'=> $control,
                'irate'=> $irate,
                'mtu'=> $mtu,
                'pvid'=> $pvid,
                'speed'=> $speed,
            )
        );


        $jsonData = json_encode($data);
        
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($jsonData),
            "X-Token: {$token}"
        );
        
        $curl = curl_init('http://'.$ip.'/switch_port?form=port_info');
        
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
        
        if (curl_errno($curl)) {
            return response()->json(['error' => curl_error($curl)]);
        }
        
        curl_close($curl);
    
        if(strstr($response,'success') !== false)
        {
           return 1;
        }
        return false;
    }

    static public function OLT_SIDE_ONT_DETAILS($ip,$token,$ifindex)
    { 
        $html = [];
  
        $Port_id = 0;$OntID = 0;
        try {
                $Parts = explode('.',$ifindex);

                $ArrayFirst =  self::API('http://'.$ip.'/onutable',$token);  
                $dataArray  = json_decode($ArrayFirst, true);   
                foreach ($dataArray['data'] as $item)  
                {
                    if ($item['port_id'] == $Parts[0] && $item['onu_id'] == $Parts[1])
                    {
                        $Port_id  = $item['port_id'];
                        $OntID    = $item['onu_id'];

                        $DetailMore =  self::API('http://'.$ip.'/onumgmt?form=base-info&port_id='.$Port_id.'&onu_id='.$OntID,$token);  
                        $moreArray  = json_decode($DetailMore, true);   
                         

                        $html['PonPort']  = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                        $html['ifAlias']  = $item['onu_name'];
                        $html['rtt']      = $item['rtt']; 
                        $html['Distance'] = $item['distance']; 
                        $html['OnuMac']   = $item['macaddr']; 
                        $html['vendor']   = $moreArray['data']['vendor'].' - '.$moreArray['data']['extmodel']; 

                        if($item['status'] == 'Online')
                        {
                            $html['Uptime']     = HSGQ::timeAgo($item['last_down_time']);
                        }
                        else
                        {
                            $html['Downtime']   = HSGQ::timeAgo($item['register_time']);
                        }
  
                        break;
                    }
                }
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }


        try {
                if($Port_id !== 0 && $OntID !== 0)
                { 
                    $ArraySecond =  self::API('http://'.$ip.'/onumgmt?form=optical-diagnose&port_id='.$Port_id.'&onu_id='.$OntID,$token);
                    $XXArray     = json_decode($ArraySecond, true);  
                    $data = $XXArray['data'];   

                    $html['Temp']  = $data['work_temprature']; 
                    $html['Volt']  = $data['work_voltage']; 
                    $html['Curr']  = $data['transmit_bias']; 
                    $html['OnuTX'] = $data['transmit_power']; 
                    $html['OnuRX'] = $data['receive_power']; 
                }
                else dd($Port_id);
        } 
        catch (\Exception $e) 
        {}


        return $html;
    }

    static public function API($Url,$accessKey)
    {
        $ch = curl_init($Url);

        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = ["X-Token: {$accessKey}",];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        
        $response = curl_exec($ch);

        if (curl_errno($ch)) 
        {
            echo 'Curl error: ' . curl_error($ch);
        }
        curl_close($ch);
        
        return $response;
    }

    static public function hexToAscii($hexString) 
    {
        $asciiString = '';

        // Split the hex string into pairs
        $hexPairs = str_split($hexString, 2);

        // Convert each pair to decimal and then to ASCII
        foreach ($hexPairs as $pair) {
            $asciiString .= chr(hexdec($pair));
        }

        return $asciiString;
    }

    static public function asciiToString($hexValues) 
    {
        // Split the input string into an array of hexadecimal values
        $hexArray = explode(' ', $hexValues);

        // Convert each hexadecimal value to its corresponding character
        $characters = array_map('chr', array_map('hexdec', $hexArray));

        // Join the characters into a string
        $result = implode('', $characters);

        return $result;
    }
    static public function Pon__Converter($Index)
    {
        return ($Index >> 8) & 0xFF;
    }
}
