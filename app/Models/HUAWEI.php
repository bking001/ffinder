<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\sshModel;
use App\Models\OLTSIDE_HUAWEI;
 

class HUAWEI extends Model
{
    use HasFactory;

    static public function Uninstall_Side_OnuInfo($ip,$read,$write,$user,$oltName)
    {
        $html = [];
        $html ['clone'] = '';

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        
        try {$PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $DescriptionClone = 0;
        $User_Not_Exist   = 0;
        
        try {
                foreach ($PonList as $key => $value) 
                {
                        $value  = str_replace("STRING: ", "", $value);
                        $value  = str_replace("\"", "", $value);
                        if (strpos($value, $user) !== false) 
                        {
                            $User_Not_Exist++;
                            $DescriptionClone++;
                            
                        
                            $PonID  = explode('.',$key);
                            $Pon    = HUAWEI::GPON_EPON_PORT($PonID[0]);
                            $Port   = $PonID[1];

                            $ItemArray = [];
                            $ItemArray ['ifIndex']     = $key;
                            $ItemArray ['description'] = $value;
                            $ItemArray ['ponPort']     = $Pon.':'.$PonID[1];


                            try {
                                        $SN  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.".$PonID[0].'.'.$PonID[1], TRUE);   
                                        $SN  = trim(str_replace("Hex-STRING: ", "", $SN));         
                                        $SN  = str_replace("\"", "", $SN);   
                                        $SN  = trim(str_replace(" ", "", $SN));
                                        if(strlen($SN) < 15 )
                                        {
                                            $SN = strtoupper(bin2hex($SN));
                                        }
                                    
                                        $ItemArray ['Mac'] = $SN;
                            }catch (\Exception $e){$ItemArray ['Mac'] = '';}    

                            $RealStatus = '';
                            try {
                                            $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$PonID[0].'.'.$PonID[1], TRUE);
                                            $xxx = current($Status);
                                            $xxx = str_replace('INTEGER: ', '', trim($xxx));
                                            $position = strpos($xxx, '1');
                                            if ($position !== false)
                                            {
                                                $xxx = 'Online';
                                            }
                                            else $xxx = 'Offline';

                                            $ItemArray ['StatusOnu'] = $xxx;
                                            $RealStatus = $xxx;

                            }catch (\Exception $e){$ItemArray ['StatusOnu'] = '';}   
                            
                            $Uptime = '';$Downtime = '';
                            if($RealStatus == 'Online')
                            {
                                try { 
                                        $Uptime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.22.".$PonID[0].'.'.$PonID[1], TRUE); 
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
                                        $ItemArray ['Uptime'] =  $Uptime;
                                } 
                                catch (\Exception $e){$ItemArray ['Uptime'] = '';}
                            }
                            else
                            {
                                try { 
                                        $Downtime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.23.".$PonID[0].'.'.$PonID[1], TRUE); 
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
                                        $ItemArray ['Downtime'] = $Downtime;
                                } 
                                catch (\Exception $e) 
                                {$ItemArray ['Downtime'] = '';}
                        
                            }
                    
                            
                            
                            $html ['ontList'.$key] = $ItemArray;
                        }
                }
        }
        catch (\Exception $e) {}


        try {$PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
       
        try {

                foreach ($PonList as $key => $value) 
                {
                        $value  = str_replace("STRING: ", "", $value);
                        $value  = str_replace("\"", "", $value);
                        if (strpos($value, $user) !== false) 
                        {
                            $User_Not_Exist++;
                            $DescriptionClone++;
                            
                        
                            $PonID  = explode('.',$key);
                            $Pon    = HUAWEI::GPON_EPON_PORT($PonID[0]);
                            $Port   = $PonID[1];

                            $ItemArray = [];
                            $ItemArray ['ifIndex']     = $key;
                            $ItemArray ['description'] = $value;
                            $ItemArray ['ponPort']     = $Pon.':'.$PonID[1];

                            
                            try {
                                        $SN  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3.".$PonID[0].'.'.$PonID[1], TRUE);   
                                        $SN  = trim(str_replace("Hex-STRING: ", "", $SN));         
                                        $SN  = str_replace("\"", "", $SN);   
                                        $SN  = trim(str_replace(" ", ":", $SN));
                                        if(strlen($SN) < 10 )
                                        {
                                            $SN = strtoupper(bin2hex($SN));
                                        }
                                    
                                        $ItemArray ['Mac'] = $SN;
                            }catch (\Exception $e){$ItemArray ['Mac'] = '';}    

                            $RealStatus = '';
                            try {
                                            $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$PonID[0].'.'.$PonID[1], TRUE);
                                            $xxx = current($Status);
                                            $xxx = str_replace('INTEGER: ', '', trim($xxx));
                                            $position = strpos($xxx, '1');
                                            if ($position !== false)
                                            {
                                                $xxx = 'Online';
                                            }
                                            else $xxx = 'Offline';

                                            $ItemArray ['StatusOnu'] = $xxx;
                                            $RealStatus = $xxx;

                            }catch (\Exception $e){$ItemArray ['StatusOnu'] = '';}   


                            $Uptime = '';$Downtime = '';
                            if($RealStatus == 'Online')
                            {
                                try { 
                                        $Uptime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.23.".$PonID[0].'.'.$PonID[1], TRUE); 
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
                                        $ItemArray ['Uptime'] =  $Uptime;
                                } 
                                catch (\Exception $e){$ItemArray ['Uptime'] = '';}
                            }
                            else
                            {
                                try { 
                                        $Downtime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.24.".$PonID[0].'.'.$PonID[1], TRUE); 
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
                                        $ItemArray ['Downtime'] = $Downtime;
                                } 
                                catch (\Exception $e) 
                                {$ItemArray ['Downtime'] = '';}
                        
                            }
                    
                            
                            
                            $html ['ontList'.$key] = $ItemArray;

                        }
                }
        }
        catch (\Exception $e) {}

        if($DescriptionClone > 1)
        {
            $html ['clone'] = 'ეს დესქრიფშენი გაწერილია '.$DescriptionClone.' - ონუზე ';
        }
        if(!$User_Not_Exist)
        {
            return response()->json(['error' => 'აბონენტის დესქრიფშენი არ მოიძებნა ოელტეზე']);
        }

        $html ['oltType']    = 'HUAWEI';
        $html ['oltAddress'] = $ip;
        $html ['oltName'] = $oltName;

        return $html;
    }

    static public function Client_Side_OnuInfo($ip,$read,$write,$user)
    {
        $html = [];
        $html ['clone'] = '';
        $DescriptionClone = 0;
        $User_Not_Exist   = 0;

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

       
        if(isset($PonList))
        {
            foreach ($PonList as $key => $value) 
            {
                    $value  = str_replace("STRING: ", "", $value);
                    $value  = str_replace("\"", "", $value);
                    if (strpos($value, $user) !== false) 
                    {
                        $User_Not_Exist++;
                        $DescriptionClone++;
                        
                       
                        $PonID  = explode('.',$key);
                        $Pon    = HUAWEI::Pon_Port($PonID[0]);
                        $Port   = $PonID[1];
                        
                        $html ['ifIndex']      = $key;
                        $html ['ponPort']      = $Pon.':'.$PonID[1];
                        $html ['description']  = $value;
    
    
                        $Signal = '';
                        try {
                                $Signal =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$PonID[0].'.'.$PonID[1], TRUE); 
                                $Signal = str_replace('INTEGER: ', '', trim($Signal));
                                $Signal = HUAWEI::SginalFixer($Signal);    
                                $html ['Dbm'] = $Signal;
    
                            }catch (\Exception $e){$html ['Dbm'] = '';}    
    
    
     
                        try {
                                $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24.".$PonID[0].'.'.$PonID[1], TRUE);
                                $ReadyReason = '';$FinalReason = '';
                                foreach ($Reason as $key => $TempD) 
                                {
                                    $ReadyReason = str_replace('INTEGER: ', '', trim($TempD));            
                                }
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
                                $html ['reason'] = $FinalReason;
                            }catch (\Exception $e){$html ['reason'] = '';}    
                             
    
    
    
                            $SN_Fixed = '';
                            try {
                                    $SN  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.".$PonID[0].'.'.$PonID[1], TRUE);   
                                    $SN  = trim(str_replace("Hex-STRING: ", "", $SN));         
                                    $SN  = str_replace("\"", "", $SN);   
                                    $SN  = trim(str_replace(" ", "", $SN));
                                    if(strlen($SN) < 15 )
                                    {
                                        $SN = strtoupper(bin2hex($SN));
                                    }
                                    $SN_Fixed   = substr($SN, 0, 8);
                                    $SN_Fixed   = hex2bin($SN_Fixed);   
                                    $html ['onuType'] = $SN_Fixed;
                                }catch (\Exception $e){$html ['onuType'] = '';}    
    
    
    
                                $xxx = '';
                                try {
                                        $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$PonID[0].'.'.$PonID[1], TRUE);
                                        $xxx = current($Status);
                                        $xxx = str_replace('INTEGER: ', '', trim($xxx));
                                        $position = strpos($xxx, '1');
                                        if ($position !== false)
                                        {
                                            $xxx = 'Online';
                                        }
                                        else $xxx = 'Offline';
    
                                        $html ['operateStatus'] = $xxx;
                                    }catch (\Exception $e){$html ['operateStatus'] = '';}    
                         
                    }
            }
        }

        try {$EPonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        if(isset($EPonList)) 
        {
            
            foreach ($EPonList as $key => $value) 
            {
                    $value  = str_replace("STRING: ", "", $value);
                    $value  = str_replace("\"", "", $value);
                    if (strpos($value, $user) !== false) 
                    {
                        $User_Not_Exist++;
                        $DescriptionClone++;
                        
                       
                        $PonID  = explode('.',$key);
                        $Pon    = HUAWEI::GPON_EPON_PORT($PonID[0]);
                        $Port   = $PonID[1];

                        $html ['ifIndex']      = $key;
                        $html ['ponPort']      = $Pon.':'.$PonID[1];
                        $html ['description']  = $value;
                        $html ['onuType']      = '-';
                        
                        $Signal = '';
                        try {
                                $Signal =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.".$PonID[0].'.'.$PonID[1], TRUE); 
                                $Signal = str_replace('INTEGER: ', '', trim($Signal));
                                $Signal = HUAWEI::SginalFixer($Signal);    
                                $html ['Dbm'] = $Signal;
    
                            }catch (\Exception $e){$html ['Dbm'] = '';}    
    
    
     
                        try {
                                $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25.".$PonID[0].'.'.$PonID[1], TRUE);
                                $ReadyReason = '';$FinalReason = '';
                                foreach ($Reason as $key => $TempD) 
                                {
                                    $ReadyReason = str_replace('INTEGER: ', '', trim($TempD));            
                                }
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
                                $html ['reason'] = $FinalReason;
                            }catch (\Exception $e){$html ['reason'] = '';}    
                             
                            $xxx = '';
                            try {
                                    $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$PonID[0].'.'.$PonID[1], TRUE);
                                    $xxx = current($Status);
                                    $xxx = str_replace('INTEGER: ', '', trim($xxx));
                                    $position = strpos($xxx, '1');
                                    if ($position !== false)
                                    {
                                        $xxx = 'Online';
                                    }
                                    else $xxx = 'Offline';

                                    $html ['operateStatus'] = $xxx;
                                }catch (\Exception $e){$html ['operateStatus'] = '';}    
                    }
            }
     
        }
 

        if($DescriptionClone > 1)
        {
            $html ['clone'] = 'ეს დესქრიფშენი გაწერილია '.$DescriptionClone.' - ონუზე ';
        }
        if(!$User_Not_Exist)
        {
            return response()->json(['error' => 'აბონენტის დესქრიფშენი არ მოიძებნა ოელტეზე']);
        }

        return $html;
    }

    static public function Huawei_Client_Side_OnuPorts($ip,$read,$write,$user)
    {

        $html = [];
        $html ['clone']    = '';
        $html ['shutdown'] = 1;
        $DescriptionClone = 0;
        $User_Not_Exist   = 0;
     
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

 
        if(isset($PonList))
        {
            foreach ($PonList as $key => $value) 
            {
                $value  = str_replace("STRING: ", "", $value);
                $value  = str_replace("\"", "", $value);
                if (strpos($value, $user) !== false) 
                {
                    $User_Not_Exist++;
                    $DescriptionClone++;
            
    
                    $PonID  = explode('.',$key);
                    $Pon    = HUAWEI::GPON_EPON_PORT($PonID[0]);
                    $Port   = $PonID[1];
                    
                    $html ['ifIndex']      = $key;
                    $html ['ponPort']      = $Pon.':'.$PonID[1];
                    $html ['description']  = $value;
    
                    $PortCount   = '';
                    $PortDuplex  = '';
                    $PortStatus  = '';
                    $Speed       = '';
            
                    try {$PortCount  = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.21.".$PonID[0].".".$Port, TRUE);}
                    catch (\Exception $e){}    
    
                    try {$PortDuplex = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.3.".$PonID[0].".".$Port, TRUE);}
                    catch (\Exception $e){} 
    
                    try {$PortStatus = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.22.".$PonID[0].".".$Port, TRUE);}
                    catch (\Exception $e){} 
    
                    try {$Speed      = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.4.".$PonID[0].".".$Port, TRUE);}
                    catch (\Exception $e){} 
    
     
    
                    $newArray = [];
                    if (is_array($PortCount))
                    {
                        foreach ($PortCount as $Zkey => $Zvalue) 
                        {
                            if (strpos($Zvalue, '-1') === false) 
                            {
                                $newArray[$Zkey] = 
                                [
                                    'Port'   => $PortCount[$Zkey],
                                    'Duplex' => $PortDuplex[$Zkey],
                                    'Status' => $PortStatus[$Zkey],
                                    'Speed'  => $Speed[$Zkey],
                                ];
                                if($Zkey === 4)break;
                            }
                        }
                    }
     
                    
                    $sizeArray = count($newArray); 
    
                    if($sizeArray)
                    {  
                        $Port_Number = 1;
                        $html ['shutdown'] = 0;
                     
                        foreach ($newArray as $key => $value) 
                        {   
                            $Fixed_TempSpeed;
    
                            $Tmp = trim($value['Speed']);
                            if (strpos($Tmp , ':') !== false) 
                            {
                                $TempSpeed       = explode('INTEGER: ',$Tmp); 
                                $Fixed_TempSpeed = $TempSpeed[1];
                            }
                            else
                            {
                                $Fixed_TempSpeed = trim($value['Speed']);
                            }
    
                            $Vlan    = ''; 
                            try {$Vlan    = trim(str_replace('INTEGER: ','',$snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.7.".$PonID[0].".".$Port.'.'.$key, TRUE))); }
                            catch (\Exception $e){} 
                                                
                            $RealSpeed = '';
                            if($Fixed_TempSpeed == 1){$RealSpeed = '10-Mbps';}
                            else if($Fixed_TempSpeed == 2){$RealSpeed = '100-Mbps';}
                            else if($Fixed_TempSpeed == 3){$RealSpeed = '1000-Mbps';}
                            else if($Fixed_TempSpeed == 4){$RealSpeed = 'auto';}
                            else if($Fixed_TempSpeed == 5){$RealSpeed = 'Auto 10-Mbps';}
                            else if($Fixed_TempSpeed == 6){$RealSpeed = 'Auto 100-Mbps';}
                            else if($Fixed_TempSpeed == 7){$RealSpeed = 'Auto 1000-Mbps';}
        
    
                            $AdminStatus    = ''; 
                            try {$AdminStatus    = trim(str_replace('INTEGER: ','',$snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.5.".$PonID[0].".".$Port.'.'.$key, TRUE)));}
                            catch (\Exception $e){} 
    
                             
                             
                            $TmpType = trim(str_replace('INTEGER: ','',$value['Port']));
                            $PortType = '';
                            if($TmpType  == 24){$PortType = 'Fast Ethernet';}
                            else if($TmpType == 34){$PortType = 'Gigabyte';}
                            else if($TmpType == 47){$PortType = 'Ethernet';}
               
    
                            $TmpDuplex = '';
                            if (strpos(trim($value['Duplex']) , ':') !== false) 
                            {
                                $Temp_Duplex = explode('INTEGER: ',trim($value['Duplex']));
                                $TmpDuplex   = $Temp_Duplex[1];
                            }
                            else
                            {
                                $TmpDuplex   = trim($value['Duplex']);
                            }
    
    
                            $Last_Duplex = '';
                            if($TmpDuplex == 1){$Last_Duplex = 'Half';}
                            else if ($TmpDuplex == 2){$Last_Duplex = 'Full';}
                            else if ($TmpDuplex == 3){$Last_Duplex = 'Auto';}
                            else if ($TmpDuplex == 4){$Last_Duplex = 'Autohalf';}
                            else if ($TmpDuplex == 5){$Last_Duplex = 'Full';}
    
                            $status = '';
                            $status = trim(str_replace('INTEGER: ','',$value['Status']));
                        
        
                            $item = [];
                            $item['portIndex']      = $key;
                            $item['vlan']           = $Vlan;    
                            $item['admin']          = $AdminStatus; 
                            $item['type']           = $PortType; 
                            $item['Duplex']         = $Last_Duplex; 
                            $item['status']         = $status;
                            $item['RealSpeed']      = $RealSpeed;
    
                            $html["port_num_$key"] = $item;  
    
                            $Port_Number++;
                        } 
                    }
                    else $html ['shutdown'] = 1;
                }
            }
        }

        try {$EPonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        if(isset($EPonList)) 
        {
            
            foreach ($EPonList as $key => $value) 
            {
                $value  = str_replace("STRING: ", "", $value);
                $value  = str_replace("\"", "", $value);
                if (strpos($value, $user) !== false) 
                {
                    $User_Not_Exist++;
                    $DescriptionClone++;
            
    
                    $PonID  = explode('.',$key);
                    $Pon    = HUAWEI::GPON_EPON_PORT($PonID[0]);
                    $Port   = $PonID[1];
                    
                    $html ['ifIndex']      = $key;
                    $html ['ponPort']      = $Pon.':'.$PonID[1];
                    $html ['description']  = $value;

                    $PortCount   = '';
                    $PortDuplex  = '';
                    $PortStatus  = '';
                    $Speed       = '';
                     
                    try {$PortCount = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.91.1.3.".$PonID[0].".".$Port, TRUE);}
                    catch (\Exception $e){} 
    
                    try {$PortDuplex = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.3.".$PonID[0].".".$Port, TRUE);}
                    catch (\Exception $e){} 
    
                    try {$PortStatus = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.31.".$PonID[0].".".$Port, TRUE);}
                    catch (\Exception $e){} 
    
                    try {$Speed      = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.4.".$PonID[0].".".$Port, TRUE);}
                    catch (\Exception $e){} 


                    $newArray = [];
                    if (is_array($PortCount))
                    {
                        foreach ($PortCount as $Zkey => $Zvalue) 
                        {    

                            $FiXedKey = explode('.',$Zkey);  

                            if (strpos($Zvalue, '-1') === false) 
                            {
                                $newArray[$FiXedKey[1]] = 
                                [
                                    'Port'   => $PortCount[$Zkey],
                                    'Duplex' => $PortDuplex[$FiXedKey[1]],
                                    'Status' => $PortStatus[$FiXedKey[1]],
                                    'Speed'  => $Speed[$FiXedKey[1]],
                                ];
                                if($FiXedKey[1] === 4)break;
                            }
                        }
                    }
     
                    $sizeArray = count($newArray); 

                    if($sizeArray)
                    {
                        $Port_Number = 1;
                        $html ['shutdown'] = 0;
                     
                        foreach ($newArray as $key => $value) 
                        {   
                            $Fixed_TempSpeed;
    
                            $Tmp = trim($value['Speed']);
                            if (strpos($Tmp , ':') !== false) 
                            {
                                $TempSpeed       = explode('INTEGER: ',$Tmp); 
                                $Fixed_TempSpeed = $TempSpeed[1];
                            }
                            else
                            {
                                $Fixed_TempSpeed = trim($value['Speed']);
                            }
    
                            $Vlan    = ''; 
                            try {$Vlan    = trim(str_replace('INTEGER: ','',$snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.5.".$PonID[0].".".$Port.'.'.$key, TRUE))); }
                            catch (\Exception $e){} 
                                                
                            $RealSpeed = '';
                            if($Fixed_TempSpeed == 1){$RealSpeed = '10-Mbps';}
                            else if($Fixed_TempSpeed == 2){$RealSpeed = '100-Mbps';}
                            else if($Fixed_TempSpeed == 3){$RealSpeed = '1000-Mbps';}
                            else if($Fixed_TempSpeed == 4){$RealSpeed = 'auto';}
                            else if($Fixed_TempSpeed == 5){$RealSpeed = 'Auto 10-Mbps';}
                            else if($Fixed_TempSpeed == 6){$RealSpeed = 'Auto 100-Mbps';}
                            else if($Fixed_TempSpeed == 7){$RealSpeed = 'Auto 1000-Mbps';}
        
    
                            $AdminStatus    = ''; 
                            try {$AdminStatus    = trim(str_replace('INTEGER: ','',$snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.7.".$PonID[0].".".$Port.'.'.$key, TRUE)));}
                            catch (\Exception $e){} 
    
                             
                             
                            $TmpType = trim(str_replace('INTEGER: ','',$value['Port']));
                            $PortType = '';
                            if($TmpType  == 24){$PortType = 'Fast Ethernet';}
                            else if($TmpType == 34){$PortType = 'Gigabyte';}
                            else if($TmpType == 47){$PortType = 'Ethernet';}
               
    
                            $TmpDuplex = '';
                            if (strpos(trim($value['Duplex']) , ':') !== false) 
                            {
                                $Temp_Duplex = explode('INTEGER: ',trim($value['Duplex']));
                                $TmpDuplex   = $Temp_Duplex[1];
                            }
                            else
                            {
                                $TmpDuplex   = trim($value['Duplex']);
                            }
    
    
                            $Last_Duplex = '';
                            if($TmpDuplex == 1){$Last_Duplex = 'Half';}
                            else if ($TmpDuplex == 2){$Last_Duplex = 'Full';}
                            else if ($TmpDuplex == 3){$Last_Duplex = 'Auto';}
                            else if ($TmpDuplex == 4){$Last_Duplex = 'Autohalf';}
                            else if ($TmpDuplex == 5){$Last_Duplex = 'Full';}
    
                            $status = '';
                            $status = trim(str_replace('INTEGER: ','',$value['Status']));
                        
        
                            $item = [];
                            $item['portIndex']      = $key;
                            $item['vlan']           = $Vlan;    
                            $item['admin']          = $AdminStatus; 
                            $item['type']           = '-'; 
                            $item['Duplex']         = $Last_Duplex; 
                            $item['status']         = $status;
                            $item['RealSpeed']      = $RealSpeed;
    
                            $html["port_num_$key"] = $item;  
    
                            $Port_Number++;
                        } 
                    }
    
                    
                }
            }

        }
  
        if(!$User_Not_Exist)
        {
            return response()->json(['error' => 'აბონენტის დესქრიფშენი არ მოიძებნა ოელტეზე']);
        }

        return $html;
    }

    static public function Huawei_Client_Side_OnuMacs($ip,$read,$write,$user)
    {

        $html = [];
        $html ['clone']    = '';
        $html ['shutdown'] = 1;
     
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {$PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $User_Not_Exist   = 0;
        $ServicePort_Pon_Port = '';$valueZ = ''; 

        if(isset($PonList))
        {
            foreach ($PonList as $keyPonList => $valuePonList) 
            {
                $valuePonList = trim(str_replace('STRING: ','',$valuePonList)); 
                $valuePonList = trim(str_replace("\"",'',$valuePonList)); 
                if (strpos($valuePonList, $user) !== false) 
                {
    
                    $ServicePort_Pon_Port = $keyPonList; 
                    $valueZ = $valuePonList;
    
                    $ServicePort_Pon_Port  = explode('.',$ServicePort_Pon_Port);
                    $UnFixed = explode('/',HUAWEI::GPON_EPON_PORT($ServicePort_Pon_Port[0]));
             
                    $User_Not_Exist++;
                   // $html ['ponPort']      = HUAWEI::Pon_Port($ServicePort_Pon_Port[0]).':'.$ServicePort_Pon_Port[1];
                    $html ['description']  = $valuePonList;
                    $html ['shutdown']     = 0;
    
    
                    $Ont   = $ServicePort_Pon_Port[1];
                    $Pon   = $UnFixed[2];
                    $Slot  = $UnFixed[1];
            
             
                    $OntArray   = array();
                    $PonArray   = array();
                    $SlotArray  = array();   
    
                    $OntID   = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.5", TRUE); 
                    $PonID   = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.4", TRUE);  
                    $SlotID  = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.3", TRUE); 
    
                    $OntID = '';$PonID = '';$SlotID = '';
                    try {
                            $OntID   = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.5", TRUE); 
                            $PonID   = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.4", TRUE);  
                            $SlotID  = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.3", TRUE); 
                        }catch (\Exception $e){}    
    
                        foreach ($OntID as $key => $value) 
                        { 
                            $value = trim(str_replace('INTEGER: ','',$value));
                            if($value == $Ont)
                            {
                                $OntArray[] = $key;
                            }
                        }      
                        foreach ($PonID as $keyPonID => $valuePonID) 
                        {
                            $valuePonID = trim(str_replace('INTEGER: ','',$valuePonID));
                            if($valuePonID == $Pon)
                            {
                                $PonArray[] = $keyPonID;
                            }
                        }    
                        foreach ($SlotID as $keySlot => $valueSlot) 
                        {
                            $valueSlot = trim(str_replace('INTEGER: ','',$valueSlot));
                            if($valueSlot == $Slot)
                            {
                                $SlotArray[] = $keySlot;
                            }
                        }       
                                            
                        $result = array_intersect($OntArray, $PonArray , $SlotArray);          
                        foreach ($result as $keyresult => $valueresult) 
                        {
                            $ab_nomService_nom = $valueresult;
                            if(isset($ab_nomService_nom))
                            {
                                
                                $OctextHex = str_pad(dechex($ab_nomService_nom), 4, '0', STR_PAD_LEFT);
                                $OctextHex = implode(' ', str_split($OctextHex, 2));
                                $OctextHex = "00 0A 01 07 00 04 00 00 ".trim($OctextHex);      
                                           // 00 0a 01 01 00 04 00 00 02 04   /0/2:4
                                // 000a0101000400000204
                                
                                $test = '';
                                try {
                                        ($snmp_RW->set(array("1.3.6.1.4.1.2011.6.103.4.1.2.1.2.2" , '1.3.6.1.4.1.2011.6.103.4.1.2.1.3.2', '1.3.6.1.4.1.2011.6.103.4.1.2.1.4.2' ),
                                        array("i" , 'x' , "i"),
                                        array( 4 , $OctextHex , 4))); 
                                                                          
                                        Sleep(6);
                                        
                                        $test = ($snmp->get(array("1.3.6.1.4.1.2011.6.103.4.1.3.1.2.2.0" , "1.3.6.1.4.1.2011.6.103.4.1.3.1.3.2.0" , "1.3.6.1.4.1.2011.6.103.4.1.3.1.4.2.0")));
                                    
            
                                        ($snmp_RW->set(array("1.3.6.1.4.1.2011.6.103.4.1.2.1.2.2" , '1.3.6.1.4.1.2011.6.103.4.1.2.1.3.2', '1.3.6.1.4.1.2011.6.103.4.1.2.1.4.2' ),
                                                            array("i" , 'x' , "i"),
                                                            array( 4 ,$OctextHex , 6)));           
                                    }catch (\Exception $e){}    
   
                                    if($test)
                                    {  
                                        foreach ($test as $key => $value) 
                                        {
                                            if($key == 'SNMPv2-SMI::enterprises.2011.6.103.4.1.3.1.2.2.0' || $key == '.1.3.6.1.4.1.2011.6.103.4.1.3.1.2.2.0' || $key == 'enterprises.2011.6.103.4.1.3.1.2.2.0'|| $key == '.iso.org.dod.internet.private.enterprises.2011.6.103.4.1.3.1.2.2.0')
                                            {
                                                continue;
                                                // $value = trim(str_replace('INTEGER: ','',$value));
                                                // $value = trim(str_replace("\"",'',$value));
                                                // if($value == 0)
                                                // {
                                                //     $item = [];
                                                //     $item['servicePort'] = $ab_nomService_nom;
                                                //     $item['vlan']        = '';    
                                                //     $item['mac']         = '';  
                                                //     $item['vendoor']     = ''; 
                                                    
                                                //     $html["port_num_$ab_nomService_nom"] = $item;  
                                                //     $html ['shutdown'] = 0;
                                                //     break;
                                                //}
                                            }
                    
                                            if($key == 'SNMPv2-SMI::enterprises.2011.6.103.4.1.3.1.3.2.0' || $key == '.1.3.6.1.4.1.2011.6.103.4.1.3.1.3.2.0' || $key == 'enterprises.2011.6.103.4.1.3.1.3.2.0' || $key == '.iso.org.dod.internet.private.enterprises.2011.6.103.4.1.3.1.3.2.0')
                                            {
                                                $value = str_replace('Hex-STRING: ','',$value);
                                                $value = str_replace('STRING: ','',$value);
                                                $value = str_replace('\"','',$value);
                                                $value = str_replace('\'','',$value);
                                                $value = str_replace("\n",'',$value);
                                                $value = trim($value);   
    
                                                $lines = explode("FF FF", $value);   
                                                
                                         
                                                foreach ($lines as $line) 
                                                {                  
                                                    $Part = explode(' ',trim($line));

                                                    if(count($Part) == 14)
                                                    {
                                                        $item['mac']            = ($Part[8].':'.$Part[9].':'.$Part[10].':'.$Part[11].':'.$Part[12].':'.$Part[13]);
                                                        $item['vendoor']        = HUAWEI::MacFind_SNMP((($Part[8].':'.$Part[9].':'.$Part[10].':'.$Part[11].':'.$Part[12].':'.$Part[13])));
                                                        $item['ponPort']        = HUAWEI::Pon_Port($ServicePort_Pon_Port[0]).':'.$ServicePort_Pon_Port[1];
                                                        $item['vlan']           = hexdec($Part[6] . $Part[7]);
                                                        $item['servicePort']    = ((int)($ab_nomService_nom)-1);
                                                        $html["port_num_".self::generateRandomHexString(32)] = $item;  
                                                        $html ['shutdown'] = 1; 
                                               
                                                    }
                                                    else if (count($Part) == 10)
                                                    {  
                                                        $item['mac']            = ($Part[4].':'.$Part[5].':'.$Part[6].':'.$Part[7].':'.$Part[8].':'.$Part[9]);
                                                        $item['vendoor']        = HUAWEI::MacFind_SNMP((($Part[4].':'.$Part[5].':'.$Part[6].':'.$Part[7].':'.$Part[8].':'.$Part[9])));
                                                        $item['ponPort']        = HUAWEI::Pon_Port($ServicePort_Pon_Port[0]).':'.$ServicePort_Pon_Port[1];
                                                        $item['vlan']           = hexdec($Part[2] . $Part[3]);
                                                        $item['servicePort']    = ((int)($ab_nomService_nom)-1);
                                                        $html["port_num_".self::generateRandomHexString(32)] = $item;  
                                                        $html ['shutdown'] = 1; 
                                                    }

                                                }        
                                            }
                                        }
                                    }
                            }
                        }
                }
            }
        }

        try {$EPonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        if(isset($EPonList)) 
        {      
            foreach ($EPonList as $keyPonList => $valuePonList) 
            {
                $valuePonList = trim(str_replace('STRING: ','',$valuePonList)); 
                $valuePonList = trim(str_replace("\"",'',$valuePonList)); 
                if (strpos($valuePonList, $user) !== false) 
                {
                    $ServicePort_Pon_Port = $keyPonList; 
                    $valueZ = $valuePonList;
    
                    $ServicePort_Pon_Port  = explode('.',$ServicePort_Pon_Port);
                    $UnFixed = explode('/',HUAWEI::GPON_EPON_PORT($ServicePort_Pon_Port[0]));
             
                    $User_Not_Exist++;

                    $html ['description']  = $valuePonList;
                    $html ['shutdown']     = 0;
    
    
                    $Ont   = $ServicePort_Pon_Port[1];
                    $Pon   = $UnFixed[2];
                    $Slot  = $UnFixed[1];
            
             
                    $OntArray   = array();
                    $PonArray   = array();
                    $SlotArray  = array();   

                    $OntID = '';$PonID = '';$SlotID = '';
                    try {
                            $OntID   = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.5", TRUE); 
                            $PonID   = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.4", TRUE);  
                            $SlotID  = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.3", TRUE); 
                        }catch (\Exception $e){}    

                        foreach ($OntID as $key => $value) 
                        { 
                            $value = trim(str_replace('INTEGER: ','',$value));
                            if($value == $Ont)
                            {
                                $OntArray[] = $key;
                            }
                        }      
                        foreach ($PonID as $keyPonID => $valuePonID) 
                        {
                            $valuePonID = trim(str_replace('INTEGER: ','',$valuePonID));
                            if($valuePonID == $Pon)
                            {
                                $PonArray[] = $keyPonID;
                            }
                        }    
                        foreach ($SlotID as $keySlot => $valueSlot) 
                        {
                            $valueSlot = trim(str_replace('INTEGER: ','',$valueSlot));
                            if($valueSlot == $Slot)
                            {
                                $SlotArray[] = $keySlot;
                            }
                        }       
                                            
                        $result = array_intersect($OntArray, $PonArray , $SlotArray);       
                      
                        foreach ($result as $keyresult => $valueresult) 
                        {
                            $ab_nomService_nom = $valueresult;
                          
                            if(isset($ab_nomService_nom))
                            {
                                $OctextHex = str_pad(dechex($ab_nomService_nom), 4, '0', STR_PAD_LEFT);
                                $OctextHex = implode(' ', str_split($OctextHex, 2));
                                $OctextHex = "00 0A 01 07 00 04 00 00 ".trim($OctextHex);    

                                $test = '';
                                try {
                                        ($snmp_RW->set(array("1.3.6.1.4.1.2011.6.103.4.1.2.1.2.2" , '1.3.6.1.4.1.2011.6.103.4.1.2.1.3.2', '1.3.6.1.4.1.2011.6.103.4.1.2.1.4.2' ),
                                        array("i" , 'x' , "i"),
                                        array( 4 ,$OctextHex , 4))); 
                                                                          
                                        Sleep(6);
                                        
                                        $test = ($snmp->get(array("1.3.6.1.4.1.2011.6.103.4.1.3.1.2.2.0" , "1.3.6.1.4.1.2011.6.103.4.1.3.1.3.2.0" , "1.3.6.1.4.1.2011.6.103.4.1.3.1.4.2.0")));
                                    
            
                                        ($snmp_RW->set(array("1.3.6.1.4.1.2011.6.103.4.1.2.1.2.2" , '1.3.6.1.4.1.2011.6.103.4.1.2.1.3.2', '1.3.6.1.4.1.2011.6.103.4.1.2.1.4.2' ),
                                                            array("i" , 'x' , "i"),
                                                            array( 4 ,$OctextHex , 6)));           
                                    }catch (\Exception $e){}

                                    if($test)
                                    {     
                                        foreach ($test as $key => $value) 
                                        {
                                            if($key == 'SNMPv2-SMI::enterprises.2011.6.103.4.1.3.1.2.2.0' || $key == '.1.3.6.1.4.1.2011.6.103.4.1.3.1.2.2.0' || $key == 'enterprises.2011.6.103.4.1.3.1.2.2.0'|| $key == '.iso.org.dod.internet.private.enterprises.2011.6.103.4.1.3.1.2.2.0')
                                            {
                                                continue;
    
                                            }
                    
                                            if($key == 'SNMPv2-SMI::enterprises.2011.6.103.4.1.3.1.3.2.0' || $key == '.1.3.6.1.4.1.2011.6.103.4.1.3.1.3.2.0' || $key == 'enterprises.2011.6.103.4.1.3.1.3.2.0' || $key == '.iso.org.dod.internet.private.enterprises.2011.6.103.4.1.3.1.3.2.0')
                                            {
                                                $value = str_replace('Hex-STRING: ','',$value);
                                                $value = str_replace('STRING: ','',$value);
                                                $value = str_replace('\"','',$value);
                                                $value = str_replace('\'','',$value);
                                                $value = str_replace("\n",'',$value);
                                                $value = trim($value);
                                           
                                                
                                                $lines = explode("FF FF", $value);   
                                                
                           
                                                foreach ($lines as $line) 
                                                {                  
                                                    $Part = explode(' ',trim($line));

                                                    if(count($Part) == 14)
                                                    {
                                                        $item['mac']            = ($Part[8].':'.$Part[9].':'.$Part[10].':'.$Part[11].':'.$Part[12].':'.$Part[13]);
                                                        $item['vendoor']        = HUAWEI::MacFind_SNMP((($Part[8].':'.$Part[9].':'.$Part[10].':'.$Part[11].':'.$Part[12].':'.$Part[13])));
                                                        $item['ponPort']        = HUAWEI::GPON_EPON_PORT($ServicePort_Pon_Port[0]).':'.$ServicePort_Pon_Port[1];
                                                        $item['vlan']           = hexdec($Part[6] . $Part[7]);
                                                        $item['servicePort']    = ((int)($ab_nomService_nom)-1);
                                                        $html["port_num_".self::generateRandomHexString(32)] = $item;  
                                                        $html ['shutdown'] = 1; 
                                               
                                                    }
                                                    else if (count($Part) == 10)
                                                    {  
                                                        $item['mac']            = ($Part[4].':'.$Part[5].':'.$Part[6].':'.$Part[7].':'.$Part[8].':'.$Part[9]);
                                                        $item['vendoor']        = HUAWEI::MacFind_SNMP((($Part[4].':'.$Part[5].':'.$Part[6].':'.$Part[7].':'.$Part[8].':'.$Part[9])));
                                                        $item['ponPort']        = HUAWEI::GPON_EPON_PORT($ServicePort_Pon_Port[0]).':'.$ServicePort_Pon_Port[1];
                                                        $item['vlan']           = hexdec($Part[2] . $Part[3]);
                                                        $item['servicePort']    = ((int)($ab_nomService_nom)-1);
                                                        $html["port_num_".self::generateRandomHexString(32)] = $item;  
                                                        $html ['shutdown'] = 1; 
                                                    }
                                                }
                                 
                                                   
                                            }
                                        }
                                    }
                            }
                             
                        }


                }
            }

        }


        if(!$User_Not_Exist)
        {
            return response()->json(['error' => 'აბონენტის დესქრიფშენი არ მოიძებნა ოელტეზე']);
        }

        if ( $html ['shutdown'] == 0)
        {    
            $html ['ponPort'] = HUAWEI::GPON_EPON_PORT($ServicePort_Pon_Port[0]).':'.$Ont;
        } 
        

        return $html;
    }

    static public function ClientSidePonSelect($ip,$read)
    {
        $html = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try { $PonList = $snmp->walk("1.3.6.1.2.1.31.1.1.1.1", TRUE);} 
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

            if (strpos($value, "GPON") !== false && strpos($value, ":") == false) 
            {
                $item = [];
                $item['PonName']       = $value;
                $item['PonIndex']      = $key;
                $html["PonList_$key"]  = $item; 
            }
            else if (strpos($value, "EPON") !== false && strpos($value, ":") == false) 
            {
                $item = [];
                $item['PonName']       = $value;
                $item['PonIndex']      = $key;
                $html["PonList_$key"]  = $item; 
            }
        }
        return $html;   
    }
     
    static public function ClientSidePonData($ip,$PonID,$read,$write) 
    {
        $html = [];
        $PonCoordinates = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $Pon  = HUAWEI::GPON_EPON_PORT($PonID); 
         
        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;


        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($PonID);
        if (strpos($OltType, 'GPON') !== false)
        {
                    
                try { $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$PonID, TRUE);
                        foreach ($PonList as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['PonList'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {
                    if (strpos($e->getMessage(), 'No response') !== false) 
                    {
                        return response()->json(['error' => $snmp->getError()]);
                    }
                    else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                    {
                        return response()->json(['error' => 'Pon is empty']);
                    }
                    else 
                    {
                        return response()->json(['error' => $e->getMessage()]);
                    }
                }
                
                try { 
                        $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3."  .$PonID, TRUE); 
                        foreach ($SN as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value      = str_replace("Hex-STRING: ", "", $value);
                            $value      = str_replace("STRING: ", "", $value);
                            $value      = str_replace("\"", "", $value);   
                            $value      = trim(str_replace(" ", "", $value));
                            if(strlen($value) < 15 )
                            {
                                $value = strtoupper(bin2hex($value)); 
                            }

                            $SN_Fixed   = substr($value, 0, 8);
                            $SN_Fixed   = hex2bin($SN_Fixed);        
                            
                            $iface[$key]['SN']       = $value;
                            $iface[$key]['SN_Fixed'] = $SN_Fixed;
                        }
                    } 
                catch (\Exception $e) 
                {$SN = '';}

                try { 
                        $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$PonID, TRUE);
                        foreach ($Status as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Status'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Status = '';}

                try { 
                        $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24.".$PonID, TRUE);
                        foreach ($Reason as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Reason'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Reason = '';}

                try {
                    $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.22.".$PonID, TRUE); 
                        foreach ($Uptime as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = str_replace("Hex-STRING: ", "", $value);
                            $value = str_replace("\"", "", $value);   
                            $value = trim($value);
                            $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                        
                            $DownTime = rtrim($output, ', '); 

                            $iface[$key]['Uptime'] = $DownTime; 
                            $iface[$key]['Second_Uptime'] = $Uptime; 
                        } 
                } 
                catch (\Exception $e) 
                {$Uptime = '';}

                try { 
                    
                        $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.23.".$PonID, TRUE); 
                        foreach ($Downtime as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = str_replace("Hex-STRING: ", "", $value);
                            $value = str_replace("\"", "", $value);   
                            $value = trim($value);
                            $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                        
                            $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                            $DownTime = rtrim($output, ', '); 

                            if($timeDifference->y > 10)
                            {
                                $iface[$key]['Downtime'] = 'Never'; 
                                $iface[$key]['Second_Downtime'] = 'Never';
                            }
                            else
                            {
                                $iface[$key]['Downtime'] = $DownTime; 
                                $iface[$key]['Second_Downtime'] = $Uptime; 
                            }
            
                        } 

                    
                } 
                catch (\Exception $e) 
                {$Downtime = '';}

                try { 
                        $Equipment = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.45.1.4." .$PonID, TRUE); 
                        foreach ($Equipment as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Equipment'] = $value;
                        }          
                    
                } 
                catch (\Exception $e) 
                {$Equipment = '';}

                sleep(1);

                try { 
                        $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.1.".$PonID, TRUE); 
                        foreach ($Control as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Control'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Control = '';}

                    foreach ($iface as $key => $value) 
                    {
                        $TotalOnu++;
                        $FinalReason;
                        $ReadyReason = $iface[$key]['Reason'];
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

                        try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$PonID.".".$key, TRUE);  
                            $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                            $Signal  = HUAWEI::SginalFixer($Signal);
                            } 
                        catch (\Exception $e) 
                        {$Signal = '';}
                        
                        if($iface[$key]['Status'] == 1)$TotalOnline++; 
                        else 
                        {
                            $TotalOffline++;
                            if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                            if($FinalReason == 'LOS')$TotalWireDown++;
                            else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                            else if($FinalReason == 'LOFI')$TotalWireDown++;
                        }

                        if(isset($iface[$key]['Control']))
                        {
                            if ($iface[$key]['Control'] == 1)
                            {
                                $Control = 'activate';
                            }
                            else if ($iface[$key]['Control'] == 2)
                            {
                                $Control = 'deactivate';            
                            }
                            else if ($iface[$key]['Control'] == -1)
                            {
                                $Control = 'invalid';
                            }
                        }

                    
                            $CoordOnuStatus = '';
                            if (strpos($iface[$key]['Status'], '1') !== false)
                            {
                                $CoordOnuStatus = 1;
                            }
                            else 
                            {
                                $CoordOnuStatus = 2;
                            }      
                            $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                        

                            $item = [];
                            $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                            $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                            $item['description']    = $iface[$key]['PonList'] ?? '-';
                            $item['type']           = ($iface[$key]['SN_Fixed'] ?? '-').'  '.($iface[$key]['Equipment'] ?? '-');
                            $item['serial']         = $iface[$key]['SN'] ?? '-';
                            $item['status']         = $iface[$key]['Status'] ?? '-';
                            $item['reason']         = $FinalReason ?? '-';
                            $item['control']        = $Control ?? '-';
                            $item['dbm']            = $Signal ?? '-';
                            $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';           
                            $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                            $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                            $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';


                            // $item = [];
                            // $item['ifindex']        = $PonID.'.'.$iface[$key]['IfId'];
                            // $item['portIndex']      = $Pon.':'.$iface[$key]['IfId'];
                            // $item['description']    = $iface[$key]['PonList'];
                            // $item['type']           = $iface[$key]['SN_Fixed'].'  '.$iface[$key]['Equipment'];
                            // $item['serial']         = $iface[$key]['SN'];
                            // $item['Match']          = $iface[$key]['Match'];
                            // $item['status']         = $iface[$key]['Status'];
                            // $item['reason']         = $FinalReason;
                            // $item['dbm']            = $Signal;
                            // $item['Downtime']       = $iface[$key]['Downtime'];
                            // $item['Uptime']         = $iface[$key]['Uptime'];

                            $html["port_num_$key"] = $item;  
                    

                    }

                try {  
                        $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.21.1.10.".$PonID, TRUE);
                        $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                        if($AdminStatus == 1)$AdminStatus = 'up';
                        else if($AdminStatus == 2)$AdminStatus = 'down';    
                    } 
                catch (\Exception $e) 
                {$AdminStatus = '';}


                

                $html["ponName"]         = $Pon; 
                $html["total"]           = $TotalOnu; 
                $html["total_power_off"] = $TotalPowerOff; 
                $html["total_wire_down"] = $TotalWireDown; 
                $html["TotalOffline"]    = $TotalOffline; 
                $html["TotalOnline"]     = $TotalOnline; 
                $html["AdminStatus"]     = $AdminStatus; 
                $html['PONcoordinates'] = $PonCoordinates;
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
                try { 
                        $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$PonID, TRUE);
                        foreach ($PonList as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['PonList'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {
                    if (strpos($e->getMessage(), 'No response') !== false) 
                    {
                        return response()->json(['error' => $snmp->getError()]);
                    }
                    else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                    {
                        return response()->json(['error' => 'Pon is empty']);
                    }
                    else 
                    {
                        return response()->json(['error' => $e->getMessage()]);
                    }
                }

                try { 
                        $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3."  .$PonID, TRUE); 
                        foreach ($SN as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value      = str_replace("Hex-STRING: ", "", $value);
                            $value      = str_replace("STRING: ", "", $value);
                            $value      = trim(str_replace("\"", "", $value));   
                            $value      = trim(str_replace(" ", ":", $value));
                            if(strlen($value) < 10 )
                            {
                                $value = strtoupper(bin2hex($value)); 
                            }
                                 
                            $iface[$key]['SN']       = $value;
                        }
                    } 
                catch (\Exception $e) 
                {$SN = '';}

                try { 
                        $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$PonID, TRUE);
                        foreach ($Status as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Status'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Status = '';}

                try { 
                        $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25.".$PonID, TRUE);
                        foreach ($Reason as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Reason'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Reason = '';}

                try {
                    $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.23.".$PonID, TRUE); 
                        foreach ($Uptime as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = str_replace("Hex-STRING: ", "", $value);
                            $value = str_replace("\"", "", $value);   
                            $value = trim($value);
                            $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                        
                            $DownTime = rtrim($output, ', '); 

                            $iface[$key]['Uptime'] = $DownTime; 
                            $iface[$key]['Second_Uptime'] = $Uptime; 
                        } 
                } 
                catch (\Exception $e) 
                {$Uptime = '';}

                try { 
                    
                        $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.24.".$PonID, TRUE); 
                        foreach ($Downtime as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = str_replace("Hex-STRING: ", "", $value);
                            $value = str_replace("\"", "", $value);   
                            $value = trim($value);
                            $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                        
                            $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                            $DownTime = rtrim($output, ', '); 

                            if($timeDifference->y > 10)
                            {
                                $iface[$key]['Downtime'] = 'Never'; 
                                $iface[$key]['Second_Downtime'] = 'Never';
                            }
                            else
                            {
                                $iface[$key]['Downtime'] = $DownTime; 
                                $iface[$key]['Second_Downtime'] = $Uptime; 
                            }
            
                        } 

                    
                } 
                catch (\Exception $e) 
                {$Downtime = '';}
                
                Sleep(1);

                try { 
                        $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.1.".$PonID, TRUE); 
                        foreach ($Control as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Control'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Control = '';}
                 
                try {  
                        $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.31.1.5.".$PonID, TRUE);
                        $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                        if($AdminStatus == 1)$AdminStatus = 'up';
                        else if($AdminStatus == 2)$AdminStatus = 'down';    
                    } 
                catch (\Exception $e) 
                {$AdminStatus = '';}


                foreach ($iface as $key => $value) 
                {
                    $TotalOnu++;
                    $FinalReason;
                    $ReadyReason = $iface[$key]['Reason'];
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

                    try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.".$PonID.".".$key, TRUE);  
                        $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                        $Signal  = HUAWEI::SginalFixer($Signal);
                        } 
                    catch (\Exception $e) 
                    {$Signal = '';}
                    
                    if($iface[$key]['Status'] == 1)$TotalOnline++; 
                    else 
                    {
                        $TotalOffline++;
                        if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                        if($FinalReason == 'LOS')$TotalWireDown++;
                        else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                        else if($FinalReason == 'LOFI')$TotalWireDown++;
                    }

                    if(isset($iface[$key]['Control']))
                    {
                        if ($iface[$key]['Control'] == 1)
                        {
                            $Control = 'activate';
                        }
                        else if ($iface[$key]['Control'] == 2)
                        {
                            $Control = 'deactivate';            
                        }
                        else if ($iface[$key]['Control'] == -1)
                        {
                            $Control = 'invalid';
                        }
                    }

                
                        $CoordOnuStatus = '';
                        if (strpos($iface[$key]['Status'], '1') !== false)
                        {
                            $CoordOnuStatus = 1;
                        }
                        else 
                        {
                            $CoordOnuStatus = 2;
                        }      
                        $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                    

                        $item = [];
                        $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                        $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                        $item['description']    = $iface[$key]['PonList'] ?? '-';
                        $item['type']           = $iface[$key]['SN_Fixed'] ?? '-';
                        $item['serial']         = $iface[$key]['SN'] ?? '-';
                        $item['status']         = $iface[$key]['Status'] ?? '-';
                        $item['reason']         = $FinalReason ?? '-';
                        $item['control']        = $Control ?? '-';
                        $item['dbm']            = $Signal ?? '-';
                        $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';           
                        $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                        $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                        $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';


                        $html["port_num_$key"] = $item;  
                

                }


                $html["ponName"]         = $Pon; 
                $html["total"]           = $TotalOnu; 
                $html["total_power_off"] = $TotalPowerOff; 
                $html["total_wire_down"] = $TotalWireDown; 
                $html["TotalOffline"]    = $TotalOffline; 
                $html["TotalOnline"]     = $TotalOnline; 
                $html["AdminStatus"]     = $AdminStatus; 
                $html['PONcoordinates']  = $PonCoordinates;

                return $html;
        }
        else
        {
            return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }

        return $html;   
    }

    static public function ClientSidePonAllOnline($ip,$PonID,$read,$write) 
    {
        $html = [];
        $PonCoordinates = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $Pon  = HUAWEI::GPON_EPON_PORT($PonID); 
      
        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;

        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($PonID);
        if (strpos($OltType, 'GPON') !== false)
        {
            try { $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$PonID, TRUE);
                    foreach ($PonList as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['PonList'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
                else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                {
                    return response()->json(['error' => 'Pon is empty']);
                }
                else 
                {
                    return response()->json(['error' => $e->getMessage()]);
                }
            }
            
            try { 
                    $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3."  .$PonID, TRUE); 
                    foreach ($SN as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value      = str_replace("Hex-STRING: ", "", $value);
                        $value      = str_replace("STRING: ", "", $value);
                        $value      = str_replace("\"", "", $value);   
                        $value      = trim(str_replace(" ", "", $value));
                        if(strlen($value) < 15 )
                        {
                            $value = strtoupper(bin2hex($value)); 
                        }

                        $SN_Fixed   = substr($value, 0, 8);
                        $SN_Fixed   = hex2bin($SN_Fixed);        
                        
                        $iface[$key]['SN']       = $value;
                        $iface[$key]['SN_Fixed'] = $SN_Fixed;
                    }
                } 
            catch (\Exception $e) 
            {$SN = '';}

            try { 
                    $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$PonID, TRUE);
                    foreach ($Status as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Status'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Status = '';}

            try { 
                    $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24.".$PonID, TRUE);
                    foreach ($Reason as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Reason'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Reason = '';}

            try {
                $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.22.".$PonID, TRUE); 
                    foreach ($Uptime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                    
                        $DownTime = rtrim($output, ', '); 
        
                        $iface[$key]['Uptime'] = $DownTime; 
                        $iface[$key]['Second_Uptime'] = $Uptime; 
                    } 
            } 
            catch (\Exception $e) 
            {$Uptime = '';}

            try { 
                
                    $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.23.".$PonID, TRUE); 
                    foreach ($Downtime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);
        
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
                                    
                        $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                        $DownTime = rtrim($output, ', '); 
        
                        if($timeDifference->y > 10)
                        {
                            $iface[$key]['Downtime'] = 'Never'; 
                            $iface[$key]['Second_Downtime'] = 'Never';
                        }
                        else
                        {
                            $iface[$key]['Downtime'] = $DownTime; 
                            $iface[$key]['Second_Downtime'] = $Uptime; 
                        }
        
                    } 
        
                
            } 
            catch (\Exception $e) 
            {$Downtime = '';}

            try { 
                    $Equipment = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.45.1.4." .$PonID, TRUE); 
                    foreach ($Equipment as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Equipment'] = $value;
                    }          
                
            } 
            catch (\Exception $e) 
            {$Equipment = '';}

            sleep(1);

            try { 
                    $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.1.".$PonID, TRUE); 
                    foreach ($Control as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Control'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Control = '';}

            
    
                foreach ($iface as $key => $value) 
                {
                    $TotalOnu++;
                    $FinalReason;
                    $ReadyReason = $iface[$key]['Reason'];
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

                    try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$PonID.".".$key, TRUE);  
                        $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                        $Signal  = HUAWEI::SginalFixer($Signal);
                        } 
                    catch (\Exception $e) 
                    {$Signal = '';}
                    
                    if($iface[$key]['Status'] == 1)$TotalOnline++; 
                    else 
                    {
                        $TotalOffline++;
                        if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                        if($FinalReason == 'LOS')$TotalWireDown++;
                        else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                        else if($FinalReason == 'LOFI')$TotalWireDown++;
                    }
                

                    if($iface[$key]['Status'] == 1)
                    {
                        $CoordOnuStatus = '';
                        if (strpos($iface[$key]['Status'], '1') !== false)
                        {
                            $CoordOnuStatus = 1;
                        }
                        else 
                        {
                            $CoordOnuStatus = 2;
                        }      
                        $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                    

                        $item = [];
                        $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                        $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                        $item['description']    = $iface[$key]['PonList'] ?? '-';
                        $item['type']           = ($iface[$key]['SN_Fixed'] ?? '-').'  '.($iface[$key]['Equipment'] ?? '-');
                        $item['serial']         = $iface[$key]['SN'] ?? '-';
                        $item['control']        = $iface[$key]['Control'] ?? '-';
                        $item['status']         = $iface[$key]['Status'] ?? '-';
                        $item['reason']         = $FinalReason ?? '-';
                        $item['dbm']            = $Signal ?? '-';
                        $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';
                        $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                        $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                        $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';

                        $html["port_num_$key"] = $item;  
                    }    
    
                }

            try {  
                    $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.21.1.10.".$PonID, TRUE);
                    $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                    if($AdminStatus == 1)$AdminStatus = 'up';
                    else if($AdminStatus == 2)$AdminStatus = 'down';    
                } 
            catch (\Exception $e) 
            {$AdminStatus = '';}


            $html["ponName"]         = $Pon; 
            $html["total"]           = $TotalOnu; 
            $html["total_power_off"] = $TotalPowerOff; 
            $html["total_wire_down"] = $TotalWireDown; 
            $html["TotalOffline"]    = $TotalOffline; 
            $html["TotalOnline"]     = $TotalOnline; 
            $html["AdminStatus"]     = $AdminStatus; 
            $html['PONcoordinates']  = $PonCoordinates;

        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try { 
                    $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$PonID, TRUE);
                    foreach ($PonList as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['PonList'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
                else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                {
                    return response()->json(['error' => 'Pon is empty']);
                }
                else 
                {
                    return response()->json(['error' => $e->getMessage()]);
                }
            }

            try { 
                    $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3."  .$PonID, TRUE); 
                    foreach ($SN as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value      = str_replace("Hex-STRING: ", "", $value);
                        $value      = str_replace("STRING: ", "", $value);
                        $value      = trim(str_replace("\"", "", $value));   
                        $value      = trim(str_replace(" ", ":", $value));
                        if(strlen($value) < 10 )
                        {
                            $value = strtoupper(bin2hex($value)); 
                        }
                            
                        $iface[$key]['SN']       = $value;
                    }
                } 
            catch (\Exception $e) 
            {$SN = '';}

            try { 
                    $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$PonID, TRUE);
                    foreach ($Status as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Status'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Status = '';}

            try { 
                    $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25.".$PonID, TRUE);
                    foreach ($Reason as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Reason'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Reason = '';}

            try {
                $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.23.".$PonID, TRUE); 
                    foreach ($Uptime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                    
                        $DownTime = rtrim($output, ', '); 

                        $iface[$key]['Uptime'] = $DownTime; 
                        $iface[$key]['Second_Uptime'] = $Uptime; 
                    } 
            } 
            catch (\Exception $e) 
            {$Uptime = '';}

            try { 
                
                    $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.24.".$PonID, TRUE); 
                    foreach ($Downtime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                    
                        $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                        $DownTime = rtrim($output, ', '); 

                        if($timeDifference->y > 10)
                        {
                            $iface[$key]['Downtime'] = 'Never'; 
                            $iface[$key]['Second_Downtime'] = 'Never';
                        }
                        else
                        {
                            $iface[$key]['Downtime'] = $DownTime; 
                            $iface[$key]['Second_Downtime'] = $Uptime; 
                        }
        
                    } 

                
            } 
            catch (\Exception $e) 
            {$Downtime = '';}
            
            Sleep(1);

            try { 
                    $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.1.".$PonID, TRUE); 
                    foreach ($Control as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Control'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Control = '';}
            
            try {  
                    $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.31.1.5.".$PonID, TRUE);
                    $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                    if($AdminStatus == 1)$AdminStatus = 'up';
                    else if($AdminStatus == 2)$AdminStatus = 'down';    
                } 
            catch (\Exception $e) 
            {$AdminStatus = '';}

            foreach ($iface as $key => $value) 
            {
                $TotalOnu++;
                $FinalReason;
                $ReadyReason = $iface[$key]['Reason'];
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

                try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.".$PonID.".".$key, TRUE);  
                    $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                    $Signal  = HUAWEI::SginalFixer($Signal);
                    } 
                catch (\Exception $e) 
                {$Signal = '';}
                
                if($iface[$key]['Status'] == 1)$TotalOnline++; 
                else 
                {
                    $TotalOffline++;
                    if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                    if($FinalReason == 'LOS')$TotalWireDown++;
                    else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                    else if($FinalReason == 'LOFI')$TotalWireDown++;
                }
            

                if($iface[$key]['Status'] == 1)
                {
                    $CoordOnuStatus = '';
                    if (strpos($iface[$key]['Status'], '1') !== false)
                    {
                        $CoordOnuStatus = 1;
                    }
                    else 
                    {
                        $CoordOnuStatus = 2;
                    }      
                    $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                

                    $item = [];
                    $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                    $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                    $item['description']    = $iface[$key]['PonList'] ?? '-';
                    $item['type']           = ($iface[$key]['SN_Fixed'] ?? '-').'  '.($iface[$key]['Equipment'] ?? '-');
                    $item['serial']         = $iface[$key]['SN'] ?? '-';
                    $item['control']        = $iface[$key]['Control'] ?? '-';
                    $item['status']         = $iface[$key]['Status'] ?? '-';
                    $item['reason']         = $FinalReason ?? '-';
                    $item['dbm']            = $Signal ?? '-';
                    $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';
                    $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                    $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                    $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';

                    $html["port_num_$key"] = $item;  
                }    

            }


            $html["ponName"]         = $Pon; 
            $html["total"]           = $TotalOnu; 
            $html["total_power_off"] = $TotalPowerOff; 
            $html["total_wire_down"] = $TotalWireDown; 
            $html["TotalOffline"]    = $TotalOffline; 
            $html["TotalOnline"]     = $TotalOnline; 
            $html["AdminStatus"]     = $AdminStatus; 
            $html['PONcoordinates']  = $PonCoordinates;
        }
        else
        {
            return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }

        return $html;   
    }

    static public function ClientSidePonAllOffline($ip,$PonID,$read,$write) 
    {
        $html = [];
        $PonCoordinates = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $Pon  = HUAWEI::GPON_EPON_PORT($PonID); 
      
        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;

        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($PonID);
        if (strpos($OltType, 'GPON') !== false)
        {
            try { $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$PonID, TRUE);
                    foreach ($PonList as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['PonList'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
                else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                {
                    return response()->json(['error' => 'Pon is empty']);
                }
                else 
                {
                    return response()->json(['error' => $e->getMessage()]);
                }
            }
            
            try { 
                    $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3."  .$PonID, TRUE); 
                    foreach ($SN as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value      = str_replace("Hex-STRING: ", "", $value);
                        $value      = str_replace("STRING: ", "", $value);
                        $value      = str_replace("\"", "", $value);   
                        $value      = trim(str_replace(" ", "", $value));
                        if(strlen($value) < 15 )
                        {
                            $value = strtoupper(bin2hex($value)); 
                        }

                        $SN_Fixed   = substr($value, 0, 8);
                        $SN_Fixed   = hex2bin($SN_Fixed);        
                        
                        $iface[$key]['SN']       = $value;
                        $iface[$key]['SN_Fixed'] = $SN_Fixed;
                    }
                } 
            catch (\Exception $e) 
            {$SN = '';}

            try { 
                    $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$PonID, TRUE);
                    foreach ($Status as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Status'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Status = '';}

            try { 
                    $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24.".$PonID, TRUE);
                    foreach ($Reason as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Reason'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Reason = '';}

            try {
                $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.22.".$PonID, TRUE); 
                    foreach ($Uptime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                    
                        $DownTime = rtrim($output, ', '); 
        
                        $iface[$key]['Uptime'] = $DownTime; 
                        $iface[$key]['Second_Uptime'] = $Uptime; 
                    } 
            } 
            catch (\Exception $e) 
            {$Uptime = '';}

            try { 
                
                    $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.23.".$PonID, TRUE); 
                    foreach ($Downtime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);
        
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
                                    
                        $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                        $DownTime = rtrim($output, ', '); 
        
                        if($timeDifference->y > 10)
                        {
                            $iface[$key]['Downtime'] = 'Never'; 
                            $iface[$key]['Second_Downtime'] = 'Never';
                        }
                        else
                        {
                            $iface[$key]['Downtime'] = $DownTime; 
                            $iface[$key]['Second_Downtime'] = $Uptime; 
                        }
        
                    } 
        
                
            } 
            catch (\Exception $e) 
            {$Downtime = '';}

            try { 
                    $Equipment = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.45.1.4." .$PonID, TRUE); 
                    foreach ($Equipment as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Equipment'] = $value;
                    }          
                
            } 
            catch (\Exception $e) 
            {$Equipment = '';}

            sleep(1);

            try { 
                    $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.1.".$PonID, TRUE); 
                    foreach ($Control as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Control'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Control = '';}
            
    
                foreach ($iface as $key => $value) 
                {
                    $TotalOnu++;
                    $FinalReason;
                    $ReadyReason = $iface[$key]['Reason'];
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

                    try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$PonID.".".$key, TRUE);  
                        $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                        $Signal  = HUAWEI::SginalFixer($Signal);
                        } 
                    catch (\Exception $e) 
                    {$Signal = '';}
                    
                    if($iface[$key]['Status'] == 1)$TotalOnline++; 
                    else 
                    {
                        $TotalOffline++;
                        if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                        if($FinalReason == 'LOS')$TotalWireDown++;
                        else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                        else if($FinalReason == 'LOFI')$TotalWireDown++;

                        $CoordOnuStatus = '';
                        if (strpos($iface[$key]['Status'], '1') !== false)
                        {
                            $CoordOnuStatus = 1;
                        }
                        else 
                        {
                            $CoordOnuStatus = 2;
                        }      
                        $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                    

                        $item = [];
                        $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                        $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                        $item['description']    = $iface[$key]['PonList'] ?? '-';
                        $item['type']           = ($iface[$key]['SN_Fixed'] ?? '-').'  '.($iface[$key]['Equipment'] ?? '-');
                        $item['serial']         = $iface[$key]['SN'] ?? '-';
                        $item['control']        = $iface[$key]['Control'] ?? '-';
                        $item['status']         = $iface[$key]['Status'] ?? '-';
                        $item['reason']         = $FinalReason ?? '-';
                        $item['dbm']            = $Signal ?? '-';
                        $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';
                        $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                        $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                        $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';

                        $html["port_num_$key"] = $item;  
                    }
                
    
    
                }

            try {  
                    $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.21.1.10.".$PonID, TRUE);
                    $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                    if($AdminStatus == 1)$AdminStatus = 'up';
                    else if($AdminStatus == 2)$AdminStatus = 'down';    
                } 
            catch (\Exception $e) 
            {$AdminStatus = '';}

    

            $html["ponName"]         = $Pon; 
            $html["total"]           = $TotalOnu; 
            $html["total_power_off"] = $TotalPowerOff; 
            $html["total_wire_down"] = $TotalWireDown; 
            $html["TotalOffline"]    = $TotalOffline; 
            $html["TotalOnline"]     = $TotalOnline; 
            $html["AdminStatus"]     = $AdminStatus; 
            $html['PONcoordinates']  = $PonCoordinates;          
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try { 
                    $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$PonID, TRUE);
                    foreach ($PonList as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['PonList'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
                else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                {
                    return response()->json(['error' => 'Pon is empty']);
                }
                else 
                {
                    return response()->json(['error' => $e->getMessage()]);
                }
            }

            try { 
                    $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3."  .$PonID, TRUE); 
                    foreach ($SN as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value      = str_replace("Hex-STRING: ", "", $value);
                        $value      = str_replace("STRING: ", "", $value);
                        $value      = trim(str_replace("\"", "", $value));   
                        $value      = trim(str_replace(" ", ":", $value));
                        if(strlen($value) < 10 )
                        {
                            $value = strtoupper(bin2hex($value)); 
                        }
                            
                        $iface[$key]['SN']       = $value;
                    }
                } 
            catch (\Exception $e) 
            {$SN = '';}

            try { 
                    $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$PonID, TRUE);
                    foreach ($Status as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Status'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Status = '';}

            try { 
                    $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25.".$PonID, TRUE);
                    foreach ($Reason as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Reason'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Reason = '';}

            try {
                $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.23.".$PonID, TRUE); 
                    foreach ($Uptime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                    
                        $DownTime = rtrim($output, ', '); 

                        $iface[$key]['Uptime'] = $DownTime; 
                        $iface[$key]['Second_Uptime'] = $Uptime; 
                    } 
            } 
            catch (\Exception $e) 
            {$Uptime = '';}

            try { 
                
                    $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.24.".$PonID, TRUE); 
                    foreach ($Downtime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                    
                        $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                        $DownTime = rtrim($output, ', '); 

                        if($timeDifference->y > 10)
                        {
                            $iface[$key]['Downtime'] = 'Never'; 
                            $iface[$key]['Second_Downtime'] = 'Never';
                        }
                        else
                        {
                            $iface[$key]['Downtime'] = $DownTime; 
                            $iface[$key]['Second_Downtime'] = $Uptime; 
                        }
        
                    } 

                
            } 
            catch (\Exception $e) 
            {$Downtime = '';}
            
            Sleep(1);

            try { 
                    $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.1.".$PonID, TRUE); 
                    foreach ($Control as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Control'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Control = '';}
            
            try {  
                    $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.31.1.5.".$PonID, TRUE);
                    $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                    if($AdminStatus == 1)$AdminStatus = 'up';
                    else if($AdminStatus == 2)$AdminStatus = 'down';    
                } 
            catch (\Exception $e) 
            {$AdminStatus = '';}                                                            

            foreach ($iface as $key => $value) 
            {
                $TotalOnu++;
                $FinalReason;
                $ReadyReason = $iface[$key]['Reason'];
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

                try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.".$PonID.".".$key, TRUE);  
                    $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                    $Signal  = HUAWEI::SginalFixer($Signal);
                    } 
                catch (\Exception $e) 
                {$Signal = '';}
                
                if($iface[$key]['Status'] == 1)$TotalOnline++; 
                else 
                {
                    $TotalOffline++;
                    if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                    if($FinalReason == 'LOS')$TotalWireDown++;
                    else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                    else if($FinalReason == 'LOFI')$TotalWireDown++;

                    $CoordOnuStatus = '';
                    if (strpos($iface[$key]['Status'], '1') !== false)
                    {
                        $CoordOnuStatus = 1;
                    }
                    else 
                    {
                        $CoordOnuStatus = 2;
                    }      
                    $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                

                    $item = [];
                    $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                    $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                    $item['description']    = $iface[$key]['PonList'] ?? '-';
                    $item['type']           = ($iface[$key]['SN_Fixed'] ?? '-').'  '.($iface[$key]['Equipment'] ?? '-');
                    $item['serial']         = $iface[$key]['SN'] ?? '-';
                    $item['control']        = $iface[$key]['Control'] ?? '-';
                    $item['status']         = $iface[$key]['Status'] ?? '-';
                    $item['reason']         = $FinalReason ?? '-';
                    $item['dbm']            = $Signal ?? '-';
                    $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';
                    $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                    $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                    $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';

                    $html["port_num_$key"] = $item;  
                }
            


            }

            
            $html["ponName"]         = $Pon; 
            $html["total"]           = $TotalOnu; 
            $html["total_power_off"] = $TotalPowerOff; 
            $html["total_wire_down"] = $TotalWireDown; 
            $html["TotalOffline"]    = $TotalOffline; 
            $html["TotalOnline"]     = $TotalOnline; 
            $html["AdminStatus"]     = $AdminStatus; 
            $html['PONcoordinates']  = $PonCoordinates;   
        }
        else
        {
            return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }


        return $html;   
    }

    static public function ClientSidePonAllPowerOff($ip,$PonID,$read,$write) 
    {
        $html = [];
        $PonCoordinates = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $Pon  = HUAWEI::GPON_EPON_PORT($PonID); 
      
        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;

        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($PonID);
        if (strpos($OltType, 'GPON') !== false)
        {
            try { $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$PonID, TRUE);
                    foreach ($PonList as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['PonList'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
                else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                {
                    return response()->json(['error' => 'Pon is empty']);
                }
                else 
                {
                    return response()->json(['error' => $e->getMessage()]);
                }
            }
            
            try { 
                    $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3."  .$PonID, TRUE); 
                    foreach ($SN as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value      = str_replace("Hex-STRING: ", "", $value);
                        $value      = str_replace("STRING: ", "", $value);
                        $value      = str_replace("\"", "", $value);   
                        $value      = trim(str_replace(" ", "", $value));
                        if(strlen($value) < 15 )
                        {
                            $value = strtoupper(bin2hex($value)); 
                        }

                        $SN_Fixed   = substr($value, 0, 8);
                        $SN_Fixed   = hex2bin($SN_Fixed);        
                        
                        $iface[$key]['SN']       = $value;
                        $iface[$key]['SN_Fixed'] = $SN_Fixed;
                    }
                } 
            catch (\Exception $e) 
            {$SN = '';}

            try { 
                    $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$PonID, TRUE);
                    foreach ($Status as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Status'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Status = '';}

            try { 
                    $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24.".$PonID, TRUE);
                    foreach ($Reason as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Reason'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Reason = '';}

            try {
                $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.22.".$PonID, TRUE); 
                    foreach ($Uptime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                    
                        $DownTime = rtrim($output, ', '); 
        
                        $iface[$key]['Uptime'] = $DownTime; 
                        $iface[$key]['Second_Uptime'] = $Uptime; 
                    } 
            } 
            catch (\Exception $e) 
            {$Uptime = '';}

            try { 
                
                    $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.23.".$PonID, TRUE); 
                    foreach ($Downtime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);
        
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
                                    
                        $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                        $DownTime = rtrim($output, ', '); 
        
                        if($timeDifference->y > 10)
                        {
                            $iface[$key]['Downtime'] = 'Never'; 
                            $iface[$key]['Second_Downtime'] = 'Never';
                        }
                        else
                        {
                            $iface[$key]['Downtime'] = $DownTime; 
                            $iface[$key]['Second_Downtime'] = $Uptime; 
                        }
        
                    } 
        
                
            } 
            catch (\Exception $e) 
            {$Downtime = '';}

            try { 
                    $Equipment = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.45.1.4." .$PonID, TRUE); 
                    foreach ($Equipment as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Equipment'] = $value;
                    }          
                
            } 
            catch (\Exception $e) 
            {$Equipment = '';}

            sleep(1);

            try { 
                    $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.1.".$PonID, TRUE); 
                    foreach ($Control as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Control'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Control = '';}
    
                foreach ($iface as $key => $value) 
                {
                    $TotalOnu++;
                    $FinalReason;
                    $ReadyReason = $iface[$key]['Reason'];
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

                    try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$PonID.".".$key, TRUE);  
                        $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                        $Signal  = HUAWEI::SginalFixer($Signal);
                        } 
                    catch (\Exception $e) 
                    {$Signal = '';}
                    
                    if($iface[$key]['Status'] == 1)$TotalOnline++; 
                    else 
                    {
                        $TotalOffline++;
                        if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                        if($FinalReason == 'LOS')$TotalWireDown++;
                        else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                        else if($FinalReason == 'LOFI')$TotalWireDown++;

                        if($FinalReason == 'Dying-Gasp')
                        {
                            $CoordOnuStatus = '';
                            if (strpos($iface[$key]['Status'], '1') !== false)
                            {
                                $CoordOnuStatus = 1;
                            }
                            else 
                            {
                                $CoordOnuStatus = 2;
                            }      
                            $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                        

                            $item = [];
                            $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                            $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                            $item['description']    = $iface[$key]['PonList'] ?? '-';
                            $item['type']           = ($iface[$key]['SN_Fixed'] ?? '-').'  '.($iface[$key]['Equipment'] ?? '-');
                            $item['serial']         = $iface[$key]['SN'] ?? '-';
                            $item['control']        = $iface[$key]['Control'] ?? '-';
                            $item['status']         = $iface[$key]['Status'] ?? '-';
                            $item['reason']         = $FinalReason ?? '-';
                            $item['dbm']            = $Signal ?? '-';
                            $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';
                            $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                            $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                            $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';
        
                            $html["port_num_$key"] = $item;  
                        }
    
                    }
                
    
    
                }

            try {  
                    $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.21.1.10.".$PonID, TRUE);
                    $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                    if($AdminStatus == 1)$AdminStatus = 'up';
                    else if($AdminStatus == 2)$AdminStatus = 'down';    
                } 
            catch (\Exception $e) 
            {$AdminStatus = '';}


            

            $html["ponName"]         = $Pon; 
            $html["total"]           = $TotalOnu; 
            $html["total_power_off"] = $TotalPowerOff; 
            $html["total_wire_down"] = $TotalWireDown; 
            $html["TotalOffline"]    = $TotalOffline; 
            $html["TotalOnline"]     = $TotalOnline; 
            $html["AdminStatus"]     = $AdminStatus; 
            $html['PONcoordinates']  = $PonCoordinates;    
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try { 
                    $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$PonID, TRUE);
                    foreach ($PonList as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['PonList'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
                else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                {
                    return response()->json(['error' => 'Pon is empty']);
                }
                else 
                {
                    return response()->json(['error' => $e->getMessage()]);
                }
            }

            try { 
                    $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3."  .$PonID, TRUE); 
                    foreach ($SN as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value      = str_replace("Hex-STRING: ", "", $value);
                        $value      = str_replace("STRING: ", "", $value);
                        $value      = trim(str_replace("\"", "", $value));   
                        $value      = trim(str_replace(" ", ":", $value));
                        if(strlen($value) < 10 )
                        {
                            $value = strtoupper(bin2hex($value)); 
                        }
                            
                        $iface[$key]['SN']       = $value;
                    }
                } 
            catch (\Exception $e) 
            {$SN = '';}

            try { 
                    $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$PonID, TRUE);
                    foreach ($Status as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Status'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Status = '';}

            try { 
                    $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25.".$PonID, TRUE);
                    foreach ($Reason as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Reason'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Reason = '';}

            try {
                $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.23.".$PonID, TRUE); 
                    foreach ($Uptime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                    
                        $DownTime = rtrim($output, ', '); 

                        $iface[$key]['Uptime'] = $DownTime; 
                        $iface[$key]['Second_Uptime'] = $Uptime; 
                    } 
            } 
            catch (\Exception $e) 
            {$Uptime = '';}

            try { 
                
                    $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.24.".$PonID, TRUE); 
                    foreach ($Downtime as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = str_replace("Hex-STRING: ", "", $value);
                        $value = str_replace("\"", "", $value);   
                        $value = trim($value);
                        $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                    
                        $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                        $DownTime = rtrim($output, ', '); 

                        if($timeDifference->y > 10)
                        {
                            $iface[$key]['Downtime'] = 'Never'; 
                            $iface[$key]['Second_Downtime'] = 'Never';
                        }
                        else
                        {
                            $iface[$key]['Downtime'] = $DownTime; 
                            $iface[$key]['Second_Downtime'] = $Uptime; 
                        }
        
                    } 

                
            } 
            catch (\Exception $e) 
            {$Downtime = '';}
            
            Sleep(1);

            try { 
                    $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.1.".$PonID, TRUE); 
                    foreach ($Control as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['Control'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {$Control = '';}
            
            try {  
                    $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.31.1.5.".$PonID, TRUE);
                    $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                    if($AdminStatus == 1)$AdminStatus = 'up';
                    else if($AdminStatus == 2)$AdminStatus = 'down';    
                } 
            catch (\Exception $e) 
            {$AdminStatus = '';}     

            foreach ($iface as $key => $value) 
            {
                $TotalOnu++;
                $FinalReason;
                $ReadyReason = $iface[$key]['Reason'];
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

                try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.".$PonID.".".$key, TRUE);  
                    $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                    $Signal  = HUAWEI::SginalFixer($Signal);
                    } 
                catch (\Exception $e) 
                {$Signal = '';}
                
                if($iface[$key]['Status'] == 1)$TotalOnline++; 
                else 
                {
                    $TotalOffline++;
                    if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                    if($FinalReason == 'LOS')$TotalWireDown++;
                    else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                    else if($FinalReason == 'LOFI')$TotalWireDown++;

                    if($FinalReason == 'Dying-Gasp')
                    {
                        $CoordOnuStatus = '';
                        if (strpos($iface[$key]['Status'], '1') !== false)
                        {
                            $CoordOnuStatus = 1;
                        }
                        else 
                        {
                            $CoordOnuStatus = 2;
                        }      
                        $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                    

                        $item = [];
                        $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                        $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                        $item['description']    = $iface[$key]['PonList'] ?? '-';
                        $item['type']           = ($iface[$key]['SN_Fixed'] ?? '-').'  '.($iface[$key]['Equipment'] ?? '-');
                        $item['serial']         = $iface[$key]['SN'] ?? '-';
                        $item['control']        = $iface[$key]['Control'] ?? '-';
                        $item['status']         = $iface[$key]['Status'] ?? '-';
                        $item['reason']         = $FinalReason ?? '-';
                        $item['dbm']            = $Signal ?? '-';
                        $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';
                        $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                        $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                        $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';
    
                        $html["port_num_$key"] = $item;  
                    }

                }
            


            }

            
            $html["ponName"]         = $Pon; 
            $html["total"]           = $TotalOnu; 
            $html["total_power_off"] = $TotalPowerOff; 
            $html["total_wire_down"] = $TotalWireDown; 
            $html["TotalOffline"]    = $TotalOffline; 
            $html["TotalOnline"]     = $TotalOnline; 
            $html["AdminStatus"]     = $AdminStatus; 
            $html['PONcoordinates']  = $PonCoordinates;    
        }
        else
        {
            return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }


        return $html;   
    }

    static public function ClientSidePonAllWireDown($ip,$PonID,$read,$write) 
    {
            $html = [];
            $PonCoordinates = [];
            $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
            $Pon  = HUAWEI::GPON_EPON_PORT($PonID); 
        
            $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline= 0;

            $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($PonID);
            if (strpos($OltType, 'GPON') !== false)
            {
                try { $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$PonID, TRUE);
                        foreach ($PonList as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['PonList'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {
                    if (strpos($e->getMessage(), 'No response') !== false) 
                    {
                        return response()->json(['error' => $snmp->getError()]);
                    }
                    else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                    {
                        return response()->json(['error' => 'Pon is empty']);
                    }
                    else 
                    {
                        return response()->json(['error' => $e->getMessage()]);
                    }
                }
                
                try { 
                        $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3."  .$PonID, TRUE); 
                        foreach ($SN as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value      = str_replace("Hex-STRING: ", "", $value);
                            $value      = str_replace("STRING: ", "", $value);
                            $value      = str_replace("\"", "", $value);   
                            $value      = trim(str_replace(" ", "", $value));
                            if(strlen($value) < 15 )
                            {
                                $value = strtoupper(bin2hex($value)); 
                            }

                            $SN_Fixed   = substr($value, 0, 8);
                            $SN_Fixed   = hex2bin($SN_Fixed);        
                            
                            $iface[$key]['SN']       = $value;
                            $iface[$key]['SN_Fixed'] = $SN_Fixed;
                        }
                    } 
                catch (\Exception $e) 
                {$SN = '';}

                try { 
                        $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$PonID, TRUE);
                        foreach ($Status as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Status'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Status = '';}

                try { 
                        $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24.".$PonID, TRUE);
                        foreach ($Reason as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Reason'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Reason = '';}

                try {
                    $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.22.".$PonID, TRUE); 
                        foreach ($Uptime as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = str_replace("Hex-STRING: ", "", $value);
                            $value = str_replace("\"", "", $value);   
                            $value = trim($value);
                            $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                        
                            $DownTime = rtrim($output, ', '); 
            
                            $iface[$key]['Uptime'] = $DownTime; 
                            $iface[$key]['Second_Uptime'] = $Uptime; 
                        } 
                } 
                catch (\Exception $e) 
                {$Uptime = '';}

                try { 
                    
                        $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.23.".$PonID, TRUE); 
                        foreach ($Downtime as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = str_replace("Hex-STRING: ", "", $value);
                            $value = str_replace("\"", "", $value);   
                            $value = trim($value);
                            $Uptime = HUAWEI::secondsToNormalTime($value);
            
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
                                        
                            $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                            $DownTime = rtrim($output, ', '); 
            
                            if($timeDifference->y > 10)
                            {
                                $iface[$key]['Downtime'] = 'Never'; 
                                $iface[$key]['Second_Downtime'] = 'Never';
                            }
                            else
                            {
                                $iface[$key]['Downtime'] = $DownTime; 
                                $iface[$key]['Second_Downtime'] = $Uptime; 
                            }
            
                        } 
            
                    
                } 
                catch (\Exception $e) 
                {$Downtime = '';}

                try { 
                        $Equipment = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.45.1.4." .$PonID, TRUE); 
                        foreach ($Equipment as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Equipment'] = $value;
                        }          
                    
                } 
                catch (\Exception $e) 
                {$Equipment = '';}

                sleep(1);

                try { 
                        $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.1.".$PonID, TRUE); 
                        foreach ($Control as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Control'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Control = '';}
                
        
                    foreach ($iface as $key => $value) 
                    {
                        $TotalOnu++;
                        $FinalReason;
                        $ReadyReason = $iface[$key]['Reason'];
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

                        try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$PonID.".".$key, TRUE);  
                            $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                            $Signal  = HUAWEI::SginalFixer($Signal);
                            } 
                        catch (\Exception $e) 
                        {$Signal = '';}
                        
                        if($iface[$key]['Status'] == 1)$TotalOnline++; 
                        else 
                        {
                            $TotalOffline++;
                            if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                            if($FinalReason == 'LOS')$TotalWireDown++;
                            else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                            else if($FinalReason == 'LOFI')$TotalWireDown++;

                            if($FinalReason == 'LOS' || $FinalReason == 'LOSi/LOBi' || $FinalReason == 'LOFI')
                            {
                                $CoordOnuStatus = '';
                                if (strpos($iface[$key]['Status'], '1') !== false)
                                {
                                    $CoordOnuStatus = 1;
                                }
                                else 
                                {
                                    $CoordOnuStatus = 2;
                                }      
                                $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                            

                                $item = [];
                                $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                                $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                                $item['description']    = $iface[$key]['PonList'] ?? '-';
                                $item['type']           = ($iface[$key]['SN_Fixed'] ?? '-').'  '.($iface[$key]['Equipment'] ?? '-');
                                $item['serial']         = $iface[$key]['SN'] ?? '-';
                                $item['control']        = $iface[$key]['Control'] ?? '-';
                                $item['status']         = $iface[$key]['Status'] ?? '-';
                                $item['reason']         = $FinalReason ?? '-';
                                $item['dbm']            = $Signal ?? '-';
                                $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';
                                $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                                $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                                $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';
            
                                $html["port_num_$key"] = $item;  
                            }
        
                        }
                    
        
        
                    }

                try {  
                        $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.21.1.10.".$PonID, TRUE);
                        $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                        if($AdminStatus == 1)$AdminStatus = 'up';
                        else if($AdminStatus == 2)$AdminStatus = 'down';    
                    } 
                catch (\Exception $e) 
                {$AdminStatus = '';}


                

                $html["ponName"]         = $Pon; 
                $html["total"]           = $TotalOnu; 
                $html["total_power_off"] = $TotalPowerOff; 
                $html["total_wire_down"] = $TotalWireDown; 
                $html["TotalOffline"]    = $TotalOffline; 
                $html["TotalOnline"]     = $TotalOnline; 
                $html["AdminStatus"]     = $AdminStatus; 
                $html['PONcoordinates']  = $PonCoordinates;
            }
            else if (strpos($OltType, 'EPON') !== false)
            {
                try { 
                        $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$PonID, TRUE);
                        foreach ($PonList as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['PonList'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {
                    if (strpos($e->getMessage(), 'No response') !== false) 
                    {
                        return response()->json(['error' => $snmp->getError()]);
                    }
                    else if (strpos($e->getMessage(), 'No Such Instance currently exists at this OID') !== false) 
                    {
                        return response()->json(['error' => 'Pon is empty']);
                    }
                    else 
                    {
                        return response()->json(['error' => $e->getMessage()]);
                    }
                }

                try { 
                        $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3."  .$PonID, TRUE); 
                        foreach ($SN as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value      = str_replace("Hex-STRING: ", "", $value);
                            $value      = str_replace("STRING: ", "", $value);
                            $value      = trim(str_replace("\"", "", $value));   
                            $value      = trim(str_replace(" ", ":", $value));
                            if(strlen($value) < 10 )
                            {
                                $value = strtoupper(bin2hex($value)); 
                            }
                                
                            $iface[$key]['SN']       = $value;
                        }
                    } 
                catch (\Exception $e) 
                {$SN = '';}

                try { 
                        $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$PonID, TRUE);
                        foreach ($Status as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Status'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Status = '';}

                try { 
                        $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25.".$PonID, TRUE);
                        foreach ($Reason as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Reason'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Reason = '';}

                try {
                    $Uptime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.23.".$PonID, TRUE); 
                        foreach ($Uptime as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = str_replace("Hex-STRING: ", "", $value);
                            $value = str_replace("\"", "", $value);   
                            $value = trim($value);
                            $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                        
                            $DownTime = rtrim($output, ', '); 

                            $iface[$key]['Uptime'] = $DownTime; 
                            $iface[$key]['Second_Uptime'] = $Uptime; 
                        } 
                } 
                catch (\Exception $e) 
                {$Uptime = '';}

                try { 
                    
                        $Downtime = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.24.".$PonID, TRUE); 
                        foreach ($Downtime as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = str_replace("Hex-STRING: ", "", $value);
                            $value = str_replace("\"", "", $value);   
                            $value = trim($value);
                            $Uptime = HUAWEI::secondsToNormalTime($value);

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
                                        
                            $output .= $timeDifference->d . ' d, ' . $timeDifference->h . ' h, ' . $timeDifference->i . ' min';      
                            $DownTime = rtrim($output, ', '); 

                            if($timeDifference->y > 10)
                            {
                                $iface[$key]['Downtime'] = 'Never'; 
                                $iface[$key]['Second_Downtime'] = 'Never';
                            }
                            else
                            {
                                $iface[$key]['Downtime'] = $DownTime; 
                                $iface[$key]['Second_Downtime'] = $Uptime; 
                            }
            
                        } 

                    
                } 
                catch (\Exception $e) 
                {$Downtime = '';}
                
                Sleep(1);

                try { 
                        $Control     = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.1.".$PonID, TRUE); 
                        foreach ($Control as $key => $value)
                        {
                            $iface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $iface[$key]['Control'] = $value;
                        }
                } 
                catch (\Exception $e) 
                {$Control = '';}
                
                try {  
                        $AdminStatus  = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.31.1.5.".$PonID, TRUE);
                        $AdminStatus  = trim(str_replace("INTEGER: ", "", $AdminStatus));
                        if($AdminStatus == 1)$AdminStatus = 'up';
                        else if($AdminStatus == 2)$AdminStatus = 'down';    
                    } 
                catch (\Exception $e) 
                {$AdminStatus = '';}     

                foreach ($iface as $key => $value) 
                {
                    $TotalOnu++;
                    $FinalReason;
                    $ReadyReason = $iface[$key]['Reason'];
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

                    try { $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.".$PonID.".".$key, TRUE);  
                        $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                        $Signal  = HUAWEI::SginalFixer($Signal);
                        } 
                    catch (\Exception $e) 
                    {$Signal = '';}
                    
                    if($iface[$key]['Status'] == 1)$TotalOnline++; 
                    else 
                    {
                        $TotalOffline++;
                        if($FinalReason == 'Dying-Gasp')$TotalPowerOff++;
                        if($FinalReason == 'LOS')$TotalWireDown++;
                        else if($FinalReason == 'LOSi/LOBi')$TotalWireDown++;
                        else if($FinalReason == 'LOFI')$TotalWireDown++;

                        if($FinalReason == 'LOS' || $FinalReason == 'LOSi/LOBi' || $FinalReason == 'LOFI')
                        {
                            $CoordOnuStatus = '';
                            if (strpos($iface[$key]['Status'], '1') !== false)
                            {
                                $CoordOnuStatus = 1;
                            }
                            else 
                            {
                                $CoordOnuStatus = 2;
                            }      
                            $PonCoordinates[] = $iface[$key]['PonList'].'|'.$Pon.':'.($iface[$key]['IfId'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Uptime'] ?? '-').'|'.str_replace(',',' ',$iface[$key]['Downtime'] ?? '-').'|'.$CoordOnuStatus;
                        

                            $item = [];
                            $item['ifindex']        = $PonID.'.'.($iface[$key]['IfId'] ?? '-');
                            $item['portIndex']      = $Pon.':'.($iface[$key]['IfId'] ?? '-');
                            $item['description']    = $iface[$key]['PonList'] ?? '-';
                            $item['type']           = ($iface[$key]['SN_Fixed'] ?? '-').'  '.($iface[$key]['Equipment'] ?? '-');
                            $item['serial']         = $iface[$key]['SN'] ?? '-';
                            $item['control']        = $iface[$key]['Control'] ?? '-';
                            $item['status']         = $iface[$key]['Status'] ?? '-';
                            $item['reason']         = $FinalReason ?? '-';
                            $item['dbm']            = $Signal ?? '-';
                            $item['Downtime']       = $iface[$key]['Downtime'] ?? '-';
                            $item['Uptime']         = $iface[$key]['Uptime'] ?? '-';
                            $item['TittleDowntime'] = $iface[$key]['Second_Downtime'] ?? '-';
                            $item['TittleUptime']   = $iface[$key]['Second_Uptime'] ?? '-';
        
                            $html["port_num_$key"] = $item;  
                        }
    
                    }
                
    
    
                }

                $html["ponName"]         = $Pon; 
                $html["total"]           = $TotalOnu; 
                $html["total_power_off"] = $TotalPowerOff; 
                $html["total_wire_down"] = $TotalWireDown; 
                $html["TotalOffline"]    = $TotalOffline; 
                $html["TotalOnline"]     = $TotalOnline; 
                $html["AdminStatus"]     = $AdminStatus; 
                $html['PONcoordinates']  = $PonCoordinates;
            }
            else
            {
                return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
            }


        return $html;   
    }

    static public function OnuRestart($ip,$read,$write,$ifIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        
        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  


        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($ifIndex); 
        if (strpos($OltType, 'GPON') !== false)
        {
            try {$snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.46.1.2.'.$ifIndex, 'i', 1); } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp_RW->getError()]);
            }
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try {$snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.57.1.2.'.$ifIndex, 'i', 1); } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp_RW->getError()]);
            }
        }
        else 
        {
           return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }

        return true;
    }
     
    static public function huawei_Onu_PortAdminStatus_OFF($ip,$read,$write,$ifindex,$portIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
 
        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($ifindex); 
        if (strpos($OltType, 'GPON') !== false)
        {
            try {$snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.62.1.5.'.$ifindex.'.'.$portIndex, 'i', '2');} 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp_RW->getError()]);
            }
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try {$snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.81.1.7.'.$ifindex.'.'.$portIndex, 'i', '2');} 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp_RW->getError()]);
            }
        }
        else 
        {
           return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }


        return true;
    }
    
    static public function huawei_Onu_PortAdminStatus_ON($ip,$read,$write,$ifindex,$portIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($ifindex); 
        if (strpos($OltType, 'GPON') !== false)
        {
            try {$snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.62.1.5.'.$ifindex.'.'.$portIndex, 'i', '1');} 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp_RW->getError()]);
            }
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try {$snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.81.1.7.'.$ifindex.'.'.$portIndex, 'i', '1');} 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp_RW->getError()]);
            }
        }
        else 
        {
           return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }

        return true;
    }

    static public function Pon_Port($value)
    {
        
       $Data = [
           [4194304000, 'GPON 0/0/0'],
           [4194304256, 'GPON 0/0/1'],
           [4194304512, 'GPON 0/0/2'],
           [4194304768, 'GPON 0/0/3'],
           [4194305024, 'GPON 0/0/4'],
           [4194305280, 'GPON 0/0/5'],
           [4194305536, 'GPON 0/0/6'],
           [4194305792, 'GPON 0/0/7'],
           [4194306048, 'GPON 0/0/8'],
           [4194306304, 'GPON 0/0/9'],
           [4194306560, 'GPON 0/0/10'],
           [4194306816, 'GPON 0/0/11'],
           [4194307072, 'GPON 0/0/12'],
           [4194307328, 'GPON 0/0/13'],
           [4194307584, 'GPON 0/0/14'],
           [4194307840, 'GPON 0/0/15'],
           [4194312192, 'GPON 0/1/0'],
           [4194312448, 'GPON 0/1/1'],
           [4194312704, 'GPON 0/1/2'],
           [4194312960, 'GPON 0/1/3'],
           [4194313216, 'GPON 0/1/4'],
           [4194313472, 'GPON 0/1/5'],
           [4194313728, 'GPON 0/1/6'],
           [4194313984, 'GPON 0/1/7'],
           [4194314240, 'GPON 0/1/8'],
           [4194314496, 'GPON 0/1/9'],
           [4194314752, 'GPON 0/1/10'],
           [4194315008, 'GPON 0/1/11'],
           [4194315264, 'GPON 0/1/12'],
           [4194315520, 'GPON 0/1/13'],
           [4194315776, 'GPON 0/1/14'],
           [4194316032, 'GPON 0/1/15'],
           [4194320384, 'GPON 0/2/0'],
           [4194320640, 'GPON 0/2/1'],
           [4194320896, 'GPON 0/2/2'],
           [4194321152, 'GPON 0/2/3'],
           [4194321408, 'GPON 0/2/4'],
           [4194321664, 'GPON 0/2/5'],
           [4194321920, 'GPON 0/2/6'],
           [4194322176, 'GPON 0/2/7'],
           [4194322432, 'GPON 0/2/8'],
           [4194322688, 'GPON 0/2/9'],
           [4194322944, 'GPON 0/2/10'],
           [4194323200, 'GPON 0/2/11'],
           [4194323456, 'GPON 0/2/12'],
           [4194323712, 'GPON 0/2/13'],
           [4194323968, 'GPON 0/2/14'],
           [4194324224, 'GPON 0/2/15'],
           [4194328576, 'GPON 0/3/0'],
           [4194328832, 'GPON 0/3/1'],
           [4194329088, 'GPON 0/3/2'],
           [4194329344, 'GPON 0/3/3'],
           [4194329600, 'GPON 0/3/4'],
           [4194329856, 'GPON 0/3/5'],
           [4194330112, 'GPON 0/3/6'],
           [4194330368, 'GPON 0/3/7'],
           [4194330624, 'GPON 0/3/8'],
           [4194330880, 'GPON 0/3/9'],
           [4194331136, 'GPON 0/3/10'],
           [4194331392, 'GPON 0/3/11'],
           [4194331648, 'GPON 0/3/12'],
           [4194331904, 'GPON 0/3/13'],
           [4194332160, 'GPON 0/3/14'],
           [4194332416, 'GPON 0/3/15'],
           [4194336768, 'GPON 0/4/0'],
           [4194337024, 'GPON 0/4/1'],
           [4194337280, 'GPON 0/4/2'],
           [4194337536, 'GPON 0/4/3'],
           [4194337792, 'GPON 0/4/4'],
           [4194338048, 'GPON 0/4/5'],
           [4194338304, 'GPON 0/4/6'],
           [4194338560, 'GPON 0/4/7'],
           [4194338816, 'GPON 0/4/8'],
           [4194339072, 'GPON 0/4/9'],
           [4194339328, 'GPON 0/4/10'],
           [4194339584, 'GPON 0/4/11'],
           [4194339840, 'GPON 0/4/12'],
           [4194340096, 'GPON 0/4/13'],
           [4194340352, 'GPON 0/4/14'],
           [4194340608, 'GPON 0/4/15'],
           [4194344960, 'GPON 0/5/0'],
           [4194345216, 'GPON 0/5/1'],
           [4194345472, 'GPON 0/5/2'],
           [4194345728, 'GPON 0/5/3'],
           [4194345984, 'GPON 0/5/4'],
           [4194346240, 'GPON 0/5/5'],
           [4194346496, 'GPON 0/5/6'],
           [4194346752, 'GPON 0/5/7'],
           [4194347008, 'GPON 0/5/8'],
           [4194347264, 'GPON 0/5/9'],
           [4194347520, 'GPON 0/5/10'],
           [4194347776, 'GPON 0/5/11'],
           [4194348032, 'GPON 0/5/12'],
           [4194348288, 'GPON 0/5/13'],
           [4194348544, 'GPON 0/5/14'],
           [4194348800, 'GPON 0/5/15'],
           [4194353152, 'GPON 0/6/0'],
           [4194353408, 'GPON 0/6/1'],
           [4194353664, 'GPON 0/6/2'],
           [4194353920, 'GPON 0/6/3'],
           [4194354176, 'GPON 0/6/4'],
           [4194354432, 'GPON 0/6/5'],
           [4194354688, 'GPON 0/6/6'],
           [4194354944, 'GPON 0/6/7'],
           [4194355200, 'GPON 0/6/8'],
           [4194355456, 'GPON 0/6/9'],
           [4194355712, 'GPON 0/6/10'],
           [4194355968, 'GPON 0/6/11'],
           [4194356224, 'GPON 0/6/12'],
           [4194356480, 'GPON 0/6/13'],
           [4194356736, 'GPON 0/6/14'],
           [4194356992, 'GPON 0/6/15']
           
       ];
    
       foreach ($Data as $item) 
       {
           if ($value == $item[0]) 
           {
               return $item[1];          
           }
       }

    }
    
    static public function GPON_EPON_PORT($ifIndex)
    {
        $board_type = ( $ifIndex & bindec('11111110000000000000000000000000') ) >> 25 ;
        switch($board_type) 
        {
            case "126":  //EPON
                $port_type="EPON ";
                $shelf_no       = ( $ifIndex & bindec('00000001111110000000000000000000') ) >> 19 ;
                $slot_no        = ( $ifIndex & bindec('00000000000001111110000000000000') ) >> 13 ;
                $port_no        = ( $ifIndex & bindec('00000000000000000001111100000000') ) >> 8  ;

                return $port_type.$shelf_no.'/'.$slot_no.'/'.$port_no;
            case "125":  //GPON
                $port_type="GPON ";
                $shelf_no       = ( $ifIndex & bindec('00000001111110000000000000000000') ) >> 19 ;
                $slot_no        = ( $ifIndex & bindec('00000000000001111110000000000000') ) >> 13 ;
                $port_no        = ( $ifIndex & bindec('00000000000000000001111100000000') ) >> 8  ;
                
                return $port_type.$shelf_no.'/'.$slot_no.'/'.$port_no;
        }
    }

    static public function SginalFixer($value)
    {
        $integerValue = (float) $value / 100;
        $decimalValue = number_format($integerValue, 2);

        $position = strpos($decimalValue, ',');
        if ($position !== false)
        {
            return '-';
        }
        return $decimalValue;
    }

    static public function MacFind_SNMP($line)
    {

        $macAddres   = HUAWEI::extractMacAddress($line);
        $Converted   = HUAWEI::format_mac_address($macAddres);
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

    static public function secondsToNormalTime($time) 
    {                  
    
        $hexArray = explode(' ', $time);
        $Year  = HUAWEI::hexToDecimal($hexArray[0].$hexArray[1]);
        $Month = HUAWEI::hexToDecimal($hexArray[2]);
        $Day   = HUAWEI::hexToDecimal($hexArray[3]);

        $Hour = HUAWEI::hexToDecimal($hexArray[4]);
        $Min  = HUAWEI::hexToDecimal($hexArray[5]);
        $Sec  = HUAWEI::hexToDecimal($hexArray[6]);
 
        $date = new \DateTime("$Year-$Month-$Day $Hour:$Min:$Sec");
        $monthName = $date->format('F');
        $Fixed_Downtime = $Year.'-'.$Month.'-'.$Day.' '.$Hour.':'.$Min.':'.$Sec;


        return $Fixed_Downtime;
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

    static public function generateRandomHexString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }

}
