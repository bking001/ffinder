<?php

namespace App\Models\Install;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\HSGQ;
use App\Models\PrivilegesModel;

class _hsgq extends Model
{
    use HasFactory;

    static public function ONT_INFO_BY_IFINDEX($ip,$ifIndex,$token)
    {
        $html = [];
        $html ['clone'] = '';
        $Real_Desc_Key = 0;
        $DescriptionClone = 0;

        $ifIndex = explode('.',$ifIndex);

 
        HSGQ::API('https://'.$ip.'/onu_allow_list?t=1709042982000',$token);
        $ArrayFirst =  HSGQ::API('http://'.$ip.'/onutable',$token);     
        $dataArray = json_decode($ArrayFirst, true);
 
        foreach ($dataArray['data'] as $item) 
        {
            if (strpos($item['port_id'],  $ifIndex[0]) !== false && strpos($item['onu_id'],  $ifIndex[1]) !== false){$Port_id = $item['port_id'];break;}
        }

        if(!empty($Port_id))   
        { 
            $ArraySecond  = HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$Port_id,$token);
            $SecDataArray = json_decode($ArraySecond, true);  
            foreach ($SecDataArray['data'] as $item) 
            {
                if ($item['onu_id'] ==  $ifIndex[1]) 
                {     
            
                        $DescriptionClone += 1;
                        $Real_Desc_Key += 1;
 
                        $html ['ifIndex']       = $item['port_id'].'.'. $item['onu_id'];
                        $html ['PonPort']       = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                        $html ['Description']   = $item['onu_name'];                    
                        $html ['Type']          = $item['dev_type'].' - '.$item['onu_type'];
                        $html ['Mac']           = $item['macaddr'];
                        $html ['Status']        = $item['status'];
                        $html ['Reason']        = $item['last_down_reason'];
                        if (is_numeric($item['receive_power']))$html ['dbm'] = round($item['receive_power'],2);
                        else   $html ['dbm']    = $item['receive_power'];
                  
                }
            }
        }    

        if($DescriptionClone > 1)
        {
            $html ['clone'] = 'ეს დესქრიფშენი გაწერილია '.$DescriptionClone.' - ონუზე ';
        }

        if (empty($Real_Desc_Key))
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }     

        return $html;
    }

    static public function ONT_PORT_BY_IFINDEX($ip,$ifIndex,$token)
    {   
        $html = [];
        $html['shutDown'] = 0;
        $Port_id = '';$OntID = '';$Description = '';$OnuStatus = '';

        Sleep(1);
        HSGQ::API('https://'.$ip.'/onu_allow_list',$token);
        Sleep(1);
        $ArrayFirst =  HSGQ::API('https://'.$ip.'/onutable',$token); 
        $dataArray = json_decode($ArrayFirst, true); 


        $ifIndex = explode('.',$ifIndex);

        foreach ($dataArray['data'] as $item) 
        {
            if ($item['port_id'] ==  $ifIndex[0] && $item['onu_id'] ==  $ifIndex[1])  
            {
                $Port_id = $item['port_id'];
                $OntID = $item['onu_id'];
                $Description = $item['onu_name'];
                $OnuStatus = $item['status'];  
                break;
            }
        }
        if(!empty($Port_id))
        { 
            if( $OnuStatus == 'Online')
            {
                $ArraySecond =  HSGQ::API('https://'.$ip.'/onumgmt?form=port_cfg&port_id='.$Port_id.'&onu_id='.$OntID.'',$token);
                $ArrayThird  =  HSGQ::API('https://'.$ip.'/onumgmt?form=port_vlan&port_id='.$Port_id.'&onu_id='.$OntID.'',$token);
                        
        
                $FirstDataArray = json_decode($ArrayThird, true);     
                $SecDataArray   = json_decode($ArraySecond, true);  
                foreach ($SecDataArray['data'] as $key => $item) 
                { 
                    $op_vlan_mode = '';$def_vlan_id = ''; 
                    foreach ($FirstDataArray['data'] as $Zitem) 
                    {  
                        if($item['op_id'] == $Zitem['op_id'])
                        {   
                            $op_vlan_mode = $Zitem['op_vlan_mode'];
                            if($Zitem['def_vlan_id'] == 0)$def_vlan_id  = 1;
                            else $def_vlan_id  = $Zitem['def_vlan_id'];
                        }
                    }    
    
                            $Xtem = [];                           
                            $Xtem['portIndex']      = $item['op_id'];
                            $Xtem['vlan']           = $def_vlan_id;
                            $Xtem['portStatus']     = $item['status'];
                            $Xtem['VlanType']       = $op_vlan_mode;
                            $Xtem['portAdmin']      = $item['enable'];
                            $html["port_num_$key"]  = $Xtem;       
                }   
            }
            else
            {
                $html['shutDown'] = 1;
            }
 
        }
        else
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }
    
        $html["Description"]  = $Description;    
        $html["PonPort"]      = 'EPON0/'.$Port_id.':'.$OntID;
        $html['ifIndex']      = $Port_id.'.'.$OntID;
        return $html;
    }

    static public function ONT_MACS_BY_IFINDEX($ip,$ifIndex,$token)    
    {
        $html = [];
        $html['shutDown'] = 0;
        $Port_id = '';$OntID = '';$Description = '';$OnuStatus = '';
        Sleep(3);
        HSGQ::API('https://'.$ip.'/onu_allow_list',$token);
        Sleep(1);
        $ArrayFirst =  HSGQ::API('https://'.$ip.'/onutable',$token); 
        $dataArray = json_decode($ArrayFirst, true); 

        $ifIndex = explode('.',$ifIndex);

        try {

                foreach ($dataArray['data'] as $item) 
                {
                    if ($item['port_id'] ==  $ifIndex[0] && $item['onu_id'] ==  $ifIndex[1]) 
                    {
                        $Port_id = $item['port_id'];
                        $OntID = $item['onu_id'];
                        $Description = $item['onu_name'];
                        $OnuStatus = $item['status'];  
                        break;
                    }
                }
 

                if(!empty($Port_id))
                { 
                    if( $OnuStatus == 'Online')
                    {
                        HSGQ::API('https://'.$ip.'/pon_mac?form=table',$token); 
                        $ArraySecond  =   HSGQ::API('https://'.$ip.'/pon_mac_table',$token);
                        $SecDataArray = json_decode($ArraySecond, true);    
                        foreach ($SecDataArray['data'] as $key => $item) 
                        { 
                            if ($item['port_id'] ==  $ifIndex[0] && $item['onu_id'] ==  $ifIndex[1]) 
                            { 
                                $Xtem = [];
                                $Xtem['vlan']           = $item['vlan_id'];
                                $Xtem['mac']            = $item['macaddr'];
                                $Xtem['vendoor']        = HSGQ::MacFind_SNMP($item['macaddr']);
                                $html["macs_num_$key"]  = $Xtem;               
                            }
                        }   
                    }
                    else
                    {
                        $html['shutDown'] = 1;
                    }
         
                }
                else
                {
                    return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
                }
            
        } 
        catch (\Exception $e) 
        {}

  
 
        $html["Description"]  = $Description;    
        $html["PonPort"]      = 'EPON0/'.$Port_id.':'.$OntID;
 
        return $html;
    }
    
    static public function OnuRestart($ip,$token,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        $Port_id = '';$OntID = '';
 
        $ifindex = explode('.',$ifindex);

        try {
                HSGQ::API('https://'.$ip.'/onu_allow_list?t=1709042982000',$token);
                $ArrayFirst = HSGQ::API('https://'.$ip.'/onutable',$token);
                $dataArray  = json_decode($ArrayFirst, true);
        
                foreach ($dataArray['data'] as $item) 
                { 
                    if ($item['port_id'] == trim($ifindex[0]) && $item['onu_id'] == trim($ifindex[1])){$Port_id = $item['port_id'];$OntID = $item['onu_id']; break;}
                } 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $ArrayFirst]);
        }
             
        if(!empty($Port_id))
        { 

            $data = array(
                'method' => 'reboot',
                'param' => array(
                    'port_id' => $Port_id,
                    'onu_id' => [$OntID]
                )
            );
            
            $jsonData = json_encode($data);
            
            $headers = array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData),
                "X-Token: {$token}"
            );
            
            $curl = curl_init('https://'.$ip.'/onu_allow_list?form=batch');
            
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($curl);
            
            if (curl_errno($curl)) 
            {
                return response()->json(['error' => curl_error($curl)]);
            }
            
            curl_close($curl);
    
            if(strstr($response,'success') !== false)
            {
                return true;
            }
            else
            {
                return response()->json(['error' => curl_error($curl)]);
            }
        
        }
        return response()->json(['error' => 'unknow error']);
    }
}
