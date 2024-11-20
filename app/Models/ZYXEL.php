<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class ZYXEL extends Model
{
    use HasFactory;

    static public function Client_Side_Info($ip,$username,$password,$read,$write,$user)
    {
        $html = [];
        $html ['clone'] = 0;
 
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $PortList = '';$MacList = '';$Link = '';$Name = '';
        $port = ''; $zyx_ab_nom = ''; $status = ''; $portIndex = ''; $mac = '';$style = '';$PortErrorLink = '';
        try { $PortList = $snmp->walk("1.3.6.1.2.1.17.4.3.1.2", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try { 
                $MacList = $snmp->walk("1.3.6.1.2.1.17.4.3.1.1", TRUE);  
        } 
        catch (\Exception $e) 
        {}

        try { 
                $Link = $snmp->walk(".1.3.6.1.2.1.2.2.1.8", TRUE);  
        } 
        catch (\Exception $e) 
        {}

        try { 
                $Name = $snmp->walk(".1.3.6.1.2.1.31.1.1.1.18", TRUE);  

                foreach ($Name as $key => $value) 
                {   
                    $value = trim(str_replace('STRING: ','',$value));
                    $pos = strpos($value, $user);
                    if ($pos !== false) 
                    {
                        $html ['clone']++;
                        $port = $key;
                        $zyx_ab_nom = $value;
                    }
                } 
        } 
        catch (\Exception $e) 
        {}


        if(!empty($zyx_ab_nom))
        {
            $PortErrors =  '';
            $Admin      =  '';

            try {
                    $PortErrors = $snmp->walk("1.3.6.1.2.1.2.2.1.14.".trim($port), TRUE);
                    $Admin      = trim(str_replace('INTEGER: ','',$snmp->get("1.3.6.1.2.1.2.2.1.7.".trim($port), TRUE))); 
            } 
            catch (\Exception $e) 
            {}
           
            if($PortErrors)
            {
                foreach ($PortErrors as $key => $value) 
                {
                    if (strpos($value, ":") !== false) 
                    {
                        $value = explode(':',$value);
                        $PortErrorLink = trim($value[1]);
                    }
                    else
                    {
                        $PortErrorLink = trim($value);
                    }   
                }
            }
 

            foreach ($Link as $key => $value) 
            {
                if($key == $port)
                {
                    if (strpos($value, ":") !== false) 
                    {
                        $part = explode(':',$value);  
                        $status = $part['1'];
                    }
                    else 
                    {
                        $status = $value;
                    }
                }
            }
     
            foreach ($PortList as $key => $value) 
            { 
                if (strpos($value, ":") !== false) 
                {
                    $part = explode(':',$value);  
                    if (trim($part['1']) == $port)
                    {
                        $portIndex = $key;
                    } 
                }
                else
                {
                    if (trim($value) == $port)
                    {
                        $portIndex = $key;
                    } 
                }
      
            }
            
            $int = 0;
            foreach ($MacList as $key => $value) 
            { 
                if ($key == $portIndex)
                {
                    $Vlan = '';$PortReal = '';
                    foreach ($PortList as $Portkey => $PortVal) 
                    {
                        $PortVal = trim(str_replace('INTEGER: ','',$PortVal));
                        if($portIndex == $Portkey)  
                        {
                            $PortReal = $PortVal;
                            $Vlan = $snmp->get("1.3.6.1.2.1.17.7.1.4.5.1.1.".(int)$PortVal, TRUE);   
                            $Vlan = trim(str_replace('Gauge32: ','',$Vlan)); 
                        }
                    }

                    $int++;
                    $mac = trim(str_replace("Hex-STRING: ", "", $value));
                    $mac = str_replace("\"", "", $mac);
 
                    $item = [];
                    $item ['mac']       = ZYXEL::convertMacAddress(trim($mac));
                    $item ['vlan']      = $Vlan;
                    $item ['vendoor']   = ZYXEL::MacFind_SNMP(ZYXEL::convertMacAddress(trim($mac)));
                    $html['mac_list_'.$int] =  $item;
                } 

                
            }
        }
        else 
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა სვიჩზე']);
        }
             
 
 
        $html['Client']         = $zyx_ab_nom;
        $html['ifIndex']        = $portIndex;
        $html['Port']           = $port;
        $html['portAdmin']      = $Admin;
        $html['Link']           = $status;
        $html['Uptime']         = ZYXEL::Uptime($ip,$username,$password,$zyx_ab_nom);
        $html['PortErrorLink']  = $PortErrorLink;
 

        return $html;
    }

    static public function ClientSideSwitchData($ip,$read,$username,$password)
    {
        $html = [];
        $PonCoordinates = [];
        $html ['clone'] = 0;
 
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $AdminList = '';$Link = '';$Name = '';
        $port = ''; $zyx_ab_nom = ''; $status = ''; $portIndex = ''; $mac = '';$style = '';$PortErrorLink = '';

        try { $Name = $snmp->walk(".1.3.6.1.2.1.31.1.1.1.18", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
 
        try { 
                $AdminList = $snmp->walk("1.3.6.1.2.1.2.2.1.7", TRUE);  
        } 
        catch (\Exception $e) 
        {}

        try { 
                $Link = $snmp->walk(".1.3.6.1.2.1.2.2.1.8", TRUE);  
        } 
        catch (\Exception $e) 
        {}

        try { 
                $PortErrors = $snmp->walk("1.3.6.1.2.1.2.2.1.14", TRUE);
        } 
        catch (\Exception $e) 
        {}
 

        $int = 0;
        foreach ($Name as $key => $value) 
        {
            $int++;
            $value = trim(str_replace('STRING: ','',$value));

            $PonCoordinates [] = $value.'|'.$key;
            $item = [];
            $item ['port']    = $key;
            $item ['name']    = $value;
            $item ['link']    = trim(str_replace('INTEGER: ','',$Link[$key])); 
            $item ['admin']   = trim(str_replace('INTEGER: ','',$AdminList[$key]));
            $item ['error']   = trim(str_replace('Counter32: ','',$PortErrors[$key]));
           // $item ['error'] = trim(str_replace('INTEGER: ','',$Link[$key]));
            $html['PortList_'.$int] = $item;
        }

        $html ['DeviceType'] = 'ZYXEL';
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
 
    static public function Uptime($ip,$username,$password,$user)
    { 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, "http://".$ip."/FirstPageStatistics.html");
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($ch, CURLOPT_USERPWD, $username.":".$password);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $result = curl_exec($ch);
    
        if (curl_errno($ch)) 
        {
            return 'Error: ' . curl_error($ch);
        } 
        else 
        {
            if($result)
            {
                $dom = new \DOMDocument();
                $dom->loadHTML($result);
                $xpath = new \DOMXPath($dom);
     
                $query = "//td[contains(., '$user')]/ancestor::tr";
                $trs = $xpath->query($query);
                $int = 0 ;
                $Info = '';
                foreach ($trs as $tr)
                {
                    if($int == 1) 
                    {
                        $Info = $dom->saveHTML($tr);
                        break;
                    }
                    $int++ ;
                }
               
                if(strlen($Info) > 3)
                {
                    $dom = new \DOMDocument();
                    $dom ->loadHTML($Info);
                    $tds = $dom->getElementsByTagName('td');
            
                    $data = array();
                    foreach ($tds as $td) 
                    {
                      $data[] = trim($td->nodeValue);
                    }
            
    
                    $uptime = "";
                    $uptime = $data[12];
                    return $uptime;
             
                }
            }
 
            return false;
        }
    }

 

    static public function convertMacAddress($macAddress) 
    {
       $formatted_mac = str_replace(' ', ':', $macAddress);
       return $formatted_mac;
    }

    static public function MacFind_SNMP($line)
    {

        $macAddres   = ZYXEL::extractMacAddress($line);
        $Converted   = ZYXEL::format_mac_address($macAddres);
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


 