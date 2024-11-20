<?php

namespace App\Models\Install;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Crypt;
use App\Models\sshModel;
use App\Models\ZTE;
use App\Models\PrivilegesModel;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;

class _zte extends Model
{
    use HasFactory;

    static public function ZTE_SEARCH($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp,$macSN)
    {
        PrivilegesModel::PrivCheck('Priv_Install');

        $html = [];
        
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
        
        $PonArray = [
            268501248, 268501504, 268501760, 268502016, 268502272, 268502528, 268502784, 268503040, 
            268503296, 268503552, 268503808, 268504064, 268504320, 268504576, 268504832, 268505088, 
            268566784, 268567040, 268567296, 268567552, 268567808, 268568064, 268568320, 268568576, 
            268568832, 268569088, 268569344, 268569600, 268569856, 268570112, 268570368, 268570624
        ];

        try {
                $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                $ServerName = trim(str_replace("\"", "" , $ServerName));
                $ServerName = trim(str_replace("\'", "" , $ServerName));
        }
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

 
        foreach ($PonArray as $value) 
        {
            for ($i=1; $i <= 128; $i++) 
            {
                $FullSn = '';
                try{
                        $SN =  $snmp->get('1.3.6.1.4.1.3902.1012.3.13.3.1.2.'.$value.'.'.$i);

                }catch (\Exception $e)
                {
                    if ($snmp->getError())break;
                }
                         
                    try{
                            $MacOnu = str_replace("Hex-STRING: ","",$SN);
                            $MacOnu = str_replace("STRING: ","",$MacOnu);
                            $MacOnu = str_replace("\"","",$MacOnu);
                            $MacOnu = str_replace("\'","",$MacOnu);
                            $MacOnu = trim(str_replace(" ","",$MacOnu));
                            $MacOnu = str_replace('\'',"",$MacOnu);
                            $key    = $value.'.'.$i;           
                            
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


                            $SN_Fixed   = substr($MacOnu, 0, 8);
                            $SN_Fixed   = hex2bin($SN_Fixed);
                            $FullSn     = $SN_Fixed.substr($MacOnu, 8, 16);

                            if (strlen($macSN) <= 5)
                            { 
                                if (strpos(substr(trim($MacOnu), -4), substr(trim($macSN), -4)) !== false) 
                                {
                                    $SN_Fixed   = substr($MacOnu, 0, 8);
                                    $SN_Fixed   = hex2bin($SN_Fixed);
                
                                    $Pon_Port = explode('.',$key); 
                                    $Pon      = self::Pon_Port($Pon_Port[0]);

                    
                                    $item = [];
                                    $item['ifindex']    = $Pon_Port[0];
                                    $item['pon']        = 'GPON '.$Pon[1];
                                    $item['ServerName'] = $ServerName;
                                    $item['Serial']     = $SN_Fixed.substr($MacOnu, 8, 16);
                                    $item['FullSn']     = $MacOnu;
                                    $item['OnyType']    = $SN_Fixed;
                                    $html['OnuList_'.$key] = $item;    
                                    $html['address']    = $ip;
                                    $html['Worker']     = $Workerusername;
                                    $html['userIp']     = $userIp;
                                    $html['sshUser']    = $sshUser;
                                    $html['sshPass']    = $sshPass;
                                    $html['ServerName'] = $ServerName;                    
                                    $html['type']       = 'ZTE';  
                                }
                            }
                            else if (strpos(trim($MacOnu), trim($macSN)) !== false)
                            {

                                $Pon_Port = explode('.',$key);  
                                $Pon      = self::Pon_Port($Pon_Port[0]); 

                                $item = [];
                                $item['ifindex']    = $Pon_Port[0];
                                $item['pon']        = 'GPON '.$Pon[1];
                                $item['FullSn']     = $MacOnu;
                                $item['ServerName'] = $ServerName;
                                $item['Serial']     = $SN_Fixed.substr($MacOnu, 8, 16);
                                $item['OnyType']    = $SN_Fixed;
                                $html['OnuList_'.$key] = $item;  
                                $html['address']    = $ip;
                                $html['Worker']     = $Workerusername;
                                $html['userIp']     = $userIp;
                                $html['sshUser']    = $sshUser;
                                $html['sshPass']    = $sshPass;
                                $html['ServerName'] = $ServerName;                    
                                $html['type']       = 'ZTE';    
                            }
                            else if (strpos(trim($FullSn), trim($macSN)) !== false)
                            {
                                $Pon_Port = explode('.',$key);  
                                $Pon      = self::Pon_Port($Pon_Port[0]); 

                                $item = [];
                                $item['ifindex']    = $Pon_Port[0];
                                $item['pon']        = 'GPON '.$Pon[1];
                                $item['FullSn']     = $MacOnu;
                                $item['ServerName'] = $ServerName;
                                $item['Serial']     = $SN_Fixed.substr($MacOnu, 8, 16);
                                $item['OnyType']    = $SN_Fixed;
                                $html['OnuList_'.$key] = $item;  
                                $html['address']    = $ip;
                                $html['Worker']     = $Workerusername;
                                $html['userIp']     = $userIp;
                                $html['sshUser']    = $sshUser;
                                $html['sshPass']    = $sshPass;
                                $html['ServerName'] = $ServerName;                    
                                $html['type']       = 'ZTE';    
                            }
                    }catch (\Exception $e){}

                    
            }
        }

        return response()->json($html);
    }

    static public function ZTE_AUTOFIND($ip,$read,$write,$sshUser,$sshPass,$Workerusername,$userIp)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);
        
        $PonArray = [
            268501248, 268501504, 268501760, 268502016, 268502272, 268502528, 268502784, 268503040, 
            268503296, 268503552, 268503808, 268504064, 268504320, 268504576, 268504832, 268505088, 
            268566784, 268567040, 268567296, 268567552, 268567808, 268568064, 268568320, 268568576, 
            268568832, 268569088, 268569344, 268569600, 268569856, 268570112, 268570368, 268570624
        ];


        try {
                $ServerName = trim(str_replace("STRING: ", "",$snmp->get("1.3.6.1.2.1.1.5.0", TRUE)));  
                $ServerName = trim(str_replace("\"", "" , $ServerName));
                $ServerName = trim(str_replace("\'", "" , $ServerName));
        }
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $exist = false;
        
        try{
                foreach ($PonArray as $value) 
                {
                    for ($i=1; $i <= 128; $i++) 
                    {
                    
                        try{
                            $SN =  $snmp->get('1.3.6.1.4.1.3902.1012.3.13.3.1.2.'.$value.'.'.$i);
                            }catch (\Exception $e){if ($snmp->getError())break;}
                                
                        
                                    $MacOnu = str_replace("Hex-STRING: ","",$SN);
                                    $MacOnu = str_replace("STRING: ","",$MacOnu);
                                    $MacOnu = str_replace("\"","",$MacOnu);
                                    $MacOnu = str_replace("\'","",$MacOnu);
                                    $MacOnu = trim(str_replace(" ","",$MacOnu));
                                    $MacOnu = str_replace('\'',"",$MacOnu);
                                    $key    = $value.'.'.$i;           
                                    
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

 
                                        $exist = true;

                                        $item = [];
                                        $item['ifindex']    = $Pon_Port[0];
                                        $item['pon']        = 'GPON '.$Pon[1];
                                        $item['FullSn']     = $MacOnu;
                                        $item['ServerName'] = $ServerName;
                                        $item['Serial']     = $SN_Fixed.substr($MacOnu, 8, 16);
                                        $item['OnyType']    = $SN_Fixed;
                                        $html['OnuList_'.$key] = $item;    
                    }

                    if($exist)
                    {
                        $html['address']    = $ip;
                        $html['Worker']     = $Workerusername;
                        $html['userIp']     = $userIp;
                        $html['sshUser']    = $sshUser;
                        $html['sshPass']    = $sshPass;
                        $html['ServerName'] = $ServerName;                    
                        $html['type']       = 'ZTE';  
                    }
 
                }
            }catch (\Exception $e){}
        

        return response()->json($html);
    }

    static public function ZTE_FAKE_INSTALL($ip,$read,$write,$macSN,$ifIndex,$pon,$NocUser,$NocIP,$ssHUser,$sshPass)
    {
        PrivilegesModel::PrivCheck('Priv_Install');

        $html = [];
        $List = [];

        $TotalOnu  = '';
 
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);

        $keysArray = array();
        $RandName  = self::generateRandomHexString(16);  
      
        try{
                $StatusOnu = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$ifIndex, TRUE);
                foreach ($StatusOnu as $key => $value) 
                {
                    $TotalOnu++;
                    $keysArray[] = $key;
                }
        
        }catch (\Exception $e)
        {
            if (strpos( $snmp->getError(), 'No Such Instance currently exists at this OID') !== false) 
            {
                $TotalOnu  = 0;
            }
            else
            {
                Log::critical('[Onu SN] '.$macSN .'\n[error] '.$snmp->getError());
                return response()->json(['error' => $snmp->getError()]);
            }   
        }

        $List ['Line']      = self::LINE_PROFILE_LIST($ip,$read);
        $List ['Service']   = self::SERVICE_PROFILE_LIST($ip,$read);
        $List ['Mgmt']      = self::MGMT_LIST($ip,$read);
 
        if($TotalOnu == 128)
        {
            return response()->json(['error' => 'ვერ ხერხდება ინსტალი  '.$pon.'   სავსეა']);   
        }
        else if($TotalOnu == 0)
        {
            $snmp_RW->set(array('1.3.6.1.4.1.3902.1012.3.28.1.1.9.'.$ifIndex.'.1', 
            '1.3.6.1.4.1.3902.1012.3.28.1.1.12.'.$ifIndex.'.1',    
            '1.3.6.1.4.1.3902.1012.3.28.1.1.1.'.$ifIndex.'.1',     
            '1.3.6.1.4.1.3902.1012.3.28.1.1.5.'.$ifIndex.'.1',     
            '1.3.6.1.4.1.3902.1012.3.28.1.1.3.'.$ifIndex.'.1',      
            ),
            array("i","i","s","x","s"),
            array( 4 , 1 ,'4port' ,'0x'.trim($macSN), $RandName));

            Sleep(1); 
            $timeout = 60;  
            $start_time = time();

            while (true) 
            {
                $html = self::ZTE_FAKE_PORT_DISPLAY($ip,$read,$write,$ifIndex,$macSN,'1');

                // Check if the function returned data
                if (isset($html['PortCount']) && $html['PortCount'] > 0) 
                {
                    $snmp_RW->set('1.3.6.1.4.1.3902.1012.3.28.1.1.9.'.$ifIndex.'.1', 'i', '6'); 
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
                    $snmp_RW->set('1.3.6.1.4.1.3902.1012.3.28.1.1.9.'.$ifIndex.'.1', 'i', '6'); 
                    return response()->json(['error' => 'ონუ ვერ გამოვიდა კავშირზე , ონუს პორტების რაოდენობა ვერ დადგინდა']);  
                }

                sleep(3);
            }
        }
        else if($TotalOnu > 0)
        {
            $NextFreeVirtualPort = 1;  
            for ($i = 1; $i <= 128; $i++) 
            {
                if (!in_array($i, $keysArray)) 
                {
                    $NextFreeVirtualPort = $i; 
                    break;
                }
            }
            if (isset($NextFreeVirtualPort))
            {
                $snmp_RW->set(array('1.3.6.1.4.1.3902.1012.3.28.1.1.9.'.$ifIndex.'.'.$NextFreeVirtualPort, 
                '1.3.6.1.4.1.3902.1012.3.28.1.1.12.'.$ifIndex.'.'.$NextFreeVirtualPort,    
                '1.3.6.1.4.1.3902.1012.3.28.1.1.1.'.$ifIndex.'.'.$NextFreeVirtualPort,     
                '1.3.6.1.4.1.3902.1012.3.28.1.1.5.'.$ifIndex.'.'.$NextFreeVirtualPort,     
                '1.3.6.1.4.1.3902.1012.3.28.1.1.3.'.$ifIndex.'.'.$NextFreeVirtualPort,      
                ),
                array("i","i","s","x","s"),
                array( 4 , 1 ,'4port' ,'0x'.trim($macSN), $RandName));

                Sleep(1); 
                $timeout = 120;  
                $start_time = time();

                while (true) 
                {
                    $html = self::ZTE_FAKE_PORT_DISPLAY($ip,$read,$write,$ifIndex,$macSN,$NextFreeVirtualPort);
       
                    // Check if the function returned data
                    if (isset($html['PortCount']) && $html['PortCount'] > 0) 
                    {
                        $snmp_RW->set('1.3.6.1.4.1.3902.1012.3.28.1.1.9.'.$ifIndex.'.'.$NextFreeVirtualPort, 'i', '6'); 
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
                        $snmp_RW->set('1.3.6.1.4.1.3902.1012.3.28.1.1.9.'.$ifIndex.'.'.$NextFreeVirtualPort, 'i', '6'); 
                        return response()->json(['error' => 'ონუ ვერ მოიძებნა']);  
                    }
                    sleep(3);
                }

            }
            else
            {
                return response()->json(['error' => 'ვერ მოხერხდა თავისუფალი ვირტუალური პორტის პოვნა']);   
            }
        }
        else
        {
            return response()->json(['error' => 'ვერ დადგინდა ონუს რაოდენობა პონზე '.$TotalOnu.'  პონის ინდექსი  '.$ifIndex]); 
        }
   
    }

    static public function ZTE_REAL_INSTALL($ip,$read,$write,$ssHUser,$sshPass,$ifIndex,$client,$macSN,$LINE,$SERVICE,$TYPE,$MODE,$PortCount,$Port1Vlan,$Port2Vlan,$Port3Vlan,$Port4Vlan,$Trunk)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
 
        $html = [];
        $html ['read']      =  Crypt::encrypt($read);
        $html ['write']     =  Crypt::encrypt($write);
       
   
        $TotalOnu  = '';         

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
 
        try{
                $StatusOnu = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$ifIndex, TRUE);
                foreach ($StatusOnu as $key => $value) 
                {
                    $TotalOnu++;
                    $keysArray[] = $key;
                }
        }catch (\Exception $e)
        {

            if (strpos( $snmp->getError(), 'No Such Instance currently exists at this OID') !== false) 
            {
                $TotalOnu  = 0;
            }
            else
            {
                Log::critical('[Onu SN] '.$macSN .'\n[error] '.$snmp->getError());
                return response()->json(['error' => $snmp->getError()]);
            }

        }

        if($TotalOnu == 128)
        {
            return response()->json(['error' => 'ვერ ხერხდება ინსტალი  '.self::Pon_Port($ifIndex)[1].'   სავსეა']);   
        }
        else if($TotalOnu == 0)
        {
            $snmp_RW->set(array('1.3.6.1.4.1.3902.1012.3.28.1.1.9.'.$ifIndex.'.1', 
            '1.3.6.1.4.1.3902.1012.3.28.1.1.12.'.$ifIndex.'.1',    
            '1.3.6.1.4.1.3902.1012.3.28.1.1.1.'.$ifIndex.'.1',     
            '1.3.6.1.4.1.3902.1012.3.28.1.1.5.'.$ifIndex.'.1',     
            '1.3.6.1.4.1.3902.1012.3.28.1.1.3.'.$ifIndex.'.1',      
            ),
            array("i","i","s","x","s"),
            array( 4 , 1 ,$TYPE ,'0x'.trim($macSN), $client));

            
            if($PortCount == 4)
            {
                if($MODE == 'ROUTER')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu 1 profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit'
                    ];
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); //  პროფილებში ჩასმა
             
                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }

                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,false);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.1';
                    return $html;
                }
                else if($MODE == 'BRIDGE')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu 1 profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                        'onu-vlan ethuni eth_0/1 up-mode add up-prio 0 up-vid '.trim((int)$Port1Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/2 up-mode add up-prio 0 up-vid '.trim((int)$Port2Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/3 up-mode add up-prio 0 up-vid '.trim((int)$Port3Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/4 up-mode add up-prio 0 up-vid '.trim((int)$Port4Vlan).'  down-mode untag',
                        'dhcp-ip ethuni eth_0/1  from-internet',
                        'dhcp-ip ethuni eth_0/2  from-internet',
                        'dhcp-ip ethuni eth_0/3  from-internet',
                        'dhcp-ip ethuni eth_0/4  from-internet',
                        'exit'
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა

                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }

                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,false);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.1';
                    return $html;

                }
                else if($MODE == 'BRIDGSTER')
                { 
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu 1 profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                        'onu-vlan ethuni eth_0/1 up-mode add up-prio 0 up-vid '.trim((int)$Port1Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/2 up-mode add up-prio 0 up-vid '.trim((int)$Port2Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/3 up-mode add up-prio 0 up-vid '.trim((int)$Port3Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/4 up-mode add up-prio 0 up-vid '.trim((int)$Port4Vlan).'  down-mode untag',
                        'dhcp-ip ethuni eth_0/1  from-internet ',
                        'dhcp-ip ethuni eth_0/2  from-internet ',
                        'dhcp-ip ethuni eth_0/3  from-internet ',
                        'dhcp-ip ethuni eth_0/4  from-internet ',
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit'
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა

                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
     
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება
    
                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                    ];
                    
                    
                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }
                   
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,true);  // ვილანების მითითება 

                    dd($commandArray,$VlansFromServiceProfile,$FilteredArray,$VlansFromServiceProfile,$Res,$SERVICE);

                    $html ['ifindex'] = $ifIndex.'.1';
                    return $html;

                }
                else if($MODE == 'TRUNK')
                {

                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu 1 profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                        'onu-vlan ethuni eth_0/1 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/2 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/3 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/4 up-mode transparent down-mode transparent',
                        'dhcp-ip ethuni eth_0/1 from-internet',
                        'dhcp-ip ethuni eth_0/2 from-internet',
                        'dhcp-ip ethuni eth_0/3 from-internet',
                        'dhcp-ip ethuni eth_0/4 from-internet',
                        'exit',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                    ];
                 
                    foreach ($Trunk as $value) 
                    {
                        $commandArray    [] = 'switchport vlan '.trim((int)$value).' tag vport 1';
                    }
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true);

                    $html ['ifindex'] = $ifIndex.'.1';
                    return $html;
                }
                else if($MODE == 'ROUTRUNK')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu 1 profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                        'onu-vlan ethuni eth_0/1 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/2 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/3 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/4 up-mode transparent down-mode transparent',
                        'dhcp-ip ethuni eth_0/1 from-internet',
                        'dhcp-ip ethuni eth_0/2 from-internet',
                        'dhcp-ip ethuni eth_0/3 from-internet',
                        'dhcp-ip ethuni eth_0/4 from-internet',
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                    ];
                 
                    foreach ($Trunk as $value) 
                    {
                        $commandArray    [] = 'switchport vlan '.trim((int)$value).' tag vport 1';
                    }
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true);

                    $html ['ifindex'] = $ifIndex.'.1';
                    return $html;
                }
                else
                {
                    return response()->json(['error' => 'ვერ მოხერხდა ონუს რეჟიმის '.$MODE.' ამოცნობა']);       
                }
            }
            else if($PortCount == 1)
            {
                if($MODE == 'ROUTER')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu 1 profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit'
                    ];
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); //  პროფილებში ჩასმა
             
                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }

                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,false);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.1';
                    return $html;
                }
                else if($MODE == 'BRIDGE')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu 1 profile line '.$LINE.' remote '.$SERVICE, 
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                        'dhcp-ip ethuni eth_0/1  from-internet',
                        'exit'
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა

                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }

                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,false);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.1';
                    return $html;

                }
                else if($MODE == 'BRIDGSTER') 
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu 1 profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                        'onu-vlan ethuni eth_0/1 up-mode add up-prio 0 up-vid '.trim((int)$Port1Vlan).'  down-mode untag',
                        'dhcp-ip ethuni eth_0/1  from-internet',
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit'
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა

                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,true);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.1';
                    return $html;
                }
                else if($MODE == 'TRUNK')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu 1 profile line '.$LINE.' remote '.$SERVICE, 
                        'exit'
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა

                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება
                    
                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':1',
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,true);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.1';
                    return $html;

                }
                else
                {
                    return response()->json(['error' => 'ვერ მოხერხდა ონუს რეჟიმის '.$MODE.' ამოცნობა']);       
                }
            }
            else
            {
                return response()->json(['error' => 'ვერ მოხერხდა ონუს პორტის რაოდენობის დადგენა']);   
            }
        }
        else if($TotalOnu > 0)   
        {
            $NextFreeVirtualPort = 1;  
            for ($i = 1; $i <= 128; $i++) 
            {
                if (!in_array($i, $keysArray)) 
                {
                    $NextFreeVirtualPort = $i; 
                    break;
                }
            } 

            if (isset($NextFreeVirtualPort))
            {
                $snmp_RW->set(array('1.3.6.1.4.1.3902.1012.3.28.1.1.9.'.$ifIndex.'.'.$NextFreeVirtualPort, 
                '1.3.6.1.4.1.3902.1012.3.28.1.1.12.'.$ifIndex.'.'.$NextFreeVirtualPort,    
                '1.3.6.1.4.1.3902.1012.3.28.1.1.1.'.$ifIndex.'.'.$NextFreeVirtualPort,     
                '1.3.6.1.4.1.3902.1012.3.28.1.1.5.'.$ifIndex.'.'.$NextFreeVirtualPort,     
                '1.3.6.1.4.1.3902.1012.3.28.1.1.3.'.$ifIndex.'.'.$NextFreeVirtualPort,      
                ),
                array("i","i","s","x","s"),
                array( 4 , 1 ,$TYPE ,'0x'.trim($macSN), $client));
            }

            if($PortCount == 4)
            {
                if($MODE == 'ROUTER')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu '.$NextFreeVirtualPort.' profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit'
                    ];
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); //  პროფილებში ჩასმა
             
                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }

                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,false);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;
                }
                else if($MODE == 'BRIDGE')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu '.$NextFreeVirtualPort.' profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                        'onu-vlan ethuni eth_0/1 up-mode add up-prio 0 up-vid '.trim((int)$Port1Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/2 up-mode add up-prio 0 up-vid '.trim((int)$Port2Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/3 up-mode add up-prio 0 up-vid '.trim((int)$Port3Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/4 up-mode add up-prio 0 up-vid '.trim((int)$Port4Vlan).'  down-mode untag',
                        'dhcp-ip ethuni eth_0/1  from-internet',
                        'dhcp-ip ethuni eth_0/2  from-internet',
                        'dhcp-ip ethuni eth_0/3  from-internet',
                        'dhcp-ip ethuni eth_0/4  from-internet',
                        'exit'
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა

                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }

                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,false);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;

                }
                else if($MODE == 'BRIDGSTER')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu '.$NextFreeVirtualPort.' profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                        'onu-vlan ethuni eth_0/1 up-mode add up-prio 0 up-vid '.trim((int)$Port1Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/2 up-mode add up-prio 0 up-vid '.trim((int)$Port2Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/3 up-mode add up-prio 0 up-vid '.trim((int)$Port3Vlan).'  down-mode untag',
                        'onu-vlan ethuni eth_0/4 up-mode add up-prio 0 up-vid '.trim((int)$Port4Vlan).'  down-mode untag',
                        'dhcp-ip ethuni eth_0/1  from-internet ',
                        'dhcp-ip ethuni eth_0/2  from-internet ',
                        'dhcp-ip ethuni eth_0/3  from-internet ',
                        'dhcp-ip ethuni eth_0/4  from-internet ',
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit'
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა
     
                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile,true);  // ვილანების გაგება

 
                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                    ];

                    foreach ($Res as $key => $value) 
                    {    
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,true);  // ვილანების მითითება 
 
                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;

 
                    return $html;

                }
                else if($MODE == 'TRUNK')
                {

                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu '.$NextFreeVirtualPort.' profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                        'onu-vlan ethuni eth_0/1 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/2 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/3 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/4 up-mode transparent down-mode transparent',
                        'dhcp-ip ethuni eth_0/1 from-internet',
                        'dhcp-ip ethuni eth_0/2 from-internet',
                        'dhcp-ip ethuni eth_0/3 from-internet',
                        'dhcp-ip ethuni eth_0/4 from-internet',
                        'exit',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                    ];
                 
                    foreach ($Trunk as $value) 
                    {
                        $commandArray    [] = 'switchport vlan '.trim((int)$value).' tag vport 1';
                    }
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true);

                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;
                }
                else if($MODE == 'ROUTRUNK')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu '.$NextFreeVirtualPort.' profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                        'onu-vlan ethuni eth_0/1 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/2 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/3 up-mode transparent down-mode transparent',
                        'onu-vlan ethuni eth_0/4 up-mode transparent down-mode transparent',
                        'dhcp-ip ethuni eth_0/1 from-internet',
                        'dhcp-ip ethuni eth_0/2 from-internet',
                        'dhcp-ip ethuni eth_0/3 from-internet',
                        'dhcp-ip ethuni eth_0/4 from-internet',
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                    ];
                 
                    foreach ($Trunk as $value) 
                    {
                        $commandArray    [] = 'switchport vlan '.trim((int)$value).' tag vport 1';
                    }
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true);

                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;
                }
                else
                {
                    return response()->json(['error' => 'ვერ მოხერხდა ონუს რეჟიმის '.$MODE.' ამოცნობა']);       
                }
            }
            else if($PortCount == 1)
            {
                if($MODE == 'ROUTER')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu '.$NextFreeVirtualPort.' profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit'
                    ];
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); //  პროფილებში ჩასმა
             
                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }

                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,false);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;
                }
                else if($MODE == 'BRIDGE')
                { 
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu '.$NextFreeVirtualPort.' profile line '.$LINE.' remote '.$SERVICE, 
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                        'dhcp-ip ethuni eth_0/1  from-internet',
                        'exit',
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა

                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }

                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,false);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;

                }
                else if($MODE == 'BRIDGSTER') 
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu '.$NextFreeVirtualPort.' profile line '.$LINE.' remote '.$SERVICE, 
                        'exit',
                        'pon-onu-mng gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                        'onu-vlan ethuni eth_0/1 up-mode add up-prio 0 up-vid '.trim((int)$Port1Vlan).'  down-mode untag',
                        'dhcp-ip ethuni eth_0/1  from-internet',
                        'ip-host 1 dhcp-enable true ping-response true traceroute-response true',
                        'exit'
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა

                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება

                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,true);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;
                }
                else if($MODE == 'TRUNK')
                {
                    $commandArray = 
                    [
                        'ena',   
                        'AIRLINK2014',
                        'conf t',
                        'interface gpon-olt_'.trim(self::Pon_Port($ifIndex)[1]), 
                        'onu '.$NextFreeVirtualPort.' profile line '.$LINE.' remote '.$SERVICE, 
                        'exit'
                    ];
                 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); // ონუს ლან პორტების ვილანში ჩასმა

                    $VlansFromServiceProfile = 
                    [
                        'ena',
                        'AIRLINK2014',                
                        'conf t',
                        // 'show pon onu-profile gpon remote ' .strtolower($SERVICE). ' cfg',
                        'show pon onu-profile gpon remote ' .$SERVICE. ' cfg',
                    ];
                    
                    $Res = sshModel::SSH($ip,22,$ssHUser,$sshPass,$VlansFromServiceProfile);  // ვილანების გაგება
                    
                    $FilteredArray = [
                        'ena',
                        'AIRLINK2014',  
                        'conf t',
                        'interface gpon-onu_'.trim(self::Pon_Port($ifIndex)[1]).':'.$NextFreeVirtualPort,
                    ];

                    foreach ($Res as $key => $value) 
                    {
                        if (strpos($value, 'flow 1 priority 0 vid') !== false)
                        {
                            $parts = explode('flow 1 priority 0 vid', trim($value));       
                            $value = trim(end($parts));
                            $FilteredArray[] = 'switchport vlan '.trim($value).' tag vport 1';
                        }
                    }
 
                    sshModel::SSH($ip,22,$ssHUser,$sshPass,$FilteredArray,true);  // ვილანების მითითება 

                    $html ['ifindex'] = $ifIndex.'.'.$NextFreeVirtualPort;
                    return $html;

                }
                else
                {
                    return response()->json(['error' => 'ვერ მოხერხდა ონუს რეჟიმის '.$MODE.' ამოცნობა']);       
                }
            }
            else
            {
                return response()->json(['error' => 'ვერ მოხერხდა ონუს პორტის რაოდენობის დადგენა']);   
            }

        }
        else
        {
            return response()->json(['error' => 'ვერ დადგინდა ონუს რაოდენობა პონზე '.$TotalOnu.'  პონის ინდექსი  '.$ifIndex]);   
        }

    }

    static public function ONT_INFO_BY_IFINDEX($ip,$ifIndex,$read)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $OnuDesc = '';
        try {
                $OnuDesc  = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$ifIndex, TRUE);
                $OnuDesc  = str_replace("$$$$", "", $OnuDesc);
                $OnuDesc  = str_replace("STRING: ", "", $OnuDesc);
                $OnuDesc  = str_replace("\"", "", $OnuDesc);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $key  = explode('.',$ifIndex);      
        $Gpon = ZTE::Pon_Port($key[0]);
        $Port =  trim($key[1]);        

        $html ['ifIndex']     = $ifIndex;
        $html ['description'] = $OnuDesc;
        $html ['ponPort']     = $Gpon[1].':'.$Port;      
        
        
        try {
            $Dbm = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$ifIndex, TRUE);
            $RealDbm = '';
            foreach ($Dbm as $key2 => $data) 
            {   
                if (strpos($data, ':') !== false)
                {
                    $data    = explode(':', $data);  
                    if( trim($data[1]) > 30000 &&  trim($data[1]) != 65535 )
                    { 
                        $RealDbm = (trim($data[1]) - 65536) *0.002-30;
                        break; 
                    }
                    else
                    {
                        $RealDbm = trim($data[1]) *0.002-30;
                        break; 
                    }      
                   
                }
                else 
                {    
                    if( trim($data) > 30000 &&  trim($data) != 65535)
                    {
                        $RealDbm = (trim($data) - 65536) *0.002-30;
                        break; 
                    }
                    else
                    {
                        $RealDbm = trim($data) *0.002-30;
                        break; 
                    }
                }      
            }
            $html ['dbm'] = round($RealDbm, 2);
        }catch (\Exception $e){$html ['dbm'] = '';}    

        try {
                    $Reason        = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.2.1.7.".$ifIndex, TRUE);
                    $valueReason = '';
                    foreach ($Reason as $keyReason => $valueReason) 
                    {
                        $valueReason = str_replace('INTEGER: ','',$valueReason);
                        $valueReason = str_replace(' ','',$valueReason);
                        $valueReason = str_replace("\"",'',$valueReason);
                        $valueReason = trim($valueReason);

                        if($valueReason == 1)$valueReason = 'Unknown';
                        else if($valueReason == 2)$valueReason = 'LOS';
                        else if($valueReason == 3)$valueReason = 'LOSi';
                        else if($valueReason == 4)$valueReason = 'LOFi';
                        else if($valueReason == 5)$valueReason = 'SFI';  
                        else if($valueReason == 7)$valueReason = 'LOAMi';
                        else if($valueReason == 9)$valueReason = 'DyingGasp';
                        else if($valueReason == 12)$valueReason = 'Manual Restart';
                        else if($valueReason == 13)$valueReason = 'Manual Shutdown';
                        else $valueReason = '-';                                
                    }
            $html ['Reason'] = $valueReason;
        }catch (\Exception $e){$html ['Reason'] = '';}    

        try {
                $StatusOnu     = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$ifIndex,TRUE);
                $Status = '';
                foreach ($StatusOnu as $keyx => $dataStatus) 
                {
                    if (strpos($dataStatus, ':') !== false)
                    {
                        $data   = explode(':', $dataStatus);  
                        $Status = trim($data[1]);  
                        break;
                    }
                    else 
                    {    
                        $Status = trim($dataStatus);  
                        break;
                    }
                }

                if ($Status == '0')$Status = 'Logging';
                else if ($Status == '1')$Status = 'Los';
                else if ($Status == '2')$Status = 'syncMib';
                else if ($Status == '3')$Status = 'Working';
                else if ($Status == '4')$Status = 'Dyinggasp';
                else if ($Status == '5')$Status = 'AuthFailed';
                else if ($Status == '6')$Status = 'Offline';
                else $Status = 'Unknow';

                $html ['StatusOnu'] = $Status;
        }catch (\Exception $e){$html ['StatusOnu'] = '';}    
  
        try {
                $Type  = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.9.".$ifIndex."", TRUE);   
                $TypeX  = '';   
                $TypeX  = str_replace("STRING: ", "", $Type);
                $TypeX  = str_replace("\"", "", $TypeX);
                $html ['Type'] = $TypeX;
        }catch (\Exception $e){$html ['Type'] = '';}    

        try {
                $Vendor   = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.1.".$ifIndex."", TRUE);   
                $VendoroX  = '';   
                $VendoroX = str_replace("STRING: ", "", $Vendor);
                $VendoroX  = str_replace("\"", "", $VendoroX);
                $html ['Vendor'] = $VendoroX;
        }catch (\Exception $e){$html ['Vendor'] = '';}            
        
        return $html;
    }

    static public function ONT_PORT_BY_IFINDEX($ip,$ifIndex,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $OnuDesc = '';
        try {
                $OnuDesc  = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$ifIndex, TRUE);
                $OnuDesc  = str_replace("$$$$", "", $OnuDesc);
                $OnuDesc  = str_replace("STRING: ", "", $OnuDesc);
                $OnuDesc  = str_replace("\"", "", $OnuDesc);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
  
        try {
                $OnuSideLinks  = $snmp->walk("1.3.6.1.4.1.3902.1012.3.50.14.1.1.7.".$ifIndex, TRUE); 
        }catch (\Exception $e){$OnuSideLinks = '';}    

        $key                = explode('.',$ifIndex);
        $Fixed_Pon_Port     = explode('/',ZTE::Pon_Port($key[0])[1]);
        $pon_port_for_link  = ZTE::Pon_Port($key[0])[1];

        $html ['ifindex']     = $ifIndex;
        $html ['description'] = $OnuDesc;
        $html ['ponPort']     = $pon_port_for_link.':'.$key[1];


        foreach ($OnuSideLinks as $keyZ => $value) 
        {
            $value  = str_replace("INTEGER: ", "", $value);
            $State = '-';
            if($value == 1)     $State = 'Link Down';
            else if($value == 2)$State = 'Half-10';
            else if($value == 3)$State = 'Full-10';
            else if($value == 4)$State = 'Half-100';
            else if($value == 5)$State = 'Full-100';
            else if($value == 6)$State = 'Full-1000';

            $AdminState = '';
            try {
                    $AdminState  = $snmp->get("1.3.6.1.4.1.3902.1012.3.50.14.1.1.5.".$ifIndex.'.'.$keyZ, TRUE); 
                    $AdminState  = str_replace("INTEGER: ", "", $AdminState); 
            }catch (\Exception $e){/**/}    

            $Duplex = '';
            try {
                    $Duplex  = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.14.1.1.3.".$ifIndex.'.'.$keyZ, TRUE); 
                    $Duplex  = str_replace("INTEGER: ", "", $Duplex);  
                    if($Duplex == 1)
                    {
                        $Duplex = 'auto';
                    }
                    else if($Duplex == 2)
                    {
                        $Duplex = 'half10';
                    }
                    else if($Duplex == 3)
                    {
                        $Duplex = 'full10';
                    }
                    else if($Duplex == 4)
                    {
                        $Duplex = 'half100';
                    }
                    else if($Duplex == 5)
                    {
                        $Duplex = 'full100';
                    }
                    else if($Duplex == 6)
                    {
                        $Duplex = 'full1000';
                    }
                    else
                    {
                        $Duplex = '-';
                    }
            }catch (\Exception $e){/**/}    

             
            $item = [];
            $item ['PortIndex']      = $keyZ;
            $item ['State']          = $State;
            $item ['AdminState']     = $AdminState;
            $item ['Duplex']         = $Duplex;
            $html["PortList_$keyZ"]  = $item; 
        }


        return $html;
    }

    static public function ONT_MAC_BY_IFINDEX($ip,$ifIndex,$read,$write)
    {
        $html = [];
        $iface = [];
        $html ['shutdown'] = 0;

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $OnuDesc = '';
        try {
                $OnuDesc  = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$ifIndex, TRUE);
                $OnuDesc  = str_replace("$$$$", "", $OnuDesc);
                $OnuDesc  = str_replace("STRING: ", "", $OnuDesc);
                $OnuDesc  = str_replace("\"", "", $OnuDesc);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
  
        $key                = explode('.',$ifIndex);
        $Fixed_Pon_Port     = explode('/',ZTE::Pon_Port($key[0])[1]);
        $pon_port_for_link  = ZTE::Pon_Port($key[0])[1];
        $FinaLpORT          = $pon_port_for_link.':'.$key[1];

        $html ['ifindex']     = $ifIndex;
        $html ['Description'] = $OnuDesc;
        $html ['OnuPort']     = $pon_port_for_link.':'.$key[1];


        if($ifIndex)     
        { 
            try {
                  $Macs = $snmp->walk(".1.3.6.1.4.1.3902.1015.6.1.3.1.5.1" , TRUE);       
            } 
            catch (\Exception $e){$Macs = '';} 
          
            $FindMac = false;
            if($Macs)
            { 
                foreach ($Macs as $keyZ => $Macs_value) 
                {                                                
                    $FlexEdKey  = explode('.',$keyZ);                                           
                    $Ifindex2   = trim($FlexEdKey[0]);                                                                                   
                    if(trim(ZTE::port_oid_to_if_convert($Ifindex2)) == trim($ifIndex) && strlen($Ifindex2) == 10) // ეს 10 საეჭვოა
                    {                                                                            
                        $FindMac = true;
                        $Recived_Pon_Port = ZTE::port_oid_to_if_convert($Ifindex2); 
                        $Vlan             = $FlexEdKey[1];
                        $RMac             = sprintf("%02X", $FlexEdKey[2]).':'.sprintf("%02X", $FlexEdKey[3]).':'.sprintf("%02X", $FlexEdKey[4]).':'.sprintf("%02X", $FlexEdKey[5]).':'.sprintf("%02X", $FlexEdKey[6]).':'.sprintf("%02X", $FlexEdKey[7]);
                        $Vendoor          = ZTE::MacFind_SNMP(($RMac));
                                                                                            
                        $item = [];
                        $item ['Recived_Pon_Port']      = $Recived_Pon_Port;
                        $item ['Vlan']                  = $Vlan;
                        $item ['RMac']                  = $RMac;
                        $item ['Vendoor']               = $Vendoor;
                        $html ['shutdown']              = 1;
                        $html["PortList_$FlexEdKey[1]"] = $item; 
                    }
                }
            } 
            if(!$FindMac)
            {
                $credentials = DB::table('devices')->where('Address',$ip)->first();

                $commandArray = 
                [
                    'ena',   
                    'AIRLINK2014',
                    'show mac-real-time gpon onu gpon-onu_'.trim($FinaLpORT)
                ];

                $Response  = sshModel::SSH_CUSTOM($ip,22,$credentials->Username,$credentials->Pass,$commandArray,true); 
            
                if(!empty($Response))
                {
                    foreach ($Response as $key => $value) 
                    {
                        $value = array_filter(explode(' ', $value));
                        $value = array_values($value);

                        $item = [];
                        $item ['Recived_Pon_Port']      = $FinaLpORT;
                        $item ['Vlan']                  = $value[1];
                        $item ['RMac']                  = ZTE::format_mac_address($value[0]);
                        $item ['Vendoor']               = ZTE::MacFind_SNMP(($value[0]));  
                        $html ['shutdown']              = 1;
                        $html ["PortList_$FlexEdKey[1]"] = $item; 
                    }
                }   
            }            
        }


        return $html;
    }

    static public function ZTE_FAKE_PORT_DISPLAY($ip,$read,$write,$ifIndex,$macSN,$NextFreeVirtualPort)
    {
        $html = [];
        $OnuPorts = 0;
        $IsDataExist = false;
        $snmp                    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp->oid_output_format = SNMP_OID_OUTPUT_NUMERIC;
        $snmp->valueretrieval    = SNMP_VALUE_PLAIN;
          
        try{
             $OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$ifIndex.'.'.$NextFreeVirtualPort, TRUE);
             foreach ($OnuDesc as $key => $value) 
             {            
                    $key = explode('.1.3.6.1.4.1.3902.1012.3.28.1.1.3.', $key);
                    $key = $key[1];

                    $value     = str_replace("$$$$", "", $value);
                    $value     = str_replace("\"", "", $value);
                    $value     = str_replace("STRING: ", "", $value);
                    $OrigKey   =  $key; 
                 
                    $OnuSideLinks  = $snmp->walk("1.3.6.1.4.1.3902.1012.3.50.14.1.1.7.".$key, TRUE);  
                     
                    $key                = explode('.',$key);
                    $pon_port_for_link  = self::Pon_Port($key[0]);
                
                    
                    foreach ($OnuSideLinks as $keyZ => $value) 
                    {
                         
                        $value  = str_replace("INTEGER: ", "", $value);
                        $State = '';
                        if($value == 1)     $State = 'Link Down';
                        else if($value == 2)$State = 'Half-10';
                        else if($value == 3)$State = 'Full-10';
                        else if($value == 4)$State = 'Half-100';
                        else if($value == 5)$State = 'Full-100';
                        else if($value == 6)$State = 'Full-1000';
                        else if($value == 65535)continue; //თუ რაღაც ჩეპეა და შეეშალა ინსტალის დროს ონუს ტიპი, დამალოს არარსებული პორტები
               
                        if(!empty($value))$IsDataExist = true;
         
                        $OnuPorts++;
                        $Duplex  = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.14.1.1.3.".$OrigKey.'.'.$keyZ, TRUE); 
                        $Duplex  = str_replace("INTEGER: ", "", $Duplex);  
                         
                        if($Duplex == 1)
                        {
                            $Duplex = 'auto';
                        }
                        else if($Duplex == 2)
                        {
                            $Duplex = 'half10';
                        }
                        else if($Duplex == 3)
                        {
                            $Duplex = 'full10';
                        }
                        else if($Duplex == 4)
                        {
                            $Duplex = 'half100';
                        }
                        else if($Duplex == 5)
                        {
                            $Duplex = 'full100';
                        }
                        else if($Duplex == 6)
                        {
                            $Duplex = 'full1000';
                        }
                        else
                        {
                            $Duplex = '-';
                        }

                        $item = [];
                        $item ['PonPort']   = $pon_port_for_link[1];
                        $item ['State']     = $State;
                        $item ['Duplex']    = $Duplex;
                        $item ['OnuPort']   = $keyZ;
                        $item ['Link']      = $value;
                        $html['Links_'.$keyZ] = $item;

                    }          
             }

        }catch (\Exception $e){}

        $html ['Serial']    = $macSN;
        $html ['PortCount'] = $OnuPorts;
        if($IsDataExist)return  $html;
        else return false;
    }

    static public function Bridge4PortVlans($ip,$read,$write,$ssHUser,$sshPass,$profile)
    {
        $snmp  = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $Vlans = $snmp->walk("1.3.6.1.4.1.3902.1015.20.2.1.2", TRUE);  

        $commandArray = [
            'ena',
            'AIRLINK2014',  
            'conf t',
            'show pon onu-profile gpon remote ' . $profile . ' cfg',
        ];
        
        $Res =  sshModel::SSH($ip,22,$ssHUser,$sshPass,$commandArray,true); //  პროფილის წაკითხვა და ვილანების ამოღება ჩასმა
        $FilteredArray = [];
        if($Res)
        {
            foreach ($Res as $key => $value) 
            {
                if (strpos($value, 'flow 1 priority 0 vid') !== false)
                {
                    $parts = explode('flow 1 priority 0 vid', trim($value));       
                    $value = trim(end($parts));

                    $VlanName = '';
                    foreach ($Vlans as $keyz => $valueZ) 
                    {
                        $valueZ = trim(str_replace('STRING: ','',$valueZ));
                        $valueZ = trim(str_replace("\"",'',$valueZ));

                        if($keyz == $value)
                        $VlanName  = $valueZ;
                    }
 
                    $item = [];
                    $item ['value']  = trim($value);
                    $item ['name']   = trim($VlanName);
                    $FilteredArray['vlan_'.$key] =  $item;
                }
            }

            return response()->json($FilteredArray);      
        }
        else
        {
            return response()->json(['error' => 'ვერ მოხერხდა '.$profile.' სერვის პროფილიდან ვილანების ამოღება']);
        } 
    }

    static public function LINE_PROFILE_LIST($ip,$read)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $LineProfiles = '';
        try{    
                $LineProfiles = $snmp->walk("1.3.6.1.4.1.3902.1015.1010.5.54.1.1.4.2.1", TRUE); 

                foreach ($LineProfiles as $key => $value) 
                {
                    $html ['LINE_PROFILES_'.$key] = self::convertDecimalToAscii_ZTE($key);
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
                $ServiceProfiles = $snmp->walk("1.3.6.1.4.1.3902.1015.1010.5.54.1.1.4.2.2", TRUE); 

                foreach ($ServiceProfiles as $key => $value) 
                {
                    $html ['SERVICE_PROFILES_'.$key] = self::convertDecimalToAscii_ZTE($key);
                }


        }catch (\Exception $e){ return response()->json(['error' => 'ვერ მოხერხდა SERVICE პროფილების სიის წამოღება ოელტედან']);  }

        return $html;
    }

    static public function MGMT_LIST($ip,$read)
    {
        $html = [];

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $MGMT = '';
        try{    
                $MGMT = $snmp->walk("1.3.6.1.4.1.3902.1015.1010.5.2.1.13", TRUE); 

                $uniqueArray = array();
                foreach ($MGMT as $key => $value) 
                {
                    if (!in_array(self::convertDecimalToAscii_ZTE_ONU_TYPE($key), $uniqueArray))
                    {
                        $uniqueArray[] = self::convertDecimalToAscii_ZTE_ONU_TYPE($key);
                    }
                }

                foreach ($uniqueArray as $key => $value) 
                {   
                    if (strpos($value, 'ZTE-') !== false)continue;
                    else $html ['MGMT_'.$key] = $value;
                }

        }catch (\Exception $e){ return response()->json(['error' => 'ვერ მოხერხდა ონუს მენეჯმენტ ტიპის სიის წამოღება ოელტედან']);  }

        return $html;
    }

    static public function VlanList($ip,$read)
    {
        $html = [];
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        $Vlans = '';
        try{    
                $Vlans = $snmp->walk("1.3.6.1.4.1.3902.1015.20.2.1.2", TRUE); 
                foreach ($Vlans as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));
                    $value = trim(str_replace("\"",'',$value));
            
                    $item = [];
                    $item ['key']   = $key;
                    $item ['value'] = $value;
                    $html ['VlanList_'.$key] = $item;
                }
       

        }catch (\Exception $e){ return response()->json(['error' => 'ვერ მოხერხდა ოელტედან ვილანების სიის წამოღება']);  }

        return $html;
    }

    static public function Pon_Port($value)
    {
        $Data = [
            [268501248, '1/1/1'],
            [268501504, '1/1/2'],
            [268501760, '1/1/3'],
            [268502016, '1/1/4'],
            [268502272, '1/1/5'],
            [268502528, '1/1/6'],
            [268502784, '1/1/7'],
            [268503040, '1/1/8'],
            [268503296, '1/1/9'],
            [268503552, '1/1/10'],
            [268503808, '1/1/11'],
            [268504064, '1/1/12'],
            [268504320, '1/1/13'],
            [268504576, '1/1/14'],
            [268504832, '1/1/15'],
            [268505088, '1/1/16'],
        
            
            [268566784, '1/2/1'],
            [268567040, '1/2/2'],
            [268567296, '1/2/3'],
            [268567552, '1/2/4'],
            [268567808, '1/2/5'],
            [268568064, '1/2/6'],
            [268568320, '1/2/7'],
            [268568576, '1/2/8'],
            [268568832, '1/2/9'],
            [268569088, '1/2/10'],
            [268569344, '1/2/11'],
            [268569600, '1/2/12'],
            [268569856, '1/2/13'],
            [268570112, '1/2/14'],
            [268570368, '1/2/15'],
            [268570624, '1/2/16']
        
            
        ];
        
        $Array = array();
        foreach ($Data as $item) 
        {
            if ($value == $item[0]) 
            {
                $Array = array($item[0], $item[1]);
                break;  
            }
        }

        return $Array;
    }

    static public function generateRandomHexString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }

    static public  function convertDecimalToAscii_ZTE($decimalValues) 
    {
        // Split the decimal values into an array
        $values = explode('.', $decimalValues);

        // Convert each decimal value to its ASCII equivalent
        $asciiString = '';
        $FirstLineMiss = false;
        foreach ($values as $decimal) 
        {
            if($FirstLineMiss)$asciiString .= chr($decimal);

            $FirstLineMiss = true;
        }

        return $asciiString;
    }

    static public  function convertDecimalToAscii_ZTE_ONU_TYPE($decimalValues) 
    {
        
        $Beta = explode('.0.',$decimalValues);
        $decimalValues = $Beta[0];

        $values = explode('.', $decimalValues);

        $asciiString = '';

        foreach ($values as $decimal) 
        {
            $asciiString .= chr($decimal);
        }

        return $asciiString;
    }
}
