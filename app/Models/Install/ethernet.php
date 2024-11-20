<?php

namespace App\Models\Install;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ZYXEL;
use App\Models\PrivilegesModel;

class ethernet extends Model
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

}
