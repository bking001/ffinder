<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class VSOLUTION extends Model
{
    use HasFactory;

    static public function Uninstall_Side_OnuInfo($ip,$read,$write,$user,$oltName)
    {
        $html  = [];
        $html ['clone'] = '';

        $Descr = '';
        $snmp  = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9" , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $Real_Desc_Key = 0;
        $DescriptionClone = 0;
        foreach ($Descr as $key => $value) 
        {
            $value = trim(str_replace('STRING:','',$value));
            $value = trim(str_replace("\"", "", $value));
            if (strpos($value, $user) !== false)
            {
                $DescriptionClone++;
                $Real_Desc_Key++;
                $Pon_Port = $key;
 
                $value = trim(str_replace('STRING: ','',$value));
                $value = trim(str_replace("\"","",$value));

                $FixedPonPort = explode('.',$Pon_Port);
 
                $ItemArray = [];
                $ItemArray ['ifIndex']     = $key;
                $ItemArray ['description'] = $value;
                $ItemArray ['ponPort']     = 'EPON0/'.$FixedPonPort[0].':'.$FixedPonPort[1];
           
                $RealOnuStatus = '';
                try {
                        $OnuStatus  = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$Pon_Port , TRUE);
                        foreach ($OnuStatus as $key => $value) 
                        {
                            $ItemArray ['StatusOnu'] = trim(str_replace('INTEGER: ','',$value));
                            $RealOnuStatus = trim(str_replace('INTEGER: ','',$value));
                        }
                }catch (\Exception $e){$ItemArray ['StatusOnu'] = '';}    

                $Uptime = '';$Downtime = '';  
                if($RealOnuStatus == 1)
                {
                    $Uptime = [];
                    try {
                            $Uptime = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.18.".$Pon_Port , TRUE);
                            $Uptime = str_replace("STRING: ", "", $Uptime);
                            $Uptime = str_replace("\"", "", $Uptime);
                            if($Uptime !== 'N/A')$Uptime = VSOLUTION::calculateUptime($Uptime);
                            $ItemArray ['Uptime'] = $Uptime;
                    } 
                    catch (\Exception $e){$ItemArray ['Uptime'] = '';} 
 
                }
                else
                {             
                    $Downtime = [];
                    try {
                            $Downtime = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.19.".$Pon_Port , TRUE);
                            $Downtime = str_replace("STRING: ", "", $Downtime);
                            $Downtime = str_replace("\"", "", $Downtime);
                            if($Downtime !== 'N/A')$Downtime = VSOLUTION::calculateUptime($Downtime);
                            $ItemArray ['Downtime'] = $Downtime;
                    } 
                    catch (\Exception $e){ $ItemArray ['Downtime'] = '';}    
                }

           
                try {
                        $OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5.".$Pon_Port , TRUE);  
                        foreach ($OnuMac as $key => $value) 
                        {
                            $value = trim(str_replace('STRING: ','',$value));
                            $ItemArray ['Mac'] = trim(str_replace("\"","",$value));
                        }
                }catch (\Exception $e){$ItemArray ['Mac'] = '';}   



                $html ['ontList'.$Real_Desc_Key] = $ItemArray;
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

           
        $html ['oltType']    = 'VSOLUTION';
        $html ['oltAddress'] = $ip;
        $html ['oltName'] = $oltName;

        return $html;
    }

    static public function Client_Side_OnuInfo($ip,$read,$write,$user)
    {
        $html  = [];
        $html ['clone'] = '';

        $Descr = '';
        $snmp  = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9" , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $Real_Desc_Key = 0;
        $DescriptionClone = 0;
        foreach ($Descr as $key => $value) 
        {
            $value = trim(str_replace('STRING:','',$value));
            $value = trim(str_replace("\"", "", $value));
            if (strpos($value, $user) !== false)
            {
                $DescriptionClone++;
                $Real_Desc_Key++;
                $Pon_Port = $key;
 
                $value = trim(str_replace('STRING: ','',$value));
                $value = trim(str_replace("\"","",$value));

                $FixedPonPort = explode('.',$Pon_Port);
                $html ['PonPort']     = 'EPON0/'.$FixedPonPort[0].':'.$FixedPonPort[1];
                $html ['Description'] = $value;
                $html ['ifIndex']     = $key;

                $OnuStatus = '';
                try {
                        $OnuStatus  = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$Pon_Port , TRUE);
                        foreach ($OnuStatus as $key => $value) 
                        {
                            $html ['OnuStatus'] = trim(str_replace('INTEGER: ','',$value));
                        }
                }catch (\Exception $e){$html ['OnuStatus'] = '';}    

                $Reason = '';
                try {
                        $Reason = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.15.".$Pon_Port, TRUE);  
                        foreach ($Reason as $key => $value) 
                        {
                            $html ['Reason'] = trim(str_replace('INTEGER: ','',$value));
                        }
                }catch (\Exception $e){$html ['Reason'] = '';}    

                $OnuMac = '';
                try {
                        $OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5.".$Pon_Port , TRUE);  
                        foreach ($OnuMac as $key => $value) 
                        {
                            $value = trim(str_replace('STRING: ','',$value));
                            $html ['OnuMac'] = trim(str_replace("\"","",$value));
                        }
                }catch (\Exception $e){$html ['OnuMac'] = '';}   

                $Vendoor = '';
                try {
                        $Vendoor = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.3.".$Pon_Port , TRUE); 
                        foreach ($Vendoor as $key => $value) 
                        {
                            $value = trim(str_replace('STRING: ','',$value));
                            $html ['Vendoor'] = trim(str_replace("\"","",$value));
                        }
                }catch (\Exception $e){$html ['Vendoor'] = '';}   

                $Model = '';
                try {
                        $Model = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.4.".$Pon_Port , TRUE); 
                        foreach ($Model as $key => $value) 
                        {
                            $value = trim(str_replace('STRING: ','',$value));
                            $html ['Model'] = trim(str_replace("\"","",$value));
                        }
                }catch (\Exception $e){$html ['Model'] = '';}   

                $Dmb = '';
                try {
                        $Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$Pon_Port , TRUE); 
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

    static public function Client_Side_OnuPorts($ip,$read,$write,$user)
    {
        $html = [];
        $html ['shutdown'] = 0;
        $User_Not_Exist    = 0;
        $Descr = '';$ifAlias = '';$Onu_Status = '';$iface = [];

        $snmp      = new \SNMP(\SNMP::VERSION_2c, $ip, $read);   

        try {
                $Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9" , TRUE);
                foreach ($Descr as $key => $value) 
                {
                    $value = str_replace("STRING: ", "", $value);
                    $value = str_replace("\"", "", $value);
                    
                }
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        if(!empty($Descr))
        {
            $Anomaly = false;
            $Pon = '';$Port = '';
            foreach ($Descr as $key => $value)
            {
               $value = trim(str_replace('STRING: ','',$value));   
               $value = trim(str_replace("\"",'',$value));
        
               if (strpos($value, $user) !== false) 
               {
                    $User_Not_Exist += 1;
                    $Pon = explode('.',$key);
                    $Port = $Pon[1];
                    $html ['PonPort'] = 'EPON0/'.$Pon[0].':'.$Port;
                    $html ['Description'] = $value;
                    $html ['ifIndex'] = $key;

                    $OnuStatus = 0;
                    try {
                        $OnuStatus  = trim(str_replace('INTEGER: ','',$snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$key , TRUE))); 
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

               }
            }
        }

        if(!$User_Not_Exist) 
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }       

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

        $Descr = '';$ifAlias = '';
        try {
                $Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9" , TRUE);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $Pon_Port = '';
        foreach ($Descr as $key => $value) 
        {
            $value = str_replace('STRING: ','',$value);
            $value = str_replace("\"", "", $value);


            if (strpos($value, $user) !== false)
            {
                $User_Not_Exist += 1;

                $FirstPon   = explode('.',$key);
                $Pon_Port   = "EPON0/".$FirstPon[0].':'.$FirstPon[1];
                $SecondPon  = 'STRING: "EPON0/'.$FirstPon[0].':'.$FirstPon[1].'"';
                $SecondPon2 = '"EPON0/'.$FirstPon[0].':'.$FirstPon[1].'"';


                $html ['PonPort'] = $Pon_Port;
                $html ['Description'] = $value;

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
            }

        }

        if(!$User_Not_Exist) 
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }       

 
        return $html;
    }

    static public function ClientSidePonSelect($ip,$read) 
    {
        $html = []; 
        $PonCoordinates = []; 
        $PonList = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try {$PonList = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.10.1.2.1.1.2", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($PonList as $key => $value) 
        {
            $value = trim(str_replace('STRING: ','',$value));
            $value = trim(str_replace("\"", "", $value));
            if (strpos($value, 'EPON0') == 0) 
            {   
                $item['PonName']       = $value;
                $item['PonIndex']      = $key;
                $html["PonList_$key"]  = $item; 
            }
        }
      
        return $html;
    }
 
    static public function ClientSidePonData($ip,$pon,$read) 
    {
        $html = [];  $PonCoordinates = [];
        $Descr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try { $Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$pon , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;

        $Reason = [];
        try {$Reason = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.15.".$pon, TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuMac = [];
        try {$OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Dmb = [];
        try {$Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuStatus = [];
        try {$OnuStatus = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Vendoor = [];
        try {$Vendoor = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.3.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Model = [];
        try {$Model = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Uptime = [];
        try {$Uptime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.18.".$pon , TRUE);} 
        catch (\Exception $e) 
        {} 
                   
        $Downtime = [];
        try {$Downtime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.19.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}        
                  
        foreach ($Descr as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 'NULL')$value = 'N/A';
            $iface[$key]['IfDescr'] = $value;
        }
        foreach ($Dmb as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('STRING: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['Dmb'] = $value;
        }
        foreach ($OnuMac as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['OnuMac'] = $value;
        }
        foreach ($OnuStatus as $key => $value) 
        {   
            $iface[$key]['Status']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $value = preg_replace('/\(\d+\)/', '', $value);
            if($value)$iface[$key]['Status'] = $value;
            else $iface[$key]['Status'] = '';
        }
        foreach ($Reason as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('INTEGER: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 1 || $value == 2){$value = 'Power Off';}
            else if($value == 0 ){$value = 'Wire Down';}
            else $value = 'N/A';
            $iface[$key]['Reason'] = $value;
        }
        foreach ($Vendoor as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Vendoor'] = $value;
            else $iface[$key]['Vendoor'] = '';
        } 
        foreach ($Model as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Model'] = $value;
            else $iface[$key]['Model'] = '';
        }     
        foreach ($Uptime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);

            if($value !== 'N/A')$value =$iface[$key]['TittleUptime'] = $value;
            else $iface[$key]['TittleUptime'] = '';

            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Uptime'] = $value;
            else $iface[$key]['Uptime'] = '';
        } 
        foreach ($Downtime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);

            if($value !== 'N/A')$value =$iface[$key]['TittleDowntime'] = $value;
            else $iface[$key]['TittleDowntime'] = '';


            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Downtime'] = $value;
            else $iface[$key]['Downtime'] = '';
        } 
        
        foreach ($iface as $key => $value)
        {
            $TotalOnu++;
            $RealDbm = 0;$dBmValue = '';
            $startPos = strpos($value['Dmb'], '(');
            $endPos   = strpos($value['Dmb'], 'dBm');
            if ($startPos !== false && $endPos !== false)
            {
                $dbmSubstring = substr($value['Dmb'], $startPos + 1, $endPos - $startPos - 1);
                $dBmValue = $dbmSubstring;
            }


            if($value['Status'] == 1)$TotalOnline += 1;
            else
            {
                if($value['Reason'] == 'Power Off')$TotalPowerOff += 1;
                else if($value['Reason'] == 'Wire Down')$TotalWireDown += 1;
                $TotalOffline += 1;
            } 
             
            $CoordOnuStatus = '';
            if (strpos($value['Status'], '1') !== false)
            {
                $CoordOnuStatus = 1;
            }
            else 
            {
                $CoordOnuStatus = 2;
            }      
            $PonCoordinates[] = $value['IfDescr'].'|'.'EPON0/'.$pon.":".$key.'|'.str_replace(',',' ',$value['Uptime']).'|'.str_replace(',',' ',$value['Downtime']).'|'.$CoordOnuStatus;
        
 
            $item = [];
            $item['IfIndex']        = $key;
            $item['PonPort']        = 'EPON0/'.$pon.":".$key;
            $item['Description']    = $value['IfDescr'];
            $item['Type']           = $value['Vendoor'].' - '.$value['Model'];
            $item['Mac']            = $value['OnuMac'];
            $item['onuStatus']      = $value['Status'];    
            $item['Reason']         = $value['Reason'];    
            $item['dbm']            = $dBmValue;    
            $item['Uptime']         = $value['Uptime'];    
            $item['Downtime']       = $value['Downtime'];   
            $item['TittleUptime']   = $value['TittleUptime'];  
            $item['TittleDowntime'] = $value['TittleDowntime'];  
             
            $html["port_num_$key"] = $item;      
        }
 
            $html["total"]      = $TotalOnu; 
            $html["online"]     = $TotalOnline; 
            $html["offline"]    = $TotalOffline; 
            $html["powerOff"]   = $TotalPowerOff; 
            $html["wireDown"]   = $TotalWireDown; 
            $html["admin"]      = 'up'; 
            $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }
    
    static public function ClientSidePonAllOnline($ip,$pon,$read) 
    {
        $html = [];  $PonCoordinates = [];
        $Descr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try { $Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$pon , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;

        $Reason = [];
        try {$Reason = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.15.".$pon, TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuMac = [];
        try {$OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Dmb = [];
        try {$Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuStatus = [];
        try {$OnuStatus = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Vendoor = [];
        try {$Vendoor = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.3.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Model = [];
        try {$Model = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Uptime = [];
        try {$Uptime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.18.".$pon , TRUE);} 
        catch (\Exception $e) 
        {} 
                   
        $Downtime = [];
        try {$Downtime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.19.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}        
                  
        foreach ($Descr as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 'NULL')$value = 'N/A';
            $iface[$key]['IfDescr'] = $value;
        }
        foreach ($Dmb as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('STRING: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['Dmb'] = $value;
        }
        foreach ($OnuMac as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['OnuMac'] = $value;
        }
        foreach ($OnuStatus as $key => $value) 
        {   
            $iface[$key]['Status']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $value = preg_replace('/\(\d+\)/', '', $value);
            if($value)$iface[$key]['Status'] = $value;
            else $iface[$key]['Status'] = '';
        }
        foreach ($Reason as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('INTEGER: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 1 || $value == 2){$value = 'Power Off';}
            else if($value == 0 ){$value = 'Wire Down';}
            else $value = 'N/A';
            $iface[$key]['Reason'] = $value;
        }
        foreach ($Vendoor as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Vendoor'] = $value;
            else $iface[$key]['Vendoor'] = '';
        } 
        foreach ($Model as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Model'] = $value;
            else $iface[$key]['Model'] = '';
        }     
        foreach ($Uptime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value !== 'N/A')$value =$iface[$key]['TittleUptime'] = $value;
            else $iface[$key]['TittleUptime'] = '';
            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Uptime'] = $value;
            else $iface[$key]['Uptime'] = '';
        } 
        foreach ($Downtime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value !== 'N/A')$value =$iface[$key]['TittleDowntime'] = $value;
            else $iface[$key]['TittleDowntime'] = '';
            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Downtime'] = $value;
            else $iface[$key]['Downtime'] = '';
        } 
        
        foreach ($iface as $key => $value)
        {
            $TotalOnu++;
            $RealDbm = 0;$dBmValue = '';
            $startPos = strpos($value['Dmb'], '(');
            $endPos   = strpos($value['Dmb'], 'dBm');
            if ($startPos !== false && $endPos !== false)
            {
                $dbmSubstring = substr($value['Dmb'], $startPos + 1, $endPos - $startPos - 1);
                $dBmValue = $dbmSubstring;
            }


            if($value['Status'] == 1)$TotalOnline += 1;
            else
            {
                if($value['Reason'] == 'Power Off')$TotalPowerOff += 1;
                else if($value['Reason'] == 'Wire Down')$TotalWireDown += 1;
                $TotalOffline += 1;
            } 
             
            if($value['Status'] == 1)
            {
                $CoordOnuStatus = '';
                if (strpos($value['Status'], '1') !== false)
                {
                    $CoordOnuStatus = 1;
                }
                else 
                {
                    $CoordOnuStatus = 2;
                }      
                $PonCoordinates[] = $value['IfDescr'].'|'.'EPON0/'.$pon.":".$key.'|'.str_replace(',',' ',$value['Uptime']).'|'.str_replace(',',' ',$value['Downtime']).'|'.$CoordOnuStatus;
                
                $item = [];
                $item['IfIndex']        = $key;
                $item['PonPort']        = 'EPON0/'.$pon.":".$key;
                $item['Description']    = $value['IfDescr'];
                $item['Type']           = $value['Vendoor'].' - '.$value['Model'];
                $item['Mac']            = $value['OnuMac'];
                $item['onuStatus']      = $value['Status'];    
                $item['Reason']         = $value['Reason'];    
                $item['dbm']            = $dBmValue;    
                $item['Uptime']         = $value['Uptime'];    
                $item['Downtime']       = $value['Downtime'];   
                $item['TittleUptime']   = $value['TittleUptime'];  
                $item['TittleDowntime'] = $value['TittleDowntime']; 
                $html["port_num_$key"] = $item;  
            }
     
        }
 
            $html["total"]      = $TotalOnu; 
            $html["online"]     = $TotalOnline; 
            $html["offline"]    = $TotalOffline; 
            $html["powerOff"]   = $TotalPowerOff; 
            $html["wireDown"]   = $TotalWireDown; 
            $html["admin"]      = 'up'; 
            $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function ClientSidePonAllOffline($ip,$pon,$read) 
    {
        $html = [];  $PonCoordinates = [];
        $Descr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try { $Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$pon , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;

        $Reason = [];
        try {$Reason = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.15.".$pon, TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuMac = [];
        try {$OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Dmb = [];
        try {$Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuStatus = [];
        try {$OnuStatus = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Vendoor = [];
        try {$Vendoor = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.3.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Model = [];
        try {$Model = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Uptime = [];
        try {$Uptime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.18.".$pon , TRUE);} 
        catch (\Exception $e) 
        {} 
                   
        $Downtime = [];
        try {$Downtime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.19.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}        
                  
        foreach ($Descr as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 'NULL')$value = 'N/A';
            $iface[$key]['IfDescr'] = $value;
        }
        foreach ($Dmb as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('STRING: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['Dmb'] = $value;
        }
        foreach ($OnuMac as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['OnuMac'] = $value;
        }
        foreach ($OnuStatus as $key => $value) 
        {   
            $iface[$key]['Status']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $value = preg_replace('/\(\d+\)/', '', $value);
            if($value)$iface[$key]['Status'] = $value;
            else $iface[$key]['Status'] = '';
        }
        foreach ($Reason as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('INTEGER: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 1 || $value == 2){$value = 'Power Off';}
            else if($value == 0 ){$value = 'Wire Down';}
            else $value = 'N/A';
            $iface[$key]['Reason'] = $value;
        }
        foreach ($Vendoor as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Vendoor'] = $value;
            else $iface[$key]['Vendoor'] = '';
        } 
        foreach ($Model as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Model'] = $value;
            else $iface[$key]['Model'] = '';
        }     
        foreach ($Uptime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value !== 'N/A')$value =$iface[$key]['TittleUptime'] = $value;
            else $iface[$key]['TittleUptime'] = '';
            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Uptime'] = $value;
            else $iface[$key]['Uptime'] = '';
        } 
        foreach ($Downtime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value !== 'N/A')$value =$iface[$key]['TittleDowntime'] = $value;
            else $iface[$key]['TittleDowntime'] = '';
            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Downtime'] = $value;
            else $iface[$key]['Downtime'] = '';
        } 
        
        foreach ($iface as $key => $value)
        {
            $TotalOnu++;
            $RealDbm = 0;$dBmValue = '';
            $startPos = strpos($value['Dmb'], '(');
            $endPos   = strpos($value['Dmb'], 'dBm');
            if ($startPos !== false && $endPos !== false)
            {
                $dbmSubstring = substr($value['Dmb'], $startPos + 1, $endPos - $startPos - 1);
                $dBmValue = $dbmSubstring;
            }


            if($value['Status'] == 1)$TotalOnline += 1;
            else
            {
                if($value['Reason'] == 'Power Off')$TotalPowerOff += 1;
                else if($value['Reason'] == 'Wire Down')$TotalWireDown += 1;
                $TotalOffline += 1;
            } 
             
            if($value['Status'] == 1)
            {
                //
            }
            else
            {
                $CoordOnuStatus = '';
                if (strpos($value['Status'], '1') !== false)
                {
                    $CoordOnuStatus = 1;
                }
                else 
                {
                    $CoordOnuStatus = 2;
                }      
                $PonCoordinates[] = $value['IfDescr'].'|'.'EPON0/'.$pon.":".$key.'|'.str_replace(',',' ',$value['Uptime']).'|'.str_replace(',',' ',$value['Downtime']).'|'.$CoordOnuStatus;

                $item = [];
                $item['IfIndex']        = $key;
                $item['PonPort']        = 'EPON0/'.$pon.":".$key;
                $item['Description']    = $value['IfDescr'];
                $item['Type']           = $value['Vendoor'].' - '.$value['Model'];
                $item['Mac']            = $value['OnuMac'];
                $item['onuStatus']      = $value['Status'];    
                $item['Reason']         = $value['Reason'];    
                $item['dbm']            = $dBmValue;    
                $item['Uptime']         = $value['Uptime'];    
                $item['Downtime']       = $value['Downtime'];   
                $item['TittleUptime']   = $value['TittleUptime'];  
                $item['TittleDowntime'] = $value['TittleDowntime']; 
                $html["port_num_$key"] = $item;  
            }
     
        }
 
            $html["total"]      = $TotalOnu; 
            $html["online"]     = $TotalOnline; 
            $html["offline"]    = $TotalOffline; 
            $html["powerOff"]   = $TotalPowerOff; 
            $html["wireDown"]   = $TotalWireDown; 
            $html["admin"]      = 'up'; 
            $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function ClientSidePonAllWireDown($ip,$pon,$read) 
    {
        $html = [];  $PonCoordinates = [];
        $Descr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try { $Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$pon , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;

        $Reason = [];
        try {$Reason = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.15.".$pon, TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuMac = [];
        try {$OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Dmb = [];
        try {$Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuStatus = [];
        try {$OnuStatus = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Vendoor = [];
        try {$Vendoor = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.3.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Model = [];
        try {$Model = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Uptime = [];
        try {$Uptime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.18.".$pon , TRUE);} 
        catch (\Exception $e) 
        {} 
                   
        $Downtime = [];
        try {$Downtime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.19.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}        
                  
        foreach ($Descr as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 'NULL')$value = 'N/A';
            $iface[$key]['IfDescr'] = $value;
        }
        foreach ($Dmb as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('STRING: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['Dmb'] = $value;
        }
        foreach ($OnuMac as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['OnuMac'] = $value;
        }
        foreach ($OnuStatus as $key => $value) 
        {   
            $iface[$key]['Status']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $value = preg_replace('/\(\d+\)/', '', $value);
            if($value)$iface[$key]['Status'] = $value;
            else $iface[$key]['Status'] = '';
        }
        foreach ($Reason as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('INTEGER: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 1 || $value == 2){$value = 'Power Off';}
            else if($value == 0 ){$value = 'Wire Down';}
            else $value = 'N/A';
            $iface[$key]['Reason'] = $value;
        }
        foreach ($Vendoor as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Vendoor'] = $value;
            else $iface[$key]['Vendoor'] = '';
        } 
        foreach ($Model as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Model'] = $value;
            else $iface[$key]['Model'] = '';
        }     
        foreach ($Uptime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value !== 'N/A')$value =$iface[$key]['TittleUptime'] = $value;
            else $iface[$key]['TittleUptime'] = '';
            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Uptime'] = $value;
            else $iface[$key]['Uptime'] = '';
        } 
        foreach ($Downtime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value !== 'N/A')$value =$iface[$key]['TittleDowntime'] = $value;
            else $iface[$key]['TittleDowntime'] = '';
            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Downtime'] = $value;
            else $iface[$key]['Downtime'] = '';
        } 
        
        foreach ($iface as $key => $value)
        {
            $TotalOnu++;
            $RealDbm = 0;$dBmValue = '';
            $startPos = strpos($value['Dmb'], '(');
            $endPos   = strpos($value['Dmb'], 'dBm');
            if ($startPos !== false && $endPos !== false)
            {
                $dbmSubstring = substr($value['Dmb'], $startPos + 1, $endPos - $startPos - 1);
                $dBmValue = $dbmSubstring;
            }


            if($value['Status'] == 1)$TotalOnline += 1;
            else
            {
                if($value['Reason'] == 'Power Off')$TotalPowerOff += 1;
                else if($value['Reason'] == 'Wire Down')$TotalWireDown += 1;
                $TotalOffline += 1;
            } 
             
            if($value['Status'] == 1)
            {
                //
            }
            else
            {
                if($value['Reason'] == 'Wire Down')
                {
                    $CoordOnuStatus = '';
                    if (strpos($value['Status'], '1') !== false)
                    {
                        $CoordOnuStatus = 1;
                    }
                    else 
                    {
                        $CoordOnuStatus = 2;
                    }      
                    $PonCoordinates[] = $value['IfDescr'].'|'.'EPON0/'.$pon.":".$key.'|'.str_replace(',',' ',$value['Uptime']).'|'.str_replace(',',' ',$value['Downtime']).'|'.$CoordOnuStatus;

                    $item = [];
                    $item['IfIndex']        = $key;
                    $item['PonPort']        = 'EPON0/'.$pon.":".$key;
                    $item['Description']    = $value['IfDescr'];
                    $item['Type']           = $value['Vendoor'].' - '.$value['Model'];
                    $item['Mac']            = $value['OnuMac'];
                    $item['onuStatus']      = $value['Status'];    
                    $item['Reason']         = $value['Reason'];    
                    $item['dbm']            = $dBmValue;    
                    $item['Uptime']         = $value['Uptime'];    
                    $item['Downtime']       = $value['Downtime'];
                    $item['TittleUptime']   = $value['TittleUptime'];  
                    $item['TittleDowntime'] = $value['TittleDowntime'];    
                    $html["port_num_$key"] = $item;  
                }
 
            }
     
        }
 
            $html["total"]      = $TotalOnu; 
            $html["online"]     = $TotalOnline; 
            $html["offline"]    = $TotalOffline; 
            $html["powerOff"]   = $TotalPowerOff; 
            $html["wireDown"]   = $TotalWireDown; 
            $html["admin"]      = 'up'; 
            $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function ClientSidePonAllPowerOff($ip,$pon,$read) 
    {
        $html = []; $PonCoordinates = [];
        $Descr = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try { $Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$pon , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;

        $Reason = [];
        try {$Reason = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.15.".$pon, TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuMac = [];
        try {$OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Dmb = [];
        try {$Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $OnuStatus = [];
        try {$OnuStatus = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Vendoor = [];
        try {$Vendoor = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.3.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Model = [];
        try {$Model = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.2.1.4.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}

        $Uptime = [];
        try {$Uptime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.18.".$pon , TRUE);} 
        catch (\Exception $e) 
        {} 
                   
        $Downtime = [];
        try {$Downtime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.19.".$pon , TRUE);} 
        catch (\Exception $e) 
        {}        
                  
        foreach ($Descr as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 'NULL')$value = 'N/A';
            $iface[$key]['IfDescr'] = $value;
        }
        foreach ($Dmb as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('STRING: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['Dmb'] = $value;
        }
        foreach ($OnuMac as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $iface[$key]['OnuMac'] = $value;
        }
        foreach ($OnuStatus as $key => $value) 
        {   
            $iface[$key]['Status']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            $value = preg_replace('/\(\d+\)/', '', $value);
            if($value)$iface[$key]['Status'] = $value;
            else $iface[$key]['Status'] = '';
        }
        foreach ($Reason as $key => $value) 
        {  
            $iface[$key]['IfId'] = $key;
            $value=explode('INTEGER: ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 1 || $value == 2){$value = 'Power Off';}
            else if($value == 0 ){$value = 'Wire Down';}
            else $value = 'N/A';
            $iface[$key]['Reason'] = $value;
        }
        foreach ($Vendoor as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Vendoor'] = $value;
            else $iface[$key]['Vendoor'] = '';
        } 
        foreach ($Model as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value)$iface[$key]['Model'] = $value;
            else $iface[$key]['Model'] = '';
        }     
        foreach ($Uptime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value !== 'N/A')$value =$iface[$key]['TittleUptime'] = $value;
            else $iface[$key]['TittleUptime'] = '';
            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Uptime'] = $value;
            else $iface[$key]['Uptime'] = '';
        } 
        foreach ($Downtime as $key => $value) 
        {
            $iface[$key]['IfId']=$key;
            $value = str_replace("STRING: ", "", $value);
            $value = str_replace("\"", "", $value);
            if($value !== 'N/A')$value =$iface[$key]['TittleDowntime'] = $value;
            else $iface[$key]['TittleDowntime'] = '';
            if($value !== 'N/A')$value = VSOLUTION::calculateUptime($value);
            if($value)$iface[$key]['Downtime'] = $value;
            else $iface[$key]['Downtime'] = '';
        } 
        
        foreach ($iface as $key => $value)
        {
            $TotalOnu++;
            $RealDbm = 0;$dBmValue = '';
            $startPos = strpos($value['Dmb'], '(');
            $endPos   = strpos($value['Dmb'], 'dBm');
            if ($startPos !== false && $endPos !== false)
            {
                $dbmSubstring = substr($value['Dmb'], $startPos + 1, $endPos - $startPos - 1);
                $dBmValue = $dbmSubstring;
            }


            if($value['Status'] == 1)$TotalOnline += 1;
            else
            {
                if($value['Reason'] == 'Power Off')$TotalPowerOff += 1;
                else if($value['Reason'] == 'Wire Down')$TotalWireDown += 1;
                $TotalOffline += 1;
            } 
             
            if($value['Status'] == 1)
            {
                //
            }
            else
            {
                if($value['Reason'] == 'Power Off')
                {
                    $CoordOnuStatus = '';
                    if (strpos($value['Status'], '1') !== false)
                    {
                        $CoordOnuStatus = 1;
                    }
                    else 
                    {
                        $CoordOnuStatus = 2;
                    }      
                    $PonCoordinates[] = $value['IfDescr'].'|'.'EPON0/'.$pon.":".$key.'|'.str_replace(',',' ',$value['Uptime']).'|'.str_replace(',',' ',$value['Downtime']).'|'.$CoordOnuStatus;
                    $item = [];
                    $item['IfIndex']        = $key;
                    $item['PonPort']        = 'EPON0/'.$pon.":".$key;
                    $item['Description']    = $value['IfDescr'];
                    $item['Type']           = $value['Vendoor'].' - '.$value['Model'];
                    $item['Mac']            = $value['OnuMac'];
                    $item['onuStatus']      = $value['Status'];    
                    $item['Reason']         = $value['Reason'];    
                    $item['dbm']            = $dBmValue;    
                    $item['Uptime']         = $value['Uptime'];    
                    $item['Downtime']       = $value['Downtime'];   
                    $item['TittleUptime']   = $value['TittleUptime'];  
                    $item['TittleDowntime'] = $value['TittleDowntime']; 
                    $html["port_num_$key"] = $item;  
                }
 
            }
     
        }
 
            $html["total"]      = $TotalOnu; 
            $html["online"]     = $TotalOnline; 
            $html["offline"]    = $TotalOffline; 
            $html["powerOff"]   = $TotalPowerOff; 
            $html["wireDown"]   = $TotalWireDown; 
            $html["admin"]      = 'up'; 
            $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function OnuPortVlanChange($ip,$read,$write,$ifindex,$portIndex,$user,$vlan,$vlanMode)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        if($vlanMode == 0)$vlan = 1;
        $Segment = explode('.',$ifindex);
   
        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                if($vlanMode == 0)
                {   
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.1.0', 'i', $Segment[0]);  
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.2.0', 'i', $Segment[1]);  
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.3.0', 'i', $portIndex);   
                                    
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.1.1.0', 'i', $Segment[0]);  
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.1.2.0', 'i', $Segment[1]);  
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.1.3.0', 'i', $portIndex);   
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.1.4.0', 'i', 1);  
                }
                else if($vlanMode == 1)
                {   
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.1.0', 'i', $Segment[0]);  
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.2.0', 'i', $Segment[1]);  
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.3.0', 'i', $portIndex);   
                                    
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.1.1.0', 'i', $Segment[0]);  
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.1.2.0', 'i', $Segment[1]);  
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.1.3.0', 'i', $portIndex);   
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.1.4.0', 'i', 2);  
        
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.2.1.0', 'i', $Segment[0]);
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.2.2.0', 'i', $Segment[1]);
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.2.3.0', 'i', $portIndex);
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.2.4.0', 'i', $vlan);
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.2.5.0', 'i', 0);
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.10.2.6.0', 'i', 1);
                }
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }

        return true;
    }

    static public function OnuAdminPortOff($ip,$read,$write,$ifindex,$portIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
    
        $Segment = explode('.',$ifindex);
         
        try {

            snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.1.0', 'i', $Segment[0]);  
            snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.2.0', 'i', $Segment[1]);  
            snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.3.0', 'i', $portIndex); 
            
            snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.1.1.0', 'i', $Segment[0]);  
            snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.1.2.0', 'i', $Segment[1]);  
            snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.1.3.0', 'i', $portIndex);
    
            snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.1.4.0', 'i', 0);

        } 
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

        $Segment = explode('.',$ifindex);

        try {

                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.1.0', 'i', $Segment[0]);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.2.0', 'i', $Segment[1]);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.3.3.0', 'i', $portIndex);    

                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.1.1.0', 'i', $Segment[0]);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.1.2.0', 'i', $Segment[1]);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.1.3.0', 'i', $portIndex);

                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'.1.3.6.1.4.1.37950.1.1.5.12.5.2.1.4.0', 'i', 1);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }

        return true;
    }

    static public function OnuRestart($ip,$read,$write,$ifIndex,$user)
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

    static public function MacFind_SNMP($line)
    {

        $macAddres   = VSOLUTION::extractMacAddress($line);
        $Converted   = VSOLUTION::format_mac_address($macAddres);
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

    static public function calculateUptime($uptimeTimestamp) 
    {
        // Current date and time
        $currentDateTime = new \DateTime();

        // Your timestamp in the format 2024/01/15 16:28:35
        $uptimeTimestamp = \DateTime::createFromFormat('Y/m/d H:i:s', $uptimeTimestamp);

        // Calculate the difference
        $uptimeDifference = $currentDateTime->diff($uptimeTimestamp);

        // Return the difference
        $uptimeString = '';
        if ($uptimeDifference->y > 0) {
            $uptimeString .= $uptimeDifference->y . " y, ";
        }

        // Add months if they exist
        if ($uptimeDifference->m > 0) {
            $uptimeString .= $uptimeDifference->m . " m, ";
        }

        // Add days if they exist
        if ($uptimeDifference->d > 0) {
            $uptimeString .= $uptimeDifference->d . " d, ";
        }

        // Add hours if they exist
        if ($uptimeDifference->h > 0) {
            $uptimeString .= $uptimeDifference->h . " h, ";
        }

        // Add minutes if they exist
        if ($uptimeDifference->i > 0) {
            $uptimeString .= $uptimeDifference->i . " min";
        }

        // Remove trailing comma and space
        $uptimeString = rtrim($uptimeString, ', ');

        return $uptimeString;
    }
}
