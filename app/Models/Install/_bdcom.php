<?php

namespace App\Models\Install;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BDCOM;
use App\Models\PrivilegesModel;

class _bdcom extends Model
{
    use HasFactory;

    static public function BDCOM_SEARCH($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp,$macSN)  
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        
        $Macs = '';$Real_Mac = '';$oldMac = '';$exist = false;

        try {
                $Macs  = $snmp->walk("1.3.6.1.4.1.3320.101.10.1.1.3", TRUE); 
        }
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {
                $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                $ServerName = trim(str_replace("\"", "" , $ServerName));
                $ServerName = trim(str_replace("\'", "" , $ServerName));  
        }catch (\Exception $e){$ServerName  = '';}     
     
        try {                                    
 
                foreach ($Macs as $key => $value) 
                {
                    $FindedKey = '';
                    
                    $value = str_replace("Hex-STRING: ", "",$value);
                    $value = str_replace("STRING:","",$value);
                    $value = str_replace("\"", "",$value); 

                    if(strlen($value) < 10 )
                    {    
                        $value      = ltrim($value);
                        $inputMac   = bin2hex($value);    
                        $macArray   = str_split($inputMac, 2);
                        $value      = implode(':', $macArray);          
                    }
                    else
                    {
                        $value      = str_replace(" ", "",$value);
                        $macArray   = str_split($value, 2);
                        $value      = implode(':', $macArray);      
                    }
                    $oldMac =  $value;

                    $macSN = str_replace(":", "",$macSN); 
                    $macSN = str_replace(".", "",$macSN);   
                    $macSN = str_replace("-", "",$macSN);  
                    $macSN = str_replace(" ", "",$macSN);  
                    $macSN = str_replace("\"", "",$macSN); 
                    $macSN = strtoupper($macSN);

                    $value = str_replace(":", "",$value); 
                    $value = str_replace(".", "",$value);   
                    $value = str_replace("-", "",$value);  
                    $value = str_replace(" ", "",$value);  
                    $value = str_replace("\"", "",$value); 
                    $value = strtoupper($value);

                     
                    if(strlen($macSN) == 4) 
                    {
                        $value = substr($value, -4);
                        if($macSN == $value)
                        {
                            $FindedKey = $key;
                            $Real_Mac  = $oldMac;
                        }                       
                    }
                    else
                    {
                        if (strpos($value , $macSN) !== false) 
                        {
                            $FindedKey = $key;
                            $Real_Mac  = $oldMac;
                        }      
                    }

                    if($FindedKey)
                    {
 
                        try {
                                $DescrCheck = trim(str_replace("STRING: ", "",$snmp->get("IF-MIB::ifAlias.".$FindedKey, TRUE)));
                        }catch (\Exception $e){$DescrCheck  = '';}     
            
                        try {
                                $PonPort = trim(str_replace("STRING: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.2.".$FindedKey, TRUE)));
                        }catch (\Exception $e){$PonPort  = '';}    
            
                        try {
                                $OnyType    = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.4.1.3320.101.10.1.1.1.".$FindedKey , TRUE)));
                                $OnyType    = trim(str_replace("\"", "" , $OnyType));
                        }catch (\Exception $e){$OnyType  = '';}    
            
                        try {
                                $Dbm        = trim(str_replace("INTEGER: ", "",$snmp->get("1.3.6.1.4.1.3320.101.10.5.1.5.".$FindedKey, TRUE)));
                                $Dbm        = BDCOM::convertToDecimal(trim($Dbm));
                        }catch (\Exception $e){$Dbm  = '';} 
            
                        try {
                                $Onu_Status = trim(str_replace("INTEGER: ", "",$snmp->get("1.3.6.1.4.1.3320.101.10.1.1.26.".$FindedKey, TRUE)));
                                if (trim($Onu_Status) == 0)$Onu_Status       = 'authenticated';
                                else if (trim($Onu_Status) == 1)$Onu_Status  = 'registered';
                                else if (trim($Onu_Status) == 2)$Onu_Status  = 'deregistered';
                                else if (trim($Onu_Status) == 3)$Onu_Status  = 'auto-configured';
                                else if (trim($Onu_Status) == 4)$Onu_Status  = 'lost';
                                else if (trim($Onu_Status) == 5)$Onu_Status  = 'standby';
                                else $Onu_Status  = 'N/A';
                        }catch (\Exception $e){$Onu_Status  = '';}    
            
                        try {
                                $OperStatus = trim(str_replace("INTEGER: ", "",$snmp->get("1.3.6.1.2.1.2.2.1.8.".$FindedKey, TRUE)));
                                $OperStatus = preg_replace('/\(\d+\)/', '', $OperStatus);
                                $OperStatus = trim(str_replace("\"",'',$OperStatus));
                        }catch (\Exception $e){$PonPort  = '';}    
            
                        try {
                                $Dereg_Index = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.4", TRUE);
                                foreach ($Dereg_Index as $key => $value) 
                                {
                                    $value = trim(str_replace('STRING: ','',$value));
                                    if (strpos($value, $DescrCheck) !== false)
                                    {
                                        try {
                                                $Temp_Dereg = trim(str_replace('INTEGER: ','',$snmp->get(".1.3.6.1.4.1.3320.101.11.1.1.11.".$key, TRUE)));
                                                if(trim($Temp_Dereg) == '8')$reason ="wire down";
                                                else if(trim($Temp_Dereg) == '9')$reason ="power off";
                                                else if(trim($Temp_Dereg) == '2')$reason ="normal";
                                                else if(trim($Temp_Dereg) == '7')$reason ="llid admin down";
                                                else if(trim($Temp_Dereg) == '255')$reason ="unknow";
                                                else $reason ="unknow";
            
                                            }catch (\Exception $e){$reason = '';}    
                                    }
                                }
                        }catch (\Exception $e){$reason = '';}   
                        

                        $Downtime = ''; $Uptime = '';
                        if($OperStatus == 'up')
                        {
                            try {
                                    $Uptime   	  = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.80.".$FindedKey, TRUE)); 
                                    $Uptime 	  = BDCOM::secondsToNormalTime($Uptime);            
                            }catch (\Exception $e){$Uptime = '';}   
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
                
                                        if(strtoupper($valueEX) == strtoupper($Real_Mac)) $ReasonSecondKey = $Zkey;
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
                            } 
                            catch (\Exception $e){$Downtime = '';}
                        }
                

                      
                        $exist = true;

                        $item = [];
                        $item['ifindex']    = $FindedKey;
                        $item['Descr']      = $DescrCheck;
                        $item['PonPort']    = $PonPort;
                        $item['Mac']        = $Real_Mac;
                        $item['OnyType']    = $OnyType;
                        $item['Dbm']        = $Dbm;
                        $item['Onu_Status'] = $Onu_Status;
                        $item['OperStatus'] = $OperStatus;
                        $item['reason']     = $reason;
                        $item['Uptime']     = $Uptime;
                        $item['Downtime']   = $Downtime;
                        $html['OnuList_'.$FindedKey] = $item; 
                    }
 
                     
                }

        } 
        catch (\Exception $e){}

 
        if($exist)
        {
            $html['address']    = $ip;
            $html['Worker']     = $Workerusername;
            $html['userIp']     = $userIp;
            $html['sshUser']    = $sshUser;
            $html['sshPass']    = $sshPass;
            $html['type']       = 'BDCOM';
            $html['ServerName'] = $ServerName;
        }
 
        return response()->json($html);
    }

    static public function ONT_INFO_BY_IFINDEX($ip,$ifIndex,$read)
    {
        $html = [];
        $ifAlias = '';

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
         
        try {
                $ifAlias = $snmp->get("IF-MIB::ifAlias.".$ifIndex, TRUE);
                $ifAlias = trim(str_replace('STRING:','',$ifAlias));
                $html ['description'] = $ifAlias;
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        if(!empty($ifIndex))
        {
            $html ['ifIndex'] = $ifIndex;
            $ifDescr = '';$ifOperStatus = '';$Onu_Status = '';$Dbm = '';$Dereg_Index = '';$OnyType = '';

            try {
                    $ifDescr            = $snmp->walk(".1.3.6.1.2.1.2.2.1.2.".$ifIndex, TRUE);
                    foreach ($ifDescr as $key => $value) 
                    {
                        $html ['ponPort'] = trim(str_replace('STRING: ','',$value));
                    }
                }catch (\Exception $e){$html ['ponPort'] = '';}    
                
                
            try {
                    $ifOperStatus       = $snmp->walk(".1.3.6.1.2.1.2.2.1.8.".$ifIndex, TRUE);
                    foreach ($ifOperStatus as $key => $value) 
                    { 
                        $value = trim(str_replace('INTEGER: ','',$value));
                        $value = preg_replace('/\(\d+\)/', '', $value);
                        $html ['operateStatus'] = trim(str_replace("\"",'',$value));
                    }
                }catch (\Exception $e){$html ['operateStatus'] = '';}         
            
            
            try {
                    $Onu_Status         = $snmp->walk("1.3.6.1.4.1.3320.101.10.1.1.26.".$ifIndex, TRUE);
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
                    $Dbm                = $snmp->walk("1.3.6.1.4.1.3320.101.10.5.1.5.".$ifIndex, TRUE);
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
                        if (strpos($value, $ifAlias) !== false)
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
                    $OnyType            = $snmp->walk("1.3.6.1.4.1.3320.101.10.1.1.1.".$ifIndex , TRUE);
                    foreach ($OnyType as $key => $value) 
                    {
                        $value = trim(str_replace('STRING: ','',$value));
                        $html ['onuType'] = trim(str_replace("\"",'',$value));
                    }
                }catch (\Exception $e){$html ['onuType'] = '';}    

            	$MacOnu = '';
			try {
					$MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$ifIndex, TRUE));				
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

                    $html ['MacOnu'] = $MacOnu;

			} 
			catch (\Exception $e){$html ['MacOnu'] = '';}
			 

        }
        else 
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }       

         
        return $html;
    }

    static public function ONT_PORT_BY_IFINDEX($ip,$ifIndex,$read)
    {
        $html = [];
        $html ['shutdown'] = 0;
        $User_Not_Exist    = 0;
        $ifDescr = '';$ifAlias = '';$Onu_Status = '';$iface = [];

        $snmp      = new \SNMP(\SNMP::VERSION_2c, $ip, $read);   
  
        try {
                $ifDescr    = $snmp->get(".1.3.6.1.2.1.2.2.1.2.".$ifIndex, TRUE); 
                $ifDescr    = str_replace("STRING: ", "", $ifDescr);
                $ifDescr    = str_replace("\"", "", $ifDescr);       
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {
                $ifAlias  = $snmp->get("IF-MIB::ifAlias.".$ifIndex, TRUE);  
                $ifAlias = str_replace("STRING: ", "", $ifAlias);
                $ifAlias = str_replace("\"", "", $ifAlias);
        }catch (\Exception $e){}    
        
        $html ['ifIndex']       = $ifIndex;
        $html ['Description']   = $ifAlias;
        $html ['PonPort']       = $ifDescr;

        try{
                $VlanTrunck = '';
                $PortVlan = '';
                $AdminStatus = '';
                $PortStatus = '';

                try {$VlanTrunck = $snmp->walk(".1.3.6.1.4.1.3320.101.12.1.1.18.".$ifIndex, TRUE);}
                catch (\Exception $e){}    

                try {$PortVlan         = $snmp->walk(".1.3.6.1.4.1.3320.101.12.1.1.3.".$ifIndex, TRUE);}
                catch (\Exception $e){}  

                try {$AdminStatus      = $snmp->walk("1.3.6.1.4.1.3320.101.12.1.1.7.".$ifIndex, TRUE);}
                catch (\Exception $e){}  
                
                try {
                        $PortStatus = $snmp->walk("1.3.6.1.4.1.3320.101.12.1.1.8.".$ifIndex, TRUE);  
                        if (!empty($PortStatus))
                        {
                            foreach ($PortStatus as $key => $value) 
                            {
                                $size = count($PortStatus);$VlanType = '';
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
         
        }catch (\Exception $e){}    

 
        return $html;
    }

    static public function ONT_MAC_BY_IFINDEX($ip,$ifIndex,$read,$write)
    {

        $html = [];$iface = [];
        $html ['shutdown'] = 0;
  
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);

        $ifDescr = '';$ifAlias = '';
        try {
                $ifDescr    = $snmp->get(".1.3.6.1.2.1.2.2.1.2.".$ifIndex, TRUE);
                $ifDescr    = str_replace("STRING: ", "", $ifDescr);
                $ifDescr    = str_replace("\"", "", $ifDescr); 
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {
                $ifAlias  = $snmp->get("IF-MIB::ifAlias.".$ifIndex, TRUE);  
                $ifAlias = str_replace("STRING: ", "", $ifAlias);
                $ifAlias = str_replace("\"", "", $ifAlias);
        }catch (\Exception $e){}    
        
        $html ['ifIndex']       = $ifIndex;
        $html ['Description']   = $ifAlias;
        $html ['PonPort']       = $ifDescr;

 
        if(!empty($ifIndex))
        {
            $IsMacTrue = false;
            $RealVlan = '';
            $resultArray = array();
            
            try {
                    $snmp_RW->set('1.3.6.1.4.1.3320.101.9.2.1.0', 'i', $ifIndex);  
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
            catch (\Exception $e){}
 
            if($resultArray)
            {
                foreach ($resultArray as $keyz => $valueZ) 
                {  
                    foreach ($valueZ as $key => $value) 
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
      
        return  $html;
    }

    static public function OnuRestart($ip,$ifIndex,$write)
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
}
