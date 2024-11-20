<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\BDCOM;
use App\Models\HUAWEI;
use App\Models\ZTE;
use App\Models\HSGQ;
use App\Models\VSOLUTION;

class globalsearch extends Model
{
    use HasFactory;

    static public function BDCOM($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp,$macSN)   
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        
        $Macs = '';$FindedKey = 0;$Real_Mac = '';$oldMac = '';

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
 
                foreach ($Macs as $key => $value) 
                {
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
                                $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                                $ServerName = trim(str_replace("\"", "" , $ServerName));
                                $ServerName = trim(str_replace("\'", "" , $ServerName));  
                        }catch (\Exception $e){$ServerName  = '';}     
    
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
                        }catch (\Exception $e){$OperStatus  = '';}    

                        $Downtime = ''; $Uptime = '';
                        if($OperStatus == 'up')
                        {
                            try {
                                    $Uptime   	  = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.80.".$FindedKey, TRUE)); 
                                    $Uptime 	  = BDCOM::secondsToNormalTime($Uptime);
                            } 
                            catch (\Exception $e){$Uptime = '';}
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
                    
                        $itemX = [];
                        $html['address']     = $ip;
                        $html['Worker']      = $Workerusername;
                        $html['userIp']      = $userIp;
                        $html['sshUser']     = $sshUser;
                        $html['sshPass']     = $sshPass;
                        $html['ServerName']  = $ServerName;
                        $html['type']        = 'BDCOM';
                        
                        $itemX['Ifindex']    = $FindedKey;
                        $itemX['Descr']      = $DescrCheck;
                        $itemX['PonPort']    = $PonPort;
                        $itemX['Mac']        = $Real_Mac;
                        $itemX['OnyType']    = $OnyType;
                        $itemX['Dbm']        = $Dbm;
                        $itemX['Onu_Status'] = $Onu_Status;
                        $itemX['OperStatus'] = $OperStatus;
                        $itemX['reason']     = $reason;
                        $itemX['Uptime']     = $Uptime;
                        $itemX['Downtime']   = $Downtime;
                        $html['OnuList_'.self::generateRandomHexString(16)] = $itemX;  
                        $FindedKey  = '';
                    }
    
                }

        } 
        catch (\Exception $e){$Macs = '';}
 
        return response()->json($html);
    }

    static public function HUAWEI($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp,$macSN)
    {  
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        
        $FindedKey = 0; $OnuSn = ''; $oldMac = '';

        try {
                $Macs = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3", TRUE); 
        }
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        
        if(isset($Macs))
        {
            try {                                    
 
                    foreach ($Macs as $key => $value) 
                    {
                        $value = str_replace("Hex-STRING: ", "",$value);
                        $value = str_replace("STRING:","",$value);
                        $value = str_replace("\"", "",$value); 
        
                        if(strlen($value) < 10 )
                        {   
                            $value   = ltrim($value);
                            $value   = bin2hex($value);                                                                                                       
                        }
                        else
                        {
                            $value   = str_replace(" ", "",$value);    
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
                                $OnuSn = $oldMac;
                            }                       
                        }
                        else
                        {
                            if (strpos($value , $macSN) !== false) 
                            {
                                $FindedKey = $key;
                                $OnuSn = $oldMac;
                            }      
                        }
        
                        if($FindedKey)
                        {      
                
                            try { 
                                    $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$FindedKey, TRUE);
                                    $xxx = current($Status);
                                    $xxx = str_replace('INTEGER: ', '', trim($xxx));
                                    $position = strpos($xxx, '1');
                                    if ($position !== false)
                                    {
                                        $Status = 'Online';
                                    }
                                    else $Status = 'Offline';
                            } 
                            catch (\Exception $e) 
                            {$Status = '';}
        
                            $Uptime = '';$Downtime = '';
                            if($Status == 'Online')
                            {
                                try { 
                                        $Uptime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.22.".$FindedKey, TRUE); 
                                        $Uptime = str_replace("Hex-STRING: ", "", $Uptime);
                                        $Uptime = str_replace("\"", "", $Uptime);   
                                        $Uptime = trim($Uptime);
                                        $Uptime = HUAWEI::secondsToNormalTime($Uptime);
                        
                                        $givenDate = new \DateTime($Uptime);
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
                                                    
                                        $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min, ';
                                                    
                                        $Uptime = rtrim($output, ', '); 
                        
                                    
                                } 
                                catch (\Exception $e) 
                                {$Uptime = '';}
                            }
                            else
                            {
                                try { 
                                        $Downtime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.23.".$FindedKey, TRUE); 
                                        $Downtime = str_replace("Hex-STRING: ", "", $Downtime);
                                        $Downtime = str_replace("\"", "", $Downtime);   
                                        $Downtime = trim($Downtime);
                                        $Downtime = HUAWEI::secondsToNormalTime($Downtime);
                        
                                        $givenDate = new \DateTime($Downtime);
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
                        
                                        if($timeDifference->y > 10)
                                        {
                                            $Downtime = 'Never'; 
                                        }
        
                                } 
                                catch (\Exception $e) 
                                {$Downtime = '';}
                        
                            }
                    
                            try { 
                                    $Reason = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24.".$FindedKey, TRUE); 
                                    $Reason  = trim(str_replace("INTEGER: ", "", $Reason));
                                    $ReadyReason = $Reason;
                                    if ($ReadyReason == 1) {
                                        $FinalReason = 'LOS';
                                    } elseif ($ReadyReason == 2) {
                                        $FinalReason = 'LOSi/LOBi';  
                                    } elseif ($ReadyReason == 3) {
                                        $FinalReason = 'LOFI';  
                                    } elseif ($ReadyReason == 4) {
                                        $FinalReason = 'SFI';
                                    } elseif ($ReadyReason == 5) {
                                        $FinalReason = 'LOAI';
                                    } elseif ($ReadyReason == 6) {
                                        $FinalReason = 'LOAMI';
                                    } elseif ($ReadyReason == 7) {
                                        $FinalReason = 'Deactive ONT Fails';
                                    } elseif ($ReadyReason == 8) {
                                        $FinalReason = 'Deactive ONT Success';
                                    } elseif ($ReadyReason == 9) {
                                        $FinalReason = 'Reset ONT';
                                    } elseif ($ReadyReason == 10) {
                                        $FinalReason = 'Re-register ONT';
                                    } elseif ($ReadyReason == 11) {
                                        $FinalReason = 'Pop Up Fail';
                                    } elseif ($ReadyReason == 13) {
                                        $FinalReason = 'Dying-Gasp';
                                    } elseif ($ReadyReason == 15) {
                                        $FinalReason = 'LOKI';
                                    } elseif ($ReadyReason == 18) {
                                        $FinalReason = 'Deactived ONT Due to the Ring';
                                    } elseif ($ReadyReason == 30) {
                                        $FinalReason = 'Shut Down ONT Optical Module';
                                    } elseif ($ReadyReason == 31) {
                                        $FinalReason = 'Reset ONT by ONT Command';
                                    } elseif ($ReadyReason == 32) {
                                        $FinalReason = 'Reset ONT by ONT Reset Button';
                                    } elseif ($ReadyReason == 33) {
                                        $FinalReason = 'Reset ONT by ONT Software';
                                    } elseif ($ReadyReason == 34) {
                                        $FinalReason = 'Deactived ONT Due to Broadcast Attack';
                                    } elseif ($ReadyReason == 35) {
                                        $FinalReason = 'Operator Check Fail';
                                    } elseif ($ReadyReason == 37) {
                                        $FinalReason = 'Rogue ONT Detected by Itself';
                                    } elseif ($ReadyReason == -1) {
                                        $FinalReason = '-';
                                    } else {
                                        $FinalReason = 'Unknown Reason';
                                    }
                            } 
                            catch (\Exception $e) 
                            {$Reason = '';}
                
                            try {   
                                    $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$FindedKey, TRUE);  
                                    $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                                    $Signal  = HUAWEI::SginalFixer($Signal);
                            } 
                            catch (\Exception $e) 
                            {$Signal = '';} 
                
                            try {   
                                    $Vendor = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.45.1.4." .$FindedKey, TRUE);
                                    $Vendor  = trim(str_replace("STRING: ", "", $Vendor));
                                    $Vendor  = trim(str_replace("\"", "", $Vendor));
                
                                    $SN_Fixed   = substr($OnuSn, 0, 8);
                                    $SN_Fixed   = hex2bin($SN_Fixed);  
                                    $valueType = $SN_Fixed.'   '.$Vendor;
                            } 
                            catch (\Exception $e) 
                            {$valueType = '';}
                    
                            try {
                                    $Name = $snmp->get('1.3.6.1.2.1.1.5.0'); 
                                    $Name = trim(str_replace('STRING: ','',$Name));
                                    $Name = trim(str_replace("\"",'',$Name));
                            } 
                            catch (\Exception $e) 
                            {$Name = '';}
                
                            try {
                                    $OnuDesc = $snmp->get('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.'.$FindedKey, TRUE); 
                                    $OnuDesc = trim(str_replace('STRING: ','',$OnuDesc));
                                    $OnuDesc = trim(str_replace("\"",'',$OnuDesc));
                            } 
                            catch (\Exception $e) 
                            {$OnuDesc = '';}
                
                            try {
                                    $Parts   = explode('.',$FindedKey);
                                    $PonPort = HUAWEI::Pon_Port($Parts[0]).':'.$Parts[1];
                                    
                            } 
                            catch (\Exception $e) 
                            {$PonPort = '';}
                
                            $itemX = [];
                            $html['address']    = $ip;
                            $html['Worker']     = $Workerusername;
                            $html['userIp']     = $userIp;
                            $html['sshUser']    = $sshUser;
                            $html['sshPass']    = $sshPass;
                            $html['ServerName'] = $Name;
                            $html['type']       = 'HUAWEI';
                            
        
                            $itemX['Ifindex']    = $FindedKey;
                            $itemX['Descr']      = $OnuDesc;
                            $itemX['PonPort']    = $PonPort;
                            $itemX['Mac']        = $OnuSn;
                            $itemX['OnyType']    = $valueType;
                            $itemX['Dbm']        = $Signal;
                            $itemX['OperStatus'] = $Status;
                            $itemX['reason']     = $FinalReason;
                            $itemX['Uptime']     = $Uptime;
                            $itemX['Downtime']   = $Downtime;
        
        
                            $html['OnuList_'.self::generateRandomHexString(16)] = $itemX;  
        
                            $FindedKey = '';
                        }
        
                    }
    
            } 
            catch (\Exception $e){}
        }
 

        try {
                $EponMacs = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3", TRUE); 
        }
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        if(isset($EponMacs))
        {   
            try {  

                foreach ($EponMacs as $key => $value) 
                {
                    $value = str_replace("Hex-STRING: ", "",$value);
                    $value = str_replace("STRING:","",$value);
                    $value = str_replace("\"", "",$value); 
    
                    if(strlen($value) < 10 )
                    {   
                        $value   = ltrim($value);
                        $value   = bin2hex($value);                                                                                                       
                    }
                    else
                    {
                        $value   = str_replace(" ", "",$value);    
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
                            $OnuSn = $oldMac;
                        }                       
                    }
                    else
                    {
                        if (strpos($value , $macSN) !== false) 
                        {
                            $FindedKey = $key;
                            $OnuSn = $oldMac;
                        }      
                    }
    
                    if($FindedKey)
                    {      
            
                        try { 
                                $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$FindedKey, TRUE);
                                $xxx = current($Status);
                                $xxx = str_replace('INTEGER: ', '', trim($xxx));
                                $position = strpos($xxx, '1');
                                if ($position !== false)
                                {
                                    $Status = 'Online';
                                }
                                else $Status = 'Offline';
                        } 
                        catch (\Exception $e) 
                        {$Status = '';}
    
                        $Uptime = '';$Downtime = '';
                        if($Status == 'Online')
                        {
                            try { 
                                    $Uptime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.23.".$FindedKey, TRUE); 
                                    $Uptime = str_replace("Hex-STRING: ", "", $Uptime);
                                    $Uptime = str_replace("\"", "", $Uptime);   
                                    $Uptime = trim($Uptime);
                                    $Uptime = HUAWEI::secondsToNormalTime($Uptime);
                    
                                    $givenDate = new \DateTime($Uptime);
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
                                                
                                    $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min, ';
                                                
                                    $Uptime = rtrim($output, ', '); 
                    
                                
                            } 
                            catch (\Exception $e) 
                            {$Uptime = '';}
                        }
                        else
                        {
                            try { 
                                    $Downtime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.24.".$FindedKey, TRUE); 
                                    $Downtime = str_replace("Hex-STRING: ", "", $Downtime);
                                    $Downtime = str_replace("\"", "", $Downtime);   
                                    $Downtime = trim($Downtime);
                                    $Downtime = HUAWEI::secondsToNormalTime($Downtime);
                    
                                    $givenDate = new \DateTime($Downtime);
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
                    
                                    if($timeDifference->y > 10)
                                    {
                                        $Downtime = 'Never'; 
                                    }
    
                            } 
                            catch (\Exception $e) 
                            {$Downtime = '';}
                    
                        }
                
                        try { 
                                $Reason = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25.".$FindedKey, TRUE); 
                                $Reason  = trim(str_replace("INTEGER: ", "", $Reason));
                                $ReadyReason = $Reason;
                                if ($ReadyReason == 1) {
                                    $FinalReason = 'LOS';
                                } elseif ($ReadyReason == 2) {
                                    $FinalReason = 'LOSi/LOBi';  
                                } elseif ($ReadyReason == 3) {
                                    $FinalReason = 'LOFI';  
                                } elseif ($ReadyReason == 4) {
                                    $FinalReason = 'SFI';
                                } elseif ($ReadyReason == 5) {
                                    $FinalReason = 'LOAI';
                                } elseif ($ReadyReason == 6) {
                                    $FinalReason = 'LOAMI';
                                } elseif ($ReadyReason == 7) {
                                    $FinalReason = 'Deactive ONT Fails';
                                } elseif ($ReadyReason == 8) {
                                    $FinalReason = 'Deactive ONT Success';
                                } elseif ($ReadyReason == 9) {
                                    $FinalReason = 'Reset ONT';
                                } elseif ($ReadyReason == 10) {
                                    $FinalReason = 'Re-register ONT';
                                } elseif ($ReadyReason == 11) {
                                    $FinalReason = 'Pop Up Fail';
                                } elseif ($ReadyReason == 13) {
                                    $FinalReason = 'Dying-Gasp';
                                } elseif ($ReadyReason == 15) {
                                    $FinalReason = 'LOKI';
                                } elseif ($ReadyReason == 18) {
                                    $FinalReason = 'Deactived ONT Due to the Ring';
                                } elseif ($ReadyReason == 30) {
                                    $FinalReason = 'Shut Down ONT Optical Module';
                                } elseif ($ReadyReason == 31) {
                                    $FinalReason = 'Reset ONT by ONT Command';
                                } elseif ($ReadyReason == 32) {
                                    $FinalReason = 'Reset ONT by ONT Reset Button';
                                } elseif ($ReadyReason == 33) {
                                    $FinalReason = 'Reset ONT by ONT Software';
                                } elseif ($ReadyReason == 34) {
                                    $FinalReason = 'Deactived ONT Due to Broadcast Attack';
                                } elseif ($ReadyReason == 35) {
                                    $FinalReason = 'Operator Check Fail';
                                } elseif ($ReadyReason == 37) {
                                    $FinalReason = 'Rogue ONT Detected by Itself';
                                } elseif ($ReadyReason == -1) {
                                    $FinalReason = '-';
                                } else {
                                    $FinalReason = 'Unknown Reason';
                                }
                        } 
                        catch (\Exception $e) 
                        {$Reason = '';}
            
                        try {   
                                $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.".$FindedKey, TRUE);  
                                $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                                $Signal  = HUAWEI::SginalFixer($Signal);
                        } 
                        catch (\Exception $e) 
                        {$Signal = '';} 
            
                        $valueType = '-';
                
                        try {
                                $Name = $snmp->get('1.3.6.1.2.1.1.5.0'); 
                                $Name = trim(str_replace('STRING: ','',$Name));
                                $Name = trim(str_replace("\"",'',$Name));
                        } 
                        catch (\Exception $e) 
                        {$Name = '';}
            
                        try {
                                $OnuDesc = $snmp->get('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.'.$FindedKey, TRUE); 
                                $OnuDesc = trim(str_replace('STRING: ','',$OnuDesc));
                                $OnuDesc = trim(str_replace("\"",'',$OnuDesc));
                        } 
                        catch (\Exception $e) 
                        {$OnuDesc = '';}
            
                        try {
                                $Parts   = explode('.',$FindedKey);
                                $PonPort = HUAWEI::GPON_EPON_PORT($Parts[0]).':'.$Parts[1];
                                
                        } 
                        catch (\Exception $e) 
                        {$PonPort = '';}
            
                        $itemX = [];
                        $html['address']    = $ip;
                        $html['Worker']     = $Workerusername;
                        $html['userIp']     = $userIp;
                        $html['sshUser']    = $sshUser;
                        $html['sshPass']    = $sshPass;
                        $html['ServerName'] = $Name;
                        $html['type']       = 'HUAWEI';
                       
    
                        $itemX['Ifindex']    = $FindedKey;
                        $itemX['Descr']      = $OnuDesc;
                        $itemX['PonPort']    = $PonPort;
                        $itemX['Mac']        = implode(':', str_split($OnuSn, 2)) ?? null; 
                        $itemX['OnyType']    = $valueType;
                        $itemX['Dbm']        = $Signal;
                        $itemX['OperStatus'] = $Status;
                        $itemX['reason']     = $FinalReason;
                        $itemX['Uptime']     = $Uptime;
                        $itemX['Downtime']   = $Downtime;
    
    
                        $html['OnuList_'.self::generateRandomHexString(16)] = $itemX;  
    
                        $FindedKey = '';
                    }
    
                }


            }catch (\Exception $e){}
        }
  
 

        return response()->json($html);
    }

    static public function ZTE($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp,$macSN)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        
        $Macs = '';$FindedKey = 0;$OnuSn = '';$FullSn = '';$oldMac = '';$Uptime = '';$Downtime = '';

        try {
                $Macs  = $snmp->walk("1.3.6.1.4.1.3902.1012.3.28.1.1.5", TRUE); 
        }
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {                                    
 
            foreach ($Macs as $key => $value) 
            {
                $value = str_replace("Hex-STRING: ", "",$value);
                $value = str_replace("STRING:","",$value);
                $value = str_replace("\"", "",$value); 

                if(strlen($value) < 10 )
                {   
                    $value   = ltrim($value);
                    $value   = bin2hex($value);                                                                                                       
                }
                else
                {
                    $value   = str_replace(" ", "",$value);    
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

                $SN_Fixed   = substr($value, 0, 8);
                $SN_Fixed   = hex2bin($SN_Fixed);
                $FullSn     = $SN_Fixed.substr($value, 8, 16);
                 
                if(strlen($macSN) == 4) 
                {
                    $value = substr($value, -4);
                    if($macSN == $value)
                    {
                        $FindedKey = $key;
                        $OnuSn = $oldMac;
                    }                       
                }
                else
                {
                    if (strpos($value , $macSN) !== false) 
                    {
                        $FindedKey = $key;
                        $OnuSn = $oldMac;
                    }  
                    else if (strpos($SN_Fixed.substr($value, 8, 16) , $macSN) !== false) 
                    {
                        $FindedKey = $key;
                        $OnuSn = $oldMac;
                    }    
                }

                if($FindedKey)
                {
        
                    try {
                            $StatusOnu = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$FindedKey, TRUE);
                            $StatusOnu = str_replace('INTEGER: ','',$StatusOnu);
                            $StatusOnu = str_replace("\"",'',$StatusOnu);
                            $StatusOnu = trim($StatusOnu);
        
                            $xxx     = 'Unknow';
                            if ($StatusOnu == '0')$xxx = 'Logging';
                            else  if ($StatusOnu == '1'){$xxx = 'Los';}
                            else  if ($StatusOnu == '2'){$xxx = 'syncMib';}
                            else  if ($StatusOnu == '3'){$xxx = 'Working';}
                            else  if ($StatusOnu == '4'){$xxx = 'Dyinggasp';}
                            else  if ($StatusOnu == '5'){$xxx = 'AuthFailed';}
                            else  if ($StatusOnu == '6'){$xxx = 'Offline';}
                    }
                    catch (\Exception $e) 
                    {$xxx = '';}
                

                    if($xxx == 'Working')
                    {
                        try{
                                $valueUptime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.5.".$FindedKey, TRUE);
                                $valueUptime = str_replace('STRING: ','',$valueUptime);
                                $valueUptime = str_replace("\"",'',$valueUptime);
                                $valueUptime = trim($valueUptime);
                                $valueUptime = ZTE::calculateUptime($valueUptime);
                                $Uptime      = $valueUptime;
                
                        }catch (\Exception $e){$Uptime = '';}
                    }
                    else
                    {
                        try{
                                $valueDowntime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.6.".$FindedKey, TRUE);
                                $valueDowntime = str_replace('STRING: ','',$valueDowntime);
                                $valueDowntime = str_replace("\"",'',$valueDowntime);
                                $valueDowntime = trim($valueDowntime);
                                $valueDowntime = ZTE::calculateUptime($valueDowntime);
                                $Downtime = $valueDowntime;

                        }catch (\Exception $e){$Downtime = '';}
                    }
                        
                    try {
                            $valueType = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.9.".$FindedKey, TRUE);
                            $valueType = str_replace('STRING: ','',$valueType);
                            $valueType = str_replace(' ','',$valueType);
                            $valueType = str_replace("\"",'',$valueType);
        
                            $valueVendor = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.1.".$FindedKey, TRUE);
                            $valueVendor = str_replace('STRING: ','',$valueVendor);
                            $valueVendor = str_replace(' ','',$valueVendor);
                            $valueVendor = str_replace("\"",'',$valueVendor);
                    }
                    catch (\Exception $e) 
                    {$valueType = '';$valueVendor = '';}
        
                        
                    try {
                            $valueReason = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.7.".$FindedKey, TRUE);
                            $valueReason = str_replace('INTEGER: ','',$valueReason);
                            $valueReason = str_replace(' ','',$valueReason);
                            $valueReason = str_replace("\"",'',$valueReason);
                            $valueReason = trim($valueReason);
        
                            if($valueReason == 1)$valueReason = 'Unknown';
                            else if($valueReason == 2)$valueReason = 'LOS';
                            else if($valueReason == 3)$valueReason = 'LOSi';
                            else if($valueReason == 1)$valueReason = 'LOAMi';
                            else if($valueReason == 9)$valueReason = 'DyingGasp';
                            else if($valueReason == 12)$valueReason = 'Manual Restart';
                            else if($valueReason == 13)$valueReason = 'Manual Shutdown';
                            else $valueReason = '-';
                    }
                    catch (\Exception $e) 
                    {$valueReason = '';}
                
        
                    try {
                            $dbm = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$FindedKey.'.1', TRUE); 
                            $dbm = str_replace('INTEGER: ','',$dbm);
                            $dbm = str_replace("\"",'',$dbm);
                            $dbm = trim($dbm);
                            if(trim($dbm) > 30000 && trim($dbm) != 65535)
                            {
                                $dbm = (trim($dbm) - 65536) *0.002-30; 
                            }
                            else
                            {
                                $dbm = trim($dbm) *0.002-30; 
                            }
                            $dbm = round($dbm,2);
                    }
                    catch (\Exception $e) 
                    {$dbm = '';}
        
                    
                    try {
                            $OnuDesc = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$FindedKey."", TRUE);
                            $OnuDesc = str_replace('STRING: ','',$OnuDesc);
                            $OnuDesc = str_replace("\"",'',$OnuDesc);
                            $OnuDesc = str_replace("$$$$",'',$OnuDesc);
                            $OnuDesc = trim($OnuDesc);
                    }
                    catch (\Exception $e) 
                    {$OnuDesc = '';}
        
        
                    try {
                            $Name = $snmp->get('1.3.6.1.2.1.1.5.0'); 
                            $Name = trim(str_replace('STRING: ','',$Name));
                            $Name = trim(str_replace("\"",'',$Name));
                    } 
                    catch (\Exception $e) 
                    {$Name = '';}
        
                    try {
                            $Parts   = explode('.',$FindedKey);
                            $PonPort = 'GPON '.ZTE::Pon_Port($Parts[0])[1].':'.$Parts[1];
                            
                    } 
                    catch (\Exception $e) 
                    {$PonPort = '';}
        
            
                    $itemX = [];
        
                    $html['address']    = $ip;
                    $html['Worker']     = $Workerusername;
                    $html['userIp']     = $userIp;
                    $html['sshUser']    = $sshUser;
                    $html['sshPass']    = $sshPass;
                    $html['ServerName'] = $Name;
                    $html['type']       = 'ZTE';
               
                   
                    $itemX['Ifindex']    = $FindedKey;
                    $itemX['Descr']      = $OnuDesc;
                    $itemX['PonPort']    = $PonPort;
                    $itemX['Fullsn']     = $FullSn;
                    $itemX['Mac']        = $OnuSn;
                    $itemX['OnyType']    = $valueVendor.' - '.$valueType;
                    $itemX['Dbm']        = $dbm;
                    $itemX['OperStatus'] = $xxx;
                    $itemX['reason']     = $valueReason;
                    $itemX['Uptime']     = $Uptime;
                    $itemX['Downtime']   = $Downtime;
                    $html['OnuList_'.self::generateRandomHexString(16)] = $itemX;  

                    $FindedKey = '';
                }

            }

        } 
        catch (\Exception $e){$Macs = '';}

        return response()->json($html);
    }

    static public function VSOLUTION($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp,$macSN)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        
        $Macs = '';$FindedKey = 0;$Real_Mac = '';$oldMac = '';

        try {
                $Macs  = $snmp->walk("1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5", TRUE); 
        }
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {                                    
                foreach ($Macs as $key => $value) 
                {
                    $value = str_replace("Hex-STRING: ", "",$value);
                    $value = str_replace("STRING:","",$value);
                    $value = str_replace("\"", "",$value); 
                    $oldMac =  strtoupper($value);  
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
                                $reason = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.15.".$FindedKey, TRUE);
                                $reason = trim(str_replace('INTEGER: ','',$reason));

                                if($reason == 1 || $reason == 2){$reason = 'Power Off';}
                                else if($reason == 0 ){$reason = 'Wire Down';}
                        } 
                        catch (\Exception $e) 
                        {$reason = '';}
                                    
                        try {
                                $Dbm = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$FindedKey , TRUE);
                                $Dbm = trim(str_replace('STRING: ','',$Dbm));
                                $Dbm = trim(str_replace("\"",'',$Dbm));

                                $startPos = strpos($Dbm, '(');
                                $endPos   = strpos($Dbm, 'dBm');
                                if ($startPos !== false && $endPos !== false)
                                {
                                    $dbmSubstring = substr($Dbm, $startPos + 1, $endPos - $startPos - 1);
                                    $Dbm = trim($dbmSubstring);
                                }
                        } 
                        catch (\Exception $e) 
                        {$Dbm = '';}

                        try {
                                $Model   = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.4.".$FindedKey , TRUE);
                                $Vendoor = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.3.".$FindedKey , TRUE);

                                $Model = trim(str_replace('STRING: ','',$Model));
                                $Model = trim(str_replace("\"",'',$Model));
                                $Vendoor = trim(str_replace('STRING: ','',$Vendoor));
                                $Vendoor = trim(str_replace("\"",'',$Vendoor));

                                $Model   = $Vendoor.' - '.$Model;
                        } 
                        catch (\Exception $e) 
                        {$Model = '';}

                        try {
                                $OnuStatus = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$FindedKey , TRUE);
                                $OnuStatus = trim(str_replace('INTEGER: ','',$OnuStatus));
                                $OnuStatus = trim(str_replace("\"",'',$OnuStatus));
                        } 
                        catch (\Exception $e) 
                        {$OnuStatus = '';}

                        $Uptime = '';$Downtime = '';
                        if($OnuStatus == 1)
                        {
                            $Uptime = [];
                            try {
                                    $Uptime = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.18.".$FindedKey , TRUE);
                                    $Uptime = str_replace("STRING: ", "", $Uptime);
                                    $Uptime = str_replace("\"", "", $Uptime);
                                    if($Uptime !== 'N/A')$Uptime = VSOLUTION::calculateUptime($Uptime);
                            } 
                            catch (\Exception $e){} 

                           
                        }
                        else
                        {             
                            $Downtime = [];
                            try {
                                    $Downtime = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.19.".$FindedKey , TRUE);
                                    $Downtime = str_replace("STRING: ", "", $Downtime);
                                    $Downtime = str_replace("\"", "", $Downtime);
                                    if($Downtime !== 'N/A')$Downtime = VSOLUTION::calculateUptime($Downtime);
                            } 
                            catch (\Exception $e){}    
                        }

                        try {
                                $Descr = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$FindedKey , TRUE);
                                $Descr = trim(str_replace('STRING: ','',$Descr));
                                $Descr = trim(str_replace("\"",'',$Descr));
                        } 
                        catch (\Exception $e) 
                        {$OnuStatus = '';}

                        try {
                                $Parts   = explode('.',$FindedKey);
                                $PonPort = 'EPON0/'.$Parts[0].':'.$Parts[1];
                                
                        } 
                        catch (\Exception $e) 
                        {$PonPort = '';}
            
                        try {
                                $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                                $ServerName = trim(str_replace("\"", "" , $ServerName));
                                $ServerName = trim(str_replace("\'", "" , $ServerName));  
                        }catch (\Exception $e){$ServerName  = '';}   

                        $itemX = [];
                        $html['address']    = $ip;
                        $html['Worker']     = $Workerusername;
                        $html['userIp']     = $userIp;
                        $html['sshUser']    = $sshUser;
                        $html['sshPass']    = $sshPass;
                        $html['ServerName'] = $ServerName;
                        $html['type']       = 'VSOLUTION';
                         
                        $itemX['Ifindex']    = $FindedKey;
                        $itemX['Descr']      = $Descr;
                        $itemX['PonPort']    = $PonPort;
                        $itemX['Mac']        = $Real_Mac;
                        $itemX['OnyType']    = $Model;
                        $itemX['Dbm']        = $Dbm;
                        $itemX['OperStatus'] = $OnuStatus;
                        $itemX['reason']     = $reason;
                        $itemX['Uptime']     = $Uptime;
                        $itemX['Downtime']   = $Downtime;
                        $html['OnuList_'.self::generateRandomHexString(16)] = $itemX;  
                        $FindedKey = '';
                    }

                }
        } 
        catch (\Exception $e){$Macs = '';}

        
        return response()->json($html);
    }

    static public function HSGQ($ip,$token,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp,$macSN)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        
        $Macs = '';$Real_Mac = '';$oldMac = '';$exist = false;

        try {
                HSGQ::API('https://'.$ip.'/onu_allow_list?t=1709042984000',$token);
                $ArrayFirst =  HSGQ::API('http://'.$ip.'/onutable',$token);
                $dataArray = json_decode($ArrayFirst, true);
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
                foreach ($dataArray['data'] as $item) 
                { 
                    $Port = -1;$Pon = -1;

                    $macSN  = str_replace(":", "",$macSN); 
                    $macSN  = str_replace(".", "",$macSN);   
                    $macSN  = str_replace("-", "",$macSN);  
                    $macSN  = str_replace(" ", "",$macSN);  
                    $macSN  = str_replace("\"", "",$macSN); 
                    $macSN  = strtoupper($macSN);
                    $oldMac = $item['macaddr'];

                    $item['macaddr']  = str_replace(":", "",$item['macaddr']); 
                    $item['macaddr']  = str_replace(".", "",$item['macaddr']);   
                    $item['macaddr']  = str_replace("-", "",$item['macaddr']);  
                    $item['macaddr']  = str_replace(" ", "",$item['macaddr']);  
                    $item['macaddr']  = str_replace("\"", "",$item['macaddr']); 
                    $item['macaddr']  = strtoupper($item['macaddr']);

                    if(strlen($macSN) == 4) 
                    {  
                        $item['macaddr'] = substr($item['macaddr'], -4);  
                        if($macSN == $item['macaddr'])
                        {
                            $Pon  = $item['port_id'];
                            $Port = $item['onu_id'];
                            $Real_Mac = $oldMac;
                        }                       
                    }
                    else
                    {
                        if (strpos($item['macaddr'], $macSN) !== false)
                        {
                            $Pon  = $item['port_id'];
                            $Port = $item['onu_id'];
                            $Real_Mac = $oldMac;
                        }    
                    }

                    if($Pon !== -1  && $Port !== -1)
                    {
             
                        $ArraySecond =   HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$Pon,$token);
                        $SecDataArray = json_decode($ArraySecond, true); 
                        foreach ($SecDataArray['data'] as $key => $item) 
                        {
                            if ($item['onu_id'] === $Port)
                            {     
             
                                if (is_numeric($item['receive_power']))$Dbm = round($item['receive_power'],2);
                                else   $Dbm   = $item['receive_power'];
            
                                 $exist = true;
                                 $Child = [];
                                 $Child['Ifindex']    = $item['port_id'].'.'.$item['onu_id'];
                                 $Child['Descr']      = $item['onu_name'];
                                 $Child['PonPort']    = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                                 $Child['Mac']        = strtoupper($item['macaddr']);
                                 $Child['OnyType']    = $item['dev_type'].' - '.$item['onu_type'];
                                 $Child['Dbm']        = $Dbm;
                                 $Child['OperStatus'] = $item['status'];
                                 $Child['reason']     = $item['last_down_reason'];
                                 $Child['Uptime']     = HSGQ::timeAgo($item['last_down_time']);
                                 $Child['Downtime']   = HSGQ::timeAgo($item['register_time']);
                                 $html['OnuList_'.$key] = $Child; 
                            }
                        }
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
            $html['ServerName'] = $ServerName;
            $html['type']       = 'HSGQ';   
        }

 

        return response()->json($html);
    }

    static public function generateRandomHexString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }
}
