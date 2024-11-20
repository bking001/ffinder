<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use App\Models\sshModel;
use Illuminate\Support\Facades\DB; 

class ZTE extends Model
{
    use HasFactory;

    static public function Client_Side_OnuInfo($ip,$read,$write,$user)
    {
        $html = [];
        $html ['clone'] = '';
         
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
            
        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $DescriptionClone = 0;
        $Real_Desc_Key = 0;
        if(isset($OnuDesc))
        {
            foreach ($OnuDesc as $key => $value) 
            {
                $oNUkEYfORrESTART = $key;
                $value  = str_replace("$$$$", "", $value);
                $value  = str_replace("STRING: ", "", $value);
                $value  = str_replace("\"", "", $value);
                if (strpos($value, $user) !== false)
                {
                    $TempKey = $key;
                    $key  = explode('.',$key);      
                    $Gpon = ZTE::Pon_Port($key[0]);
                    $Port =  trim($key[1]);
    
    
    
                    $Real_Desc_Key++;
                    $DescriptionClone++;
                    $html ['ifIndex']     = $oNUkEYfORrESTART;
                    $html ['description'] = $value;
                    $html ['ponPort']     = $Gpon[1].':'.$Port;
    
      
    
                    try {
                            $Dbm = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".trim($key[0]).'.'.trim($key[1]), TRUE);
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
                                $Reason        = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.2.1.7.".trim($key[0]).'.'.trim($key[1]), TRUE);
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
                            $StatusOnu     = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".trim($key[0]).'.'.trim($key[1]),TRUE);
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
                            $Type  = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.9.".$TempKey."", TRUE);   
                            $TypeX  = '';   
                            $TypeX  = str_replace("STRING: ", "", $Type);
                            $TypeX  = str_replace("\"", "", $TypeX);
                            $html ['Type'] = $TypeX;
                    }catch (\Exception $e){$html ['Type'] = '';}    
    
                    try {
                            $Vendor   = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.1.".$TempKey."", TRUE);   
                            $VendoroX  = '';   
                            $VendoroX = str_replace("STRING: ", "", $Vendor);
                            $VendoroX  = str_replace("\"", "", $VendoroX);
                            $html ['Vendor'] = $VendoroX;
                    }catch (\Exception $e){$html ['Vendor'] = '';}                    
                     
                       
    
                }
            }
        }
 

        if($DescriptionClone > 1)
        {
            $html ['clone'] = 'ეს დესქრიფშენი გაწერილია '.$DescriptionClone.' - ონუზე ';
        }
        if(empty($Real_Desc_Key))
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }       
                 
        return $html;
    }

    static public function Client_Side_OnuPorts($ip,$read,$write,$user)
    {
        $html = [];
        $html ['clone'] = '';
        $Real_Desc_Key  = 0;
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
            
        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
         
        if(is_array($OnuDesc))
        {
            foreach ($OnuDesc as $key => $value) 
            {            
                    $value  = str_replace("$$$$", "", $value);
                    $value  = str_replace("\"", "", $value);
                    $value  = str_replace("STRING: ", "", $value);
                    $Ex_ab_nom = $value;
                    $OrigKey =  $key; 
    
                if (strpos($value, $user) !== false) 
                {   
                    $Real_Desc_Key++;          
                    $OnuSideLinks = '';
                    try {
                            $OnuSideLinks  = $snmp->walk("1.3.6.1.4.1.3902.1012.3.50.14.1.1.7.".$key, TRUE); 
                    }catch (\Exception $e){/**/}    
    
    
                    $key                = explode('.',$key);
                    $Fixed_Pon_Port     = explode('/',ZTE::Pon_Port($key[0])[1]);
                    $pon_port_for_link  = ZTE::Pon_Port($key[0])[1];
    
                    $html ['ifindex']     = $OrigKey;
                    $html ['description'] = $value;
                    try{
                        $html ['ponPort']     = $pon_port_for_link.':'.$key[1];
                    }catch (\Exception $e){$html ['ponPort'] = '-';}    
                     
    
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
                                $AdminState  = $snmp->get("1.3.6.1.4.1.3902.1012.3.50.14.1.1.5.".$OrigKey.'.'.$keyZ, TRUE); 
                                $AdminState  = str_replace("INTEGER: ", "", $AdminState); 
                        }catch (\Exception $e){/**/}    
    
                        $Duplex = '';
                        try {
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
                        }catch (\Exception $e){/**/}    
    
                         
                        $item = [];
                        $item ['PortIndex']      = $keyZ;
                        $item ['State']          = $State;
                        $item ['AdminState']     = $AdminState;
                        $item ['Duplex']         = $Duplex;
                        $html["PortList_$keyZ"]  = $item; 
                    }
                }
            }
    
        }
        else 
        {
            return response()->json(['error' => 'No response Try Again']);
        }
 
        if(empty($Real_Desc_Key))
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }              
                 
        return $html;
    }
    
    static public function Client_Side_OnuMacs($ip,$read,$write,$user)   // 2810740   2819335
    {
        $html = [];$iface = [];
        $html ['clone'] = '';
        $html ['shutdown'] = 0;
        $User_Not_Exist    = 0;
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);

 
        try {
                $OnuDesc    = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3", TRUE);  
        } 
        catch (\Exception $e){$OnuDesc = '';}
 
        foreach ($OnuDesc as $key => $value) 
        {           
            $value  = str_replace("$$$$", "", $value);
            $value  = str_replace("\"", "", $value);
            $value  = str_replace("STRING: ", "", $value);
            $Real_Key = explode('.',$key); 

            if (strpos($value, $user) !== false) 
            {
                $User_Not_Exist = 1;
                $Fixed_Pon_Port  = ZTE::Pon_Port(trim($Real_Key[0]));
                $FinaLpORT       = $Fixed_Pon_Port[1].':'.$Real_Key[1];   

                $html ['Description']  = $value;
                $html ['OnuPort']      = $FinaLpORT;
  
                
                if($FinaLpORT)     
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
                            $Ifindex    = trim($FlexEdKey[0]);                                                                  
                            if(trim(ZTE::port_oid_to_if_convert($Ifindex)) == trim($FinaLpORT) && strlen($Ifindex) == 10) // ეს 10 საეჭვოა
                            {                                                                            
                                $FindMac = true;
                                $Recived_Pon_Port = ZTE::port_oid_to_if_convert($Ifindex); 
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
                                $item ['RMac']                  = self::format_mac_address($value[0]);
                                $item ['Vendoor']               = ZTE::MacFind_SNMP(($value[0]));  
                                $html ['shutdown']              = 1;
                                $html ["PortList_$FlexEdKey[1]"] = $item; 
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

        return  $html;
    }

    static public function ClientSidePonSelect($ip,$read) 
    {
        $html = []; 
        $Select_Pon_List = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);

        try {$Select_Pon_List = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.13.1.1.2", TRUE); } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($Select_Pon_List as $key => $value) 
        {
            $item = [];
            $item['PonName']       = ZTE::Pon_Port($key)[1];
            $item['PonIndex']      = $key;
            $html["PonList_$key"]  = $item;       
        }
        return $html;
    }
     
    static public function ClientSidePonData($ip,$pon,$read,$write) 
    {   
        $html = []; 
        $PonCoordinates = [];
        $OnuDesc = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
   
        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$pon."", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        if(empty($OnuDesc))return response()->json(['error' => 'Pon Is Empty']);

 
        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;

        $AllArray = [];
        foreach ($OnuDesc as $key => $value) 
        { 
            $Description = str_replace('STRING: ','',$value);
            $Description = str_replace("\"",'',$Description);
            $Description = trim($Description);
            $AllArray[$key]['Description'] = $Description;

            $Admin = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.1.".$pon.".".$key, TRUE); 
            $Admin = str_replace('INTEGER: ','',$Admin);
            $Admin = str_replace(' ','',$Admin);
            $Admin = str_replace("\"",'',$Admin);
            $AllArray[$key]['Admin'] = trim($Admin);

            $dbm = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$pon.".".$key.'.1', TRUE); 
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
            $AllArray[$key]['dbm'] = round($dbm,2);

            $StatusOnu = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$pon.".".$key, TRUE);
            $StatusOnu = str_replace('INTEGER: ','',$StatusOnu);
            $StatusOnu = str_replace("\"",'',$StatusOnu);
            $StatusOnu = trim($StatusOnu);
 
            $xxx     = 'Unknow';
            if ($StatusOnu == '0')$xxx = 'Logging';
            else  if ($StatusOnu == '1'){$xxx = 'Los';$TotalWireDown +=1;$TotalOffline +=1;}
            else  if ($StatusOnu == '2'){$xxx = 'syncMib';$TotalOffline +=1;}
            else  if ($StatusOnu == '3'){$xxx = 'Working';$TotalOnline +=1;}
            else  if ($StatusOnu == '4'){$xxx = 'Dyinggasp';$TotalPowerOff +=1;$TotalOffline +=1;}
            else  if ($StatusOnu == '5'){$xxx = 'AuthFailed';$TotalOffline +=1;}
            else  if ($StatusOnu == '6'){$xxx = 'Offline';$TotalOffline +=1;}
            $AllArray[$key]['StatusOnu'] =  $xxx;

            $valueSN = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.5.".$pon.".".$key, TRUE);
            $valueSN = str_replace('Hex-STRING: ','',$valueSN);
            $valueSN = str_replace('STRING: ','',$valueSN);
            $valueSN = str_replace(' ','',$valueSN);
            $valueSN = str_replace("\"",'',$valueSN);

            if(strlen($valueSN) < 10 )
            {  
                $valueSN  = bin2hex($valueSN);
            }

            $SN_Fixed   = substr($valueSN, 0, 8);
            $SN_Fixed   = hex2bin($SN_Fixed);

            $AllArray[$key]['SN'] = $valueSN;
            $AllArray[$key]['FULL_SN'] = $SN_Fixed.substr($valueSN, 8, 16);

            $valueType = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.9.".$pon.".".$key, TRUE);
            $valueType = str_replace('STRING: ','',$valueType);
            $valueType = str_replace(' ','',$valueType);
            $valueType = str_replace("\"",'',$valueType);
            $AllArray[$key]['Type'] = $valueType;

            $valueVendor = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.1.".$pon.".".$key, TRUE);
            $valueVendor = str_replace('STRING: ','',$valueVendor);
            $valueVendor = str_replace(' ','',$valueVendor);
            $valueVendor = str_replace("\"",'',$valueVendor);
            $AllArray[$key]['Vendor'] = $valueVendor;

            $valueReason = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.7.".$pon.".".$key, TRUE);
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
            $AllArray[$key]['Reason'] = $valueReason;

            $valueUptime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.5.".$pon.".".$key, TRUE);
            $valueUptime = str_replace('STRING: ','',$valueUptime);
            $valueUptime = str_replace("\"",'',$valueUptime);
            $valueUptime = trim($valueUptime);
            $AllArray[$key]['TitleUptime'] = $valueUptime;
            $valueUptime = ZTE::calculateUptime($valueUptime);
            $AllArray[$key]['Uptime'] = $valueUptime;
            

            $valueDowntime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.6.".$pon.".".$key, TRUE);
            $valueDowntime = str_replace('STRING: ','',$valueDowntime);
            $valueDowntime = str_replace("\"",'',$valueDowntime);
            $valueDowntime = trim($valueDowntime);
            $AllArray[$key]['TitleDowntime'] = $valueDowntime;
            $valueDowntime = ZTE::calculateUptime($valueDowntime);
            $AllArray[$key]['Downtime'] = $valueDowntime;
        }



        foreach ($AllArray as $key => $value) 
        {
            $TotalOnu++;
            $OnuDescFixedValue;
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("$$$$", "", $value);
            $OnuDescFixedValue  = str_replace("\"", "", $value);
            $Gpon = ZTE::Pon_Port($pon);
   
            $CoordOnuStatus = '';
            if (strpos($value['StatusOnu'], 'Working') !== false)
            {
                $CoordOnuStatus = 1;
            }
            else 
            {
                $CoordOnuStatus = 2;
            }      
            $PonCoordinates[] = $value['Description'].'|'.$pon.'.'.$key.'|'.str_replace(',',' ',$value['Uptime']).'|'.str_replace(',',' ',$value['Downtime']).'|'.$CoordOnuStatus;
        
 
            $item = [];
			$item['IfIndex']   	  	  = $pon.'.'.$key; 
			$item['PonPort']   	  	  = $Gpon[1].':'.$key;   
			$item['Description']	  = $value['Description'];   
			$item['Type'] 		 	  = $value['Type'];   
            $item['Vendor'] 		  = $value['Vendor'];
            $item['Admin'] 	 	      = $value['Admin'];  
			$item['MacOnu'] 	 	  = $value['SN'];  
            $item['FULL_SN'] 	 	  = $value['FULL_SN'];  
			$item['OnuOperateStatus'] = $value['StatusOnu'];
			$item['Deregreason'] 	  = $value['Reason'];
			$item['Onu_RX'] 	  	  = $value['dbm'];
			$item['Uptime'] 	  	  = $value['Uptime']; 
			$item['DownTime'] 	  	  = $value['Downtime'];
            $item['TitleUptime'] 	  = $value['TitleUptime'];
            $item['TitleDownTime'] 	  = $value['TitleDowntime'];

			$html["onu_num$key"] = $item;  	 
        }

        $html['PonName']  		= 'GPON '.$Gpon[1]; 
        $html['Online']  		= $TotalOnline; 
		$html['Offline']  		= $TotalOffline; 
		$html['PonAdmin'] 		= 'up'; 
		$html['TotalOnu'] 		= $TotalOnu; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }
         
    static public function ClientSidePonAllOnline($ip,$pon,$read,$write) 
    {   
        $html = []; 
        $PonCoordinates = [];
        $OnuDesc = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
   
        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$pon."", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        if(empty($OnuDesc))return response()->json(['error' => 'Pon Is Empty']);

 
        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;

        $AllArray = [];
        foreach ($OnuDesc as $key => $value) 
        { 
            $Description = str_replace('STRING: ','',$value);
            $Description = str_replace("\"",'',$Description);
            $Description = trim($Description);
            $AllArray[$key]['Description'] = $Description;

            $Admin = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.1.".$pon.".".$key, TRUE); 
            $Admin = str_replace('INTEGER: ','',$Admin);
            $Admin = str_replace(' ','',$Admin);
            $Admin = str_replace("\"",'',$Admin);
            $AllArray[$key]['Admin'] = trim($Admin);

            $dbm = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$pon.".".$key.'.1', TRUE); 
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
            $AllArray[$key]['dbm'] = round($dbm,2);

            $StatusOnu = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$pon.".".$key, TRUE);
            $StatusOnu = str_replace('INTEGER: ','',$StatusOnu);
            $StatusOnu = str_replace("\"",'',$StatusOnu);
            $StatusOnu = trim($StatusOnu);
 
            $xxx     = 'Unknow';
            if ($StatusOnu == '0')$xxx = 'Logging';
            else  if ($StatusOnu == '1'){$xxx = 'Los';$TotalWireDown +=1;$TotalOffline +=1;}
            else  if ($StatusOnu == '2'){$xxx = 'syncMib';$TotalOffline +=1;}
            else  if ($StatusOnu == '3'){$xxx = 'Working';$TotalOnline +=1;}
            else  if ($StatusOnu == '4'){$xxx = 'Dyinggasp';$TotalPowerOff +=1;$TotalOffline +=1;}
            else  if ($StatusOnu == '5'){$xxx = 'AuthFailed';$TotalOffline +=1;}
            else  if ($StatusOnu == '6'){$xxx = 'Offline';$TotalOffline +=1;}
            $AllArray[$key]['StatusOnu'] =  $xxx;

            $valueSN = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.5.".$pon.".".$key, TRUE);
            $valueSN = str_replace('Hex-STRING: ','',$valueSN);
            $valueSN = str_replace('STRING: ','',$valueSN);
            $valueSN = str_replace(' ','',$valueSN);
            $valueSN = str_replace("\"",'',$valueSN);

            if(strlen($valueSN) < 10 )
            {  
                $valueSN  = bin2hex($valueSN);
            }

            $SN_Fixed   = substr($valueSN, 0, 8);
            $SN_Fixed   = hex2bin($SN_Fixed);

            $AllArray[$key]['SN'] = $valueSN;
            $AllArray[$key]['FULL_SN'] = $SN_Fixed.substr($valueSN, 8, 16);

            $valueType = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.9.".$pon.".".$key, TRUE);
            $valueType = str_replace('STRING: ','',$valueType);
            $valueType = str_replace(' ','',$valueType);
            $valueType = str_replace("\"",'',$valueType);
            $AllArray[$key]['Type'] = $valueType;

            $valueVendor = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.1.".$pon.".".$key, TRUE);
            $valueVendor = str_replace('STRING: ','',$valueVendor);
            $valueVendor = str_replace(' ','',$valueVendor);
            $valueVendor = str_replace("\"",'',$valueVendor);
            $AllArray[$key]['Vendor'] = $valueVendor;

            $valueReason = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.7.".$pon.".".$key, TRUE);
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
            $AllArray[$key]['Reason'] = $valueReason;

            $valueUptime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.5.".$pon.".".$key, TRUE);
            $valueUptime = str_replace('STRING: ','',$valueUptime);
            $valueUptime = str_replace("\"",'',$valueUptime);
            $valueUptime = trim($valueUptime);  
            $AllArray[$key]['TitleUptime'] = $valueUptime;
            $valueUptime = ZTE::calculateUptime($valueUptime);
            $AllArray[$key]['Uptime'] = $valueUptime;


            $valueDowntime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.6.".$pon.".".$key, TRUE);
            $valueDowntime = str_replace('STRING: ','',$valueDowntime);
            $valueDowntime = str_replace("\"",'',$valueDowntime);
            $valueDowntime = trim($valueDowntime);
            $AllArray[$key]['TitleDowntime'] = $valueDowntime;
            $valueDowntime = ZTE::calculateUptime($valueDowntime);
            $AllArray[$key]['Downtime'] = $valueDowntime;
        }



        foreach ($AllArray as $key => $value) 
        {
            $TotalOnu++;
            $OnuDescFixedValue;
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("$$$$", "", $value);
            $OnuDescFixedValue  = str_replace("\"", "", $value);
            $Gpon = ZTE::Pon_Port($pon);
   
            if($value['StatusOnu'] == 'Working')
            {
                $CoordOnuStatus = '';
                if (strpos($value['StatusOnu'], 'Working') !== false)
                {
                    $CoordOnuStatus = 1;
                }
                else 
                {
                    $CoordOnuStatus = 2;
                }      
                $PonCoordinates[] = $value['Description'].'|'.$pon.'.'.$key.'|'.str_replace(',',' ',$value['Uptime']).'|'.str_replace(',',' ',$value['Downtime']).'|'.$CoordOnuStatus;
            
                $item = [];
                $item['IfIndex']   	  	  = $pon.'.'.$key; 
                $item['PonPort']   	  	  = $Gpon[1].':'.$key;   
                $item['Description']	  = $value['Description'];   
                $item['Type'] 		 	  = $value['Type'];   
                $item['Vendor'] 		  = $value['Vendor'];
                $item['Admin'] 	 	      = $value['Admin'];  
                $item['MacOnu'] 	 	  = $value['SN'];  
                $item['FULL_SN'] 	 	  = $value['FULL_SN'];  
                $item['OnuOperateStatus'] = $value['StatusOnu'];
                $item['Deregreason'] 	  = $value['Reason'];
                $item['Onu_RX'] 	  	  = $value['dbm'];
                $item['Uptime'] 	  	  = $value['Uptime']; 
                $item['DownTime'] 	  	  = $value['Downtime'];
                $item['TitleUptime'] 	  = $value['TitleUptime'];
                $item['TitleDownTime']    = $value['TitleDowntime'];
                $html["onu_num$key"] = $item;  
            }
 	 
        }

        $html['PonName']  		= 'GPON '.$Gpon[1]; 
        $html['Online']  		= $TotalOnline; 
		$html['Offline']  		= $TotalOffline; 
		$html['PonAdmin'] 		= 'up'; 
		$html['TotalOnu'] 		= $TotalOnu; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }
         
    static public function ClientSidePonAllOffline($ip,$pon,$read,$write) 
    {   
        $html = []; 
        $PonCoordinates = [];
        $OnuDesc = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
   
        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$pon."", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        if(empty($OnuDesc))return response()->json(['error' => 'Pon Is Empty']);

 
        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;

        $AllArray = [];
        foreach ($OnuDesc as $key => $value) 
        { 
            $Description = str_replace('STRING: ','',$value);
            $Description = str_replace("\"",'',$Description);
            $Description = trim($Description);
            $AllArray[$key]['Description'] = $Description;

            $Admin = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.1.".$pon.".".$key, TRUE); 
            $Admin = str_replace('INTEGER: ','',$Admin);
            $Admin = str_replace(' ','',$Admin);
            $Admin = str_replace("\"",'',$Admin);
            $AllArray[$key]['Admin'] = trim($Admin);

            $dbm = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$pon.".".$key.'.1', TRUE); 
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
            $AllArray[$key]['dbm'] = round($dbm,2);

            $StatusOnu = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$pon.".".$key, TRUE);
            $StatusOnu = str_replace('INTEGER: ','',$StatusOnu);
            $StatusOnu = str_replace("\"",'',$StatusOnu);
            $StatusOnu = trim($StatusOnu);
 
            $xxx     = 'Unknow';
            if ($StatusOnu == '0')$xxx = 'Logging';
            else  if ($StatusOnu == '1'){$xxx = 'Los';$TotalWireDown +=1;$TotalOffline +=1;}
            else  if ($StatusOnu == '2'){$xxx = 'syncMib';$TotalOffline +=1;}
            else  if ($StatusOnu == '3'){$xxx = 'Working';$TotalOnline +=1;}
            else  if ($StatusOnu == '4'){$xxx = 'Dyinggasp';$TotalPowerOff +=1;$TotalOffline +=1;}
            else  if ($StatusOnu == '5'){$xxx = 'AuthFailed';$TotalOffline +=1;}
            else  if ($StatusOnu == '6'){$xxx = 'Offline';$TotalOffline +=1;}
            $AllArray[$key]['StatusOnu'] =  $xxx;

            $valueSN = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.5.".$pon.".".$key, TRUE);
            $valueSN = str_replace('Hex-STRING: ','',$valueSN);
            $valueSN = str_replace('STRING: ','',$valueSN);
            $valueSN = str_replace(' ','',$valueSN);
            $valueSN = str_replace("\"",'',$valueSN);

            if(strlen($valueSN) < 10 )
            {  
                $valueSN  = bin2hex($valueSN);
            }

            $SN_Fixed   = substr($valueSN, 0, 8);
            $SN_Fixed   = hex2bin($SN_Fixed);

            $AllArray[$key]['SN'] = $valueSN;
            $AllArray[$key]['FULL_SN'] = $SN_Fixed.substr($valueSN, 8, 16);

            $valueType = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.9.".$pon.".".$key, TRUE);
            $valueType = str_replace('STRING: ','',$valueType);
            $valueType = str_replace(' ','',$valueType);
            $valueType = str_replace("\"",'',$valueType);
            $AllArray[$key]['Type'] = $valueType;

            $valueVendor = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.1.".$pon.".".$key, TRUE);
            $valueVendor = str_replace('STRING: ','',$valueVendor);
            $valueVendor = str_replace(' ','',$valueVendor);
            $valueVendor = str_replace("\"",'',$valueVendor);
            $AllArray[$key]['Vendor'] = $valueVendor;

            $valueReason = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.7.".$pon.".".$key, TRUE);
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
            $AllArray[$key]['Reason'] = $valueReason;

            $valueUptime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.5.".$pon.".".$key, TRUE);
            $valueUptime = str_replace('STRING: ','',$valueUptime);
            $valueUptime = str_replace("\"",'',$valueUptime);
            $valueUptime = trim($valueUptime);
            $AllArray[$key]['TitleUptime'] = $valueUptime;
            $valueUptime = ZTE::calculateUptime($valueUptime);
            $AllArray[$key]['Uptime'] = $valueUptime;


            $valueDowntime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.6.".$pon.".".$key, TRUE);
            $valueDowntime = str_replace('STRING: ','',$valueDowntime);
            $valueDowntime = str_replace("\"",'',$valueDowntime);
            $valueDowntime = trim($valueDowntime);
            $AllArray[$key]['TitleDowntime'] = $valueDowntime;
            $valueDowntime = ZTE::calculateUptime($valueDowntime);
            $AllArray[$key]['Downtime'] = $valueDowntime;
            
        }



        foreach ($AllArray as $key => $value) 
        {
            $TotalOnu++;
            $OnuDescFixedValue;
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("$$$$", "", $value);
            $OnuDescFixedValue  = str_replace("\"", "", $value);
            $Gpon = ZTE::Pon_Port($pon);
   
            if($value['StatusOnu'] == 'Working')
            {
                //
            }
            else
            {
                $CoordOnuStatus = '';
                if (strpos($value['StatusOnu'], 'Working') !== false)
                {
                    $CoordOnuStatus = 1;
                }
                else 
                {
                    $CoordOnuStatus = 2;
                }      
                $PonCoordinates[] = $value['Description'].'|'.$pon.'.'.$key.'|'.str_replace(',',' ',$value['Uptime']).'|'.str_replace(',',' ',$value['Downtime']).'|'.$CoordOnuStatus;
            
                $item = [];
                $item['IfIndex']   	  	  = $pon.'.'.$key; 
                $item['PonPort']   	  	  = $Gpon[1].':'.$key;   
                $item['Description']	  = $value['Description'];   
                $item['Type'] 		 	  = $value['Type'];   
                $item['Admin'] 	 	      = $value['Admin'];  
                $item['Vendor'] 		  = $value['Vendor'];
                $item['MacOnu'] 	 	  = $value['SN'];  
                $item['FULL_SN'] 	 	  = $value['FULL_SN'];  
                $item['OnuOperateStatus'] = $value['StatusOnu'];
                $item['Deregreason'] 	  = $value['Reason'];
                $item['Onu_RX'] 	  	  = $value['dbm'];
                $item['Uptime'] 	  	  = $value['Uptime']; 
                $item['DownTime'] 	  	  = $value['Downtime'];
                $item['TitleUptime'] 	  = $value['TitleUptime'];
                $item['TitleDownTime']    = $value['TitleDowntime'];
                $html["onu_num$key"] = $item;  
            }
 	 
        }

        $html['PonName']  		= 'GPON '.$Gpon[1]; 
        $html['Online']  		= $TotalOnline; 
		$html['Offline']  		= $TotalOffline; 
		$html['PonAdmin'] 		= 'up'; 
		$html['TotalOnu'] 		= $TotalOnu; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }
         
    static public function ClientSidePonAllWireDown($ip,$pon,$read,$write) 
    {   
        $html = []; 
        $PonCoordinates = [];
        $OnuDesc = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
   
        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$pon."", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        if(empty($OnuDesc))return response()->json(['error' => 'Pon Is Empty']);

 
        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;

        $AllArray = [];
        foreach ($OnuDesc as $key => $value) 
        { 
            $Description = str_replace('STRING: ','',$value);
            $Description = str_replace("\"",'',$Description);
            $Description = trim($Description);
            $AllArray[$key]['Description'] = $Description;

            $Admin = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.1.".$pon.".".$key, TRUE); 
            $Admin = str_replace('INTEGER: ','',$Admin);
            $Admin = str_replace(' ','',$Admin);
            $Admin = str_replace("\"",'',$Admin);
            $AllArray[$key]['Admin'] = trim($Admin);

            $dbm = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$pon.".".$key.'.1', TRUE); 
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
            $AllArray[$key]['dbm'] = round($dbm,2);

            $StatusOnu = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$pon.".".$key, TRUE);
            $StatusOnu = str_replace('INTEGER: ','',$StatusOnu);
            $StatusOnu = str_replace("\"",'',$StatusOnu);
            $StatusOnu = trim($StatusOnu);
 
            $xxx     = 'Unknow';
            if ($StatusOnu == '0')$xxx = 'Logging';
            else  if ($StatusOnu == '1'){$xxx = 'Los';$TotalWireDown +=1;$TotalOffline +=1;}
            else  if ($StatusOnu == '2'){$xxx = 'syncMib';$TotalOffline +=1;}
            else  if ($StatusOnu == '3'){$xxx = 'Working';$TotalOnline +=1;}
            else  if ($StatusOnu == '4'){$xxx = 'Dyinggasp';$TotalPowerOff +=1;$TotalOffline +=1;}
            else  if ($StatusOnu == '5'){$xxx = 'AuthFailed';$TotalOffline +=1;}
            else  if ($StatusOnu == '6'){$xxx = 'Offline';$TotalOffline +=1;}
            $AllArray[$key]['StatusOnu'] =  $xxx;

            $valueSN = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.5.".$pon.".".$key, TRUE);
            $valueSN = str_replace('Hex-STRING: ','',$valueSN);
            $valueSN = str_replace('STRING: ','',$valueSN);
            $valueSN = str_replace(' ','',$valueSN);
            $valueSN = str_replace("\"",'',$valueSN);

            if(strlen($valueSN) < 10 )
            {  
                $valueSN  = bin2hex($valueSN);
            }

            $SN_Fixed   = substr($valueSN, 0, 8);
            $SN_Fixed   = hex2bin($SN_Fixed);

            $AllArray[$key]['SN'] = $valueSN;
            $AllArray[$key]['FULL_SN'] = $SN_Fixed.substr($valueSN, 8, 16);

            $valueType = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.9.".$pon.".".$key, TRUE);
            $valueType = str_replace('STRING: ','',$valueType);
            $valueType = str_replace(' ','',$valueType);
            $valueType = str_replace("\"",'',$valueType);
            $AllArray[$key]['Type'] = $valueType;

            $valueVendor = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.1.".$pon.".".$key, TRUE);
            $valueVendor = str_replace('STRING: ','',$valueVendor);
            $valueVendor = str_replace(' ','',$valueVendor);
            $valueVendor = str_replace("\"",'',$valueVendor);
            $AllArray[$key]['Vendor'] = $valueVendor;

            $valueReason = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.7.".$pon.".".$key, TRUE);
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
            $AllArray[$key]['Reason'] = $valueReason;

            $valueUptime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.5.".$pon.".".$key, TRUE);
            $valueUptime = str_replace('STRING: ','',$valueUptime);
            $valueUptime = str_replace("\"",'',$valueUptime);
            $valueUptime = trim($valueUptime);
            $AllArray[$key]['TitleUptime'] = $valueUptime;
            $valueUptime = ZTE::calculateUptime($valueUptime);
            $AllArray[$key]['Uptime'] = $valueUptime;


            $valueDowntime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.6.".$pon.".".$key, TRUE);
            $valueDowntime = str_replace('STRING: ','',$valueDowntime);
            $valueDowntime = str_replace("\"",'',$valueDowntime);
            $valueDowntime = trim($valueDowntime);
            $AllArray[$key]['TitleDowntime'] = $valueDowntime;
            $valueDowntime = ZTE::calculateUptime($valueDowntime);
            $AllArray[$key]['Downtime'] = $valueDowntime;
        }



        foreach ($AllArray as $key => $value) 
        {
            $TotalOnu++;
            $OnuDescFixedValue;
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("$$$$", "", $value);
            $OnuDescFixedValue  = str_replace("\"", "", $value);
            $Gpon = ZTE::Pon_Port($pon);
   
            if($value['StatusOnu'] == 'Working')
            {
                //
            }
            else
            {
                
                if($value['Reason'] == 'LOS' || $value['Reason'] == 'LOSi' ||$value['Reason'] == 'LOFi' )
                {
                    $CoordOnuStatus = '';
                    if (strpos($value['StatusOnu'], 'Working') !== false)
                    {
                        $CoordOnuStatus = 1;
                    }
                    else 
                    {
                        $CoordOnuStatus = 2;
                    }      
                    $PonCoordinates[] = $value['Description'].'|'.$pon.'.'.$key.'|'.str_replace(',',' ',$value['Uptime']).'|'.str_replace(',',' ',$value['Downtime']).'|'.$CoordOnuStatus;
                
                    $item = [];
                    $item['IfIndex']   	  	  = $pon.'.'.$key; 
                    $item['PonPort']   	  	  = $Gpon[1].':'.$key;   
                    $item['Description']	  = $value['Description'];   
                    $item['Type'] 		 	  = $value['Type'];   
                    $item['Vendor'] 		  = $value['Vendor'];
                    $item['Admin'] 	 	      = $value['Admin'];  
                    $item['MacOnu'] 	 	  = $value['SN'];  
                    $item['FULL_SN'] 	 	  = $value['FULL_SN'];  
                    $item['OnuOperateStatus'] = $value['StatusOnu'];
                    $item['Deregreason'] 	  = $value['Reason'];
                    $item['Onu_RX'] 	  	  = $value['dbm'];
                    $item['Uptime'] 	  	  = $value['Uptime']; 
                    $item['DownTime'] 	  	  = $value['Downtime'];
                    $item['TitleUptime'] 	  = $value['TitleUptime'];
                    $item['TitleDownTime']    = $value['TitleDowntime'];
                    $html["onu_num$key"] = $item;  
                }
 
            }
 	 
        }

        $html['PonName']  		= 'GPON '.$Gpon[1]; 
        $html['Online']  		= $TotalOnline; 
		$html['Offline']  		= $TotalOffline; 
		$html['PonAdmin'] 		= 'up'; 
		$html['TotalOnu'] 		= $TotalOnu; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function ClientSidePonAllPowerOff($ip,$pon,$read,$write) 
    {   
        $html = []; 
        $PonCoordinates = [];
        $OnuDesc = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
   
        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$pon."", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        if(empty($OnuDesc))return response()->json(['error' => 'Pon Is Empty']);

 
        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;

        $AllArray = [];
        foreach ($OnuDesc as $key => $value) 
        { 
            $Description = str_replace('STRING: ','',$value);
            $Description = str_replace("\"",'',$Description);
            $Description = trim($Description);
            $AllArray[$key]['Description'] = $Description;

            $Admin = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.1.".$pon.".".$key, TRUE); 
            $Admin = str_replace('INTEGER: ','',$Admin);
            $Admin = str_replace(' ','',$Admin);
            $Admin = str_replace("\"",'',$Admin);
            $AllArray[$key]['Admin'] = trim($Admin);


            $dbm = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$pon.".".$key.'.1', TRUE); 
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
            $AllArray[$key]['dbm'] = round($dbm,2);

            $StatusOnu = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$pon.".".$key, TRUE);
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
            $AllArray[$key]['StatusOnu'] =  $xxx;

            $valueSN = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.5.".$pon.".".$key, TRUE);
            $valueSN = str_replace('Hex-STRING: ','',$valueSN);
            $valueSN = str_replace('STRING: ','',$valueSN);
            $valueSN = str_replace(' ','',$valueSN);
            $valueSN = str_replace("\"",'',$valueSN);

            if(strlen($valueSN) < 10 )
            {  
                $valueSN  = bin2hex($valueSN);
            }

            $SN_Fixed   = substr($valueSN, 0, 8);
            $SN_Fixed   = hex2bin($SN_Fixed);

            $AllArray[$key]['SN'] = $valueSN;
            $AllArray[$key]['FULL_SN'] = $SN_Fixed.substr($valueSN, 8, 16);

            $valueType = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.9.".$pon.".".$key, TRUE);
            $valueType = str_replace('STRING: ','',$valueType);
            $valueType = str_replace(' ','',$valueType);
            $valueType = str_replace("\"",'',$valueType);
            $AllArray[$key]['Type'] = $valueType;

            $valueVendor = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.11.2.1.1.".$pon.".".$key, TRUE);
            $valueVendor = str_replace('STRING: ','',$valueVendor);
            $valueVendor = str_replace(' ','',$valueVendor);
            $valueVendor = str_replace("\"",'',$valueVendor);
            $AllArray[$key]['Vendor'] = $valueVendor;

            $valueReason = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.7.".$pon.".".$key, TRUE);
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
            $AllArray[$key]['Reason'] = $valueReason;

            $valueUptime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.5.".$pon.".".$key, TRUE);
            $valueUptime = str_replace('STRING: ','',$valueUptime);
            $valueUptime = str_replace("\"",'',$valueUptime);
            $valueUptime = trim($valueUptime);
            $AllArray[$key]['TitleUptime'] = $valueUptime;
            $valueUptime = ZTE::calculateUptime($valueUptime);
            $AllArray[$key]['Uptime'] = $valueUptime;


            $valueDowntime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.6.".$pon.".".$key, TRUE);
            $valueDowntime = str_replace('STRING: ','',$valueDowntime);
            $valueDowntime = str_replace("\"",'',$valueDowntime);
            $valueDowntime = trim($valueDowntime);
            $AllArray[$key]['TitleDowntime'] = $valueDowntime;
            $valueDowntime = ZTE::calculateUptime($valueDowntime);
            $AllArray[$key]['Downtime'] = $valueDowntime;
        }

 

        foreach ($AllArray as $key => $value) 
        {
            $TotalOnu++;
            $OnuDescFixedValue;
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("$$$$", "", $value);
            $OnuDescFixedValue  = str_replace("\"", "", $value);
            $Gpon = ZTE::Pon_Port($pon);
   
            if($value['StatusOnu'] == 'Working')
            {
                $TotalOnline +=1;
            }
            else
            {
                if ($value['StatusOnu'] == 'Los'){$TotalWireDown +=1;$TotalOffline +=1;}
                else  if ($value['StatusOnu'] == 'syncMib'){$TotalOffline +=1;}
                else  if ($value['StatusOnu'] == 'Dyinggasp'){$TotalOffline +=1;}
                else  if ($value['StatusOnu'] == 'AuthFailed'){$TotalOffline +=1;}
                else  if ($value['StatusOnu'] == 'Offline'){$TotalOffline +=1;}

                if($value['Reason'] == 'DyingGasp')
                {
                    $TotalPowerOff +=1;

                    $PonCoordinates [] = $value['Description'];
                    $item = [];
                    $item['IfIndex']   	  	  = $pon.'.'.$key; 
                    $item['PonPort']   	  	  = $Gpon[1].':'.$key;   
                    $item['Description']	  = $value['Description'];   
                    $item['Type'] 		 	  = $value['Type'];   
                    $item['Admin'] 	 	      = $value['Admin'];  
                    $item['Vendor'] 		  = $value['Vendor'];
                    $item['MacOnu'] 	 	  = $value['SN'];  
                    $item['FULL_SN'] 	 	  = $value['FULL_SN'];  
                    $item['OnuOperateStatus'] = $value['StatusOnu'];
                    $item['Deregreason'] 	  = $value['Reason'];
                    $item['Onu_RX'] 	  	  = $value['dbm'];
                    $item['Uptime'] 	  	  = $value['Uptime']; 
                    $item['DownTime'] 	  	  = $value['Downtime'];
                    $item['TitleUptime'] 	  = $value['TitleUptime'];
                    $item['TitleDownTime']    = $value['TitleDowntime'];
                    $html["onu_num$key"] = $item;  
                }
 
            }
 	 
        }

        $html['PonName']  		= 'GPON '.$Gpon[1]; 
        $html['Online']  		= $TotalOnline; 
		$html['Offline']  		= $TotalOffline; 
		$html['PonAdmin'] 		= 'up'; 
		$html['TotalOnu'] 		= $TotalOnu; 
		$html['TotalWireDown'] 	= $TotalWireDown; 
		$html['TotalPowerOff'] 	= $TotalPowerOff; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function zte_Onu_PortAdminStatus_OFF($ip,$read,$write,$ifindex,$portIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
 
        try {$snmp_RW->set('1.3.6.1.4.1.3902.1012.3.50.14.1.1.5.'.$ifindex.'.'.$portIndex, 'i', '2');} 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }

        return true;
    }

    static public function zte_Onu_PortAdminStatus_ON($ip,$read,$write,$ifindex,$portIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {$snmp_RW->set('1.3.6.1.4.1.3902.1012.3.50.14.1.1.5.'.$ifindex.'.'.$portIndex, 'i', '1');} 
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

        try {$snmp_RW->set('1.3.6.1.4.1.3902.1012.3.50.11.3.1.1.'.$ifIndex, 'i', 1); } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }
        
        return true;
    }

    static public function Uninstall_Side_OnuInfo($ip,$read,$write,$user,$oltName)
    {
        $html = [];
        $html ['clone'] = '';
         
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
            
        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $DescriptionClone = 0;
        $Real_Desc_Key = 0;
        foreach ($OnuDesc as $key => $value) 
        {
            $oNUkEYfORrESTART = $key;
            $value  = str_replace("$$$$", "", $value);
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("\"", "", $value);
            if (strpos($value, $user) !== false)
            {
                $TempKey = $key;
                $key  = explode('.',$key);      
                $Gpon = ZTE::Pon_Port($key[0]);
                $Port =  trim($key[1]);

                $Real_Desc_Key++;
                $DescriptionClone++;


                $ItemArray = [];
                $ItemArray ['ifIndex']     = $oNUkEYfORrESTART;
                $ItemArray ['description'] = $value;
                $ItemArray ['ponPort']     = $Gpon[1].':'.$Port;

                $xxx = '';
                try {
                        $StatusOnu     = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".trim($key[0]).'.'.trim($key[1]),TRUE);
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

                        $xxx = $Status;
                        $ItemArray ['StatusOnu'] = $Status;
                }catch (\Exception $e){$ItemArray ['StatusOnu'] = '';}    
 
                if($xxx == 'Working')
                {
                    try{
                            $valueUptime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.5.".trim($key[0]).'.'.trim($key[1]), TRUE);
                            $valueUptime = str_replace('STRING: ','',$valueUptime);
                            $valueUptime = str_replace("\"",'',$valueUptime);
                            $valueUptime = trim($valueUptime);
                            $valueUptime = ZTE::calculateUptime($valueUptime);
                            $Uptime      = $valueUptime;
                            $ItemArray ['Uptime'] = $Uptime;
            
                    }catch (\Exception $e){$ItemArray ['Uptime'] = '';}
                }
                else
                {
                    try{
                            $valueDowntime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.6.".trim($key[0]).'.'.trim($key[1]), TRUE);
                            $valueDowntime = str_replace('STRING: ','',$valueDowntime);
                            $valueDowntime = str_replace("\"",'',$valueDowntime);
                            $valueDowntime = trim($valueDowntime);
                            $valueDowntime = ZTE::calculateUptime($valueDowntime);
                            $Downtime = $valueDowntime;
                            $ItemArray ['Downtime'] = $Downtime;

                    }catch (\Exception $e){ $ItemArray ['Downtime'] = '';}
                }
 

                try {
                        $valueSN = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.5.".$TempKey, TRUE);
                        $valueSN = str_replace('Hex-STRING: ','',$valueSN);
                        $valueSN = str_replace('STRING: ','',$valueSN);
                        $valueSN = str_replace(' ','',$valueSN);
                        $valueSN = str_replace("\"",'',$valueSN);
            
                        if(strlen($valueSN) < 10 )
                        {  
                            $valueSN  = bin2hex($valueSN);
                        }
                        $ItemArray ['Mac'] = $valueSN;

                }catch (\Exception $e){$ItemArray ['Mac'] = '';}                    
                 
                $html ['ontList'.$oNUkEYfORrESTART] = $ItemArray;

            }
        }

        $html ['oltType']    = 'ZTE';
        $html ['oltAddress'] = $ip;
        $html ['oltName'] = $oltName;


        if($DescriptionClone > 1)
        {
            $html ['clone'] = 'ეს დესქრიფშენი გაწერილია '.$DescriptionClone.' - ონუზე ';
        }
        if(empty($Real_Desc_Key))
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }       
                 
        return $html;
    }

    static public function calculateUptime($originalDateTimeString) 
    {
        // Create a DateTime object from the original string
        $originalDateTime = new \DateTime($originalDateTimeString);
    
        // Get the current DateTime
        $currentDateTime = new \DateTime();
    
        // Calculate the difference between the original date and the current date
        $uptimeInterval = $currentDateTime->diff($originalDateTime);
    
        // Extract the individual components (years, months, days, hours, minutes)
        $uptimeYears = $uptimeInterval->format('%y');
        $uptimeMonths = $uptimeInterval->format('%m');
        $uptimeDays = $uptimeInterval->format('%d');
        $uptimeHours = $uptimeInterval->format('%h');
        $uptimeMinutes = $uptimeInterval->format('%i');
    
        // Build and return the uptime string
        $uptimeString = "";
        $uptimeString .= ($uptimeYears > 0) ? "{$uptimeYears} y, " : "";
        $uptimeString .= ($uptimeMonths > 0) ? "{$uptimeMonths} m, " : "";
        $uptimeString .= "{$uptimeDays} d, {$uptimeHours} h, {$uptimeMinutes} min";
    
        if($uptimeYears > 10)$uptimeString = 'Never';
    
        return $uptimeString;
    }

    static public function port_oid_to_if_convert($portdec) 
    {
        // Конвертирует OID интерфейса в название порта
        $dec = decbin($portdec);
        $mactypenum = substr("$dec", 0, -28); // type 4 bit
        $macshelfnum = substr("$dec", 4, -24); // shelf 4 bit
        $macslotnum = substr("$dec", 8, -20); // slot 4 bit
        $macoltportnum = substr("$dec", 12, -16); // olt port 4 bit
        $maconuportnum = substr("$dec", 16, -8); // onu port 8 bit
        $port = (bindec($macshelfnum) + 1) . "/" . (bindec($macslotnum) + 1) . "/" . (bindec($macoltportnum) + 1) . ":" . (bindec($maconuportnum) + 1);
        return $port;
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
}
