<?php

namespace App\Models\Install;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\PrivilegesModel;
use App\Models\VSOLUTION;

class _vsolution extends Model
{
    use HasFactory;

    static public function VSOLUTION_SEARCH($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp,$macSN)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        
        $Macs = '';$Real_Mac = '';$oldMac = '';$exist = false;

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
                $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                $ServerName = trim(str_replace("\"", "" , $ServerName));
                $ServerName = trim(str_replace("\'", "" , $ServerName));  
        }catch (\Exception $e){$ServerName  = '';}   

        try {                                    
                foreach ($Macs as $key => $value) 
                {
                    $FindedKey = 0;

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
             
                        $Uptime = '';$Downtime = '';
                        if($OnuStatus == 1)
                        {
                    
                            try {
                                    $Uptime = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.18.".$FindedKey , TRUE);
                                    $Uptime = str_replace("STRING: ", "", $Uptime);
                                    $Uptime = str_replace("\"", "", $Uptime);
                                    if($Uptime !== 'N/A')$Uptime = VSOLUTION::calculateUptime($Uptime);
                            } 
                            catch (\Exception $e){$Uptime = '';} 

                        
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
                            catch (\Exception $e){$Downtime = '';}    
                        }
              
            
                         $exist = true;
            
                         $item = [];
                         $item['ifindex']    = $FindedKey;
                         $item['address']    = $ip;
                         $item['Descr']      = $Descr;
                         $item['PonPort']    = $PonPort;
                         $item['Mac']        = $Real_Mac;
                         $item['OnyType']    = $Model;
                         $item['Dbm']        = $Dbm;
                         $item['OperStatus'] = $OnuStatus;
                         $item['reason']     = $reason;
                         $item['Uptime']     = $Uptime;
                         $item['Downtime']   = $Downtime;
                         $html['OnuList_'.$key] = $item; 
                    }
            
                }

        } 
        catch (\Exception $e){$Macs = '';}

        

 
        if($exist)
        {
            $html['address']    = $ip;
            $html['Worker']     = $Workerusername;
            $html['userIp']     = $userIp;
            $html['sshUser']    = $sshUser;
            $html['sshPass']    = $sshPass;
            $html['type']       = 'VSOLUTION';
            $html['ServerName'] = $ServerName;
        }

        return response()->json($html);
    }

    static public function ONT_INFO_BY_IFINDEX($ip,$ifIndex,$read)
    {
        $html  = [];
     
        $snmp  = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $Descr = '';
        try {
                $Descr = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$ifIndex , TRUE);
                $Descr = trim(str_replace('STRING:','',$Descr));
                $Descr = trim(str_replace("\"", "", $Descr));
        
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $FixedPonPort = explode('.',$ifIndex);
        $html ['PonPort']     = 'EPON0/'.$FixedPonPort[0].':'.$FixedPonPort[1];
        $html ['Description'] = $Descr;
        $html ['ifIndex']     = $ifIndex;

        $OnuStatus = '';
        try {
                $OnuStatus  = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$ifIndex , TRUE);
                foreach ($OnuStatus as $key => $value) 
                {
                    $html ['OnuStatus'] = trim(str_replace('INTEGER: ','',$value));
                }
        }catch (\Exception $e){$html ['OnuStatus'] = '';}    

        $Reason = '';
        try {
                $Reason = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.15.".$ifIndex, TRUE);  
                foreach ($Reason as $key => $value) 
                {
                    $html ['Reason'] = trim(str_replace('INTEGER: ','',$value));
                }
        }catch (\Exception $e){$html ['Reason'] = '';}    

        $OnuMac = '';
        try {
                $OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5.".$ifIndex , TRUE);  
                foreach ($OnuMac as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));
                    $html ['OnuMac'] = trim(str_replace("\"","",$value));
                }
        }catch (\Exception $e){$html ['OnuMac'] = '';}   

        $Vendoor = '';
        try {
                $Vendoor = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.3.".$ifIndex , TRUE); 
                foreach ($Vendoor as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));
                    $html ['Vendoor'] = trim(str_replace("\"","",$value));
                }
        }catch (\Exception $e){$html ['Vendoor'] = '';}   

        $Model = '';
        try {
                $Model = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.4.".$ifIndex , TRUE); 
                foreach ($Model as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));
                    $html ['Model'] = trim(str_replace("\"","",$value));
                }
        }catch (\Exception $e){$html ['Model'] = '';}   

        $Dmb = '';
        try {
                $Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$ifIndex , TRUE); 
                foreach ($Dmb as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));
                    $value = trim(str_replace("\"","",$value));
                    $startPos = strpos($value, '(');
                    $endPos   = strpos($value, 'dBm');
                    if ($startPos !== false && $endPos !== false)
                    {
                        $dbmSubstring = substr($value, $startPos + 1, $endPos - $startPos - 1);
                        $int = (float)abs((float)trim($dbmSubstring));   
                    }

                    $html ['Dmb'] = trim($dbmSubstring);
                }
        }catch (\Exception $e){$html ['Dmb'] = '';}   
  
        return $html;
    }

    static public function OnuRestart($ip,$write,$ifIndex)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        
        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                $IfIndex = explode('.',$ifIndex);
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.2.11.1.0', 'i', $IfIndex[0]);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.2.11.2.0', 'i', $IfIndex[1]);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.2.11.3.0', 'i', 1);  
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }
        
        return true;
    }

    static public function ONT_PORT_BY_IFINDEX($ip,$ifIndex,$read,$write)
    {
        $html = [];
        $html ['shutdown'] = 0;
        $User_Not_Exist    = 0;
        $Descr = '';$ifAlias = '';$Onu_Status = '';$iface = [];

        $snmp      = new \SNMP(\SNMP::VERSION_2c, $ip, $read);   

        $Pon = explode('.',$ifIndex);
        $Port = $Pon[1];
        $html ['PonPort'] = 'EPON0/'.$Pon[0].':'.$Port;
        $html ['ifIndex']  = $ifIndex;
        try {
                $Descr = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$ifIndex , TRUE);  
                $Descr = str_replace("STRING: ", "", $Descr);
                $Descr = str_replace("\"", "", $Descr);
                $html['Description'] = $Descr;

 
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $OnuStatus = 0;
        try {
            $OnuStatus  = trim(str_replace('INTEGER: ','',$snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$ifIndex , TRUE))); 
        }
        catch (\Exception $e){}    

        
                if($OnuStatus == 1)
                {         
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.5.3.1.0', 'i',$Pon[0]);   
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.5.3.2.0', 'i',$Port);
                    uSleep(500);
                    $html ['shutdown'] += 1;
                    $PortState = 0;
                    try {
                            $PortState  = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.5.1.9.1.4",TRUE); 
                    }
                    catch (\Exception $e){}   
                    
                    if(!empty($PortState))
                    {
                        foreach ($PortState as $keyPortState => $valuePortState) 
                        {   
                            $RealPort = explode('.',$keyPortState);

                            $valuePortState = trim(str_replace('INTEGER: ','',$valuePortState));   
                            $valuePortState = trim(str_replace("\"",'',$valuePortState));
                
                            $AdminStatus = '';
                            try {
                                $AdminStatus  = trim(str_replace('INTEGER: ','',$snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.5.1.3.1.4.".$Pon[0].'.'.$Port.'.'.$RealPort[2],false))); 
                            }
                            catch (\Exception $e){}    

                            $PvId = '';
                            try {
                                    $PvId =  trim(str_replace('INTEGER: ','',$snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.5.1.10.1.5.".$Pon[0].'.'.$Port.'.'.$RealPort[2],false)));  
                            }
                            catch (\Exception $e){}    


                            $VlanMode = '';
                            try {
                                    $VlanMode  = trim(str_replace('INTEGER: ','',$snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.5.1.10.1.4.".$Pon[0].'.'.$Port.'.'.$RealPort[2],false)));
                            }
                            catch (\Exception $e){}    
                        
                            if($VlanMode == 0)
                            {
                                $VlanMode = 'transparent';
                                $PvId     = 1;
                            }
                            else  if($VlanMode == 1)
                            {
                                $VlanMode = 'tag';
                            }
                            
                             

                            $item = [];                           
                            $item['portIndex']  = $RealPort[2];
                            $item['vlan']       = $PvId;
                            $item['portStatus'] = $valuePortState;
                            $item['VlanType']   = $VlanMode;
                            $item['portAdmin']  = $AdminStatus;                           
                            $html["port_num_$keyPortState"] = $item;               
                        }
                    }
                }
        


        return response()->json($html);
    }
     
    static public function ONT_MAC_BY_IFINDEX($ip,$ifIndex,$read,$write)
    {
        $html = [];$iface = [];
        $html ['shutdown'] = 0;
        $User_Not_Exist    = 0;
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);

        $Descr = '';$ifAlias = '';
        try {
                $Descr = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$ifIndex , TRUE);
                $Descr = str_replace('STRING: ','',$Descr);
                $Descr = str_replace("\"", "", $Descr);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $FirstPon   = explode('.',$ifIndex);
        $Pon_Port   = "EPON0/".$FirstPon[0].':'.$FirstPon[1];
        $SecondPon  = 'STRING: "EPON0/'.$FirstPon[0].':'.$FirstPon[1].'"';
        $SecondPon2 = '"EPON0/'.$FirstPon[0].':'.$FirstPon[1].'"';        

        $html ['PonPort']       = $Pon_Port;
        $html ['Description']   = $Descr;
        $html ['ifIndex']       = $ifIndex;

         
        $Pon_Port = '';
 
        $resultArray = array();
        $IndexForMac = '';         
                try {
                        $IndexForMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.10.3.2.1.5", TRUE); 
                        foreach ($IndexForMac as $key => $value) 
                        {             
                            if (strpos($value, 'STRING') !== false)
                            {
                                if($SecondPon == $value)$resultArray[] = $key;  
                            }   
                            else
                            { 
                                if($SecondPon2 == $value) $resultArray[] = $key;  
                            } 
                        }
                } 
                catch (\Exception $e) 
                {}

                $resultArrayOfMac  = array();
                $resultArrayOfVlan = array();

                try {               
                            foreach ($resultArray as $key => $value) 
                            {
                                $MacFromOnu  = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.10.3.2.1.3.".$value, TRUE);   
                                $MacVlan     = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.10.3.2.1.2.".$value, TRUE);   
            
                                foreach ($MacFromOnu as $key => $value2) 
                                {
                                    if (strpos($value2, ':') !== false)
                                    {
                                        $TmpMac  = explode('Hex-STRING: ',$value2);
                                        $TmpMac2 = (str_replace(" ", ":", trim($TmpMac[1])));
                                        $resultArrayOfMac = array_merge($resultArrayOfMac, [$TmpMac2]);
                                    }
                                    else
                                    {
                                        $TmpMac2 = (str_replace("\"", "", trim($value2)));
                                        $TmpMac2 = (str_replace(" ", ":", trim($TmpMac2)));
                                        $resultArrayOfMac = array_merge($resultArrayOfMac, [$TmpMac2]);
                                    }
            
                                }     
            
                                foreach ($MacVlan as $key => $value3) 
                                {
                                    if (strpos($value3, ':') !== false)
                                    {
                                        $TmpVlan  = explode('INTEGER: ',$value3);
                                        $resultArrayOfVlan = array_merge($resultArrayOfVlan, [$TmpVlan[1]]);
                                    }
                                    else
                                    {
                                        $resultArrayOfVlan = array_merge($resultArrayOfVlan, [$value3]);
                                    }
            
                                }
                            }
                } 
                catch (\Exception $e) 
                {}
                

                foreach ($resultArrayOfMac as $key => $value) 
                {
                    $html ['shutdown'] = 1;
                    $item = [];
                    $item['vlan']           = $resultArrayOfVlan[$key];
                    $item['mac']            = $resultArrayOfMac[$key];
                    $item['vendoor']        = VSOLUTION::MacFind_SNMP(($resultArrayOfMac[$key]));                         
                    $html["macs_num_$key"]  = $item;   
                }  
       

 
        return $html;
    }
 


}
