<?php

namespace App\Models\Install;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\sshModel;
use App\Models\HUAWEI;
use App\Models\PrivilegesModel;
use Illuminate\Support\Facades\DB; 


use Illuminate\Support\Facades\Storage;
use DOMDocument;
use DOMXPath;

class _huawei extends Model
{
    use HasFactory;


    static public function HUAWEI_SEARCH($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp,$macSN)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        $html = [];
        $EponMac = $macSN;

        $ServerName = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
        
        try{
              $Unregistered = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.48.1.2", TRUE);    

        }catch (\Exception $e)
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
 
        try{
                foreach ($Unregistered as $key => $Serial) 
                {
                    $MacOnu = str_replace("Hex-STRING: ","",$Serial);
                    $MacOnu = str_replace("STRING: ","",$MacOnu);
                    $MacOnu = str_replace("\"","",$MacOnu);
                    $MacOnu = str_replace("\'","",$MacOnu);
                    $MacOnu = trim(str_replace(" ","",$MacOnu));
                    $MacOnu = str_replace('\'',"",$MacOnu);
            
                    
                    if(strlen($MacOnu) < 15 )
                    {
                        $MacOnu = strtoupper(bin2hex($MacOnu));    
                    }
            
                    $macSN = str_replace(":", "",$macSN); 
                    $macSN = str_replace(".", "",$macSN);   
                    $macSN = str_replace("-", "",$macSN);  
                    $macSN = str_replace(" ", "",$macSN);  
                    $macSN = str_replace("\"", "",$macSN); 
                    $macSN = strtoupper($macSN);
            
                    $MacOnu = str_replace(":", "",$MacOnu); 
                    $MacOnu = str_replace(".", "",$MacOnu);   
                    $MacOnu = str_replace("-", "",$MacOnu);  
                    $MacOnu = str_replace(" ", "",$MacOnu);  
                    $MacOnu = str_replace("\"", "",$MacOnu); 
                    $MacOnu = strtoupper($MacOnu);

                    if (strlen($macSN) <= 5)
                    {
                        if (strpos(substr(trim($MacOnu), -4), substr(trim($macSN), -4)) !== false) 
                        {
                            $SN_Fixed   = substr($MacOnu, 0, 8);
                            $SN_Fixed   = hex2bin($SN_Fixed);
        
                            $Pon_Port = explode('.',$key);   
                            $Pon      = self::Pon_Port($Pon_Port[0]);


                            $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                            $ServerName = trim(str_replace("\"", "" , $ServerName));
                            $ServerName = trim(str_replace("\'", "" , $ServerName));


                            $Uptime = '-';
                            $Time = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.48.1.8.".$key, TRUE);
                            foreach ($Time as $keyZ => $value) 
                            {
                                $value = trim(str_replace('Hex-STRING: ','',$value)); 
                                
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
                                            
                                $Uptime = rtrim($output, ', ');   
                            }
            
                            $item = [];
                            $item['ifindex']    = $Pon_Port[0];
                            $item['pon']        = $Pon;                  
                            $item['Serial']     = $SN_Fixed.substr($MacOnu, 8, 16);
                            $item['FullSn']     = $MacOnu;
                            $item['OnyType']    = $SN_Fixed;
                            $item['Uptime']     = $Uptime;
                            $html['OnuList_'.$key] = $item;

                            
                            $html['address']    = $ip;
                            $html['Worker']     = $Workerusername;
                            $html['userIp']     = $userIp;
                            $html['sshUser']    = $sshUser;
                            $html['sshPass']    = $sshPass;
                            $html['ServerName'] = $ServerName;                    
                            $html['type']       = 'HUAWEI';  
                        }
                    }
                    else if (strpos(trim($MacOnu), trim($macSN)) !== false)
                    {

                        $SN_Fixed   = substr($MacOnu, 0, 8);
                        $SN_Fixed   = hex2bin($SN_Fixed);

                        $Pon_Port = explode('.',$key);   
                        $Pon      = self::Pon_Port($Pon_Port[0]);

 
                        $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                        $ServerName = trim(str_replace("\"", "" , $ServerName));
                        $ServerName = trim(str_replace("\'", "" , $ServerName));


                        
                        $Uptime = '-';
                        $Time = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.48.1.8.".$key, TRUE);
                        foreach ($Time as $keyZ => $value) 
                        {
                            $value = trim(str_replace('Hex-STRING: ','',$value)); 
                            
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
                                        
                            $Uptime = rtrim($output, ', ');   
                        }

                        $item['ifindex']    = $Pon_Port[0];
                        $item['pon']        = $Pon;                       
                        $item['Serial']     = $SN_Fixed.substr($MacOnu, 8, 16);
                        $item['FullSn']     = $MacOnu;
                        $item['OnyType']    = $SN_Fixed;   
                        $item['Uptime']     = $Uptime;
                        $html['OnuList_'.$key] = $item;  
                        
                        
                        $html['address']    = $ip;
                        $html['Worker']     = $Workerusername;
                        $html['userIp']     = $userIp;
                        $html['sshUser']    = $sshUser;
                        $html['sshPass']    = $sshPass;
                        $html['ServerName'] = $ServerName;                    
                        $html['type']       = 'HUAWEI';  
                    }
 
                }
        }catch (\Exception $e)
        {}


        try {
            $EponMacs = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.58.1.2", TRUE); 
        }
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
                
  

        try{
                foreach ($EponMacs as $key => $Serial) 
                {
                    $MacOnu = str_replace("Hex-STRING: ","",$Serial);
                    $MacOnu = str_replace("STRING: ","",$MacOnu);
                    $MacOnu = str_replace("\"","",$MacOnu);
                    $MacOnu = str_replace("\'","",$MacOnu);
                    $MacOnu = trim(str_replace(" ","",$MacOnu));
                    $MacOnu = str_replace('\'',"",$MacOnu);
                
                    if(strlen($MacOnu) < 10 )
                    {
                        $MacOnu = strtoupper(bin2hex($MacOnu));    
                    }
            
                    $EponMac = str_replace(":", "",$EponMac); 
                    $EponMac = str_replace(".", "",$EponMac);   
                    $EponMac = str_replace("-", "",$EponMac);  
                    $EponMac = str_replace(" ", "",$EponMac);  
                    $EponMac = str_replace("\"", "",$EponMac); 
                    $EponMac = strtoupper($EponMac);
             
                    $MacOnu = str_replace(":", "",$MacOnu); 
                    $MacOnu = str_replace(".", "",$MacOnu);   
                    $MacOnu = str_replace("-", "",$MacOnu);  
                    $MacOnu = str_replace(" ", "",$MacOnu);  
                    $MacOnu = str_replace("\"", "",$MacOnu); 
                    $MacOnu = strtoupper($MacOnu);
 
                    if (strlen($EponMac) <= 5)
                    {
                        if (strpos(substr(trim($MacOnu), -4), substr(trim($EponMac), -4)) !== false) 
                        {
                            $SN_Fixed   = substr($MacOnu, 0, 8);
                            $SN_Fixed   = hex2bin($SN_Fixed);
        
                            $Pon_Port = explode('.',$key);   
                            $Pon      = self::GPON_EPON_PORT($Pon_Port[0]);


                            $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                            $ServerName = trim(str_replace("\"", "" , $ServerName));
                            $ServerName = trim(str_replace("\'", "" , $ServerName));
     

                            $Uptime = '-';
                            $Time = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.58.1.7.".$key, TRUE);
                            foreach ($Time as $keyZ => $value) 
                            {
                                $value = trim(str_replace('Hex-STRING: ','',$value)); 
                                
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
                                            
                                $Uptime = rtrim($output, ', ');   
                            }
            

                            $item = []; 
                            $item['ifindex']    = $Pon_Port[0];
                            $item['pon']        = $Pon;                       
                            $item['Serial']     = implode(':', str_split($MacOnu, 2)) ?? null; 
                            $item['FullSn']     = implode(':', str_split($MacOnu, 2)) ?? null; 
                            $item['OnyType']    = '-';   
                            $item['Uptime']     = $Uptime;
                            $html['OnuList_'.$key] = $item;  
                            
                    
                            $html['address']    = $ip;
                            $html['Worker']     = $Workerusername;
                            $html['userIp']     = $userIp;
                            $html['sshUser']    = $sshUser;
                            $html['sshPass']    = $sshPass;
                            $html['ServerName'] = $ServerName;                    
                            $html['type']       = 'HUAWEI';  
 
                        }
                    }
                    else if (strpos(trim($MacOnu), trim($EponMac)) !== false)
                    {

                        $SN_Fixed   = substr($MacOnu, 0, 8);
                        $SN_Fixed   = hex2bin($SN_Fixed);

                        $Pon_Port = explode('.',$key);            
                        $Pon      = self::GPON_EPON_PORT($Pon_Port[0]);  

 
                        $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                        $ServerName = trim(str_replace("\"", "" , $ServerName));
                        $ServerName = trim(str_replace("\'", "" , $ServerName));

                        
                        
                        $Uptime = '-';
                        $Time = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.58.1.7.".$key, TRUE);
                        foreach ($Time as $keyZ => $value) 
                        {
                            $value = trim(str_replace('Hex-STRING: ','',$value)); 
                            
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
                                        
                            $Uptime = rtrim($output, ', ');   
                        }
                    
                        $item['ifindex']    = $Pon_Port[0];
                        $item['pon']        = $Pon;                       
                        $item['Serial']     = implode(':', str_split($MacOnu, 2)) ?? null; 
                        $item['FullSn']     = implode(':', str_split($MacOnu, 2)) ?? null; 
                        $item['OnyType']    = '-';   
                        $item['Uptime']     = $Uptime;
                        $html['OnuList_'.$key] = $item;  
                        
                   
                        $html['address']    = $ip;
                        $html['Worker']     = $Workerusername;
                        $html['userIp']     = $userIp;
                        $html['sshUser']    = $sshUser;
                        $html['sshPass']    = $sshPass;
                        $html['ServerName'] = $ServerName;                    
                        $html['type']       = 'HUAWEI';  

                    }
 
                }
        }catch (\Exception $e){}

        return response()->json($html);
    }

    static public function HUAWEI_AUTOFIND($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp)
    {
        PrivilegesModel::PrivCheck('Priv_Install');

        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
  
        try{
              $Unregistered = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.48.1.2", TRUE); 

        }catch (\Exception $e)
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
 
        $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
        $ServerName = trim(str_replace("\"", "" , $ServerName));
        $ServerName = trim(str_replace("\'", "" , $ServerName));

        try{
                foreach ($Unregistered as $key => $Serial) 
                {
                    $MacOnu = str_replace("Hex-STRING: ","",$Serial);
                    $MacOnu = str_replace("STRING: ","",$MacOnu);
                    $MacOnu = str_replace("\"","",$MacOnu);
                    $MacOnu = str_replace("\'","",$MacOnu);
                    $MacOnu = trim(str_replace(" ","",$MacOnu));
                    $MacOnu = str_replace('\'',"",$MacOnu);
            
                    
                    if(strlen($MacOnu) < 15 )
                    {
                        $MacOnu = strtoupper(bin2hex($MacOnu));    
                    }
            
            
                    $MacOnu = str_replace(":", "",$MacOnu); 
                    $MacOnu = str_replace(".", "",$MacOnu);   
                    $MacOnu = str_replace("-", "",$MacOnu);  
                    $MacOnu = str_replace(" ", "",$MacOnu);  
                    $MacOnu = str_replace("\"", "",$MacOnu); 
                    $MacOnu = strtoupper($MacOnu);

                   

                    $SN_Fixed   = substr($MacOnu, 0, 8);
                    $SN_Fixed   = hex2bin($SN_Fixed);

                    $Pon_Port = explode('.',$key);   
                    $Pon      = self::Pon_Port($Pon_Port[0]);


                    $Uptime = '-';
                    $Time = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.48.1.8.".$key, TRUE);
                    foreach ($Time as $keyZ => $value) 
                    {
                        $value = trim(str_replace('Hex-STRING: ','',$value)); 
                        
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
                                    
                        $Uptime = rtrim($output, ', ');   
                    }
 

                        $item = [];
                        $item['ifindex']    = $Pon_Port[0];
                        $item['pon']        = $Pon;                       
                        $item['Serial']     = $SN_Fixed.substr($MacOnu, 8, 16);
                        $item['FullSn']     = $MacOnu;
                        $item['OnyType']    = $SN_Fixed;   
                        $item['Uptime']     = $Uptime;
                        $html['OnuList_'.$key] = $item;             
                }

                    $html['address']    = $ip;
                    $html['Worker']     = $Workerusername;
                    $html['userIp']     = $userIp;
                    $html['sshUser']    = $sshUser;
                    $html['sshPass']    = $sshPass;
                    $html['ServerName'] = $ServerName;                    
                    $html['type']       = 'HUAWEI';  
              
        }catch (\Exception $e)
        {}

 
        try{
            $EponMacs = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.58.1.2", TRUE); 

        }catch (\Exception $e)
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
      
        if(isset($EponMacs))
        {
            try{
                    foreach ($EponMacs as $key => $Serial) 
                    {
                        $MacOnu = str_replace("Hex-STRING: ","",$Serial);
                        $MacOnu = str_replace("STRING: ","",$MacOnu);
                        $MacOnu = str_replace("\"","",$MacOnu);
                        $MacOnu = str_replace("\'","",$MacOnu);
                        $MacOnu = trim(str_replace(" ","",$MacOnu));
                        $MacOnu = str_replace('\'',"",$MacOnu);
                
                        
                        if(strlen($MacOnu) < 10 )
                        {
                            $MacOnu = strtoupper(bin2hex($MacOnu));    
                        }
                
                
                        $MacOnu = str_replace(":", "",$MacOnu); 
                        $MacOnu = str_replace(".", "",$MacOnu);   
                        $MacOnu = str_replace("-", "",$MacOnu);  
                        $MacOnu = str_replace(" ", "",$MacOnu);  
                        $MacOnu = str_replace("\"", "",$MacOnu); 
                        $MacOnu = strtoupper($MacOnu);

                    

                        $SN_Fixed   = substr($MacOnu, 0, 8);
                        $SN_Fixed   = hex2bin($SN_Fixed);

                        $Pon_Port = explode('.',$key);   
                        $Pon      = self::GPON_EPON_PORT($Pon_Port[0]);


                        $Uptime = '-';
                        $Time = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.58.1.7.".$key, TRUE);    
                        foreach ($Time as $keyZ => $value) 
                        {
                            $value = trim(str_replace('Hex-STRING: ','',$value)); 
                            
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
                                        
                            $Uptime = rtrim($output, ', ');      
                        }
    
                        
                            $item = [];
                            $item['ifindex']    = $Pon_Port[0];
                            $item['pon']        = $Pon;                       
                            $item['Serial']     = implode(':', str_split($MacOnu, 2)) ?? null; 
                            $item['FullSn']     = implode(':', str_split($MacOnu, 2)) ?? null; 
                            $item['OnyType']    = '-';   
                            $item['Uptime']     = $Uptime;
                            $html['OnuList_'.$key] = $item;            
                    }

                        $html['address']    = $ip;
                        $html['Worker']     = $Workerusername;
                        $html['userIp']     = $userIp;
                        $html['sshUser']    = $sshUser;
                        $html['sshPass']    = $sshPass;
                        $html['ServerName'] = $ServerName;                    
                        $html['type']       = 'HUAWEI';  
                
            }catch (\Exception $e){}

        }


        return response()->json($html);
    }

    static public function HUAWEI_REAL_INSTALL($ip,$read,$write,$ssHUser,$sshPass,$ifIndex,$user,$pon,$macSN,$LINE,$SERVICE,$MODE,$PortCount)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        Sleep(15);
        $html = [];
        $html ['read']      =  Crypt::encrypt($read);
        $html ['write']     =  Crypt::encrypt($write);

        $TotalOnu  = 0;
 
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
 

        try{
                $TotalOnu = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.21.1.16.".$ifIndex."", TRUE);
                $TotalOnu = str_replace('INTEGER: ','',$TotalOnu);

                $shelf = 0;$slot = 0;$ponZ = 0;
                $UnfixedPon = self::Pon_Port($ifIndex); 
                $UnfixedPon = str_replace('GPON','',$UnfixedPon);
                $UnfixedPon = trim($UnfixedPon);
                $UnfixedPon = explode('/',$UnfixedPon);

                $shelf = $UnfixedPon[0];
                $slot  = $UnfixedPon[1];
                $ponZ  = $UnfixedPon[2];
        
        }catch (\Exception $e)
        {
            //return response()->json(['error' => $snmp->getError()]);
        }

        if($TotalOnu == 128)
        {
            return response()->json(['error' => 'ვერ ხერხდება ინსტალი  '.$pon.'   სავსეა']);   
        }
        else if($TotalOnu == 0)
        {
            //if(isset($shelf) && isset($slot) && isset($ponZ))
            //{
               
                    $snmp_RW->set(array('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.2.'.$ifIndex.'.0', // authentication mode sn(1)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.'.$ifIndex.'.0',  // sn
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.7.'.$ifIndex.'.0',  // line profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.8.'.$ifIndex.'.0',  // service profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.6.'.$ifIndex.'.0',  // management mode omci(1)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$ifIndex.'.0', // createAndGo(4) ,  destroy(6)
                    ),
                    array("i" , 'x' , "s", "s", "i", "i"),
                    array( 1 , $macSN, $LINE,$SERVICE,1 ,1)); 
        

                $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.'.$ifIndex.'.0' , 's', $user); 

                $dataArray = self::HUAWEI_PUT_VLAN_PORTS($ip,$read,$write,$user,$ifIndex,$MODE,$SERVICE,'0',$PortCount);
                self::HUAWEI_CREATE_SERVICE_PORTS($ip,$read,$write,$user,$ifIndex,$LINE,'0',$MODE,$dataArray);
                
                $html ['ifindex'] = $ifIndex.'.0';
                return $html;
            //}
            // else
            // {
            //     return response()->json(['error' => 'მოხდა შეცდომა shelf , slot , pon ის გაგებისას']); 
            // }   
        }
        else if($TotalOnu > 0)
        {  
            $OnuList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$ifIndex, TRUE);
            foreach ($OnuList as $key => $value) 
            {
                $keysArray[] = $key;
            }

            $NextFreeVirtualPort = null;
            for ($i = 0; $i <= 128; $i++) 
            {
                if (!in_array($i, $keysArray)) 
                {
                    $NextFreeVirtualPort = $i; 
                    break;
                }
            }             

            if (isset($NextFreeVirtualPort))
            {
                            
                if(isset($shelf) && isset($slot) && isset($ponZ))
                {
                    $snmp_RW->set(array('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.2.'.$ifIndex.'.'.$NextFreeVirtualPort, // authentication mode sn(1)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.'.$ifIndex.'.'.$NextFreeVirtualPort,  // sn
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.7.'.$ifIndex.'.'.$NextFreeVirtualPort,  // line profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.8.'.$ifIndex.'.'.$NextFreeVirtualPort,  // service profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.6.'.$ifIndex.'.'.$NextFreeVirtualPort,  // management mode omci(1)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$ifIndex.'.'.$NextFreeVirtualPort, // createAndGo(4) ,  destroy(6)
                    ),
                    array("i" , 'x' , "s", "s", "i", "i"),
                    array( 1 , $macSN, $LINE,$SERVICE,1 ,1)); 

                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.'.$ifIndex.'.'.$NextFreeVirtualPort , 's', $user); 

                    $dataArray = self::HUAWEI_PUT_VLAN_PORTS($ip,$read,$write,$user,$ifIndex,$MODE,$SERVICE,$NextFreeVirtualPort,$PortCount);
                    self::HUAWEI_CREATE_SERVICE_PORTS($ip,$read,$write,$user,$ifIndex,$LINE,$NextFreeVirtualPort,$MODE,$dataArray);
                    
                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;
                }
                else
                {
                    return response()->json(['error' => 'მოხდა შეცდომა shelf , slot , pon ის გაგებისას']); 
                }   
                
            }
            else
            {
                return response()->json(['error' => 'მოხდა შეცდომა ვირტუალური პორტის დადგენისას']); 
            }
        }
    }

    static public function HUAWEI_EPON_REAL_INSTALL($ip,$read,$write,$ssHUser,$sshPass,$ifIndex,$user,$pon,$macSN,$LINE,$SERVICE,$MODE,$PortCount,$Iphost)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        Sleep(15);
        $html = [];
        $html ['read']      =  Crypt::encrypt($read);
        $html ['write']     =  Crypt::encrypt($write);

        $TotalOnu  = 0;
 
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);

        try{
                $TotalOnu = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.31.1.12.".$ifIndex."", TRUE);
                $TotalOnu = str_replace('INTEGER: ','',$TotalOnu);

        }catch (\Exception $e)
        {
            //return response()->json(['error' => $snmp->getError()]);
        }
     
        if($TotalOnu == 128)
        {
            return response()->json(['error' => 'ვერ ხერხდება ინსტალი  '.$pon.'   სავსეა']);   
        }
        else if($TotalOnu == 0)
        {
            $macSN = str_replace(':','',$macSN);
            $snmp_RW->set(array('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.2.'.$ifIndex.'.0',  // authentication mode mac(3)  
                                '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3.'.$ifIndex.'.0',  // sn
                                '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.7.'.$ifIndex.'.0',  // line profile
                                '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.8.'.$ifIndex.'.0',  // service profile
                                '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.6.'.$ifIndex.'.0',  // management mode oam(1)  
                                '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.10.'.$ifIndex.'.0', // createAndGo(4) ,  destroy(6)
            ),
            array("i",'x',"s","s","i","i"), 
            array(3,$macSN,$LINE,$SERVICE,1,1)); 

            $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.'.$ifIndex.'.0' , 's', $user); 


            self::HUAWEI_EPON_PUT_VLAN_PORTS($ip,$read,$write,$user,$ifIndex,$MODE,$SERVICE,'0',$PortCount,$Iphost); 
            self::HUAWEI_EPON_CREATE_SERVICE_PORTS($ip,$read,$write,$user,$ifIndex,$SERVICE,0,$MODE,$Iphost);
           
            $html ['ifindex'] = $ifIndex.'.0';
            return $html;

        }
        else if($TotalOnu > 0)
        {
            $OnuList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$ifIndex, TRUE);
            foreach ($OnuList as $key => $value) 
            {
                $keysArray[] = $key;
            }

            $NextFreeVirtualPort = null;
            for ($i = 0; $i <= 128; $i++) 
            {
                if (!in_array($i, $keysArray)) 
                {
                    $NextFreeVirtualPort = $i; 
                    break;
                }
            }             

            if (isset($NextFreeVirtualPort))
            {
                    $macSN = str_replace(':','',$macSN);
                    $snmp_RW->set(array('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.2.'.$ifIndex.'.'.$NextFreeVirtualPort, // authentication mode sn(1)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3.'.$ifIndex.'.'.$NextFreeVirtualPort,  // sn
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.7.'.$ifIndex.'.'.$NextFreeVirtualPort,  // line profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.8.'.$ifIndex.'.'.$NextFreeVirtualPort,  // service profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.6.'.$ifIndex.'.'.$NextFreeVirtualPort,  // management mode omci(1)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.10.'.$ifIndex.'.'.$NextFreeVirtualPort, // createAndGo(4) ,  destroy(6)
                    ),
                    array("i" , 'x' , "s", "s", "i", "i"),
                    array( 3 , $macSN, $LINE,$SERVICE,1 ,1)); 

                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.'.$ifIndex.'.'.$NextFreeVirtualPort , 's', $user); 

                    self::HUAWEI_EPON_PUT_VLAN_PORTS($ip,$read,$write,$user,$ifIndex,$MODE,$SERVICE,$NextFreeVirtualPort,$PortCount,$Iphost);    
                    self::HUAWEI_EPON_CREATE_SERVICE_PORTS($ip,$read,$write,$user,$ifIndex,$SERVICE,$NextFreeVirtualPort,$MODE,$Iphost);

                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;
            }
            else
            {
                return response()->json(['error' => 'მოხდა შეცდომა ვირტუალური პორტის დადგენისას']); 
            }
        }
        
    }
          
    static public function ONT_INFO_BY_IFINDEX($ip,$ifIndex,$read)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        if (strpos(self::GPON_EPON_PORT($ifIndex), 'EPON') !== false) 
        {
            try {
            
                $PonList = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$ifIndex, TRUE);
                $PonList = trim(str_replace('STRING: ','',$PonList));
                $PonList = trim(str_replace("\"",'',$PonList));
    
                $PonID  = explode('.',$ifIndex);
                $Pon    = self::GPON_EPON_PORT($PonID[0]);
                $Port   = $PonID[1];
                
                $html ['ifIndex']      = $ifIndex;
                $html ['ponPort']      = $Pon.':'.$PonID[1];
                $html ['description']  = $PonList;
    
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
            }

            $Signal = '';
            try {
                    $Signal =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.".$ifIndex, TRUE); 
                    $Signal = str_replace('INTEGER: ', '', trim($Signal));
                    $Signal = HUAWEI::SginalFixer($Signal);    
                    $html ['Dbm'] = $Signal;
    
            }catch (\Exception $e){$html ['Dbm'] = '';}    

            try {
                $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25.".$ifIndex, TRUE); 
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

            $html ['onuType'] = '-';

            $xxx = '';
            try {
                        $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$ifIndex, TRUE);
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
        else
        {
            try {
            
                $PonList = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$ifIndex, TRUE);
                $PonList = trim(str_replace('STRING: ','',$PonList));
                $PonList = trim(str_replace("\"",'',$PonList));
    
                $PonID  = explode('.',$ifIndex);
                $Pon    = self::Pon_Port($PonID[0]);
                $Port   = $PonID[1];
                
                $html ['ifIndex']      = $ifIndex;
                $html ['ponPort']      = $Pon.':'.$PonID[1];
                $html ['description']  = $PonList;
    
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
            }
    
            $Signal = '';
            try {
                    $Signal =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$ifIndex, TRUE); 
                    $Signal = str_replace('INTEGER: ', '', trim($Signal));
                    $Signal = HUAWEI::SginalFixer($Signal);    
                    $html ['Dbm'] = $Signal;
    
            }catch (\Exception $e){$html ['Dbm'] = '';}    
    
            try {
                $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24.".$ifIndex, TRUE);
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
                    $SN  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.".$ifIndex, TRUE);   
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
                        $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$ifIndex, TRUE);
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

        return $html;
    }

    static public function ONT_PORT_BY_IFINDEX($ip,$ifIndex,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        

        if (strpos(self::GPON_EPON_PORT($ifIndex), 'EPON') !== false) 
        {
            try {
            
                $PonList = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$ifIndex, TRUE);
                $PonList = trim(str_replace('STRING: ','',$PonList));
                $PonList = trim(str_replace("\"",'',$PonList));
    
                $PonID  = explode('.',$ifIndex);
                $Pon    = self::GPON_EPON_PORT($PonID[0]);
                $Port   = $PonID[1];
                
                $html ['ifIndex']      = $ifIndex;
                $html ['ponPort']      = $Pon.':'.$PonID[1];
                $html ['description']  = $PonList;
    
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
            }

            if(isset($PonList))
            {
                $PortCount   = '';
                $PortDuplex  = '';
                $PortStatus  = '';
                $Speed       = '';
        
                try {$PortCount = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.91.1.3.".$ifIndex, TRUE);}
                catch (\Exception $e){} 

                try {$PortDuplex = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.3.".$ifIndex, TRUE);}
                catch (\Exception $e){} 

                try {$PortStatus = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.31.".$ifIndex, TRUE);}
                catch (\Exception $e){} 

                try {$Speed      = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.4.".$ifIndex, TRUE);}
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
                        try {$Vlan    = trim(str_replace('INTEGER: ','',$snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.5.".$ifIndex.'.'.$key, TRUE))); }
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
                        try {$AdminStatus    = trim(str_replace('INTEGER: ','',$snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.81.1.7.".$ifIndex.'.'.$key, TRUE)));}
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
    
            }
      

        }
        else
        {
            try {
            
                $PonList = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$ifIndex, TRUE);
                $PonList = trim(str_replace('STRING: ','',$PonList));
                $PonList = trim(str_replace("\"",'',$PonList));
    
                $PonID  = explode('.',$ifIndex);
                $Pon    = self::Pon_Port($PonID[0]);
                $Port   = $PonID[1];
                
                $html ['ifIndex']      = $ifIndex;
                $html ['ponPort']      = $Pon.':'.$PonID[1];
                $html ['description']  = $PonList;
    
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
            }
    
            if(isset($PonList))
            {
    
                $PortCount   = '';
                $PortDuplex  = '';
                $PortStatus  = '';
                $Speed       = '';
        
                try {$PortCount  = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.21.".$ifIndex, TRUE);}
                catch (\Exception $e){}    
        
                try {$PortDuplex = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.3.".$ifIndex, TRUE);}
                catch (\Exception $e){} 
        
                try {$PortStatus = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.22.".$ifIndex, TRUE);}
                catch (\Exception $e){} 
        
                try {$Speed      = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.4.".$ifIndex, TRUE);}
                catch (\Exception $e){} 
        
        
                $newArray = [];
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
                        try {$Vlan    = trim(str_replace('INTEGER: ','',$snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.7.".$ifIndex.'.'.$key, TRUE))); }
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
                        try {$AdminStatus    = trim(str_replace('INTEGER: ','',$snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.5.".$ifIndex.'.'.$key, TRUE)));}
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
            }
        }

        return $html;
    }
     
    static public function ONT_MAC_BY_IFINDEX($ip,$ifIndex,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  


        $OltType = self::GPON_EPON_PORT($ifIndex); 
        if (strpos($OltType, 'GPON') !== false)
        {
     
            $PonList = '';
            try {
                
                $PonList = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$ifIndex, TRUE);
                $PonList = trim(str_replace('STRING: ','',$PonList));
                $PonList = trim(str_replace("\"",'',$PonList));
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
            }
    
           
    
            $ServicePort_Pon_Port = $ifIndex; 
            $valueZ = $PonList;
    
            $ServicePort_Pon_Port  = explode('.',$ServicePort_Pon_Port);
            $UnFixed = explode('/',HUAWEI::Pon_Port($ServicePort_Pon_Port[0]));
     
    
            $html ['ifIndex']      = $ifIndex;
            $html ['ponPort']      = HUAWEI::GPON_EPON_PORT($ServicePort_Pon_Port[0]).':'.$ServicePort_Pon_Port[1];
            $html ['description']  = $PonList;
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
                                        $value = trim(str_replace('INTEGER: ','',$value));
                                        $value = trim(str_replace("\"",'',$value));
                                        if($value == 0)
                                        {
                                            $item = [];
                                            $item['servicePort'] = $ab_nomService_nom;
                                            $item['vlan']        = '';    
                                            $item['mac']         = '';  
                                            $item['vendoor']     = ''; 
                                            
                                            $html["port_num_$ab_nomService_nom"] = $item;  
                                            $html ['shutdown'] = 0;
                                            break;
                                        }
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
                        else 
                        {
                            $html ['shutdown'] = 0;
                        }
                    }
                }
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            $PonList = '';
            try {
                
                $PonList = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$ifIndex, TRUE);
                $PonList = trim(str_replace('STRING: ','',$PonList));
                $PonList = trim(str_replace("\"",'',$PonList));
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    return response()->json(['error' => $snmp->getError()]);
                }
            }
    
           
    
            $ServicePort_Pon_Port = $ifIndex; 
            $valueZ = $PonList;
    
            $ServicePort_Pon_Port  = explode('.',$ServicePort_Pon_Port);
            $UnFixed = explode('/',HUAWEI::GPON_EPON_PORT($ServicePort_Pon_Port[0]));
     
    
            $html ['ifIndex']      = $ifIndex;
            $html ['ponPort']      = HUAWEI::GPON_EPON_PORT($ServicePort_Pon_Port[0]).':'.$ServicePort_Pon_Port[1];
            $html ['description']  = $PonList;
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
                                        $value = str_replace("\n",'',$value);
                                        $value = str_replace('\'','',$value);
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
                        else 
                        {
                            $html ['shutdown'] = 0;
                        }

                    }
                }
        }
        else 
        {
           return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }

        return $html;
    }
     
    static public function HUAWEI_PUT_VLAN_PORTS($ip,$read,$write,$user,$ifIndex,$MODE,$serviceProfile,$onuIfindex,$PortCount)
    {  
        PrivilegesModel::PrivCheck('Priv_Install');
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
 
        $dataArray = self:: HUAWEI_SERVICE_PROFILE_READ($ip,$read,$serviceProfile); 
        
 
        if (!self::hasDuplicatesInEth($dataArray))
        { 
            $OnePort = false;

            foreach ($dataArray as $item) 
            {             
                $eth  = trim($item['eth']);
                $vlan = trim($item['vlan']);
                                
                //   BRIGE NO IPHOST , ROUTER only IPHOST , BRIGE+ROUTER  all 
                          
                if($eth == 'iphost' && ($MODE == 'ROUTER' || $MODE == 'BRIDGSTER'))
                {   
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.65.1.2.'.$ifIndex.'.'.$onuIfindex.'.1', 'i', trim($vlan));    
                }
                if($MODE !== 'ROUTER' && $eth !== 'iphost')
                {           
                    if($PortCount == 1 &&  trim($eth) == 1)
                    {
                        $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.62.1.7.'.$ifIndex.'.'.$onuIfindex.'.'.trim($eth), 'i', trim($vlan)); 
                        $OnePort = true; 
                    }         
                    else if($PortCount == 4)
                    {
                        $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.62.1.7.'.$ifIndex.'.'.$onuIfindex.'.'.trim($eth), 'i', trim($vlan));  
                    }    
  
                }    
            }

            // if($PortCount == 1 && $MODE !== 'ROUTER' && $OnePort == false)
            // {
            //     return response()->json(['error' => ''.$serviceProfile.' პროფილში ვერ მოიძებნა eth_1']); 
            // }
            // else
            // {
                return $dataArray;
            //}
            
        }
        else
        {
            return response()->json(['error' => 'დაფიქსირდა დუბლირებული მონაცემები '.$serviceProfile.' პროფილში']); 
        }
    }

    static public function HUAWEI_EPON_PUT_VLAN_PORTS($ip,$read,$write,$user,$ifIndex,$MODE,$serviceProfile,$onuIfindex,$PortCount,$Iphost)
    {  
        PrivilegesModel::PrivCheck('Priv_Install');
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
 
        $OntIndex = self::GPON_EPON_PORT($ifIndex).'  '.$onuIfindex;   
        $OntIndex = str_replace('EPON ','',$OntIndex);
        $OntIndex = explode('/',$OntIndex);

        $commandArray = [];  

        $OntIndex = $OntIndex[0].'/'.$OntIndex[1].' '.$OntIndex[2];
 
        Sleep(60);

        $dataArray = self:: HUAWEI_EPON_SERVICE_PROFILE_READ($ip,$read,$serviceProfile);    
        
        if (!self::hasDuplicatesInEth($dataArray))
        { 

            //   BRIGE NO IPHOST , ROUTER only IPHOST , BRIGE+ROUTER  all 
                          
            if(($MODE == 'ROUTER' || $MODE == 'BRIDGSTER'))
            {            
                self::HuaweiePON_FTP($user,$MODE,$ifIndex,(int)$Iphost,$PortCount); 
                $credentials = DB::table('devices')->where('Address',$ip)->first();
          
                $ftp = DB::table('parameters')->where('type','olt_ftp')->first();
                      
                              
                $commandArray = 
                [
                                  '',
                                  'ena',   
                                  'diagnose',
                                  'ont-load stop',
                                  'ont-load info configuration '.$user.'.xml.gz ftp 10.196.3.97 finderftp NQnWKdAUmZN5I65',
                                  'ont-load select '.$OntIndex,
                                  'ont-load start',
                                  '',
                                  'display ont-load result '.$OntIndex
                ];
                         
                $Response  = sshModel::SSH_EPON_CUSTOM($ip,22,$credentials->Username,$credentials->Pass,$commandArray,true); 
                    
                if(Storage::disk('ftp')->exists($user.'.xml.gz'))
                {
                    Storage::disk('ftp')->delete($user.'.xml.gz');
                }      
            }
            else if ($MODE == 'BRIDGE')
            {
                self::HuaweiePON_FTP($user,$MODE,$ifIndex,(int)$Iphost,$PortCount); 
                $credentials = DB::table('devices')->where('Address',$ip)->first();
          
                $ftp = DB::table('parameters')->where('type','olt_ftp')->first();
                                         
                $commandArray = 
                [
                                  '',
                                  'ena',   
                                  'diagnose',
                                  'ont-load stop',
                                  'ont-load info configuration '.$user.'.xml.gz ftp 10.196.3.97 finderftp NQnWKdAUmZN5I65',
                                  'ont-load select '.$OntIndex,
                                  'ont-load start',
                                  '',
                                  'display ont-load result '.$OntIndex
                ];
                         
                $Response  = sshModel::SSH_EPON_CUSTOM($ip,22,$credentials->Username,$credentials->Pass,$commandArray,true); 
                    
                if(Storage::disk('ftp')->exists($user.'.xml.gz'))
                {
                    Storage::disk('ftp')->delete($user.'.xml.gz');
                }      
            }

            foreach ($dataArray as $item) 
            {             
                $eth  = trim($item['eth']);
                $vlan = trim($item['vlan']);

                if($MODE !== 'ROUTER')
                {           
                    if($PortCount == 1 &&  trim($eth) == 1)
                    {
                        $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.81.1.5.'.$ifIndex.'.'.$onuIfindex.'.'.trim($eth), 'i', trim($vlan)); 
                    }         
                    else if($PortCount == 4)
                    {
                        $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.81.1.5.'.$ifIndex.'.'.$onuIfindex.'.'.trim($eth), 'i', trim($vlan));  
                    }    
                }              
            }

            return $dataArray;
        }
        else
        {
            return response()->json(['error' => 'დაფიქსირდა დუბლირებული მონაცემები '.$serviceProfile.' პროფილში']); 
        }
    }

    static public function HUAWEI_CREATE_SERVICE_PORTS($ip,$read,$write,$user,$ifIndex,$lineProfile,$onuIfindex,$MODE,$serviceProfile)
    { 
        PrivilegesModel::PrivCheck('Priv_Install');
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
 
        $shelf = 0;$slot = 0;$ponZ = 0;
        $UnfixedPon = self::Pon_Port($ifIndex); 
        $UnfixedPon = str_replace('GPON','',$UnfixedPon);
        $UnfixedPon = trim($UnfixedPon);
        $UnfixedPon = explode('/',$UnfixedPon);

        $shelf = $UnfixedPon[0];
        $slot  = $UnfixedPon[1];
        $ponZ  = $UnfixedPon[2];

        if(isset($shelf) && isset($slot) && isset($ponZ) && isset($user))
        {
            $GemVlanArray = self::HUAWEI_LINE_PROFILE_READ($ip,$read,$lineProfile);   
 
            if ($GemVlanArray !== null)
            {
                $iphostVlan = 0;
                foreach ($serviceProfile as $key => $value) 
                {
                   if($value['eth'] == 'iphost')$iphostVlan = $value['vlan'];
                }
 
                foreach ($GemVlanArray as $item) 
                {

                    $Gem  = $item['mapping'];
                    $vlan = $item['Vlan'];
                    
                    try
                    {
                        $snmp->get("1.3.6.1.4.1.2011.5.6.1.1.1.2.".(int)$vlan, TRUE);
                    }
                    catch (\Exception $e) 
                    {
                        return response()->json(['error' =>$lineProfile.' ამ პროფილში აღწერილი '.$vlan.' ვილანი არ არსებობს']); 
                    }
                    
                    if(!empty($Gem) && !empty($vlan))
                    {
                        if($MODE == 'BRIDGE')
                        {
                            if($vlan !== $iphostVlan)
                            {
                                $NextFreeServicePort   =  $snmp->walk("1.3.6.1.4.1.2011.5.14.5.1" , TRUE);
                                foreach ($NextFreeServicePort as $key => $value) 
                                {
                                    $NextFreeServicePort   = trim(str_replace("INTEGER: ", "",$value));
                                }
            
                                $snmp_RW->set(array("1.3.6.1.4.1.2011.5.14.5.2.1.2.".$NextFreeServicePort,  // shelf
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.3.'.$NextFreeServicePort,  // slot
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.4.'.$NextFreeServicePort,  // pon
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.5.'.$NextFreeServicePort,  // ont
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.6.'.$NextFreeServicePort,  // gem
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.7.'.$NextFreeServicePort,  // gpon
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.8.'.$NextFreeServicePort,  // vlan                           
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.11.'.$NextFreeServicePort, // 1
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.12.'.$NextFreeServicePort, // vlan
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.15.'.$NextFreeServicePort, // create sp                                       
                                                    ),
            
                                array("i","i","i","i","i","i","i","i","i","i"),
                                array(trim($shelf) , trim($slot) , trim($ponZ) , $onuIfindex , trim($Gem) , 4 , trim($vlan) , 1 , trim($vlan) , 4 ));  
        
                                $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.17.'.$NextFreeServicePort , 's', $user);  
                            }
                        }
                        else if($MODE == 'ROUTER')
                        {
                            if($vlan == $iphostVlan)
                            {
                                $NextFreeServicePort   =  $snmp->walk("1.3.6.1.4.1.2011.5.14.5.1" , TRUE);
                                foreach ($NextFreeServicePort as $key => $value) 
                                {
                                    $NextFreeServicePort   = trim(str_replace("INTEGER: ", "",$value));
                                }
            
                                $snmp_RW->set(array("1.3.6.1.4.1.2011.5.14.5.2.1.2.".$NextFreeServicePort,  // shelf
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.3.'.$NextFreeServicePort,  // slot
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.4.'.$NextFreeServicePort,  // pon
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.5.'.$NextFreeServicePort,  // ont
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.6.'.$NextFreeServicePort,  // gem
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.7.'.$NextFreeServicePort,  // gpon
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.8.'.$NextFreeServicePort,  // vlan                           
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.11.'.$NextFreeServicePort, // 1
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.12.'.$NextFreeServicePort, // vlan
                                                    '1.3.6.1.4.1.2011.5.14.5.2.1.15.'.$NextFreeServicePort, // create sp                                       
                                                    ),
            
                                array("i","i","i","i","i","i","i","i","i","i"),
                                array(trim($shelf) , trim($slot) , trim($ponZ) , $onuIfindex , trim($Gem) , 4 , trim($vlan) , 1 , trim($vlan) , 4 ));  
        
                                $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.17.'.$NextFreeServicePort , 's', $user);  
                            }
                        }
                        else 
                        {
                            $NextFreeServicePort   =  $snmp->walk("1.3.6.1.4.1.2011.5.14.5.1" , TRUE);
                            foreach ($NextFreeServicePort as $key => $value) 
                            {
                                $NextFreeServicePort   = trim(str_replace("INTEGER: ", "",$value));
                            }
        
                            $snmp_RW->set(array("1.3.6.1.4.1.2011.5.14.5.2.1.2.".$NextFreeServicePort,  // shelf
                                                '1.3.6.1.4.1.2011.5.14.5.2.1.3.'.$NextFreeServicePort,  // slot
                                                '1.3.6.1.4.1.2011.5.14.5.2.1.4.'.$NextFreeServicePort,  // pon
                                                '1.3.6.1.4.1.2011.5.14.5.2.1.5.'.$NextFreeServicePort,  // ont
                                                '1.3.6.1.4.1.2011.5.14.5.2.1.6.'.$NextFreeServicePort,  // gem
                                                '1.3.6.1.4.1.2011.5.14.5.2.1.7.'.$NextFreeServicePort,  // gpon
                                                '1.3.6.1.4.1.2011.5.14.5.2.1.8.'.$NextFreeServicePort,  // vlan                           
                                                '1.3.6.1.4.1.2011.5.14.5.2.1.11.'.$NextFreeServicePort, // 1
                                                '1.3.6.1.4.1.2011.5.14.5.2.1.12.'.$NextFreeServicePort, // vlan
                                                '1.3.6.1.4.1.2011.5.14.5.2.1.15.'.$NextFreeServicePort, // create sp                                       
                                                ),
        
                            array("i","i","i","i","i","i","i","i","i","i"),
                            array(trim($shelf) , trim($slot) , trim($ponZ) , $onuIfindex , trim($Gem) , 4 , trim($vlan) , 1 , trim($vlan) , 4 ));  
    
                            $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.17.'.$NextFreeServicePort , 's', $user);      
                        }

                    }
                    else
                    {
                        return response()->json(['error' => "გემ პორტი ან ვილანი ვერ მოპიძებნა : ".$Gem.'  '.$vlan]); 
                    }               
                }

            }
            else
            {
                return response()->json(['error' => 'მოხდა შეცდომა LINE PROFILE წაკითხვისას']); 
            }   
        }
        else
        {
            return response()->json(['error' => 'მოხდა შეცდომა shelf , slot , pon ის გაგებისას']); 
        }
    }

    static public function HUAWEI_EPON_CREATE_SERVICE_PORTS($ip,$read,$write,$user,$ifIndex,$ServiceProfile,$onuIfindex,$MODE,$Iphost)
    { 
        PrivilegesModel::PrivCheck('Priv_Install');
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
 
        $shelf = 0;$slot = 0;$ponZ = 0;
        $UnfixedPon = self::GPON_EPON_PORT($ifIndex); 
        $UnfixedPon = str_replace('EPON','',$UnfixedPon);
        $UnfixedPon = trim($UnfixedPon);
        $UnfixedPon = explode('/',$UnfixedPon);

        $shelf = (int)$UnfixedPon[0];
        $slot  = (int)$UnfixedPon[1];
        $ponZ  = (int)$UnfixedPon[2];

        if(isset($shelf) && isset($slot) && isset($ponZ) && isset($user))
        {
            $VlanArray = self::HUAWEI_EPON_SERVICE_PROFILE_READ_FOR_VLANS_CUSTOM($ip,$read,$ServiceProfile);
    
            if ($VlanArray !== null)
            {

                if($MODE !== 'ROUTER')
                {
                    if($MODE == 'BRIDGE')
                    {
                        foreach ($VlanArray as $vlan) 
                        { 
                            
                                        try
                                        {
                                            $snmp->get("1.3.6.1.4.1.2011.5.6.1.1.1.2.".(int)$vlan, TRUE);
                                        }
                                        catch (\Exception $e) 
                                        {
                                            return response()->json(['error' =>$ServiceProfile.' ამ პროფილში აღწერილი '.$vlan.' ვილანი არ არსებობს']); 
                                        }
        
        
                                        $NextFreeServicePort   =  $snmp->walk("1.3.6.1.4.1.2011.5.14.5.1" , TRUE);
                                        foreach ($NextFreeServicePort as $key => $value) 
                                        {
                                            $NextFreeServicePort   = trim(str_replace("INTEGER: ", "",$value));
                                        }
                
                                        try
                                        {
                                            $snmp_RW->set(array("1.3.6.1.4.1.2011.5.14.5.2.1.2.".$NextFreeServicePort,  // shelf
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.3.'.$NextFreeServicePort,  // slot
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.4.'.$NextFreeServicePort,  // pon
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.5.'.$NextFreeServicePort,  // ont
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.7.'.$NextFreeServicePort,  // epon
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.8.'.$NextFreeServicePort,  // vlan                           
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.11.'.$NextFreeServicePort, // 1
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.12.'.$NextFreeServicePort, // vlan
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.15.'.$NextFreeServicePort, // create sp                                       
                                                                ),
                        
                                            array("i","i","i","i","i","i","i","i","i"),
                                            array(trim($shelf) , trim($slot) , trim($ponZ) , (int)$onuIfindex  , 6 , (int)$vlan , 1 , (int)$vlan , 4 ));  
                    
                                            $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.17.'.(int)$NextFreeServicePort , 's', $user); 
                                        }
                                        catch (\Exception $e) 
                                        {
                                            return response()->json(['error' => $snmp->getError()]); 
                                        } 
                        }
                    }
                    else if ($MODE == 'BRIDGSTER')
                    {
                        foreach ($VlanArray as $vlan) 
                        { 
                            
                                        try
                                        {
                                            $snmp->get("1.3.6.1.4.1.2011.5.6.1.1.1.2.".(int)$vlan, TRUE);
                                        }
                                        catch (\Exception $e) 
                                        {
                                            return response()->json(['error' =>$ServiceProfile.' ამ პროფილში აღწერილი '.$vlan.' ვილანი არ არსებობს']); 
                                        }
        
        
                                        $NextFreeServicePort   =  $snmp->walk("1.3.6.1.4.1.2011.5.14.5.1" , TRUE);
                                        foreach ($NextFreeServicePort as $key => $value) 
                                        {
                                            $NextFreeServicePort   = trim(str_replace("INTEGER: ", "",$value));
                                        }
                
                                        try
                                        {
                                            $snmp_RW->set(array("1.3.6.1.4.1.2011.5.14.5.2.1.2.".$NextFreeServicePort,  // shelf
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.3.'.$NextFreeServicePort,  // slot
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.4.'.$NextFreeServicePort,  // pon
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.5.'.$NextFreeServicePort,  // ont
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.7.'.$NextFreeServicePort,  // epon
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.8.'.$NextFreeServicePort,  // vlan                           
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.11.'.$NextFreeServicePort, // 1
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.12.'.$NextFreeServicePort, // vlan
                                                                '1.3.6.1.4.1.2011.5.14.5.2.1.15.'.$NextFreeServicePort, // create sp                                       
                                                                ),
                        
                                            array("i","i","i","i","i","i","i","i","i"),
                                            array(trim($shelf) , trim($slot) , trim($ponZ) , (int)$onuIfindex  , 6 , (int)$vlan , 1 , (int)$vlan , 4 ));  
                    
                                            $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.17.'.(int)$NextFreeServicePort , 's', $user); 
                                        }
                                        catch (\Exception $e) 
                                        {
                                            return response()->json(['error' => $snmp->getError()]); 
                                        } 
                        }
                    }
 
                }
               

                if($Iphost !== 'none' && $MODE !== 'BRIDGE')
                {
                    $NextFreeServicePort   =  $snmp->walk("1.3.6.1.4.1.2011.5.14.5.1" , TRUE);
                    foreach ($NextFreeServicePort as $key => $value) 
                    {
                        $NextFreeServicePort   = trim(str_replace("INTEGER: ", "",$value));
                    }
    
                    try
                    {
                        $snmp_RW->set(array("1.3.6.1.4.1.2011.5.14.5.2.1.2.".$NextFreeServicePort,  // shelf
                                            '1.3.6.1.4.1.2011.5.14.5.2.1.3.'.$NextFreeServicePort,  // slot
                                            '1.3.6.1.4.1.2011.5.14.5.2.1.4.'.$NextFreeServicePort,  // pon
                                            '1.3.6.1.4.1.2011.5.14.5.2.1.5.'.$NextFreeServicePort,  // ont
                                            '1.3.6.1.4.1.2011.5.14.5.2.1.7.'.$NextFreeServicePort,  // epon
                                            '1.3.6.1.4.1.2011.5.14.5.2.1.8.'.$NextFreeServicePort,  // vlan                           
                                            '1.3.6.1.4.1.2011.5.14.5.2.1.11.'.$NextFreeServicePort, // 1
                                            '1.3.6.1.4.1.2011.5.14.5.2.1.12.'.$NextFreeServicePort, // vlan
                                            '1.3.6.1.4.1.2011.5.14.5.2.1.15.'.$NextFreeServicePort, // create sp                                       
                                            ),
    
                        array("i","i","i","i","i","i","i","i","i"),
                        array(trim($shelf) , trim($slot) , trim($ponZ) , (int)$onuIfindex  , 6 , (int)$Iphost , 1 , (int)$Iphost , 4 ));  
    
                        $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.17.'.(int)$NextFreeServicePort , 's', $user);  
                    }
                    catch (\Exception $e) 
                    {
                        return response()->json(['error' => $snmp->getError()]); 
                    } 
                }

            }
            else
            {
                return response()->json(['error' => 'მოხდა შეცდომა SERVICE PROFILE წაკითხვისას']); 
            }   
        }
        else
        {
            return response()->json(['error' => 'მოხდა შეცდომა shelf , slot , pon ის გაგებისას']); 
        }
    }
    
    static public function HUAWEI_EPON_SERVICE_PROFILE_READ($ip,$read,$serviceProfile)
    {
        $SvcArray = [];
        $JsonBody = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp->oid_increasing_check = false;

        $Service_Profile_List = $snmp->walk('1.3.6.1.4.1.2011.6.128.1.1.3.46.1.6', TRUE);   
        foreach ($Service_Profile_List as $key => $value) 
        {
            $value = trim(str_replace('INTEGER: ','',$value));
           
            $SvcArray = self::EPON_CustomDecimalToAscii($key);       //DD($Service_Profile_List,$key,$SvcArray,$serviceProfile);
            if(trim($SvcArray['name']) == trim($serviceProfile))
            {        
                    $hwGponDeviceSrvProfPortVlanCfgPortVlanType = trim(str_replace('INTEGER: ','', $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.3.46.1.4.".$key, TRUE)));    
                if ($hwGponDeviceSrvProfPortVlanCfgPortVlanType == 2)
                {
                        $rowData =  [                       
                            'eth' => $SvcArray['eth'],
                            'vlan' => $SvcArray['vlan'],
                            'vlanType' => 'translation',  
                        ];
                        $JsonBody[] = $rowData;   
                }     
            }   
        }
        
        return $JsonBody;
    }

    static public function HUAWEI_EPON_SERVICE_PROFILE_READ_FOR_VLANS($ip,$read,$serviceProfile)
    {                  
        $SvcArray = [];
        $JsonBody = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp->oid_increasing_check = false;
        
        $Service_Profile_List = $snmp->walk('1.3.6.1.4.1.2011.6.128.1.1.3.46.1.6', TRUE);     
        foreach ($Service_Profile_List as $key => $value) 
        {
            $value = trim(str_replace('INTEGER: ','',$value));
           
            $SvcArray = self::EPON_CustomDecimalToAscii($key);       
            if(trim($SvcArray['name']) == trim($serviceProfile))
            {        
                    $hwGponDeviceSrvProfPortVlanCfgPortVlanType = trim(str_replace('INTEGER: ','', $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.3.46.1.4.".$key, TRUE)));    
                if ($hwGponDeviceSrvProfPortVlanCfgPortVlanType == 2)
                {
                        // $rowData =  [                       
                        //     'vlan' => $SvcArray['vlan'],  
                        // ];
                        $JsonBody[] = (int)$SvcArray['vlan']; 
                }     
            }   
        }

        $collection = collect($JsonBody);

        $uniqueValues = $collection->unique()->values()->all();
 
        try{    
                $ServiceProfiles = $snmp->walk("1.3.6.1.4.1.2011.5.6.1.1.1.2", TRUE); 

                foreach ($ServiceProfiles as $key => $value) 
                {
                    $value = str_replace('STRING: ','',$value);
                   
                    $uniqueValues[] = $key;
                }

        }catch (\Exception $e){}
        
 
        return $uniqueValues;
    }

    static public function HUAWEI_EPON_SERVICE_PROFILE_READ_FOR_VLANS_CUSTOM($ip,$read,$serviceProfile)
    {
        $SvcArray = [];
        $JsonBody = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp->oid_increasing_check = false;
        $Service_Profile_List = $snmp->walk('1.3.6.1.4.1.2011.6.128.1.1.3.46.1.6', TRUE);   
        foreach ($Service_Profile_List as $key => $value) 
        {
            $value = trim(str_replace('INTEGER: ','',$value));
           
            $SvcArray = self::EPON_CustomDecimalToAscii($key);       //DD($Service_Profile_List,$key,$SvcArray,$serviceProfile);
            if(trim($SvcArray['name']) == trim($serviceProfile))
            {        
                    $hwGponDeviceSrvProfPortVlanCfgPortVlanType = trim(str_replace('INTEGER: ','', $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.3.46.1.4.".$key, TRUE)));    
                if ($hwGponDeviceSrvProfPortVlanCfgPortVlanType == 2)
                {
                        // $rowData =  [                       
                        //     'vlan' => $SvcArray['vlan'],  
                        // ];
                        $JsonBody[] = (int)$SvcArray['vlan']; 
                }     
            }   
        }

        $collection = collect($JsonBody);

        $uniqueValues = $collection->unique()->values()->all();
 
        return $uniqueValues;
    }
 
    static public function HUAWEI_SERVICE_PROFILE_READ($ip,$read,$serviceProfile)
    {
        $SvcArray = [];
        $JsonBody = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $Service_Profile_List = $snmp->walk('1.3.6.1.4.1.2011.6.128.1.1.3.68.1.8', TRUE);
        foreach ($Service_Profile_List as $key => $value) 
        {
            $value = trim(str_replace('INTEGER: ','',$value));

            $SvcArray = self::CustomDecimalToAscii($key);    
            if(trim($SvcArray['name']) == trim($serviceProfile))
            {        
                    $hwGponDeviceSrvProfPortVlanCfgPortVlanType = trim(str_replace('INTEGER: ','', $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.3.68.1.6.".$key, TRUE))); 
                if ($hwGponDeviceSrvProfPortVlanCfgPortVlanType == 2)
                {
                        $rowData =  [                       
                            'eth' => $SvcArray['eth'],
                            'vlan' => $SvcArray['vlan'],
                            'vlanType' => 'translation',  
                        ];
                        $JsonBody[] = $rowData;   
                }     
            }   
        }

        return $JsonBody;
    }

    static public function HUAWEI_LINE_PROFILE_READ($ip,$read,$lineProfile)
    {   
        $LineArray       = [];
        $JsonBody        = [];
        $LineGemMapArray = [];
        
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $hwGponDeviceLineProfMappingCfgVlanId   =  $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.3.64.1.8", TRUE); 
        foreach ($hwGponDeviceLineProfMappingCfgVlanId as $key => $value) 
        {
            $value            = trim(str_replace('INTEGER: ','',$value));
            $LineGemMapArray  = self::GET_GEM_MAPPING_FROM_LINE_PROFILE($key);                                       
            if($LineGemMapArray['name'] == $lineProfile)
            {
                $rowData =  [                       
                                'mapping'     => $LineGemMapArray['mapping'],
                                'Nextmapping' => $LineGemMapArray['mapping2'],
                                'Vlan'        => $value,
                            ];
                $JsonBody[] = $rowData;             
            }
        }
    
        return $JsonBody;
    }

    static public function HUAWEI_FAKE_INSTALL($ip,$read,$write,$macSN,$ifIndex,$pon,$NocUser,$NocIP,$ssHUser,$sshPass)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        $html = [];
        $List = [];
 
        $TotalOnu  = 0;
 
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
 
        try {
                $Macs = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3", TRUE); 
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

                    if (strpos($value , $macSN) !== false) 
                    {
                        $Parts   = explode('.',$key);
                        $PonPort = HUAWEI::Pon_Port($Parts[0]).':'.$Parts[1];
                        return response()->json(['error' => 'ეს ონუ ამ სერიულით '.$macSN.' უკვე დარეგისტრირებულია '.$PonPort.' პონზე']);
                    }
                }
        }
        catch (\Exception $e){}
   
        try{
                $TotalOnu = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.21.1.16.".$ifIndex."", TRUE);
                $TotalOnu = str_replace('INTEGER: ','',$TotalOnu);

                $shelf = 0;$slot = 0;$ponZ = 0;
                $UnfixedPon = self::Pon_Port($ifIndex); 
                $UnfixedPon = str_replace('GPON','',$UnfixedPon);
                $UnfixedPon = trim($UnfixedPon);
                $UnfixedPon = explode('/',$UnfixedPon);

                $shelf = $UnfixedPon[0];
                $slot  = $UnfixedPon[1];
                $ponZ  = $UnfixedPon[2];
        
        }catch (\Exception $e){}
 
         $List ['Line']      = self::LINE_PROFILE_LIST($ip,$read);
         $List ['Service']   = self::SERVICE_PROFILE_LIST($ip,$read);
         $List ['Vlans']     = self::VLANS_LIST($ip,$read);
 
        if($TotalOnu == 128)
        {
            return response()->json(['error' => 'ვერ ხერხდება ინსტალი  '.$pon.'   სავსეა']);   
        }
        else if($TotalOnu == 0)
        {
            if(isset($shelf) && isset($slot) && isset($ponZ))
            {
                $snmp_RW->set(array('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.2.'.$ifIndex.'.0', // authentication mode sn(1)  
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.'.$ifIndex.'.0',  // sn
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.7.'.$ifIndex.'.0',  // line profile
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.8.'.$ifIndex.'.0',  // service profile
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.6.'.$ifIndex.'.0',  // management mode omci(1)  
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$ifIndex.'.0', // createAndGo(4) ,  destroy(6)
                ),
                array("i" , 'x' , "s", "s", "i", "i"),
                array( 1 , $macSN, 'line-profile_default_0','srv-profile_default_0',1 ,1 )); 
 
                $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.'.$ifIndex.'.0' , 's', self::generateRandomHexString(16)); 
            }
            else
            {
                return response()->json(['error' => 'მოხდა შეცდომა Next Free Service Porti ის გაგებისას']);   
            }
  
            Sleep(1); 
            $timeout = 20;  
            $start_time = time();

            while (true) 
            {
                $html = self::HUAWEI_FAKE_PORT_DISPLAY($ip,$read,$write,$ifIndex,$macSN,'0');
 
                // Check if the function returned data
                if (isset($html['PortCount']) && $html['PortCount'] > 0) 
                {
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$ifIndex.'.0' , 'i', 6);
                    $html['ListArray']  = $List;
                    $html['Worker']     = $NocUser;   
                    $html['userIp']     = $NocIP;
                    $html['sshUser']    = $ssHUser;
                    $html['sshPass']    = $sshPass;
                    return response()->json($html); 
                }

                // Check if the timeout has been reached
                if (time() - $start_time >= $timeout) 
                {
                    // Timeout reached, exit the loop
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$ifIndex.'.0' , 'i', 6);
                    return response()->json(['error' => 'ონუ ვერ გამოვიდა კავშირზე , ონუს პორტების რაოდენობა ვერ დადგინდა']);  
                }

                sleep(3);
            }
        }
        else if($TotalOnu > 0)
        {
            $OnuList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$ifIndex, TRUE);
            foreach ($OnuList as $key => $value) 
            {
                $keysArray[] = $key;
            }
            $NextFreeVirtualPort = '';  
            for ($i = 0; $i <= 128; $i++) 
            {
                if (!in_array($i, $keysArray)) 
                {
                    $NextFreeVirtualPort = $i; 
                    break;
                }
            }

            if (isset($NextFreeVirtualPort))
            {
                if( isset($shelf) && isset($slot) && isset($ponZ))
                { 
                    $snmp_RW->set(array('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.2.'.$ifIndex.'.'.$NextFreeVirtualPort,  // authentication mode sn(1)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.'.$ifIndex.'.'.$NextFreeVirtualPort,  // sn
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.7.'.$ifIndex.'.'.$NextFreeVirtualPort,  // line profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.8.'.$ifIndex.'.'.$NextFreeVirtualPort,  // service profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.6.'.$ifIndex.'.'.$NextFreeVirtualPort,  // management mode omci(1)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$ifIndex.'.'.$NextFreeVirtualPort, // createAndGo(4) ,  destroy(6)
                    ),
                    array("i",'x',"s","s","i","i"),
                    array(1,$macSN,'line-profile_default_0','srv-profile_default_0',1,1)); 
     
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.'.$ifIndex.'.'.$NextFreeVirtualPort , 's', self::generateRandomHexString(16)); 

                    Sleep(1); 
                    $timeout = 20;  
                    $start_time = time();
        
                    while (true) 
                    {
                        $html = self::HUAWEI_FAKE_PORT_DISPLAY($ip,$read,$write,$ifIndex,$macSN,$NextFreeVirtualPort);
         
                        // Check if the function returned data
                        if (isset($html['PortCount']) && $html['PortCount'] > 0) 
                        {
                            $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$ifIndex.'.'.$NextFreeVirtualPort , 'i', 6);
                            $html['ListArray']  = $List;
                            $html['Worker']     = $NocUser;   
                            $html['userIp']     = $NocIP;
                            $html['sshUser']    = $ssHUser;
                            $html['sshPass']    = $sshPass;
                            return response()->json($html); 
                        }
        
                        // Check if the timeout has been reached
                        if (time() - $start_time >= $timeout) 
                        {
                            // Timeout reached, exit the loop
                            $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$ifIndex.'.'.$NextFreeVirtualPort , 'i', 6);
                            return response()->json(['error' => 'ონუ ვერ გამოვიდა კავშირზე , ონუს პორტების რაოდენობა ვერ დადგინდა']);  
                        }
        
                        sleep(3);
                    }
                }
                else
                {
                    return response()->json(['error' => 'მოხდა შეცდომა Next Free Service Porti ის გაგებისას']);   
                }
            }
            else
            {
                return response()->json(['error' => 'მოხდა შეცდომა ვირტუალური პორტის დადგენისას']);
            }
        }

    }

    static public function HUAWEI_EPON_FAKE_INSTALL($ip,$read,$write,$macSN,$ifIndex,$pon,$NocUser,$NocIP,$ssHUser,$sshPass)
    { 
        PrivilegesModel::PrivCheck('Priv_Install');
        $html = [];
        $List = [];
       
        $TotalOnu  = 0;
 
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
 
        try {
                $Macs = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3", TRUE); 
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

                    if (strpos($value , $macSN) !== false) 
                    {
                        $Parts   = explode('.',$key);
                        $PonPort = HUAWEI::GPON_EPON_PORT($Parts[0]).':'.$Parts[1];
                        return response()->json(['error' => 'ეს ონუ ამ სერიულით '.$macSN.' უკვე დარეგისტრირებულია '.$PonPort.' პონზე']);
                    }
                }
        }
        catch (\Exception $e){}
 
        try{
                $TotalOnu = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.31.1.12.".$ifIndex."", TRUE);
                $TotalOnu = str_replace('INTEGER: ','',$TotalOnu);
 
                $shelf = 0;$slot = 0;$ponZ = 0;
                $UnfixedPon = self::Pon_Port($ifIndex); 
                $UnfixedPon = str_replace('GPON','',$UnfixedPon);
                $UnfixedPon = trim($UnfixedPon);
                $UnfixedPon = explode('/',$UnfixedPon);

                $shelf = $UnfixedPon[0];
                $slot  = $UnfixedPon[1];
                $ponZ  = $UnfixedPon[2];
        
        }catch (\Exception $e){}
        
        
        $List ['Line']      = self::EPON_LINE_PROFILE_LIST($ip,$read);
        $List ['Service']   = self::EPON_SERVICE_PROFILE_LIST($ip,$read);
        $List ['Vlans']     = self::VLANS_LIST($ip,$read);
       
        if($TotalOnu == 128)
        {
            return response()->json(['error' => 'ვერ ხერხდება ინსტალი  '.$pon.'   სავსეა']);   
        }
        else if($TotalOnu == 0)
        {
            if( isset($shelf) && isset($slot) && isset($ponZ))
            { 
                $macSN = str_replace(':','',$macSN);
                $snmp_RW->set(array('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.2.'.$ifIndex.'.0',  // authentication mode mac(3)  
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3.'.$ifIndex.'.0',  // sn
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.7.'.$ifIndex.'.0',  // line profile
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.8.'.$ifIndex.'.0',  // service profile
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.6.'.$ifIndex.'.0',  // management mode oam(1)  
                                    '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.10.'.$ifIndex.'.0', // createAndGo(4) ,  destroy(6)
                ),
                array("i",'x',"s","s","i","i"), 
                array(3,$macSN,reset($List ['Line']),reset($List ['Service']),1,1)); 
 
                $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.'.$ifIndex.'.0' , 's', self::generateRandomHexString(16)); 

                Sleep(1); 
                $timeout = 110;  
                $start_time = time();

                while (true) 
                {
                    $html = self::HUAWEI_EPON_FAKE_PORT_DISPLAY($ip,$read,$write,$ifIndex,$macSN,0);
     
                    // Check if the function returned data
                    if (isset($html['PortCount']) && $html['PortCount'] > 0) 
                    {    
                        $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.10.'.$ifIndex.'.0' , 'i', 6);
                        $html['ListArray']  = $List;
                        $html['Worker']     = $NocUser;   
                        $html['userIp']     = $NocIP;
                        $html['sshUser']    = $ssHUser;
                        $html['sshPass']    = $sshPass;
                        return response()->json($html); 
                    }
    
                    // Check if the timeout has been reached
                    if (time() - $start_time >= $timeout) 
                    {
                        // Timeout reached, exit the loop
                        $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.10.'.$ifIndex.'.0' , 'i', 6);
                        return response()->json(['error' => 'ონუ ვერ გამოვიდა კავშირზე , ონუს პორტების რაოდენობა ვერ დადგინდა']);  
                    }
    
                    sleep(3);
                }

            }
            else
            {
                return response()->json(['error' => 'მოხდა შეცდომა Next Free Service Porti ის გაგებისას']);   
            }
        }
        else if($TotalOnu > 0)
        {
            $OnuList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$ifIndex, TRUE);
            foreach ($OnuList as $key => $value) 
            {
                $keysArray[] = $key;
            }
            $NextFreeVirtualPort = '';  
            for ($i = 0; $i <= 128; $i++) 
            {
                if (!in_array($i, $keysArray)) 
                {
                    $NextFreeVirtualPort = $i; 
                    break;
                }
            }

 
            if (isset($NextFreeVirtualPort))
            {
                if( isset($shelf) && isset($slot) && isset($ponZ))
                { 
                    $macSN = str_replace(':','',$macSN);
                    $snmp_RW->set(array('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.2.'.$ifIndex.'.'.$NextFreeVirtualPort,  // authentication mode mac(3)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3.'.$ifIndex.'.'.$NextFreeVirtualPort,  // sn
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.7.'.$ifIndex.'.'.$NextFreeVirtualPort,  // line profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.8.'.$ifIndex.'.'.$NextFreeVirtualPort,  // service profile
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.6.'.$ifIndex.'.'.$NextFreeVirtualPort,  // management mode oam(1)  
                                        '1.3.6.1.4.1.2011.6.128.1.1.2.53.1.10.'.$ifIndex.'.'.$NextFreeVirtualPort, // createAndGo(4) ,  destroy(6)
                    ),
                    array("i",'x',"s","s","i","i"), 
                    array(3,$macSN,reset($List ['Line']),reset($List ['Service']),1,1)); 
     
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.'.$ifIndex.'.'.$NextFreeVirtualPort , 's', self::generateRandomHexString(16)); 

                    Sleep(1); 
                    $timeout = 110;  
                    $start_time = time();

                    while (true) 
                    {
                        $html = self::HUAWEI_EPON_FAKE_PORT_DISPLAY($ip,$read,$write,$ifIndex,$macSN,$NextFreeVirtualPort);
         
                        // Check if the function returned data
                        if (isset($html['PortCount']) && $html['PortCount'] > 0) 
                        {    
                            $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.10.'.$ifIndex.'.'.$NextFreeVirtualPort , 'i', 6);
                            $html['ListArray']  = $List;
                            $html['Worker']     = $NocUser;   
                            $html['userIp']     = $NocIP;
                            $html['sshUser']    = $ssHUser;
                            $html['sshPass']    = $sshPass;
                            return response()->json($html); 
                        }
        
                        // Check if the timeout has been reached
                        if (time() - $start_time >= $timeout) 
                        {
                            // Timeout reached, exit the loop
                            $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.10.'.$ifIndex.'.'.$NextFreeVirtualPort , 'i', 6);
                            return response()->json(['error' => 'ონუ ვერ გამოვიდა კავშირზე , ონუს პორტების რაოდენობა ვერ დადგინდა']);  
                        }
        
                        sleep(3);
                    }

                }
                else
                {
                    return response()->json(['error' => 'მოხდა შეცდომა Next Free Service Porti ის გაგებისას']);   
                }
            }
            else
            {
                return response()->json(['error' => 'მოხდა შეცდომა ვირტუალური პორტის დადგენისას']);
            }
        }
    }

    static public function HUAWEI_FAKE_PORT_DISPLAY($ip,$read,$write,$ifIndex,$macSN,$NextFreeVirtualPort)
    {
        $html = [];
        $sizeArray = 0;
        $IsDataExist = false;
        $snmp                    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp->oid_output_format = SNMP_OID_OUTPUT_NUMERIC;
        $snmp->valueretrieval    = SNMP_VALUE_PLAIN;
          
        try{
             $PortCount  = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.21.".$ifIndex.".".$NextFreeVirtualPort, TRUE); 
             $PortDuplex = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.3.".$ifIndex.".".$NextFreeVirtualPort, TRUE);   
             $PortStatus = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.22.".$ifIndex.".".$NextFreeVirtualPort, TRUE); 
        }catch (\Exception $e){}


        try{
                $newArray = [];
                foreach ($PortCount as $key => $value) 
                {
                    if (strpos($value, '-1') === false) 
                    {                
                        $newArray[$key] = 
                        [
                            'Port'   => $PortCount[$key],
                            'Duplex' => $PortDuplex[$key],
                            'Status' => $PortStatus[$key],
                        ];
                        if($key === 4)break;
                    }
                }

                $sizeArray = count($newArray);  

                if($sizeArray)
                {
                    $Port_Number = 1;
                    foreach ($newArray as $key => $value) 
                    {
                        
                            $TmpType;
                            if (strpos(trim($value['Port']) , ':') !== false) 
                            {
                                $Type    = explode('INTEGER: ',trim($value['Port']));
                                $TmpType = $Type[1];
                            }
                            else
                            {
                                $TmpType = trim($value['Port']);
                            }
            
            
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
            
        
                            $TmpStatus;
                            if (strpos(trim($value['Status']) , ':') !== false) 
                            {
                                $Last_Port_Status = explode('INTEGER: ',trim($value['Status']));
                                $TmpStatus        = $Last_Port_Status[1];
                            }
                            else 
                            {
                                $TmpStatus        = trim($value['Status']);
                            }
        
                            if($TmpStatus == 1)
                            {
                                $Last_Port_StatusEx = 'Link Up';
                            }
                            else if($TmpStatus == 2)
                            {
                                $Last_Port_StatusEx = 'Link Down';
                            }
    
                            $IsDataExist = true;

                            $item = [];
                            $item ['PonPort']   = self::Pon_Port($ifIndex).':'.$Port_Number;
                            $item ['State']     = $Last_Port_StatusEx;
                            $item ['Duplex']    = $Last_Duplex;
                            $item ['OnuPort']   = $key;
                            $item ['PortType']  = $PortType;
                            $html['Links_'.$key] = $item;

                            $Port_Number++;
                    }
 
                }

  
        }catch (\Exception $e){}


        $html ['Serial']    = $macSN;
        $html ['TypeInstall'] = 'GPON';  
        $html ['PortCount'] = $sizeArray;   
        if($IsDataExist)return $html;
        else return false;
    }

    static public function HUAWEI_EPON_FAKE_PORT_DISPLAY($ip,$read,$write,$ifIndex,$macSN,$NextFreeVirtualPort)
    {
        $html = [];
        $sizeArray = 0;
        $IsDataExist = false;
        $snmp                    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp->oid_output_format = SNMP_OID_OUTPUT_NUMERIC;
        $snmp->valueretrieval    = SNMP_VALUE_PLAIN;
          
        try{
             $PortCount  = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.91.1.3.".$ifIndex.".".$NextFreeVirtualPort, TRUE); 
        }catch (\Exception $e){}


        try{
                $newArray = [];
                foreach ($PortCount as $key => $value) 
                {
                    if (strpos($value, '-1') === false) 
                    {                
                        $newArray[$key] = 
                        [
                            'Port'   => $PortCount[$key],
                            'Duplex' => '-',
                            'Status' => '-',
                        ];
                        if($key === 4)break;
                    }
                }

                $sizeArray = count($newArray);  

                if($sizeArray)
                {
                    $Port_Number = 1;
                    foreach ($newArray as $key => $value) 
                    {
    
                            $IsDataExist = true;

                            $item = [];             
                            $item ['PonPort']   = self::GPON_EPON_PORT($ifIndex).':'.$Port_Number;
                            $item ['State']     = '-';
                            $item ['Duplex']    = '-';
                            $item ['OnuPort']   = $Port_Number;
                            $item ['PortType']  = '-';
                            $html['Links_'.$key] = $item;

                            $Port_Number++;
                    }
 
                }

  
        }catch (\Exception $e){}


        $html ['Serial']    = $macSN;
        $html ['TypeInstall'] = 'EPON';   
        $html ['PortCount'] = $sizeArray;   
        if($IsDataExist)return $html;
        else return false;
    }

    static public function VLANS_LIST($ip,$read)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $ServiceProfiles = '';
        try{    
                $ServiceProfiles = $snmp->walk("1.3.6.1.4.1.2011.5.6.1.1.1.2", TRUE); 

                foreach ($ServiceProfiles as $key => $value) 
                {
                    $value = str_replace('STRING: ','',$value);
                    $item = [];
                    $item['value'] = $key;
                    $item['name']  = $value;
                    $html ['VLANS_LIST_'.$key] = $item;
                }


        }catch (\Exception $e){ return response()->json(['error' => 'ვერ მოხერხდა SERVICE პროფილების სიის წამოღება ოელტედან']);  }

        return $html;
    }

    static public function LINE_PROFILE_LIST($ip,$read)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $LineProfiles = '';
        try{    
                $LineProfiles = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.3.61.1.2", TRUE); 

                foreach ($LineProfiles as $key => $value) 
                {
                    $html ['LINE_PROFILES_'.$key] = self::convertDecimalToAscii($key);
                }
 

        }catch (\Exception $e){ return response()->json(['error' => 'ვერ მოხერხდა LINE პროფილების სიის წამოღება ოელტედან']);  }

        return $html;
    }

    static public function SERVICE_PROFILE_LIST($ip,$read)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $ServiceProfiles = '';
        try{    
                $ServiceProfiles = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.3.65.1.2", TRUE); 

                foreach ($ServiceProfiles as $key => $value) 
                {
                    $html ['SERVICE_PROFILES_'.$key] = self::convertDecimalToAscii($key);
                }


        }catch (\Exception $e){ return response()->json(['error' => 'ვერ მოხერხდა SERVICE პროფილების სიის წამოღება ოელტედან']);  }

        return $html;
    }

    static public function EPON_LINE_PROFILE_LIST($ip,$read)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $LineProfiles = '';
        try{    
                $LineProfiles = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.3.41.1.2", TRUE); 

                foreach ($LineProfiles as $key => $value) 
                {
                    $html ['LINE_PROFILES_'.$key] = self::convertDecimalToAscii($key);
                }
 

        }catch (\Exception $e){ return response()->json(['error' => 'ვერ მოხერხდა LINE პროფილების სიის წამოღება ოელტედან']);  }

        return $html;
    }

    static public function EPON_SERVICE_PROFILE_LIST($ip,$read)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $ServiceProfiles = '';
        try{    
                $ServiceProfiles = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.3.43.1.2", TRUE); 

                foreach ($ServiceProfiles as $key => $value) 
                {
                    $html ['SERVICE_PROFILES_'.$key] = self::convertDecimalToAscii($key);
                }


        }catch (\Exception $e){ return response()->json(['error' => 'ვერ მოხერხდა SERVICE პროფილების სიის წამოღება ოელტედან']);  }

        return $html;
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

    static public function generateRandomHexString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }

    static public function convertDecimalToAscii($decimalValues) 
    {
        // Split the decimal values into an array
        $values = explode('.', $decimalValues);

        // Convert each decimal value to its ASCII equivalent
        $asciiString = '';
        foreach ($values as $decimal) {
            $asciiString .= chr($decimal);
        }

        return $asciiString;
    }

    static public function GET_GEM_MAPPING_FROM_LINE_PROFILE($decimalValues)
    {
        $values = explode('.', $decimalValues);

        $asciiArray = ['name' => ''];
        $count = count($values);
        $limit = max(0, $count - 2);
        for ($i = 1; $i < $limit; $i++) 
        {
            $asciiArray['name'] .= chr($values[$i]); 
        }

        if ($count > $limit) 
        {
            $asciiArray['mapping'] = $values[$limit];
        }
        if ($count > $limit + 1) 
        {
            $asciiArray['mapping2'] = $values[$limit + 1];
        }
        return $asciiArray;
    }

    static public function CustomDecimalToAscii($decimalValues)
    {
        // Split the decimal values into an array
        $values = explode('.', $decimalValues);

        // Convert each decimal value to its ASCII equivalent
        $asciiArray = ['name' => ''];

        // Determine the number of elements to convert (excluding last 4)
        $count = count($values);
        $limit = max(0, $count - 5);

        // Iterate through the decimal values
        for ($i = 1; $i < $limit; $i++) 
        {    
            $asciiArray['name'] .= chr($values[$i]);  
        }
        
        if ($count > $limit) 
        {
            if($values[$limit] == 50)
            {
                $asciiArray['eth'] = 'iphost';
            }
            else
            {
                $asciiArray['eth'] = $values[$limit+1];
            }
            
        }

        if ($count > $limit + 1) 
        {
            $asciiArray['vlan'] = $values[$limit + 2];
        }

        return $asciiArray;
    }

    static public function EPON_CustomDecimalToAscii($decimalValues)
    {
        // Split the decimal values into an array
        $values = explode('.', $decimalValues);

        // Convert each decimal value to its ASCII equivalent
        $asciiArray = ['name' => ''];

        // Determine the number of elements to convert (excluding last 4)
        $count = count($values);
        $limit = max(0, $count - 3);

        // Iterate through the decimal values
        for ($i = 1; $i < $limit; $i++) 
        {    
            $asciiArray['name'] .= chr($values[$i]);  
        }
        
        if ($count > $limit) 
        {
            if($values[$limit] == 50)
            {
                $asciiArray['eth'] = 'iphost';
            }
            else
            {
                $asciiArray['eth'] = $values[$limit + 1];
            }  
        }

        if ($count > $limit + 1) 
        {
            $asciiArray['vlan'] = $values[$limit + 2];
        }

        return $asciiArray;
    }

    static public function hasDuplicatesInEth($array) 
    {
        // Extract the 'eth' values from the array
        $ethValues = array_column($array, 'eth');

        // Count the occurrences of each 'eth' value
        $ethValueCounts = array_count_values($ethValues);

        // Check if any 'eth' value has a count greater than 1
        foreach ($ethValueCounts as $count) 
        {
            if ($count > 1) 
            {
                return true; // Duplicates found
            }
        }

        return false; // No duplicates found
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

    static public function HuaweiePON_FTP($user,$MODE,$ifIndex,$vlan,$PortCount)  
    {

        if($PortCount == 1)
        {
           if($MODE == 'ROUTER')
           { 
                $disk = Storage::disk('ftp');
                $filePath  = storage_path('app/1port-router-vlan-dhcp.xml');
        
                // Check if the file exists locally
                if (!file_exists($filePath)) 
                {
                    return response()->json(['error' => "XML file not found in Finder storage"]);
                }
        
                $fileContents = file_get_contents($filePath);   
        
                // Modify the XML content
                $modifiedXml = self::modifyXml($fileContents,(int)$vlan);  
                    
                // Compress the modified XML back into .gz format
                $compressedXml = gzencode($modifiedXml);
        
                if ($compressedXml === false) 
                {
                    return response()->json(['error' => "Failed to compress XML"]);
                }
        
                // Upload the compressed XML back to FTP
                $uploadResult = $disk->put($user.'.xml.gz', $compressedXml);
        
                if ($uploadResult) 
                {
                    return "GZ->XML config file and uploaded successfully";
                } 
                else 
                {
                    return response()->json(['error' => "Failed to upload modified GZ->XML config file"]);
                }
           }
           else if($MODE == 'BRIDGSTER')
           {
                $disk = Storage::disk('ftp');
                $filePath  = storage_path('app/1port-bridge-router.xml');
        
                // Check if the file exists locally
                if (!file_exists($filePath)) 
                {
                    return response()->json(['error' => "XML file not found in Finder storage"]);
                }
        
                $fileContents = file_get_contents($filePath);   
        
                // Modify the XML content
                $modifiedXml = self::modifyXml($fileContents,(int)$vlan);  
                    
                // Compress the modified XML back into .gz format
                $compressedXml = gzencode($modifiedXml);
        
                if ($compressedXml === false) 
                {
                    return response()->json(['error' => "Failed to compress XML"]);
                }
        
                // Upload the compressed XML back to FTP
                $uploadResult = $disk->put($user.'.xml.gz', $compressedXml);
        
                if ($uploadResult) 
                {
                    return "GZ->XML config file and uploaded successfully";
                } 
                else 
                {
                    return response()->json(['error' => "Failed to upload modified GZ->XML config file"]);
                }
           }
        }
        else if($PortCount == 4)
        {
            if($MODE == 'ROUTER')
            {
                $disk = Storage::disk('ftp');
                $filePath  = storage_path('app/4port_router_mode_wan_nat-last-modifi.xml');
        
                // Check if the file exists locally
                if (!file_exists($filePath)) 
                {
                    return response()->json(['error' => "XML file not found in Finder storage"]);
                }
        
                $fileContents = file_get_contents($filePath);   
        
                // Modify the XML content
                $modifiedXml = self::modifyXml($fileContents,(int)$vlan);  
                    
                // Compress the modified XML back into .gz format
                $compressedXml = gzencode($modifiedXml);
        
                if ($compressedXml === false) 
                {
                    return response()->json(['error' => "Failed to compress XML"]);
                }
        
                // Upload the compressed XML back to FTP
                $uploadResult = $disk->put($user.'.xml.gz', $compressedXml);
        
                if ($uploadResult) 
                {
                    return "GZ->XML config file and uploaded successfully";
                } 
                else 
                {
                    return response()->json(['error' => "Failed to upload modified GZ->XML config file"]);
                }
            }
            else if($MODE == 'BRIDGE')
            {
                $disk = Storage::disk('ftp');
                $filePath  = storage_path('app/4port_bridge-last-modify.xml');
        
                // Check if the file exists locally
                if (!file_exists($filePath)) 
                {
                    return response()->json(['error' => "XML file not found in Finder storage"]);
                }
        
                $fileContents = file_get_contents($filePath);   
                  
                // Compress the modified XML back into .gz format
                $compressedXml = gzencode($fileContents);
        
                if ($compressedXml === false) 
                {
                    return response()->json(['error' => "Failed to compress XML"]);
                }
        
                // Upload the compressed XML back to FTP
                $uploadResult = $disk->put($user.'.xml.gz', $compressedXml);
        
                if ($uploadResult) 
                {
                    return "GZ->XML config file and uploaded successfully";
                } 
                else 
                {
                    return response()->json(['error' => "Failed to upload modified GZ->XML config file"]);
                }
            }
            else if($MODE == 'BRIDGSTER')
            {
                $disk = Storage::disk('ftp');
                $filePath  = storage_path('app/4port_bridge-last-modify.xml');
        
                // Check if the file exists locally
                if (!file_exists($filePath)) 
                {
                    return response()->json(['error' => "XML file not found in Finder storage"]);
                }
        
                $fileContents = file_get_contents($filePath);   
        
                // Modify the XML content
                $modifiedXml = self::modifyXml($fileContents,(int)$vlan);  
                    
                // Compress the modified XML back into .gz format
                $compressedXml = gzencode($modifiedXml);
        
                if ($compressedXml === false) 
                {
                    return response()->json(['error' => "Failed to compress XML"]);
                }
        
                // Upload the compressed XML back to FTP
                $uploadResult = $disk->put($user.'.xml.gz', $compressedXml);
        
                if ($uploadResult) 
                {
                    return "GZ->XML config file and uploaded successfully";
                } 
                else 
                {
                    return response()->json(['error' => "Failed to upload modified GZ->XML config file"]);
                }
            }
        }
        else
        {
            return response()->json(['error' => "პორტების რაოდენობა ვერ დადგინდა HuaweiePON_FTP ფუნქციაში"]);
        }

    }

    static private function modifyXml($xmlContents,$vlan)
    {
        // Load XML string
        $xml = new DOMDocument();
        $xml->loadXML($xmlContents);

        // Modify the VLAN value
        $xpath = new DOMXPath($xml);
        $nodes = $xpath->query('//WANIPConnectionInstance[@InstanceID="1"]');
        foreach ($nodes as $node) {
            $node->setAttribute('X_HW_VLAN', $vlan);
        }

        // Save modified XML back to string
        return $xml->saveXML();
    }
}
