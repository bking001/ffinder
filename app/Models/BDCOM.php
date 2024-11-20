<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class BDCOM extends Model
{
    use HasFactory;

    static public function Uninstall_Side_OnuInfo($ip,$read,$write,$user,$oltName)
    {
                $html = [];
                $html ['clone'] = '';
         
                $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

                try {$ifAlias = $snmp->walk("IF-MIB::ifAlias", TRUE);} 
                catch (\Exception $e) 
                {
                    if (strpos($e->getMessage(), 'No response') !== false) 
                    {
                        return response()->json(['error' => $snmp->getError()]);
                    }
                }
        
                 
                $DescriptionClone = 0;

                foreach ($ifAlias as $key => $value) 
                {
                    $Real_Desc_Key = 0;

                    $value = trim(str_replace('STRING:','',$value));
                    if (strpos($value, $user) !== false)
                    {
                        $Real_Desc_Key = $key;
                        $DescriptionClone++;

                        $ItemArray = [];
                        $ItemArray ['ifIndex']     = $key;
                        $ItemArray ['description'] = $value;
   
                       try {
                                $ifDescr            = $snmp->walk(".1.3.6.1.2.1.2.2.1.2.".$Real_Desc_Key, TRUE);
                                foreach ($ifDescr as $key => $value) 
                                {
                                    $ItemArray ['ponPort'] = trim(str_replace('STRING: ','',$value));
                                }
                        }catch (\Exception $e){$html ['ponPort'] = '';}    

                        $MacOnu = '';
                        try {
                                    $MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$Real_Desc_Key, TRUE));				
                                    $MacOnu = str_replace("STRING:","",$MacOnu);
                                    $MacOnu = str_replace("\"", "",$MacOnu); 
                
                
                                    if(strlen($MacOnu) < 10 )
                                    {    
                                        $MacOnu     = ltrim($MacOnu);
                                        $inputMac   = bin2hex($MacOnu);    
                                        $macArray   = str_split($inputMac, 2);
                                        $MacOnu     = implode(':', $macArray);          
                                    }
                                    else
                                    {
                                        $MacOnu     = str_replace(" ", "",$MacOnu);
                                        $macArray   = str_split($MacOnu, 2);
                                        $MacOnu     = implode(':', $macArray);      
                                    }
                                    $ItemArray ['Mac'] = $MacOnu;
                                     
                            }catch (\Exception $e){ $ItemArray ['Mac'] = '';}
                            


                        try {
                                $OperStatus = '';  
                                $ifOperStatus       = $snmp->walk(".1.3.6.1.2.1.2.2.1.8.".$Real_Desc_Key, TRUE);
                                foreach ($ifOperStatus as $key => $value) 
                                { 
                                    $value = trim(str_replace('INTEGER: ','',$value));
                                    $value = preg_replace('/\(\d+\)/', '', $value);
                                    $ItemArray ['StatusOnu'] = trim(str_replace("\"",'',$value));
                                    $OperStatus = $value;
                                }

                                $Downtime = ''; $Uptime = '';   
                                if($OperStatus == 'up')
                                {
                                    try {
                                            $Uptime   	  = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.80.".$Real_Desc_Key, TRUE)); 
                                            $Uptime 	  = BDCOM::secondsToNormalTime($Uptime);
                                            $ItemArray ['Uptime']=  $Uptime;
                                    } 
                                    catch (\Exception $e){$ItemArray ['Uptime']= '';}
                                }
                                else
                                {
                                    $ReasonSecondKey = '';
                                    try {
                                        
                                            $Sec_Index_By_Onu_Mac  = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.3", TRUE);
                                    
                                            foreach ($Sec_Index_By_Onu_Mac as $Zkey => $valueEX) 
                                            {
                                                $valueEX = str_replace("Hex-STRING: ", "",$valueEX);  
                                                $valueEX = str_replace("STRING:","",$valueEX);
                                                $valueEX = str_replace("\"", "",$valueEX); 
        
        
                                                if(strlen($valueEX) < 10 )
                                                {    
                                                    $valueEX    = ltrim($valueEX);
                                                    $inputMac   = bin2hex($valueEX);    
                                                    $macArray   = str_split($inputMac, 2);
                                                    $valueEX    = implode(':', $macArray);          
                                                }
                                                else
                                                {
                                                    $valueEX      = str_replace(" ", "",$valueEX);
                                                    $macArray     = str_split($valueEX, 2);
                                                    $valueEX      = implode(':', $macArray);      
                                                }
        
                                                if(strtoupper($valueEX) == strtoupper($MacOnu)) $ReasonSecondKey = $Zkey;
                                            }
        
        
                                    } 
                                    catch (\Exception $e){$Sec_Index_By_Onu_Mac = '';}
                                     
                                    try {
                                            $Downtime  = $snmp->walk(".1.3.6.1.4.1.3320.101.11.1.1.10.".$ReasonSecondKey , TRUE);   
                                            foreach ($Downtime as $Xkey => $value_Downtime) 
                                            {
                                                $value_Downtime = str_replace('Hex-STRING: ','',$value_Downtime);
                                                $value_Downtime = trim(str_replace("\"","",$value_Downtime));
                            
                            
                                                $hexArray = explode(' ', $value_Downtime);
                                                $Year  = BDCOM::hexToDecimal($hexArray[0].$hexArray[1]);
                                                $Month = BDCOM::hexToDecimal($hexArray[2]);
                                                $Day   = BDCOM::hexToDecimal($hexArray[3]);
                            
                                                $Hour = BDCOM::hexToDecimal($hexArray[4]);
                                                $Min  = BDCOM::hexToDecimal($hexArray[5]);
                                                $Sec  = BDCOM::hexToDecimal($hexArray[6]);
                                            
                                                $date = new \DateTime("$Year-$Month-$Day $Hour:$Min:$Sec");
                                                $monthName = $date->format('F');
                                                $Tittle_Downtime = $Year.'-'.$Month.'-'.$Day.' '.$Hour.':'.$Min.':'.$Sec;
                                            }
                                        
                                            $givenDate = new \DateTime($Tittle_Downtime);
                                            $currentDateTime = new \DateTime();
                                            $timeDifference = $givenDate->diff($currentDateTime);
                                            $output = '';
                                            if ($timeDifference->y > 0) 
                                            {
                                                $output .= $timeDifference->y . ' y, ';
                                            }
                                                    
                                            if ($timeDifference->m > 0) 
                                            {
                                                $output .= $timeDifference->m . ' m, ';
                                            }
                                                    
                                            $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';       
                                            $Downtime = rtrim($output, ', '); 
                                            $ItemArray ['Downtime'] = $Downtime;
                                    } 
                                    catch (\Exception $e){$ItemArray ['Downtime'] = '';}
                                }
                            }catch (\Exception $e){$ItemArray ['StatusOnu'] = '';}    
                            
                      
                        $html ['ontList'.$Real_Desc_Key] = $ItemArray;
                    }   
                }
                if($DescriptionClone == 0)
                {
                    return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
                }       

                if($DescriptionClone > 1)
                {
                    $html ['clone'] = 'ეს დესქრიფშენი გაწერილია '.$DescriptionClone.' - ონუზე ';
                }
                
                $html ['oltType']    = 'BDCOM';
                $html ['oltAddress'] = $ip;
                $html ['oltName'] = $oltName;
        
                 
        return $html;
    }

    static public function Client_Side_OnuInfo($ip,$read,$write,$user)
    {
                $html = [];
                $html ['clone'] = '';
         
                $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

                try {$ifAlias = $snmp->walk("IF-MIB::ifAlias", TRUE);} 
                catch (\Exception $e) 
                {
                    if (strpos($e->getMessage(), 'No response') !== false) 
                    {
                        return response()->json(['error' => $snmp->getError()]);
                    }
                }
        
                $Real_Desc_Key = 0;
                $DescriptionClone = 0;

                foreach ($ifAlias as $key => $value) 
                {
                    $value = trim(str_replace('STRING:','',$value));
                    if (strpos($value, $user) !== false)
                    {
                        $Real_Desc_Key = $key;
                        $DescriptionClone++;
                        $html ['description'] = $value;
                    }   
                }
                if($DescriptionClone > 1)
                {
                    $html ['clone'] = 'ეს დესქრიფშენი გაწერილია '.$DescriptionClone.' - ონუზე ';
                }
       
                if(!empty($Real_Desc_Key))
                {
                    $html ['ifIndex'] = trim((int)$Real_Desc_Key);
                    $ifDescr = '';$ifOperStatus = '';$Onu_Status = '';$Dbm = '';$Dereg_Index = '';$OnyType = '';

                    try {
                            $ifDescr            = $snmp->walk(".1.3.6.1.2.1.2.2.1.2.".$Real_Desc_Key, TRUE);
                            foreach ($ifDescr as $key => $value) 
                            {
                                $html ['ponPort'] = trim(str_replace('STRING: ','',$value));
                            }
                        }catch (\Exception $e){$html ['ponPort'] = '';}    
                        
                        
                    try {
                            $ifOperStatus       = $snmp->walk(".1.3.6.1.2.1.2.2.1.8.".$Real_Desc_Key, TRUE);
                            foreach ($ifOperStatus as $key => $value) 
                            { 
                                $value = trim(str_replace('INTEGER: ','',$value));
                                $value = preg_replace('/\(\d+\)/', '', $value);
                                $html ['operateStatus'] = trim(str_replace("\"",'',$value));
                            }
                        }catch (\Exception $e){$html ['operateStatus'] = '';}         
                    
                    
                    try {
                            $Onu_Status         = $snmp->walk("1.3.6.1.4.1.3320.101.10.1.1.26.".$Real_Desc_Key, TRUE);
                            foreach ($Onu_Status as $key => $value) 
                            {                       
                                $value = trim(str_replace('INTEGER: ','',$value));
        
                                if (trim($value) == 0)$html ['onuStatus']       = 'authenticated';
                                else if (trim($value) == 1)$html ['onuStatus']  = 'registered';
                                else if (trim($value) == 2)$html ['onuStatus']  = 'deregistered';
                                else if (trim($value) == 3)$html ['onuStatus']  = 'auto-configured';
                                else if (trim($value) == 4)$html ['onuStatus']  = 'lost';
                                else if (trim($value) == 5)$html ['onuStatus']  = 'standby';
                                else $html ['onuStatus']  = 'N/A';
                            }
                        }catch (\Exception $e){$html ['onuStatus'] = '';}    


                    try {
                            $Dbm                = $snmp->walk("1.3.6.1.4.1.3320.101.10.5.1.5.".$Real_Desc_Key, TRUE);
                            foreach ($Dbm as $key => $value) 
                            {
                                $value = trim(str_replace('INTEGER: ','',($value)));
                                $html ['Dbm'] =  BDCOM::convertToDecimal(trim($value));
                            }
                        }catch (\Exception $e){$html ['Dbm'] = '';}     


                    try {
                            $Dereg_Index        = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.4", TRUE);
                            foreach ($Dereg_Index as $key => $value) 
                            {
                                $value = trim(str_replace('STRING: ','',$value));
                                if (strpos($value, $user) !== false)
                                {
                                    try {
                                            $Temp_Dereg = trim(str_replace('INTEGER: ','',$snmp->get(".1.3.6.1.4.1.3320.101.11.1.1.11.".$key, TRUE)));
                                            if(trim($Temp_Dereg) == '8')$html ['reason'] ="wire down";
                                            else if(trim($Temp_Dereg) == '9')$html ['reason'] ="power off";
                                            else if(trim($Temp_Dereg) == '2')$html ['reason'] ="normal";
                                            else if(trim($Temp_Dereg) == '7')$html ['reason'] ="llid admin down";
                                            else if(trim($Temp_Dereg) == '255')$html ['reason'] ="unknow";
                                            else $html ['reason'] ="unknow";

                                        }catch (\Exception $e){$html ['reason'] = '';}    
                                }
                            }
                        }catch (\Exception $e){$html ['reason'] = '';}                      


                    try {
                            $OnyType            = $snmp->walk("1.3.6.1.4.1.3320.101.10.1.1.1.".$Real_Desc_Key , TRUE);
                            foreach ($OnyType as $key => $value) 
                            {
                                $value = trim(str_replace('STRING: ','',$value));
                                $html ['onuType'] = trim(str_replace("\"",'',$value));
                            }
                        }catch (\Exception $e){$html ['onuType'] = '';}    

                }
                else 
                {
                    return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
                }       

                 
        return $html;
    }

    static public function Client_Side_OnuPorts($ip,$read,$write,$user)
    {
        $html = [];
        $html ['clone']    = '';
        $html ['shutdown'] = 0;
        $User_Not_Exist    = 0;
        $ifDescr = '';$ifAlias = '';$Onu_Status = '';$iface = [];

        $snmp      = new \SNMP(\SNMP::VERSION_2c, $ip, $read);   

        try {
                $ifDescr   = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);asort($ifDescr);
                foreach ($ifDescr as $key => $value) 
                {
                    $iface[$key]['IfId']=$key;
                    $value = str_replace("STRING: ", "", $value);
                    $value = str_replace("\"", "", $value);
                    $value = trim($value);
                    $iface[$key]['IfDescr'] = trim($value);
                }
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        

        try {
                    $ifAlias  = $snmp->walk("IF-MIB::ifAlias", TRUE);  
                    foreach ($ifAlias as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);
                        $value = trim($value);
                        $iface[$key]['ifAlias'] = trim($value);  
                    }
        }
        catch (\Exception $e){}    

        foreach ($iface as $key => $value)
        {
            if (strpos(trim($value['ifAlias']), $user) !== false)
            {
                $User_Not_Exist++;
                $If_Index = $key;
                $html ['ifIndex'] = $key;
                $VlanTrunck = '';
                $PortVlan = '';
                $AdminStatus = '';
                $PortStatus = '';

                $Pon_Port = $value['IfDescr'];
                $Desc     = $value['ifAlias'];

                $html ['description'] = $value['ifAlias'];
                $html ['ponPort']     = $value['IfDescr'];

                try {$VlanTrunck = $snmp->walk(".1.3.6.1.4.1.3320.101.12.1.1.18.".$key, TRUE);}
                catch (\Exception $e){}    

                try {$PortVlan         = $snmp->walk(".1.3.6.1.4.1.3320.101.12.1.1.3.".$key, TRUE);}
                catch (\Exception $e){}  

                try {$AdminStatus      = $snmp->walk("1.3.6.1.4.1.3320.101.12.1.1.7.".$key, TRUE);}
                catch (\Exception $e){}  

                try {
                        $PortStatus = $snmp->walk("1.3.6.1.4.1.3320.101.12.1.1.8.".$key, TRUE);
                        if (!empty($PortStatus))
                        {
                            foreach ($PortStatus as $key => $value) 
                            {
                                $size = count($PortStatus);$VlanType = '';$VlanID = '';$AdminStatusPort = '';
                                if($size <= 4)
                                {
                                    if(str_replace("INTEGER: ","", $VlanTrunck[$key]) == 0)$VlanType = "transparent";
                                    else if(str_replace("INTEGER: ","", $VlanTrunck[$key]) == 1)$VlanType = "tag";
                                    else if(str_replace("INTEGER: ","", $VlanTrunck[$key]) == 2)$VlanType = "translation";
                                    else if(str_replace("INTEGER: ","", $VlanTrunck[$key]) == 3)$VlanType = "stacking";
                                    else if(str_replace("INTEGER: ","", $VlanTrunck[$key]) == 4 || str_replace("INTEGER: ","", $VlanTrunck[$key]) == 254)$VlanType = "none";
                                                    
                                    $item = [];
                                    $item['portIndex'] = $key;
                                    $item['vlan'] = trim(str_replace("INTEGER: ", "", $PortVlan[$key]));
                                    $item['portStatus'] = trim(str_replace('INTEGER: ', '', $value));
                                    $item['VlanType'] = trim(str_replace("INTEGER: ", "", $VlanType));
                                    $item['portAdmin'] = trim(str_replace("INTEGER: ", "", $AdminStatus[$key]));                              
                                    $html["port_num_$key"] = $item;  
                                }
                                else
                                {
                                    $html ['shutdown'] = 1;
                                }
                            }
                        }
                        else
                        {
                            $html ['uni_ports'] = 'This ONU has no uni ports';
                        }
                    }
                catch (\Exception $e){}  
            }
        }

        if(!$User_Not_Exist) 
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }       

        $html ['User_Not_Exist'] = $User_Not_Exist;
        return $html;
    }

    static public function Client_Side_OnuMacs($ip,$read,$write,$user)   
    {
        $html = [];$iface = [];
        $html ['clone'] = '';
        $html ['shutdown'] = 0;
        $User_Not_Exist    = 0;
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);

        $ifDescr = '';$ifAlias = '';
        try {
                $ifDescr   = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);
                foreach ($ifDescr as $key => $value) 
                {
                    $iface[$key]['IfId']=$key;
                    $value=explode(' ', $value);
                    $value=end($value);
                    $value=trim($value);
                    $value = str_replace("\"", "", $value);
                    $iface[$key]['IfDescr'] = $value;
                }   
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {
                $ifAlias = $snmp->walk("IF-MIB::ifAlias", TRUE);  
                foreach ($ifAlias as $key => $value)
                {
                    $iface[$key]['IfId']=$key;
                    $value = str_replace("STRING: ", "", $value);
                    $value = str_replace("\"", "", $value);
                    $value = trim($value);
                    $iface[$key]['ifAlias']=trim($value);
                } 
        } 
        catch (\Exception $e) 
        {
            //
        }
         
        $MacIndex = '';$ab_nom_alias = '';$Pon_Port = '';
        foreach ($iface as $key => $value)  
        {                        
            if (strpos($value['ifAlias'], $user) !== false)   
            { 
                $User_Not_Exist++;
                $MacIndex     = $key;
                $ab_nom_alias = trim($value['ifAlias']);
                $Pon_Port     = $value['IfDescr'];

                $html ['description'] = $value['ifAlias'];
                $html ['ponPort']     = $value['IfDescr'];
            }
        }
  
        if(!empty($MacIndex))
        {
            $IsMacTrue = false;
            $RealVlan = '';
            $resultArray = array();
            
            try {
                    $snmp_RW->set('1.3.6.1.4.1.3320.101.9.2.1.0', 'i', $MacIndex);  
                    $Mac  = $snmp_RW->walk("1.3.6.1.4.1.3320.101.9.2.3", TRUE);             
              
                    foreach ($Mac as $key => $value) 
                    {
                        $lastNumbers = substr($key, strrpos($key, '.') + 1);            
                        if (!isset($resultArray[$lastNumbers])) 
                        {
                            $resultArray[$lastNumbers] = array();
                        }           
                        $resultArray[$lastNumbers][] = $value;
                    }
                } 
            catch (\Exception $e) 
            {
               //  
            }
 
            if($resultArray)
            {
                foreach ($resultArray as $keyz => $value) 
                {  
                    foreach ($value as $key => $value) 
                    {
                        if($key == 1)
                        {
                             
                            if (strpos($value, ':') !== false)
                            {
                                $Vlan     = explode('INTEGER: ',$value);
                                $RealVlan = $Vlan[1];
                            }
                            else 
                            {
                                $RealVlan = $value;
                            }
                        }
                        else if ($key == 2)
                        { 
                            $RealMac = ''; 
                            $RealMac = str_replace('Hex-STRING: ','',$value); 
                            $RealMac = str_replace('STRING: ','',$RealMac); 
                            $RealMac = trim(str_replace("\"", "",$RealMac));
                            $RealMac = str_replace(" ", ":",$RealMac);
                            if(strlen($RealMac) < 10 )
                            {  
                                $inputMac  = bin2hex($RealMac);
                                $RealMac  = substr($inputMac, 0, 2) . ':' . substr($inputMac, 2, 2) . ':' . substr($inputMac, 4, 2). ':' . substr($inputMac, 6, 2). ':' . substr($inputMac, 8, 2). ':' . substr($inputMac, 10, 2);
                            }

                     
                            $IsMacTrue = true;
                            $item = [];
                            $item['portIndex']      = $key;
                            $item['vlan']           = $RealVlan;
                            $item['mac']            = $RealMac;
                            $item['vendoor']        = BDCOM::MacFind_SNMP(($RealMac));
                            $html["MacList_$keyz"]  = $item; 
                        }
      
                    }
                }
                if(!$IsMacTrue)$html['shutdown'] = '1';
            }
            else $html['shutdown'] = '1';
        }

        if(!$User_Not_Exist) 
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }       

        return  $html;
    }

    static public function OnuAdminPortOff($ip,$read,$write,$ifindex,$portIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {$snmp_RW->set('1.3.6.1.4.1.3320.101.12.1.1.7.'.$ifindex.'.'.$portIndex, 'i', '2');} 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }

        return true;
    }

    static public function OnuAdminPortON($ip,$read,$write,$ifindex,$portIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {$snmp_RW->set('1.3.6.1.4.1.3320.101.12.1.1.7.'.$ifindex.'.'.$portIndex, 'i', '1');} 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }

        return true;
    }

    static public function OnuPortVlanChange($ip,$read,$write,$ifindex,$portIndex,$user,$vlan,$vlanMode)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        if($vlanMode == 0)$vlan = 1;
   
        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {$snmp_RW->set('1.3.6.1.4.1.3320.101.12.1.1.18.'.$ifindex.'.'.$portIndex, 'i', $vlanMode);} 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }

        try {$snmp_RW->set('1.3.6.1.4.1.3320.101.12.1.1.3.'.$ifindex.'.'.$portIndex, 'i', $vlan);} 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }
        
        return true;
    }
    
    static public function ClientSidePonStatus($ip,$read) 
    {
        $html = []; 
        $ifDescr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try {$ifDescr = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($ifDescr as $key => $value) 
        {
            $value = trim(str_replace('STRING: ','',$value));

            if (strpos($value, "EPON") !== false && strpos($value, ":") == false) 
            {
                $item = [];
                $item['PonName']       = $value;
                $item['PonIndex']      = $key;
                $html["PonList_$key"]  = $item; 
            }
        }
        return $html;
    }

    static public function ClientSidePonData($ip,$pon,$read) 
    {
        $html = []; 
        $PonCoordinates = [];
        $ifDescr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try {$ifDescr = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        $Sec_Index_By_Onu_Mac = '';
        try {$Sec_Index_By_Onu_Mac  = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.3", TRUE);} 
        catch (\Exception $e){$Sec_Index_By_Onu_Mac = '';}


        $PonIndex = '';
        try {$PonIndex  = $snmp->walk(".1.3.6.1.4.1.3320.101.9.1.1.2", TRUE);} 
        catch (\Exception $e){$PonIndex = '';}


        $Pon_Key = '';$TotalPowerOff = 0;$TotalWireDown = 0;
        foreach ($ifDescr as $key => $value) 
        {
            if(str_replace("STRING: ", "", $value) == strtoupper($pon))
            {
                $Pon_Key = $key;    
            }
        }

        $resultArray = array();
        $arrayCount  = 0;
        if($PonIndex && !empty($PonIndex))
        {
            foreach ($PonIndex as $key => $value) 
            {
                if(str_replace("INTEGER: ", "", $value) == $Pon_Key)
                {
                    $resultArray[$arrayCount++] = $key;
                }
            }
        }
        else
        {
            return response()->json(['error' => "Pon Is Empty"]);
        }
 

        $IsAdminStatus = '';
        try {$IsAdminStatus  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.9.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $activeOnuNum = '';
        try {$activeOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.21.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $inactiveOnuNum = '';
        try {$inactiveOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.22.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $IsAdminStatus = str_replace('INTEGER: ','',$IsAdminStatus);
        if($IsAdminStatus == 1)
        {
            $Admin = "up";
        }
        else 
        {
            $Admin = "down";
        }

		$Online  = '';$Offline = '';
        $Online  = str_replace('INTEGER: ','',$activeOnuNum);      
        $Offline = str_replace('INTEGER: ','',$inactiveOnuNum);     
 

		foreach ($resultArray as $key => $value) 
		{
			 
			$Status = '';
			try {$Status = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.8.".$value, TRUE));} 
			catch (\Exception $e){$Status = '';}

			$Description = '';
			try {$Description = str_replace("STRING: ", "",$snmp->get("IF-MIB::ifAlias.".$value, TRUE)); if(!$Description)$Description = 'N/A';} 
			catch (\Exception $e){$Description = 'N/A';}
			 
			$Type = '';
			try {
					$Type = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.1.".$value, TRUE));
					$Type = trim(str_replace("\"", "",$Type));	
			} 
			catch (\Exception $e){$Type = '';}
			 

			$MacOnu = '';
			try {
					$MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$value, TRUE));				
                    $MacOnu = str_replace("STRING:","",$MacOnu);
                    $MacOnu = str_replace("\"", "",$MacOnu); 


                    if(strlen($MacOnu) < 10 )
                    {    
                        $MacOnu     = ltrim($MacOnu);
                        $inputMac   = bin2hex($MacOnu);    
                        $macArray   = str_split($inputMac, 2);
                        $MacOnu     = implode(':', $macArray);          
                    }
                    else
                    {
                        $MacOnu     = str_replace(" ", "",$MacOnu);
                        $macArray   = str_split($MacOnu, 2);
                        $MacOnu     = implode(':', $macArray);      
                    }

                    $MacForSecondIndex = $MacOnu;
                    // $MacOnu = trim(str_replace("STRING: ", "",$MacOnu));
                    // $MacOnu = trim(str_replace("\"", "",$MacOnu));
                    // $MacOnu = str_replace(" ", ":",$MacOnu);
                    // if(strlen($MacOnu) < 15 )
                    // {  
                    //     $inputMac  = bin2hex($MacOnu);
                    //     $MacOnu    = substr($inputMac, 0, 4) . '.' . substr($inputMac, 4, 4) . '.' . substr($inputMac, 8, 4);
                    // }
			} 
			catch (\Exception $e){$MacOnu = '';}
			 
			 
			$OnuOperateStatus = '';
			try {
					$OnuOperateStatus =  str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.26.".$value, TRUE));
					$OnuStatus = "";
                    if (trim($OnuOperateStatus) == 0)$OnuStatus      = 'authenticated';
                    else if (trim($OnuOperateStatus) == 1)$OnuStatus = 'registered';
                    else if (trim($OnuOperateStatus) == 2)$OnuStatus = 'deregistered';
                    else if (trim($OnuOperateStatus) == 3)$OnuStatus = 'auto-configured';
                    else if (trim($OnuOperateStatus) == 4)$OnuStatus = 'lost';
                    else if (trim($OnuOperateStatus) == 5)$OnuStatus = 'standby';
                    else $OnuStatus = 'N/A';
			} 
			catch (\Exception $e){$OnuOperateStatus = '';}

			$ReasonSecondKey;$Deregreason;
			foreach ($Sec_Index_By_Onu_Mac as $Zkey => $valueEX) 
			{
				$valueEX = str_replace("Hex-STRING: ", "",$valueEX);  
                $valueEX = str_replace("STRING:","",$valueEX);
                $valueEX = str_replace("\"", "",$valueEX); 


                if(strlen($valueEX) < 10 )
                {    
                    $valueEX    = ltrim($valueEX);
                    $inputMac   = bin2hex($valueEX);    
                    $macArray   = str_split($inputMac, 2);
                    $valueEX    = implode(':', $macArray);          
                }
                else
                {
                    $valueEX      = str_replace(" ", "",$valueEX);
                    $macArray     = str_split($valueEX, 2);
                    $valueEX      = implode(':', $macArray);      
                }

				// if(strlen($valueEX) < 15 )
				// {  
				// 	$valueEX      = str_replace("\"", "",$valueEX); 
				// 	$inputMac     = bin2hex($valueEX);  
				// 	$macArray     = str_split($inputMac, 2);
				// 	$valueEX      = implode(':', $macArray);              
				// }
				if(strtoupper($valueEX) == strtoupper($MacForSecondIndex)) $ReasonSecondKey = $Zkey;
			}

			$Onu_StatusX = '';
			if(!empty($ReasonSecondKey))
			{
				try {																		 
						$Deregreason = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.11.1.1.11.".$ReasonSecondKey, TRUE));
						if(trim($Deregreason) == '8'){$Onu_StatusX = "wire down";if(strpos($Status,'down') !== false)$TotalWireDown++;}
						else if(trim($Deregreason) == '9'){$Onu_StatusX = "power off";if(strpos($Status,'down') !== false)$TotalPowerOff++;}
						else if(trim($Deregreason) == '2')$Onu_StatusX = "normal";
						else if(trim($Deregreason) == '7')$Onu_StatusX = "llid admin down";
						else if(trim($Deregreason) == '255')$Onu_StatusX = "unknow";
						else if(trim($Deregreason) == '0')$Onu_StatusX = "unknow";
				} 
				catch (\Exception $e){$Onu_StatusX = '';}
			}
			 

			$Onu_RX = '';
			try {
					$Onu_RX = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.5.1.5.".$value, TRUE));
					$Onu_RX = BDCOM::convertToDecimal($Onu_RX);
			} 
			catch (\Exception $e){$Onu_RX = '';}
			 
			
			$UptimeX = '';$TittleUptime = '';
			try {
					$Uptime   	  = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.80.".$value, TRUE)); 
					$UptimeX 	  = BDCOM::secondsToNormalTime($Uptime);
					$pastTime 	  = time() - trim($Uptime);
                    $TittleUptime = date("Y-m-d H:i:s", $pastTime);
			} 
			catch (\Exception $e){$UptimeX = '';$TittleUptime = '';}
			 

			$Downtime = '';$Tittle_Downtime = '';
			try {
					$Downtime  = $snmp->walk(".1.3.6.1.4.1.3320.101.11.1.1.10.".$ReasonSecondKey , TRUE);   
					foreach ($Downtime as $Xkey => $value_Downtime) 
					{
						$value_Downtime = str_replace('Hex-STRING: ','',$value_Downtime);
						$value_Downtime = trim(str_replace("\"","",$value_Downtime));
	
	
						$hexArray = explode(' ', $value_Downtime);
						$Year  = BDCOM::hexToDecimal($hexArray[0].$hexArray[1]);
						$Month = BDCOM::hexToDecimal($hexArray[2]);
						$Day   = BDCOM::hexToDecimal($hexArray[3]);
	
						$Hour = BDCOM::hexToDecimal($hexArray[4]);
						$Min  = BDCOM::hexToDecimal($hexArray[5]);
						$Sec  = BDCOM::hexToDecimal($hexArray[6]);
					
						$date = new \DateTime("$Year-$Month-$Day $Hour:$Min:$Sec");
						$monthName = $date->format('F');
						$Tittle_Downtime = $Year.'-'.$Month.'-'.$Day.' '.$Hour.':'.$Min.':'.$Sec;
					}

					$givenDate = new \DateTime($Tittle_Downtime);
                    $currentDateTime = new \DateTime();
                    $timeDifference = $givenDate->diff($currentDateTime);
                    $output = '';
                    if ($timeDifference->y > 0) 
					{
                        $output .= $timeDifference->y . ' y, ';
                    }
                            
                    if ($timeDifference->m > 0) 
					{
                        $output .= $timeDifference->m . ' m, ';
                    }
                            
                    $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';       
                    $Downtime = rtrim($output, ', '); 
			} 
			catch (\Exception $e){$Downtime = '';$Tittle_Downtime = '';}
			 

			$PonPort = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.2.".$value, TRUE));


            $CoordOnuStatus = '';
            if (strpos($Status, '1') !== false)
            {
                $CoordOnuStatus = 1;
            }
            else 
            {
                $CoordOnuStatus = 2;
            }
             
            $PonCoordinates[] = $Description.'|'.$PonPort.'|'.str_replace(',',' ',$UptimeX).'|'.str_replace(',',' ',$Downtime).'|'.$CoordOnuStatus;

			$item = [];
			$item['IfIndex']   	  	  = $value; 
			$item['PonPort']   	  	  = $PonPort;   
			$item['onuStatus']   	  = $Status;   
			$item['Description']	  = $Description;     
			$item['Type'] 		 	  = $Type;     
			$item['MacOnu'] 	 	  = $MacOnu;  
			$item['OnuOperateStatus'] = $OnuStatus;  
			$item['Deregreason'] 	  = $Onu_StatusX;  
			$item['Onu_RX'] 	  	  = $Onu_RX;  
			$item['Uptime'] 	  	  = $UptimeX;  
			$item['TittleUptime'] 	  = $TittleUptime;  
			$item['DownTime'] 	  	  = $Downtime; 
			$item['Tittle_Downtime']  = $Tittle_Downtime; 
			$html["onu_num$key"] = $item;  	 
		}

		$html ['Online']  		= $Online; 
		$html ['Offline']  		= $Offline; 
		$html ['PonAdmin'] 		= $Admin; 
		$html['TotalOnu'] 		= $arrayCount; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 

        $html['PONcoordinates'] = $PonCoordinates;
 
        return $html;
    }
    
    static public function ClientSidePonAllOnline($ip,$pon,$read) 
    {
        $html = []; 
        $PonCoordinates = [];
        $ifDescr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try {$ifDescr = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        $Sec_Index_By_Onu_Mac = '';
        try {$Sec_Index_By_Onu_Mac  = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.3", TRUE);} 
        catch (\Exception $e){$Sec_Index_By_Onu_Mac = '';}


        $PonIndex = '';
        try {$PonIndex  = $snmp->walk(".1.3.6.1.4.1.3320.101.9.1.1.2", TRUE);} 
        catch (\Exception $e){$PonIndex = '';}


        $Pon_Key = '';$TotalPowerOff = 0;$TotalWireDown = 0;
        foreach ($ifDescr as $key => $value) 
        {
            if(str_replace("STRING: ", "", $value) == strtoupper($pon))
            {
                $Pon_Key = $key;    
            }
        }

        $resultArray = array();
        $arrayCount  = 0;
        if($PonIndex && !empty($PonIndex))
        {
            foreach ($PonIndex as $key => $value) 
            {
                if(str_replace("INTEGER: ", "", $value) == $Pon_Key)
                {
                    $resultArray[$arrayCount++] = $key;
                }
            }
        }
        else
        {
            return response()->json(['error' => "Pon Is Empty"]);
        }
 

        $IsAdminStatus = '';
        try {$IsAdminStatus  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.9.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $activeOnuNum = '';
        try {$activeOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.21.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $inactiveOnuNum = '';
        try {$inactiveOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.22.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $IsAdminStatus = str_replace('INTEGER: ','',$IsAdminStatus);
        if($IsAdminStatus == 1)
        {
            $Admin = "up";
        }
        else 
        {
            $Admin = "down";
        }

		$Online  = '';$Offline = '';
        $Online  = str_replace('INTEGER: ','',$activeOnuNum);      
        $Offline = str_replace('INTEGER: ','',$inactiveOnuNum);     
 

		foreach ($resultArray as $key => $value) 
		{
			 
			$Status = '';
			try {$Status = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.8.".$value, TRUE));} 
			catch (\Exception $e){$Status = '';}

			$Description = '';
			try {$Description = str_replace("STRING: ", "",$snmp->get("IF-MIB::ifAlias.".$value, TRUE)); if(!$Description)$Description = 'N/A';} 
			catch (\Exception $e){$Description = 'N/A';}
			 
			$Type = '';
			try {
					$Type = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.1.".$value, TRUE));
					$Type = trim(str_replace("\"", "",$Type));	
			} 
			catch (\Exception $e){$Type = '';}
			 

			$MacOnu = '';
			try {
					$MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$value, TRUE));				
                    $MacOnu = str_replace("STRING:","",$MacOnu);
                    $MacOnu = str_replace("\"", "",$MacOnu); 


                    if(strlen($MacOnu) < 10 )
                    {    
                        $MacOnu     = ltrim($MacOnu);
                        $inputMac   = bin2hex($MacOnu);    
                        $macArray   = str_split($inputMac, 2);
                        $MacOnu     = implode(':', $macArray);          
                    }
                    else
                    {
                        $MacOnu     = str_replace(" ", "",$MacOnu);
                        $macArray   = str_split($MacOnu, 2);
                        $MacOnu     = implode(':', $macArray);      
                    }

                    $MacForSecondIndex = $MacOnu;
                    // $MacOnu = trim(str_replace("STRING: ", "",$MacOnu));
                    // $MacOnu = trim(str_replace("\"", "",$MacOnu));
                    // $MacOnu = str_replace(" ", ":",$MacOnu);
                    // if(strlen($MacOnu) < 15 )
                    // {  
                    //     $inputMac  = bin2hex($MacOnu);
                    //     $MacOnu    = substr($inputMac, 0, 4) . '.' . substr($inputMac, 4, 4) . '.' . substr($inputMac, 8, 4);
                    // }
			} 
			catch (\Exception $e){$MacOnu = '';}
			 
			 
			$OnuOperateStatus = '';
			try {
					$OnuOperateStatus =  str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.26.".$value, TRUE));
					$OnuStatus = "";
                    if (trim($OnuOperateStatus) == 0)$OnuStatus      = 'authenticated';
                    else if (trim($OnuOperateStatus) == 1)$OnuStatus = 'registered';
                    else if (trim($OnuOperateStatus) == 2)$OnuStatus = 'deregistered';
                    else if (trim($OnuOperateStatus) == 3)$OnuStatus = 'auto-configured';
                    else if (trim($OnuOperateStatus) == 4)$OnuStatus = 'lost';
                    else if (trim($OnuOperateStatus) == 5)$OnuStatus = 'standby';
                    else $OnuStatus = 'N/A';
			} 
			catch (\Exception $e){$OnuOperateStatus = '';}

			$ReasonSecondKey;$Deregreason;
			foreach ($Sec_Index_By_Onu_Mac as $Zkey => $valueEX) 
			{
				$valueEX = str_replace("Hex-STRING: ", "",$valueEX);  
                $valueEX = str_replace("STRING:","",$valueEX);
                $valueEX = str_replace("\"", "",$valueEX); 


                if(strlen($valueEX) < 10 )
                {    
                    $valueEX    = ltrim($valueEX);
                    $inputMac   = bin2hex($valueEX);    
                    $macArray   = str_split($inputMac, 2);
                    $valueEX    = implode(':', $macArray);          
                }
                else
                {
                    $valueEX      = str_replace(" ", "",$valueEX);
                    $macArray     = str_split($valueEX, 2);
                    $valueEX      = implode(':', $macArray);      
                }

				// if(strlen($valueEX) < 15 )
				// {  
				// 	$valueEX      = str_replace("\"", "",$valueEX); 
				// 	$inputMac     = bin2hex($valueEX);  
				// 	$macArray     = str_split($inputMac, 2);
				// 	$valueEX      = implode(':', $macArray);              
				// }
				if(strtoupper($valueEX) == strtoupper($MacForSecondIndex)) $ReasonSecondKey = $Zkey;
			}

			$Onu_StatusX = '';
			if(!empty($ReasonSecondKey))
			{
				try {																		 
						$Deregreason = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.11.1.1.11.".$ReasonSecondKey, TRUE));
						if(trim($Deregreason) == '8'){$Onu_StatusX = "wire down";if(strpos($Status,'down') !== false)$TotalWireDown++;}
						else if(trim($Deregreason) == '9'){$Onu_StatusX = "power off";if(strpos($Status,'down') !== false)$TotalPowerOff++;}
						else if(trim($Deregreason) == '2')$Onu_StatusX = "normal";
						else if(trim($Deregreason) == '7')$Onu_StatusX = "llid admin down";
						else if(trim($Deregreason) == '255')$Onu_StatusX = "unknow";
						else if(trim($Deregreason) == '0')$Onu_StatusX = "unknow";
				} 
				catch (\Exception $e){$Onu_StatusX = '';}
			}
			 

			$Onu_RX = '';
			try {
					$Onu_RX = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.5.1.5.".$value, TRUE));
					$Onu_RX = BDCOM::convertToDecimal($Onu_RX);
			} 
			catch (\Exception $e){$Onu_RX = '';}
			 
			
			$UptimeX = '';$TittleUptime = '';
			try {
					$Uptime   	  = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.80.".$value, TRUE)); 
					$UptimeX 	  = BDCOM::secondsToNormalTime($Uptime);
					$pastTime 	  = time() - trim($Uptime);
                    $TittleUptime = date("Y-m-d H:i:s", $pastTime);
			} 
			catch (\Exception $e){$UptimeX = '';$TittleUptime = '';}
			 

			$Downtime = '';$Tittle_Downtime = '';
			try {
					$Downtime  = $snmp->walk(".1.3.6.1.4.1.3320.101.11.1.1.10.".$ReasonSecondKey , TRUE);   
					foreach ($Downtime as $Xkey => $value_Downtime) 
					{
						$value_Downtime = str_replace('Hex-STRING: ','',$value_Downtime);
						$value_Downtime = trim(str_replace("\"","",$value_Downtime));
	
	
						$hexArray = explode(' ', $value_Downtime);
						$Year  = BDCOM::hexToDecimal($hexArray[0].$hexArray[1]);
						$Month = BDCOM::hexToDecimal($hexArray[2]);
						$Day   = BDCOM::hexToDecimal($hexArray[3]);
	
						$Hour = BDCOM::hexToDecimal($hexArray[4]);
						$Min  = BDCOM::hexToDecimal($hexArray[5]);
						$Sec  = BDCOM::hexToDecimal($hexArray[6]);
					
						$date = new \DateTime("$Year-$Month-$Day $Hour:$Min:$Sec");
						$monthName = $date->format('F');
						$Tittle_Downtime = $Year.'-'.$Month.'-'.$Day.' '.$Hour.':'.$Min.':'.$Sec;
					}

					$givenDate = new \DateTime($Tittle_Downtime);
                    $currentDateTime = new \DateTime();
                    $timeDifference = $givenDate->diff($currentDateTime);
                    $output = '';
                    if ($timeDifference->y > 0) 
					{
                        $output .= $timeDifference->y . ' y, ';
                    }
                            
                    if ($timeDifference->m > 0) 
					{
                        $output .= $timeDifference->m . ' m, ';
                    }
                            
                    $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';       
                    $Downtime = rtrim($output, ', '); 
			} 
			catch (\Exception $e){$Downtime = '';$Tittle_Downtime = '';}
			 

			$PonPort = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.2.".$value, TRUE));

            if(strpos($Status, 'up') !== false)
            {
 
                $CoordOnuStatus = '';
                if (strpos($Status, '1') !== false)
                {
                    $CoordOnuStatus = 1;
                }
                else 
                {
                    $CoordOnuStatus = 2;
                }      
                $PonCoordinates[] = $Description.'|'.$PonPort.'|'.str_replace(',',' ',$UptimeX).'|'.str_replace(',',' ',$Downtime).'|'.$CoordOnuStatus;

                $item = [];
                $item['IfIndex']   	  	  = $value; 
                $item['PonPort']   	  	  = $PonPort;   
                $item['onuStatus']   	  = $Status;   
                $item['Description']	  = $Description;     
                $item['Type'] 		 	  = $Type;     
                $item['MacOnu'] 	 	  = $MacOnu;  
                $item['OnuOperateStatus'] = $OnuStatus;  
                $item['Deregreason'] 	  = $Onu_StatusX;  
                $item['Onu_RX'] 	  	  = $Onu_RX;  
                $item['Uptime'] 	  	  = $UptimeX;  
                $item['TittleUptime'] 	  = $TittleUptime;  
                $item['DownTime'] 	  	  = $Downtime; 
                $item['Tittle_Downtime']  = $Tittle_Downtime; 
                $html["onu_num$key"] = $item;  
            }

		}

		$html ['Online']  		= $Online; 
		$html ['Offline']  		= $Offline; 
		$html ['PonAdmin'] 		= $Admin; 
		$html['TotalOnu'] 		= $arrayCount; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 

        $html['PONcoordinates'] = $PonCoordinates;
 
        return $html;
    }

    static public function ClientSidePonAllOfflinea($ip,$pon,$read) 
    {
        $html = []; 
        $PonCoordinates = [];
        $ifDescr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try {$ifDescr = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        $Sec_Index_By_Onu_Mac = '';
        try {$Sec_Index_By_Onu_Mac  = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.3", TRUE);} 
        catch (\Exception $e){$Sec_Index_By_Onu_Mac = '';}


        $PonIndex = '';
        try {$PonIndex  = $snmp->walk(".1.3.6.1.4.1.3320.101.9.1.1.2", TRUE);} 
        catch (\Exception $e){$PonIndex = '';}


        $Pon_Key = '';$TotalPowerOff = 0;$TotalWireDown = 0;
        foreach ($ifDescr as $key => $value) 
        {
            if(str_replace("STRING: ", "", $value) == strtoupper($pon))
            {
                $Pon_Key = $key;    
            }
        }

        $resultArray = array();
        $arrayCount  = 0;
        if($PonIndex && !empty($PonIndex))
        {
            foreach ($PonIndex as $key => $value) 
            {
                if(str_replace("INTEGER: ", "", $value) == $Pon_Key)
                {
                    $resultArray[$arrayCount++] = $key;
                }
            }
        }
        else
        {
            return response()->json(['error' => "Pon Is Empty"]);
        }
 

        $IsAdminStatus = '';
        try {$IsAdminStatus  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.9.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $activeOnuNum = '';
        try {$activeOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.21.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $inactiveOnuNum = '';
        try {$inactiveOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.22.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $IsAdminStatus = str_replace('INTEGER: ','',$IsAdminStatus);
        if($IsAdminStatus == 1)
        {
            $Admin = "up";
        }
        else 
        {
            $Admin = "down";
        }

		$Online  = '';$Offline = '';
        $Online  = str_replace('INTEGER: ','',$activeOnuNum);      
        $Offline = str_replace('INTEGER: ','',$inactiveOnuNum);     
 

		foreach ($resultArray as $key => $value) 
		{
			 
			$Status = '';
			try {$Status = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.8.".$value, TRUE));} 
			catch (\Exception $e){$Status = '';}

			$Description = '';
			try {$Description = str_replace("STRING: ", "",$snmp->get("IF-MIB::ifAlias.".$value, TRUE)); if(!$Description)$Description = 'N/A';} 
			catch (\Exception $e){$Description = 'N/A';}
			 
			$Type = '';
			try {
					$Type = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.1.".$value, TRUE));
					$Type = trim(str_replace("\"", "",$Type));	
			} 
			catch (\Exception $e){$Type = '';}
			 

			$MacOnu = '';
			try {
					$MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$value, TRUE));				
                    $MacOnu = str_replace("STRING:","",$MacOnu);
                    $MacOnu = str_replace("\"", "",$MacOnu); 


                    if(strlen($MacOnu) < 10 )
                    {    
                        $MacOnu     = ltrim($MacOnu);
                        $inputMac   = bin2hex($MacOnu);    
                        $macArray   = str_split($inputMac, 2);
                        $MacOnu     = implode(':', $macArray);          
                    }
                    else
                    {
                        $MacOnu     = str_replace(" ", "",$MacOnu);
                        $macArray   = str_split($MacOnu, 2);
                        $MacOnu     = implode(':', $macArray);      
                    }

                    $MacForSecondIndex = $MacOnu;
                    // $MacOnu = trim(str_replace("STRING: ", "",$MacOnu));
                    // $MacOnu = trim(str_replace("\"", "",$MacOnu));
                    // $MacOnu = str_replace(" ", ":",$MacOnu);
                    // if(strlen($MacOnu) < 15 )
                    // {  
                    //     $inputMac  = bin2hex($MacOnu);
                    //     $MacOnu    = substr($inputMac, 0, 4) . '.' . substr($inputMac, 4, 4) . '.' . substr($inputMac, 8, 4);
                    // }
			} 
			catch (\Exception $e){$MacOnu = '';}
			 
			 
			$OnuOperateStatus = '';
			try {
					$OnuOperateStatus =  str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.26.".$value, TRUE));
					$OnuStatus = "";
                    if (trim($OnuOperateStatus) == 0)$OnuStatus      = 'authenticated';
                    else if (trim($OnuOperateStatus) == 1)$OnuStatus = 'registered';
                    else if (trim($OnuOperateStatus) == 2)$OnuStatus = 'deregistered';
                    else if (trim($OnuOperateStatus) == 3)$OnuStatus = 'auto-configured';
                    else if (trim($OnuOperateStatus) == 4)$OnuStatus = 'lost';
                    else if (trim($OnuOperateStatus) == 5)$OnuStatus = 'standby';
                    else $OnuStatus = 'N/A';
			} 
			catch (\Exception $e){$OnuOperateStatus = '';}

			$ReasonSecondKey;$Deregreason;
			foreach ($Sec_Index_By_Onu_Mac as $Zkey => $valueEX) 
			{
				$valueEX = str_replace("Hex-STRING: ", "",$valueEX);  
                $valueEX = str_replace("STRING:","",$valueEX);
                $valueEX = str_replace("\"", "",$valueEX); 


                if(strlen($valueEX) < 10 )
                {    
                    $valueEX    = ltrim($valueEX);
                    $inputMac   = bin2hex($valueEX);    
                    $macArray   = str_split($inputMac, 2);
                    $valueEX    = implode(':', $macArray);          
                }
                else
                {
                    $valueEX      = str_replace(" ", "",$valueEX);
                    $macArray     = str_split($valueEX, 2);
                    $valueEX      = implode(':', $macArray);      
                }

				// if(strlen($valueEX) < 15 )
				// {  
				// 	$valueEX      = str_replace("\"", "",$valueEX); 
				// 	$inputMac     = bin2hex($valueEX);  
				// 	$macArray     = str_split($inputMac, 2);
				// 	$valueEX      = implode(':', $macArray);              
				// }
				if(strtoupper($valueEX) == strtoupper($MacForSecondIndex)) $ReasonSecondKey = $Zkey;
			}

			$Onu_StatusX = '';
			if(!empty($ReasonSecondKey))
			{
				try {																		 
						$Deregreason = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.11.1.1.11.".$ReasonSecondKey, TRUE));
						if(trim($Deregreason) == '8'){$Onu_StatusX = "wire down";if(strpos($Status,'down') !== false)$TotalWireDown++;}
						else if(trim($Deregreason) == '9'){$Onu_StatusX = "power off";if(strpos($Status,'down') !== false)$TotalPowerOff++;}
						else if(trim($Deregreason) == '2')$Onu_StatusX = "normal";
						else if(trim($Deregreason) == '7')$Onu_StatusX = "llid admin down";
						else if(trim($Deregreason) == '255')$Onu_StatusX = "unknow";
						else if(trim($Deregreason) == '0')$Onu_StatusX = "unknow";
				} 
				catch (\Exception $e){$Onu_StatusX = '';}
			}
			 

			$Onu_RX = '';
			try {
					$Onu_RX = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.5.1.5.".$value, TRUE));
					$Onu_RX = BDCOM::convertToDecimal($Onu_RX);
			} 
			catch (\Exception $e){$Onu_RX = '';}
			 
			
			$UptimeX = '';$TittleUptime = '';
			try {
					$Uptime   	  = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.80.".$value, TRUE)); 
					$UptimeX 	  = BDCOM::secondsToNormalTime($Uptime);
					$pastTime 	  = time() - trim($Uptime);
                    $TittleUptime = date("Y-m-d H:i:s", $pastTime);
			} 
			catch (\Exception $e){$UptimeX = '';$TittleUptime = '';}
			 

			$Downtime = '';$Tittle_Downtime = '';
			try {
					$Downtime  = $snmp->walk(".1.3.6.1.4.1.3320.101.11.1.1.10.".$ReasonSecondKey , TRUE);   
					foreach ($Downtime as $Xkey => $value_Downtime) 
					{
						$value_Downtime = str_replace('Hex-STRING: ','',$value_Downtime);
						$value_Downtime = trim(str_replace("\"","",$value_Downtime));
	
	
						$hexArray = explode(' ', $value_Downtime);
						$Year  = BDCOM::hexToDecimal($hexArray[0].$hexArray[1]);
						$Month = BDCOM::hexToDecimal($hexArray[2]);
						$Day   = BDCOM::hexToDecimal($hexArray[3]);
	
						$Hour = BDCOM::hexToDecimal($hexArray[4]);
						$Min  = BDCOM::hexToDecimal($hexArray[5]);
						$Sec  = BDCOM::hexToDecimal($hexArray[6]);
					
						$date = new \DateTime("$Year-$Month-$Day $Hour:$Min:$Sec");
						$monthName = $date->format('F');
						$Tittle_Downtime = $Year.'-'.$Month.'-'.$Day.' '.$Hour.':'.$Min.':'.$Sec;
					}

					$givenDate = new \DateTime($Tittle_Downtime);
                    $currentDateTime = new \DateTime();
                    $timeDifference = $givenDate->diff($currentDateTime);
                    $output = '';
                    if ($timeDifference->y > 0) 
					{
                        $output .= $timeDifference->y . ' y, ';
                    }
                            
                    if ($timeDifference->m > 0) 
					{
                        $output .= $timeDifference->m . ' m, ';
                    }
                            
                    $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';       
                    $Downtime = rtrim($output, ', '); 
			} 
			catch (\Exception $e){$Downtime = '';$Tittle_Downtime = '';}
			 

			$PonPort = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.2.".$value, TRUE));

            if(strpos($Status, 'down') !== false)
            {
                $CoordOnuStatus = '';
                if (strpos($Status, '1') !== false)
                {
                    $CoordOnuStatus = 1;
                }
                else 
                {
                    $CoordOnuStatus = 2;
                }      
                $PonCoordinates[] = $Description.'|'.$PonPort.'|'.str_replace(',',' ',$UptimeX).'|'.str_replace(',',' ',$Downtime).'|'.$CoordOnuStatus;

                $item = [];
                $item['IfIndex']   	  	  = $value; 
                $item['PonPort']   	  	  = $PonPort;   
                $item['onuStatus']   	  = $Status;   
                $item['Description']	  = $Description;     
                $item['Type'] 		 	  = $Type;     
                $item['MacOnu'] 	 	  = $MacOnu;  
                $item['OnuOperateStatus'] = $OnuStatus;  
                $item['Deregreason'] 	  = $Onu_StatusX;  
                $item['Onu_RX'] 	  	  = $Onu_RX;  
                $item['Uptime'] 	  	  = $UptimeX;  
                $item['TittleUptime'] 	  = $TittleUptime;  
                $item['DownTime'] 	  	  = $Downtime; 
                $item['Tittle_Downtime']  = $Tittle_Downtime; 
                $html["onu_num$key"] = $item;  
            }

		}

		$html ['Online']  		= $Online; 
		$html ['Offline']  		= $Offline; 
		$html ['PonAdmin'] 		= $Admin; 
		$html['TotalOnu'] 		= $arrayCount; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 

        $html['PONcoordinates'] = $PonCoordinates;
 
        return $html;
    }

    static public function ClientSidePonAllPowerOff($ip,$pon,$read) 
    {
        $html = []; 
        $PonCoordinates = [];
        $ifDescr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try {$ifDescr = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        $Sec_Index_By_Onu_Mac = '';
        try {$Sec_Index_By_Onu_Mac  = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.3", TRUE);} 
        catch (\Exception $e){$Sec_Index_By_Onu_Mac = '';}


        $PonIndex = '';
        try {$PonIndex  = $snmp->walk(".1.3.6.1.4.1.3320.101.9.1.1.2", TRUE);} 
        catch (\Exception $e){$PonIndex = '';}


        $Pon_Key = '';$TotalPowerOff = 0;$TotalWireDown = 0;
        foreach ($ifDescr as $key => $value) 
        {
            if(str_replace("STRING: ", "", $value) == strtoupper($pon))
            {
                $Pon_Key = $key;    
            }
        }

        $resultArray = array();
        $arrayCount  = 0;
        if($PonIndex && !empty($PonIndex))
        {
            foreach ($PonIndex as $key => $value) 
            {
                if(str_replace("INTEGER: ", "", $value) == $Pon_Key)
                {
                    $resultArray[$arrayCount++] = $key;
                }
            }
        }
        else
        {
            return response()->json(['error' => "Pon Is Empty"]);
        }
 

        $IsAdminStatus = '';
        try {$IsAdminStatus  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.9.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $activeOnuNum = '';
        try {$activeOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.21.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $inactiveOnuNum = '';
        try {$inactiveOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.22.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $IsAdminStatus = str_replace('INTEGER: ','',$IsAdminStatus);
        if($IsAdminStatus == 1)
        {
            $Admin = "up";
        }
        else 
        {
            $Admin = "down";
        }

		$Online  = '';$Offline = '';
        $Online  = str_replace('INTEGER: ','',$activeOnuNum);      
        $Offline = str_replace('INTEGER: ','',$inactiveOnuNum);     
 

		foreach ($resultArray as $key => $value) 
		{
			 
			$Status = '';
			try {$Status = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.8.".$value, TRUE));} 
			catch (\Exception $e){$Status = '';}

			$Description = '';
			try {$Description = str_replace("STRING: ", "",$snmp->get("IF-MIB::ifAlias.".$value, TRUE)); if(!$Description)$Description = 'N/A';} 
			catch (\Exception $e){$Description = 'N/A';}
			 
			$Type = '';
			try {
					$Type = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.1.".$value, TRUE));
					$Type = trim(str_replace("\"", "",$Type));	
			} 
			catch (\Exception $e){$Type = '';}
			 

			$MacOnu = '';
			try {
					$MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$value, TRUE));				
                    $MacOnu = str_replace("STRING:","",$MacOnu);
                    $MacOnu = str_replace("\"", "",$MacOnu); 


                    if(strlen($MacOnu) < 10 )
                    {    
                        $MacOnu     = ltrim($MacOnu);
                        $inputMac   = bin2hex($MacOnu);    
                        $macArray   = str_split($inputMac, 2);
                        $MacOnu     = implode(':', $macArray);          
                    }
                    else
                    {
                        $MacOnu     = str_replace(" ", "",$MacOnu);
                        $macArray   = str_split($MacOnu, 2);
                        $MacOnu     = implode(':', $macArray);      
                    }

                    $MacForSecondIndex = $MacOnu;
                    // $MacOnu = trim(str_replace("STRING: ", "",$MacOnu));
                    // $MacOnu = trim(str_replace("\"", "",$MacOnu));
                    // $MacOnu = str_replace(" ", ":",$MacOnu);
                    // if(strlen($MacOnu) < 15 )
                    // {  
                    //     $inputMac  = bin2hex($MacOnu);
                    //     $MacOnu    = substr($inputMac, 0, 4) . '.' . substr($inputMac, 4, 4) . '.' . substr($inputMac, 8, 4);
                    // }
			} 
			catch (\Exception $e){$MacOnu = '';}
			 
			 
			$OnuOperateStatus = '';
			try {
					$OnuOperateStatus =  str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.26.".$value, TRUE));
					$OnuStatus = "";
                    if (trim($OnuOperateStatus) == 0)$OnuStatus      = 'authenticated';
                    else if (trim($OnuOperateStatus) == 1)$OnuStatus = 'registered';
                    else if (trim($OnuOperateStatus) == 2)$OnuStatus = 'deregistered';
                    else if (trim($OnuOperateStatus) == 3)$OnuStatus = 'auto-configured';
                    else if (trim($OnuOperateStatus) == 4)$OnuStatus = 'lost';
                    else if (trim($OnuOperateStatus) == 5)$OnuStatus = 'standby';
                    else $OnuStatus = 'N/A';
			} 
			catch (\Exception $e){$OnuOperateStatus = '';}

			$ReasonSecondKey;$Deregreason;
			foreach ($Sec_Index_By_Onu_Mac as $Zkey => $valueEX) 
			{
				$valueEX = str_replace("Hex-STRING: ", "",$valueEX);  
                $valueEX = str_replace("STRING:","",$valueEX);
                $valueEX = str_replace("\"", "",$valueEX); 


                if(strlen($valueEX) < 10 )
                {    
                    $valueEX    = ltrim($valueEX);
                    $inputMac   = bin2hex($valueEX);    
                    $macArray   = str_split($inputMac, 2);
                    $valueEX    = implode(':', $macArray);          
                }
                else
                {
                    $valueEX      = str_replace(" ", "",$valueEX);
                    $macArray     = str_split($valueEX, 2);
                    $valueEX      = implode(':', $macArray);      
                }

				// if(strlen($valueEX) < 15 )
				// {  
				// 	$valueEX      = str_replace("\"", "",$valueEX); 
				// 	$inputMac     = bin2hex($valueEX);  
				// 	$macArray     = str_split($inputMac, 2);
				// 	$valueEX      = implode(':', $macArray);              
				// }
				if(strtoupper($valueEX) == strtoupper($MacForSecondIndex)) $ReasonSecondKey = $Zkey;
			}

			$Onu_StatusX = '';
			if(!empty($ReasonSecondKey))
			{
				try {																		 
						$Deregreason = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.11.1.1.11.".$ReasonSecondKey, TRUE));
						if(trim($Deregreason) == '8'){$Onu_StatusX = "wire down";if(strpos($Status,'down') !== false)$TotalWireDown++;}
						else if(trim($Deregreason) == '9'){$Onu_StatusX = "power off";if(strpos($Status,'down') !== false)$TotalPowerOff++;}
						else if(trim($Deregreason) == '2')$Onu_StatusX = "normal";
						else if(trim($Deregreason) == '7')$Onu_StatusX = "llid admin down";
						else if(trim($Deregreason) == '255')$Onu_StatusX = "unknow";
						else if(trim($Deregreason) == '0')$Onu_StatusX = "unknow";
				} 
				catch (\Exception $e){$Onu_StatusX = '';}
			}
			 

			$Onu_RX = '';
			try {
					$Onu_RX = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.5.1.5.".$value, TRUE));
					$Onu_RX = BDCOM::convertToDecimal($Onu_RX);
			} 
			catch (\Exception $e){$Onu_RX = '';}
			 
			
			$UptimeX = '';$TittleUptime = '';
			try {
					$Uptime   	  = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.80.".$value, TRUE)); 
					$UptimeX 	  = BDCOM::secondsToNormalTime($Uptime);
					$pastTime 	  = time() - trim($Uptime);
                    $TittleUptime = date("Y-m-d H:i:s", $pastTime);
			} 
			catch (\Exception $e){$UptimeX = '';$TittleUptime = '';}
			 

			$Downtime = '';$Tittle_Downtime = '';
			try {
					$Downtime  = $snmp->walk(".1.3.6.1.4.1.3320.101.11.1.1.10.".$ReasonSecondKey , TRUE);   
					foreach ($Downtime as $Xkey => $value_Downtime) 
					{
						$value_Downtime = str_replace('Hex-STRING: ','',$value_Downtime);
						$value_Downtime = trim(str_replace("\"","",$value_Downtime));
	
	
						$hexArray = explode(' ', $value_Downtime);
						$Year  = BDCOM::hexToDecimal($hexArray[0].$hexArray[1]);
						$Month = BDCOM::hexToDecimal($hexArray[2]);
						$Day   = BDCOM::hexToDecimal($hexArray[3]);
	
						$Hour = BDCOM::hexToDecimal($hexArray[4]);
						$Min  = BDCOM::hexToDecimal($hexArray[5]);
						$Sec  = BDCOM::hexToDecimal($hexArray[6]);
					
						$date = new \DateTime("$Year-$Month-$Day $Hour:$Min:$Sec");
						$monthName = $date->format('F');
						$Tittle_Downtime = $Year.'-'.$Month.'-'.$Day.' '.$Hour.':'.$Min.':'.$Sec;
					}

					$givenDate = new \DateTime($Tittle_Downtime);
                    $currentDateTime = new \DateTime();
                    $timeDifference = $givenDate->diff($currentDateTime);
                    $output = '';
                    if ($timeDifference->y > 0) 
					{
                        $output .= $timeDifference->y . ' y, ';
                    }
                            
                    if ($timeDifference->m > 0) 
					{
                        $output .= $timeDifference->m . ' m, ';
                    }
                            
                    $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';       
                    $Downtime = rtrim($output, ', '); 
			} 
			catch (\Exception $e){$Downtime = '';$Tittle_Downtime = '';}
			 

			$PonPort = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.2.".$value, TRUE));

            if(strpos($Onu_StatusX, 'power off') !== false && (strpos($Status, 'down') !== false))    
            {
                $CoordOnuStatus = '';
                if (strpos($Status, '1') !== false)
                {
                    $CoordOnuStatus = 1;
                }
                else 
                {
                    $CoordOnuStatus = 2;
                }      
                $PonCoordinates[] = $Description.'|'.$PonPort.'|'.str_replace(',',' ',$UptimeX).'|'.str_replace(',',' ',$Downtime).'|'.$CoordOnuStatus;

                $item = [];
                $item['IfIndex']   	  	  = $value; 
                $item['PonPort']   	  	  = $PonPort;   
                $item['onuStatus']   	  = $Status;   
                $item['Description']	  = $Description;     
                $item['Type'] 		 	  = $Type;     
                $item['MacOnu'] 	 	  = $MacOnu;  
                $item['OnuOperateStatus'] = $OnuStatus;  
                $item['Deregreason'] 	  = $Onu_StatusX;  
                $item['Onu_RX'] 	  	  = $Onu_RX;  
                $item['Uptime'] 	  	  = $UptimeX;  
                $item['TittleUptime'] 	  = $TittleUptime;  
                $item['DownTime'] 	  	  = $Downtime; 
                $item['Tittle_Downtime']  = $Tittle_Downtime; 
                $html["onu_num$key"] = $item;  
            }

		}

		$html ['Online']  		= $Online; 
		$html ['Offline']  		= $Offline; 
		$html ['PonAdmin'] 		= $Admin; 
		$html['TotalOnu'] 		= $arrayCount; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 

        $html['PONcoordinates'] = $PonCoordinates;
 
        return $html;
    }

    static public function ClientSidePonAllWireDown($ip,$pon,$read) 
    {
        $html = []; 
        $PonCoordinates = [];
        $ifDescr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try {$ifDescr = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        $Sec_Index_By_Onu_Mac = '';
        try {$Sec_Index_By_Onu_Mac  = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.3", TRUE);} 
        catch (\Exception $e){$Sec_Index_By_Onu_Mac = '';}


        $PonIndex = '';
        try {$PonIndex  = $snmp->walk(".1.3.6.1.4.1.3320.101.9.1.1.2", TRUE);} 
        catch (\Exception $e){$PonIndex = '';}


        $Pon_Key = '';$TotalPowerOff = 0;$TotalWireDown = 0;
        foreach ($ifDescr as $key => $value) 
        {
            if(str_replace("STRING: ", "", $value) == strtoupper($pon))
            {
                $Pon_Key = $key;    
            }
        }

        $resultArray = array();
        $arrayCount  = 0;
        if($PonIndex && !empty($PonIndex))
        {
            foreach ($PonIndex as $key => $value) 
            {
                if(str_replace("INTEGER: ", "", $value) == $Pon_Key)
                {
                    $resultArray[$arrayCount++] = $key;
                }
            }
        }
        else
        {
            return response()->json(['error' => "Pon Is Empty"]);
        }
 

        $IsAdminStatus = '';
        try {$IsAdminStatus  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.9.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $activeOnuNum = '';
        try {$activeOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.21.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $inactiveOnuNum = '';
        try {$inactiveOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.22.".$Pon_Key , TRUE);} 
        catch (\Exception $e){}

        $IsAdminStatus = str_replace('INTEGER: ','',$IsAdminStatus);
        if($IsAdminStatus == 1)
        {
            $Admin = "up";
        }
        else 
        {
            $Admin = "down";
        }

		$Online  = '';$Offline = '';
        $Online  = str_replace('INTEGER: ','',$activeOnuNum);      
        $Offline = str_replace('INTEGER: ','',$inactiveOnuNum);     
 

		foreach ($resultArray as $key => $value) 
		{
			 
			$Status = '';
			try {$Status = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.8.".$value, TRUE));} 
			catch (\Exception $e){$Status = '';}

			$Description = '';
			try {$Description = str_replace("STRING: ", "",$snmp->get("IF-MIB::ifAlias.".$value, TRUE)); if(!$Description)$Description = 'N/A';} 
			catch (\Exception $e){$Description = 'N/A';}
			 
			$Type = '';
			try {
					$Type = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.1.".$value, TRUE));
					$Type = trim(str_replace("\"", "",$Type));	
			} 
			catch (\Exception $e){$Type = '';}
			 

			$MacOnu = '';
			try {
					$MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$value, TRUE));				
                    $MacOnu = str_replace("STRING:","",$MacOnu);
                    $MacOnu = str_replace("\"", "",$MacOnu); 


                    if(strlen($MacOnu) < 10 )
                    {    
                        $MacOnu     = ltrim($MacOnu);
                        $inputMac   = bin2hex($MacOnu);    
                        $macArray   = str_split($inputMac, 2);
                        $MacOnu     = implode(':', $macArray);          
                    }
                    else
                    {
                        $MacOnu     = str_replace(" ", "",$MacOnu);
                        $macArray   = str_split($MacOnu, 2);
                        $MacOnu     = implode(':', $macArray);      
                    }

                    $MacForSecondIndex = $MacOnu;
                    // $MacOnu = trim(str_replace("STRING: ", "",$MacOnu));
                    // $MacOnu = trim(str_replace("\"", "",$MacOnu));
                    // $MacOnu = str_replace(" ", ":",$MacOnu);
                    // if(strlen($MacOnu) < 15 )
                    // {  
                    //     $inputMac  = bin2hex($MacOnu);
                    //     $MacOnu    = substr($inputMac, 0, 4) . '.' . substr($inputMac, 4, 4) . '.' . substr($inputMac, 8, 4);
                    // }
			} 
			catch (\Exception $e){$MacOnu = '';}
			 
			 
			$OnuOperateStatus = '';
			try {
					$OnuOperateStatus =  str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.26.".$value, TRUE));
					$OnuStatus = "";
                    if (trim($OnuOperateStatus) == 0)$OnuStatus      = 'authenticated';
                    else if (trim($OnuOperateStatus) == 1)$OnuStatus = 'registered';
                    else if (trim($OnuOperateStatus) == 2)$OnuStatus = 'deregistered';
                    else if (trim($OnuOperateStatus) == 3)$OnuStatus = 'auto-configured';
                    else if (trim($OnuOperateStatus) == 4)$OnuStatus = 'lost';
                    else if (trim($OnuOperateStatus) == 5)$OnuStatus = 'standby';
                    else $OnuStatus = 'N/A';
			} 
			catch (\Exception $e){$OnuOperateStatus = '';}

			$ReasonSecondKey;$Deregreason;
			foreach ($Sec_Index_By_Onu_Mac as $Zkey => $valueEX) 
			{
				$valueEX = str_replace("Hex-STRING: ", "",$valueEX);  
                $valueEX = str_replace("STRING:","",$valueEX);
                $valueEX = str_replace("\"", "",$valueEX); 


                if(strlen($valueEX) < 10 )
                {    
                    $valueEX    = ltrim($valueEX);
                    $inputMac   = bin2hex($valueEX);    
                    $macArray   = str_split($inputMac, 2);
                    $valueEX    = implode(':', $macArray);          
                }
                else
                {
                    $valueEX      = str_replace(" ", "",$valueEX);
                    $macArray     = str_split($valueEX, 2);
                    $valueEX      = implode(':', $macArray);      
                }

				// if(strlen($valueEX) < 15 )
				// {  
				// 	$valueEX      = str_replace("\"", "",$valueEX); 
				// 	$inputMac     = bin2hex($valueEX);  
				// 	$macArray     = str_split($inputMac, 2);
				// 	$valueEX      = implode(':', $macArray);              
				// }
				if(strtoupper($valueEX) == strtoupper($MacForSecondIndex)) $ReasonSecondKey = $Zkey;
			}

			$Onu_StatusX = '';
			if(!empty($ReasonSecondKey))
			{
				try {																		 
						$Deregreason = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.11.1.1.11.".$ReasonSecondKey, TRUE));
						if(trim($Deregreason) == '8'){$Onu_StatusX = "wire down";if(strpos($Status,'down') !== false)$TotalWireDown++;}
						else if(trim($Deregreason) == '9'){$Onu_StatusX = "power off";if(strpos($Status,'down') !== false)$TotalPowerOff++;}
						else if(trim($Deregreason) == '2')$Onu_StatusX = "normal";
						else if(trim($Deregreason) == '7')$Onu_StatusX = "llid admin down";
						else if(trim($Deregreason) == '255')$Onu_StatusX = "unknow";
						else if(trim($Deregreason) == '0')$Onu_StatusX = "unknow";
				} 
				catch (\Exception $e){$Onu_StatusX = '';}
			}
			 

			$Onu_RX = '';
			try {
					$Onu_RX = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.5.1.5.".$value, TRUE));
					$Onu_RX = BDCOM::convertToDecimal($Onu_RX);
			} 
			catch (\Exception $e){$Onu_RX = '';}
			 
			
			$UptimeX = '';$TittleUptime = '';
			try {
					$Uptime   	  = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.80.".$value, TRUE)); 
					$UptimeX 	  = BDCOM::secondsToNormalTime($Uptime);
					$pastTime 	  = time() - trim($Uptime);
                    $TittleUptime = date("Y-m-d H:i:s", $pastTime);
			} 
			catch (\Exception $e){$UptimeX = '';$TittleUptime = '';}
			 

			$Downtime = '';$Tittle_Downtime = '';
			try {
					$Downtime  = $snmp->walk(".1.3.6.1.4.1.3320.101.11.1.1.10.".$ReasonSecondKey , TRUE);   
					foreach ($Downtime as $Xkey => $value_Downtime) 
					{
						$value_Downtime = str_replace('Hex-STRING: ','',$value_Downtime);
						$value_Downtime = trim(str_replace("\"","",$value_Downtime));
	
	
						$hexArray = explode(' ', $value_Downtime);
						$Year  = BDCOM::hexToDecimal($hexArray[0].$hexArray[1]);
						$Month = BDCOM::hexToDecimal($hexArray[2]);
						$Day   = BDCOM::hexToDecimal($hexArray[3]);
	
						$Hour = BDCOM::hexToDecimal($hexArray[4]);
						$Min  = BDCOM::hexToDecimal($hexArray[5]);
						$Sec  = BDCOM::hexToDecimal($hexArray[6]);
					
						$date = new \DateTime("$Year-$Month-$Day $Hour:$Min:$Sec");
						$monthName = $date->format('F');
						$Tittle_Downtime = $Year.'-'.$Month.'-'.$Day.' '.$Hour.':'.$Min.':'.$Sec;
					}

					$givenDate = new \DateTime($Tittle_Downtime);
                    $currentDateTime = new \DateTime();
                    $timeDifference = $givenDate->diff($currentDateTime);
                    $output = '';
                    if ($timeDifference->y > 0) 
					{
                        $output .= $timeDifference->y . ' y, ';
                    }
                            
                    if ($timeDifference->m > 0) 
					{
                        $output .= $timeDifference->m . ' m, ';
                    }
                            
                    $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';       
                    $Downtime = rtrim($output, ', '); 
			} 
			catch (\Exception $e){$Downtime = '';$Tittle_Downtime = '';}
			 

			$PonPort = str_replace("STRING: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.2.".$value, TRUE));

            if(strpos($Onu_StatusX, 'wire down') !== false && (strpos($Status, 'down') !== false))
            {
                $CoordOnuStatus = '';
                if (strpos($Status, '1') !== false)
                {
                    $CoordOnuStatus = 1;
                }
                else 
                {
                    $CoordOnuStatus = 2;
                }      
                $PonCoordinates[] = $Description.'|'.$PonPort.'|'.str_replace(',',' ',$UptimeX).'|'.str_replace(',',' ',$Downtime).'|'.$CoordOnuStatus;

                $item = [];
                $item['IfIndex']   	  	  = $value; 
                $item['PonPort']   	  	  = $PonPort;   
                $item['onuStatus']   	  = $Status;   
                $item['Description']	  = $Description;     
                $item['Type'] 		 	  = $Type;     
                $item['MacOnu'] 	 	  = $MacOnu;  
                $item['OnuOperateStatus'] = $OnuStatus;  
                $item['Deregreason'] 	  = $Onu_StatusX;  
                $item['Onu_RX'] 	  	  = $Onu_RX;  
                $item['Uptime'] 	  	  = $UptimeX;  
                $item['TittleUptime'] 	  = $TittleUptime;  
                $item['DownTime'] 	  	  = $Downtime; 
                $item['Tittle_Downtime']  = $Tittle_Downtime; 
                $html["onu_num$key"] = $item;  
            }

		}

		$html ['Online']  		= $Online; 
		$html ['Offline']  		= $Offline; 
		$html ['PonAdmin'] 		= $Admin; 
		$html['TotalOnu'] 		= $arrayCount; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 

        $html['PONcoordinates'] = $PonCoordinates;
 
        return $html;
    }

    static public function OnuRestart($ip,$read,$write,$ifIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {$snmp_RW->set('1.3.6.1.4.1.3320.101.10.1.1.29.'.$ifIndex, 'i', 0);} 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }
        
        return true;
    }

    static public function convertToDecimal($integerValue) 
    {
        $dbmValue = $integerValue / 10;
        return $dbmValue;
    }

    static public function MacFind_SNMP($line)
    {

        $macAddres   = BDCOM::extractMacAddress($line);
        $Converted   = BDCOM::format_mac_address($macAddres);
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
     
	static public function secondsToNormalTime($seconds) 
	{                                 
		$days = floor($seconds / (3600 * 24));
		$seconds %= (3600 * 24);
		
		$hours = floor($seconds / 3600);
		$seconds %= 3600;
		
		$minutes = floor($seconds / 60);
		$seconds %= 60;

		$result = $days.' d, '.$hours.' h, '.$minutes.' m';

		return $result;
	}

	static public function hexToDecimal($hex) 
	{
		$decimal = hexdec($hex);
	
		if ($decimal < 10) 
		{
			return (string)$decimal;
		}
		return $decimal;
	}

}
