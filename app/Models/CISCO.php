<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class CISCO extends Model
{
    use HasFactory;

    
    static public function Client_Side_Info($ip,$username,$password,$read,$write,$user)
    {
     
        $html = [];
      
        $clone = 0;
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);     

        $Alias = '';
        try {$Alias  = $snmp->walk("1.3.6.1.2.1.31.1.1.1.18", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

     
        foreach ($Alias as $key => $value) 
        {
            $value = str_replace('STRING: ','', $value);
            $value = str_replace("\"",'', $value);

            if (strpos($value, $user) !== false) 
            {
                $clone++;
                $html['Client']         = $value;
                $html['ifIndex']        = $key;

                $html['Name']       = '';
                $html['Status']     = '';
                $html['Admin']      = '';
                $html['Speed']      = '';
                $html['InError']    = '';
                $html['OutError']   = '';

                try { 
                        $Name       = str_replace('STRING: ','',$snmp->get("1.3.6.1.2.1.2.2.1.2.".$key, TRUE));                
                        $Status     = str_replace('INTEGER: ','',$snmp->get("1.3.6.1.2.1.2.2.1.8.".$key, TRUE));
                        $Admin      = str_replace('INTEGER: ','',$snmp->get("1.3.6.1.2.1.2.2.1.7.".$key, TRUE));
                        $Speed      = str_replace('Gauge32: ','',$snmp->get("1.3.6.1.2.1.31.1.1.1.15.".$key, TRUE));                      
                        $InError    = str_replace('Counter32: ','',$snmp->get("1.3.6.1.2.1.2.2.1.14.".$key, TRUE));
                        $OutError   = str_replace('Counter32: ','',$snmp->get("1.3.6.1.2.1.2.2.1.20.".$key, TRUE));
                } 
                catch (\Exception $e) 
                {}

                $html['Name']       = $Name;
                $html['Status']     = $Status;
                $html['Admin']      = $Admin;
                $html['Speed']      = $Speed;
                $html['InError']    = $InError;
                $html['OutError']   = $OutError;

          

                            $lastTwoDigits = substr($key, -2);  

                            try { 
                                    $Vlan = $snmp->walk("1.3.6.1.4.1.9.9.46.1.3.1.1.4.1", TRUE);  
                            } 
                            catch (\Exception $e) 
                            {}
                
                            
                            foreach ($Vlan as $keyVlan => $ValueOfVlan) 
                            {
                                $MacSNMP = new \SNMP(\SNMP::VERSION_2c, $ip, $read.'@'.$keyVlan);  

                           
                                try { 
                                        $iNDEX  = $MacSNMP->walk("1.3.6.1.2.1.17.4.3.1.2", TRUE);  

                                        foreach($iNDEX as $key => $value) 
                                        {
                                            $value = str_replace('INTEGER: ','',$value);         
                                            if($value == $lastTwoDigits)
                                            {
                                                try { 
                                                        $Macs = $MacSNMP->walk("1.3.6.1.2.1.17.4.3.1.1.".$key, TRUE); 
                                                } 
                                                catch (\Exception $e) 
                                                {}
                                                
                                                if(!empty($Macs))
                                                {
                                                    foreach ($Macs as $keyMacs => $valueMacs) 
                                                    {
                                                        $valueMacs = str_replace('Hex-STRING: ','',$valueMacs);
                                                        $valueMacs = trim(str_replace("\"",'', $valueMacs));
                                                        $valueMacs = str_replace(" ",':', $valueMacs);
                                                        
                                                        $Vendoor = '';
                                                        if(!empty($valueMacs))
                                                        {
                                                            $Vendoor = CISCO::MacFind_SNMP($valueMacs);
                                                        }
                                                        
                                                        $Params = [];
                                                        $Params ['name']    = $Name;
                                                        $Params ['Vlan']    = $keyVlan;
                                                        $Params ['Mac']     = $valueMacs;
                                                        $Params ['Vendoor'] = $Vendoor;

                                                        $html ['Mac_List_'.$keyMacs] = $Params;
                                                    }
                                                }
                                            }
                                        } 


                                } 
                                catch (\Exception $e) 
                                {$iNDEX  = '';}
                                 
                                 
                            } 
             
                
            }


            

        }


        if(!$clone)
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა სვიჩზე']);
        }
  


        $html ['clone'] = $clone;

        return $html;
    }

    static public function ClientSideSwitchData($ip,$read,$username,$password)
    {
        $html = [];
        $PonCoordinates = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $Name = '';
        try {$Name = ($snmp->walk("1.3.6.1.2.1.2.2.1.2", TRUE));} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        try {
                foreach ($Name as $key => $value) 
                {
                    $value = str_replace('STRING: ','', $value);
                    if(strpos($value,'GigabitEthernet') !== FALSE)
                    {
                        $Status     = str_replace('INTEGER: ','',$snmp->get("1.3.6.1.2.1.2.2.1.8.".$key, TRUE));
                        $Alias      = str_replace('STRING: ','',$snmp->get("1.3.6.1.2.1.31.1.1.1.18.".$key, TRUE));
                        $Speed      = str_replace('Gauge32: ','',$snmp->get("1.3.6.1.2.1.31.1.1.1.15.".$key, TRUE));
                        $Mtu        = str_replace('INTEGER: ','',$snmp->get("1.3.6.1.2.1.2.2.1.4.".$key, TRUE));               
                        $InError    = str_replace('Counter32: ','',$snmp->get("1.3.6.1.2.1.2.2.1.14.".$key, TRUE));
                        $OutError   = str_replace('Counter32: ','',$snmp->get("1.3.6.1.2.1.2.2.1.20.".$key, TRUE));
                        $Admin      = str_replace('INTEGER: ','',$snmp->get("1.3.6.1.2.1.2.2.1.7.".$key, TRUE));
        
                        $PonCoordinates [] = $Alias.'|'.$value;
                        $Params = [];
                        $Params ['ifindex']  = $key;
                        $Params ['port']     = $value;
                        $Params ['name']     = $Alias;
                        $Params ['Status']   = $Status;
                        $Params ['Admin']    = $Admin;
                        $Params ['Speed']    = $Speed;
                        $Params ['Mtu']      = $Mtu;
                        $Params ['InError']  = $InError;
                        $Params ['OutError'] = $OutError;
        
                        $html ['PortList_'.$key] = $Params;        
                    }
                }
        } 
        catch (\Exception $e) 
        {
            $Params = [];
            $Params ['port']     = '';
            $Params ['name']     = '';
            $Params ['Status']   = '';
            $Params ['Speed']    = '';
            $Params ['Mtu']      ='';
            $Params ['InError']  = '';
            $Params ['OutError'] = '';
        
            $html ['PortList_'.$key] = $Params;  
        }

        $html ['DeviceType'] = 'CISCO';
        $html['PONcoordinates'] = $PonCoordinates;

        return $html;
    }

    static public function AdminPortOff($ip,$write,$port)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');  
 
        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {$snmp_RW->set('1.3.6.1.2.1.2.2.1.7.'.$port, 'i', '2');} 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }

        return true;
    }

    static public function AdminPortON($ip,$write,$port)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {$snmp_RW->set('1.3.6.1.2.1.2.2.1.7.'.$port ,'i', '1');} 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }

        return true;
    }
    static public function MacFind_SNMP($line)
    {

        $macAddres   = CISCO::extractMacAddress($line);
        $Converted   = CISCO::format_mac_address($macAddres);
        $Converted   = strtoupper($Converted);

        $json_string = Storage::get('mac-vendors-export.json');
        $mac_vendors = json_decode($json_string, true);
        $mac_prefix  = substr($Converted, 0, 8);


        foreach ($mac_vendors as $vendor)
        {
            if ($vendor['macPrefix'] === strtoupper($mac_prefix))
            {
                if ( strtoupper($mac_prefix) === '00:00:00')
                {
                    return "Unknow Device";
                }
                $Mac = trim(str_replace("\"",'',$vendor['vendorName']));
                return ($Mac);
            }
        }
    }
  
    static public function extractMacAddress($string)
    {
        preg_match('/([0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4})|(([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2}))|(([0-9A-Fa-f]{4}[-]){2}([0-9A-Fa-f]{4}))/', $string, $matches);
        if (isset($matches[0])) {
            return $matches[0];
        }
        return false;
    }

    static public function format_mac_address($mac_address) 
    {
        $mac_address = preg_replace('/[^0-9A-Fa-f]/', '', $mac_address);
        $mac_address = str_pad($mac_address, 12, '0', STR_PAD_LEFT);
        $mac_address = strtoupper($mac_address);
        $mac_address = implode(':', str_split($mac_address, 2));
        return $mac_address;
    }
}
