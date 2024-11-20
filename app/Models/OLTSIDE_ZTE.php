<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OLTSIDE_ZTE extends Model
{
    use HasFactory;

    static public function OLT_SIDE_SYSTEMINFO($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $Uptime = '';

        try {$Uptime = $snmp->get('SNMPv2-MIB::sysUpTime.0');} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {
                $Firmware = $snmp->get('1.3.6.1.4.1.3902.1015.2.1.2.2.1.4.1.1.1');  
                $Firmware = trim(str_replace('STRING: ','',$Firmware));
                $Firmware = trim(str_replace("\"",'',$Firmware));
                $html ['firmware'] = $Firmware; 
        } 
        catch (\Exception $e) 
        {$html ['firmware'] = '';}

        try {
                $Hardware = $snmp->get('1.3.6.1.4.1.3902.1015.2.1.2.2.1.1.1.1.1');           
                $Hardware = trim(str_replace('STRING: ','',$Hardware));
                $Hardware = trim(str_replace("\"",'',$Hardware));
                $html ['hardware'] = $Hardware; 
        } 
        catch (\Exception $e) 
        {$html ['hardware'] = '';}

        try {
                $Name = $snmp->get('1.3.6.1.2.1.1.5.0'); 
                $Name = trim(str_replace('STRING: ','',$Name));
                $Name = trim(str_replace("\"",'',$Name));
                $html ['name'] = $Name; 
        } 
        catch (\Exception $e) 
        {$html ['name'] = '';}
 
        try {
                $Versions = $snmp->get("SNMPv2-MIB::sysDescr.0", TRUE); 
                $Versions = trim(str_replace('STRING: ','',$Versions));
                $Versions = trim(str_replace("\"",'',$Versions));

                $Ver1 = '';$Ver2 = '';
                if (strpos($Versions, 'ZXPON ') !== false)
                {
                    $Ver2 = explode('ZXPON',$Versions);
                    $V2  = explode('Software',$Ver2[1]);
                    $Ver2 = $V2[0]; 
                }
                if (strpos($Versions, 'ZXPON ') !== false)
                {
                    $Ver1 = explode(' ',$Versions);
                    $V2   = explode('Software',$Ver1[0]);
                    $Ver1 = $V2[0]; 
                }
                 
                $html ['version'] =  $Ver1.' '.$Ver2; 
        } 
        catch (\Exception $e) 
        {$html ['version'] = '';}

        try {
                if (strpos($Uptime, "Timeticks") !== false) 
                {
                    $position = strpos($Uptime, ')');
                    if ($position !== false) 
                    { 
                        $html ['uptime'] = substr($Uptime, $position + strlen(')'));
                        
                    }
                }
                else 
                {
                    $html ['uptime'] = $Uptime;
                }
        } 
        catch (\Exception $e) 
        {$html ['uptime'] = '';}

        try {
                
                $OnuStatus = $snmp->walk("1.3.6.1.4.1.3902.1012.3.28.2.1.4" , TRUE);
                $OnlineOnu = 0;$OfflinOnu = 0;$Total = 0;
                foreach ($OnuStatus as $keyStatusOnu => $valueStatusOnu) 
                {                    
                    $valueStatusOnu = trim(str_replace('INTEGER: ','',$valueStatusOnu)); 
                    if ($valueStatusOnu == '3'){$OnlineOnu++;$Total++;}
                    else {$OfflinOnu++;$Total++;}
                }

                $html ['totalOnline']   = $OnlineOnu;    
                $html ['totalOffline']  = $OfflinOnu;    
                $html ['totalOnt']      = $Total; 
        } 
        catch (\Exception $e) 
        {$html ['totalOnline'] = '';$html ['totalOffline'] = '';$html ['totalOnt'] = '';}

        return $html;
    }

    static public function OLT_SIDE_SWITCHPORTS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $Pon_List = '';
 
        try {   
              $Pon_List = $snmp->walk("IF-MIB::ifName", TRUE);    
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {   
             
            foreach ($Pon_List as $key => $value) 
            {
                $OperateStatus = $snmp->get("IF-MIB::ifOperStatus.".$key , TRUE);   
                $value = str_replace('STRING: ','',$value);
                $value = trim($value);

                $OperateStatus = str_replace('INTEGER: ','',$OperateStatus);
                $OperateStatus = trim($OperateStatus);

                $item = [];
                $item ['name']          = $value; 
                $item ['status']        = $OperateStatus;
                $html ['PonList_'.$key] = $item;

            }
        } 
        catch (\Exception $e) 
        {}
        return $html;
    }

    static public function OLT_SIDE_PON_CHARTS($ip,$read,$write)
    {
        $html = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $TotalOnu = '';

        try {   
                $TotalOnu =  $snmp->walk("1.3.6.1.4.1.3902.1012.3.13.1.1.13" , TRUE);
                foreach ($TotalOnu as $key => $value) 
                {
                    $value = str_replace('INTEGER: ','',$value);
                    $value = trim($value);
        
                    $html[] = array(
                        'key'      => $key,
                        'ifDescr'  => 'GPON '.self::Pon_Port($key)[1],
                        'value'    => (int)$value,
                    );
                }
        } 
        catch (\Exception $e) 
        {}

        return $html;
    }
 
    static public function OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                $snmp_RW->set('1.3.6.1.4.1.3902.1012.3.28.1.1.3.'.$ifindex , 's', $descr); 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
           
        return true;
    }
 
    static public function OLT_SIDE_ONU_UNINSTALL($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                $snmp_RW->set('1.3.6.1.4.1.3902.1012.3.28.1.1.9.'.$ifindex, 'i', '6'); 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
    }

    static public function OLT_SIDE_PON_PARAMETERS($ip,$read,$write)
    {
        $html = [];
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        
        $Pon_List = '';
        try {
                $Pon_List = $snmp->walk("IF-MIB::ifName", TRUE);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }

        $Global_Online = 0; $Global_Total = 0; $Global_Offline = 0;

        try {
                $TotalOnu   = $snmp->walk("1.3.6.1.4.1.3902.1012.3.13.1.1.13", TRUE);   
                $StatusOnu  = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.2.1.4", TRUE); 
        } 
        catch (\Exception $e) 
        {$TotalOnu = '';$StatusOnu = '';}

       
        try {
                foreach ($Pon_List as $key => $value) 
                {
                    $value = str_replace('STRING: ','',$value);
                    $value = trim($value);
                    if(strpos($value, 'gpon') !== false)
                    {
                        $OperateStatus = $snmp->get("IF-MIB::ifOperStatus.".$key , TRUE);    
                        $OperateStatus = trim(str_replace('INTEGER: ','',$OperateStatus));
                    }

                    $ifAlias = $snmp->get("IF-MIB::ifAlias.".$key , TRUE);    
                    $ifAlias = trim(str_replace('STRING: ','',$ifAlias));
    
                    $ifAdmin = $snmp->get("1.3.6.1.2.1.2.2.1.7.".$key , TRUE);    
                    $ifAdmin = trim(str_replace('INTEGER: ','',$ifAdmin));

                    $zxAnOpticalTemperature = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.12.".$key , TRUE);    
                    $zxAnOpticalTemperature = trim(str_replace('INTEGER: ','',$zxAnOpticalTemperature));
                    $zxAnOpticalTemperature = round($zxAnOpticalTemperature * 0.001,1)." °C";

                    $Volt = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.10.".$key , TRUE);     
                    $Volt = trim(str_replace('INTEGER: ','',$Volt));
                    $Volt = round($Volt * 0.001,2)." V";

                    $TX = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.4.".$key , TRUE);    
                    $TX = trim(str_replace('INTEGER: ','',$TX));
                    $TX = round($TX/1000,3);

                    $Curr = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.9.".$key , TRUE);     
                    $Curr = trim(str_replace('INTEGER: ','',$Curr));
                    $Curr = round($Curr * 0.001,1)." (mA)";
            
                    $sfp = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.13.".$key , TRUE);     
                    $sfp = trim(str_replace('STRING: ','',$sfp));
                    $sfp = trim(str_replace("\"",'',$sfp));


                    $OnlineOnu = 0;$OfflinOnu = 0;
                    foreach ($StatusOnu as $keyStatusOnu => $valueStatusOnu) 
                    {                    
                        $keyStatusOnu = explode('.',$keyStatusOnu); 
                        $keyStatusOnu = self::Pon_Key_Convert($keyStatusOnu[0]);

                        if(trim($keyStatusOnu[1]) == trim($key))
                        {              
                            $valueStatusOnu = trim(str_replace('INTEGER: ','',$valueStatusOnu));
                            if ($valueStatusOnu == '3'){$OnlineOnu++;$Global_Online++;}
                            else {$Global_Offline++;$OfflinOnu++;}
                        }
                    }

                    if (strpos($value, 'gpon') !== false)
                    {
                        $item = [];
                        $item ['ifindex']   = $key;
                        $item ['ponport']   = $value;
                        $item ['name']      = $ifAlias;
                        $item ['state']     = $OperateStatus;
                        $item ['admin']     = $ifAdmin;
                        $item ['sfp']       = $sfp;
                        $item ['temp']      = $zxAnOpticalTemperature;
                        $item ['tx']        = $TX;
                        $item ['volt']      = $Volt;
                        $item ['current']   = $Curr;
                        $item ['Online']    = $OnlineOnu;
                        $item ['Offline']   = $OfflinOnu;
                        $item ['Total']     = $OnlineOnu + $OfflinOnu;
        
                        $html['PonList_'.$key] = $item;
                    }
 
                }
        } 
        catch (\Exception $e) 
        {}
        

        $html ['TotalOnline']   = $Global_Online;
        $html ['TotalOffline']  = $Global_Offline;
        $html ['TotalTotal']    = $Global_Online + $Global_Offline;

        return $html;
    }
     
    static public function OLT_SIDE_PON_DESCRIPTION($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Pon');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
        
        if( $descr == 'N/A')$descr = ' ';
 
        try {     
                $snmp_RW->set('1.3.6.1.2.1.31.1.1.1.18.'.$ifindex, 's', $descr);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
    }

    static public function OLT_SIDE_PON_TURNON($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Pon');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                $snmp_RW->set('1.3.6.1.2.1.2.2.1.7.'.$ifindex, 'i', '1');
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
    }

    static public function OLT_SIDE_PON_TURNOFF($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Pon');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                $snmp_RW->set('1.3.6.1.2.1.2.2.1.7.'.$ifindex, 'i', '2');
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
    }


    static public function encode_gponOnuIndex($shelf, $slot, $port, $onu_num) {
        // Adjust the bit shifting and base calculation to match the desired output
        $result = (($shelf - 1) << 24) + (($slot - 1) << 20) + (($port - 1) << 16) + (($onu_num - 1) << 8);
        return $result;
    }

  

    static public function OLT_SIDE_UPLINKS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
 
        $Pon_List = [];
        try { 
                $Pon_List = $snmp->walk("IF-MIB::ifName", TRUE);   
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

// dd(self::encode_gponOnuIndex(1, 3, 2, 1));
dd($snmp->walk("IF-MIB::ifIndex.268566528", TRUE));
        
        foreach ($Pon_List as $key => $value)
        {
            $value = str_replace('STRING: ','',$value);
            $value = trim($value);
            if(strpos($value, 'gei') !== false)
            {
                try {
                        $ifAlias = $snmp->get("IF-MIB::ifAlias.".$key , TRUE);    
                        $ifAlias = trim(str_replace('STRING: ','',$ifAlias));
                } 
                catch (\Exception $e) 
                {$ifAlias = '';}
                 
                try {
                        $ifAdmin = $snmp->get("1.3.6.1.2.1.2.2.1.7.".$key , TRUE);    
                        $ifAdmin = trim(str_replace('INTEGER: ','',$ifAdmin));
                } 
                catch (\Exception $e) 
                {$ifAdmin = '';}

             
                try {
                        $zxAnOpticalTemperature = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.12.".$key , TRUE);    
                        $zxAnOpticalTemperature = trim(str_replace('INTEGER: ','',$zxAnOpticalTemperature));
                        $zxAnOpticalTemperature = round($zxAnOpticalTemperature * 0.001,1)." °C";
                } 
                catch (\Exception $e) 
                {$zxAnOpticalTemperature = '';}

               
                try {
                        $Volt = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.10.".$key , TRUE);     
                        $Volt = trim(str_replace('INTEGER: ','',$Volt));
                        $Volt = round($Volt * 0.001,1)." V";
                } 
                catch (\Exception $e) 
                {$Volt = '';}


                try {
                        $TX = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.4.".$key , TRUE);    
                        $TX = trim(str_replace('INTEGER: ','',$TX));
                        $TX = round($TX/1000,1)." (DBm)";
                } 
                catch (\Exception $e) 
                {$TX = '';}

              
                try {
                        $Curr = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.9.".$key , TRUE);     
                        $Curr = trim(str_replace('INTEGER: ','',$Curr));
                        $Curr = number_format($Curr / 1000, 3)." (mA)";
                } 
                catch (\Exception $e) 
                {$Curr = '';}
             

                try {
                        $RX = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.1.".$key , TRUE);     
                        $RX = trim(str_replace('INTEGER: ','',$RX));
                        if($RX == -80000)
                        {
                            $RX = "-"; 
                        }
                        else if($RX == 2147483647)
                        {
                            $RX = "-"; 
                        }
                        else
                        {
                            $RX = round($RX * 0.001,1)." (DBm)";
                        }
                } 
                catch (\Exception $e) 
                {$RX = '';}

                try {
                        $ActualSpeed = $snmp->get(".1.3.6.1.4.1.3902.1015.3.1.2.1.3.".$key , TRUE);     
                        $ActualSpeed = trim(str_replace('INTEGER: ','',$ActualSpeed));
                        if($ActualSpeed == 1)
                        {
                            $ActualSpeed = 'speed10';
                        }
                        else if($ActualSpeed == 2)
                        {
                            $ActualSpeed = 'speed100';
                        }
                        else if($ActualSpeed == 3)
                        {
                            $ActualSpeed = 'speed1000';
                        }
                        else if($ActualSpeed == 4)
                        {
                            $ActualSpeed = 'speed10000';
                        }
                        else if($ActualSpeed == 5)
                        {
                            $ActualSpeed = 'autoSpeed';
                        }
                        else
                        {
                            $ActualSpeed = '-';
                        }
                } 
                catch (\Exception $e) 
                {$ActualSpeed = '';}
                
           
                try {
                        $Duplex = $snmp->get(".1.3.6.1.4.1.3902.1015.3.1.2.1.2.".$key , TRUE);     
                        $Duplex = trim(str_replace('INTEGER: ','',$Duplex));
                        if($Duplex == 1)
                        {
                            $Duplex = 'autoNegotiate';
                        }
                        else if($Duplex == 2)
                        {
                            $Duplex = 'half';
                        }
                        else if($Duplex == 3)
                        {
                            $Duplex = 'full';
                        }
                } 
                catch (\Exception $e) 
                {$Duplex = '';}

                try {
                        $Connector = $snmp->get(".1.3.6.1.4.1.3902.1015.3.1.2.1.5.".$key , TRUE);     
                        $Connector = trim(str_replace('INTEGER: ','',$Connector));
                        if($Connector == 1)
                        {
                            $Connector = 'noUse';
                        }
                        else if($Connector == 2)
                        {
                            $Connector = 'auto';
                        }
                        else if($Connector == 3)
                        {
                            $Connector = 'fibre';
                        }
                        else if($Connector == 4)
                        {
                            $Connector = 'copper';
                        }
                } 
                catch (\Exception $e) 
                {$Connector = '';}

                              
                try {
                        $OperateStatus = $snmp->get("IF-MIB::ifOperStatus.".$key , TRUE);    
                        $OperateStatus = trim(str_replace('INTEGER: ','',$OperateStatus));
                        if(strpos($OperateStatus, 'up') !== false)
                        {
                            $OperateStatus = 'UP';
                        }
                        else
                        {
                            $OperateStatus = 'DOWN';
                            $TX = '-';
                            $Volt = '-';
                            $Curr = '-';
                            $zxAnOpticalTemperature = '-';
                            $RX  = '-';
                        }
                        if($Connector == 'copper')
                        {
                            $TX = '-';
                            $Volt = '-';
                            $Curr = '-';
                            $zxAnOpticalTemperature = '-';
                            $RX  = '-';
                        }
                } 
                catch (\Exception $e) 
                {$OperateStatus = '';}

                
                $item = [];
                $item ['ifindex']           = $key;
                $item ['port']              = $value;
                $item ['name']              = $ifAlias;
                $item ['rx']                = $RX;
                $item ['tx']                = $TX;
                $item ['temp']              = $zxAnOpticalTemperature;
                $item ['volt']              = $Volt;
                $item ['current']           = $Curr;
                $item ['duplex']            = $Duplex;
                $item ['speed']             = $ActualSpeed;
                $item ['admin']             = $ifAdmin;
                $item ['state']             = $OperateStatus;
                $item ['type']              = $Connector;
                $html ['UplinkList_'.$key]  = $item;
               
            }
        }

 

        return $html;
    }

    static public function OLT_SIDE_UPLINK_DESCRIPTION($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Uplink');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
        
        if( $descr == 'N/A')$descr = ' ';
 
        try {     
                $snmp_RW->set('1.3.6.1.2.1.31.1.1.1.18.'.$ifindex, 's', $descr);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
    }
     
    static public function OLT_SIDE_UPLINK_TURNON($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Uplink');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                $snmp_RW->set('1.3.6.1.2.1.2.2.1.7.'.$ifindex, 'i', '1'); 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
    }

    static public function OLT_SIDE_UPLINK_TURNOFF($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Uplink');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                $snmp_RW->set('1.3.6.1.2.1.2.2.1.7.'.$ifindex, 'i', '2'); 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
    }

    static public function OLT_SIDE_ONT_DETAILS($ip,$read,$ifindex)
    { 
        $html = [];

        $snmp= new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {
                $ifDescr = $snmp->get("1.3.6.1.4.1.3902.1012.3.28.1.1.3.".$ifindex ,TRUE);
                $ifDescr =  trim(str_replace("STRING: ","",$ifDescr));
                $ifDescr =  trim(str_replace("$$$$","",$ifDescr));
                $ifDescr =  trim(str_replace("\"","",$ifDescr));
                $html['ifAlias'] = $ifDescr;
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }


        try {
                $Status  = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$ifindex, TRUE);
      
                $Status = str_replace('INTEGER: ', '', trim($Status));   
                $position = strpos($Status, '3');
                if ($position !== false)
                {
                    $html['Status'] = 1;
                }
                else $html['Status'] = 0;
        } 
        catch (\Exception $e) 
        {$html['Status'] = '-';}
    

        try {
                $Distance = $snmp->get("1.3.6.1.4.1.3902.1012.3.11.4.1.2.".$ifindex , TRUE);
                $Distance =  trim(str_replace("INTEGER: ","",$Distance));

                if($html['Status'] == 1)$html['Distance'] = $Distance;
                else $html['Distance'] = '-';
                 
        } 
        catch (\Exception $e) 
        {$html['Distance'] = '-';}
       
        try {
                $OnuTX = $snmp->walk("1.3.6.1.4.1.3902.1012.3.50.12.1.1.14.".$ifindex , TRUE);
                foreach ($OnuTX as $key => $value)
                {
                    $OnuTX = trim(str_replace("INTEGER: ", "", $value));  
                    if(trim($OnuTX) > 30000 && trim($OnuTX) != 65535)
                    {
                        $OnuTX = (trim($OnuTX) - 65536) *0.002-30; 
                    }
                    else
                    {
                        $OnuTX = trim($OnuTX) *0.002-30; 
                    }
                }
 
                if($html['Status'] == 1)$html['OnuTX'] = round($OnuTX,2);
                else $html['OnuTX'] = '-';
       
        } 
        catch (\Exception $e) 
        {$html['OnuTX'] = '-';}
 
        try {
                $OltRX = $snmp->get("1.3.6.1.4.1.3902.1015.1010.11.2.1.2.".$ifindex , TRUE);
                $OltRX = trim(str_replace("INTEGER: ", "", $OltRX));  
                $OltRX = number_format($OltRX / 1000, 3);  

                
                if($html['Status'] == 1)$html['OltRX'] = $OltRX;
                else $html['OltRX'] = '-';
        } 
        catch (\Exception $e) 
        {$html['OltRX'] = '-';}

        try {
                $Temp = $snmp->walk("1.3.6.1.4.1.3902.1012.3.50.12.1.1.19.".$ifindex , TRUE);
                foreach ($Temp as $key => $value) 
                {
                    $Temp = trim(str_replace("INTEGER: ", "", $value));
                    $Temp = round(number_format($Temp / 250, 3),1);   
                }
  
                if($html['Status'] == 1)$html['Temp'] = $Temp;
                else $html['Temp'] = '-';
      
        } 
        catch (\Exception $e) 
        {$html['Temp'] = '-';}

        try {
                $Volt = $snmp->walk("1.3.6.1.4.1.3902.1012.3.50.12.1.1.17.".$ifindex , TRUE); 
                foreach ($Volt as $key => $value) 
                {
                    $Volt = round((int)trim(str_replace("INTEGER: ", "", $value))  / 50 , 2);  
                }
 
                if($html['Status'] == 1)$html['Volt'] = $Volt;
                else $html['Volt'] = '-';
            
        } 
        catch (\Exception $e) 
        {$html['Volt'] = '-';}     

        try {
                $Curr = $snmp->walk("1.3.6.1.4.1.3902.1012.3.50.12.1.1.18.".$ifindex , TRUE);
                foreach ($Curr as $key => $value) 
                {
                    $Curr = round(trim(str_replace("INTEGER: ", "", $value))  / 500 , 1);  
                }

                if($html['Status'] == 1)$html['Curr'] = $Curr;
                else $html['Curr'] = '-';
               
        } 
        catch (\Exception $e) 
        {$html['Curr'] = '-';}   

        try {
                $Service_Line  = $snmp->walk(".1.3.6.1.4.1.3902.1015.1010.5.54.2.1.1.".$ifindex."", TRUE);  
                foreach ($Service_Line as $key => $value) 
                {
                    $value = trim(str_replace("STRING: ", "", $value)); 
                    $value = trim(str_replace("\"", "", $value));   
    
                    if($key == 1)$html['LineName'] =  $value;
                    else if($key == 2)$html['ServiceName'] =  $value;
                }

        } 
        catch (\Exception $e) 
        {$html['LineName'] = '-';$html['ServiceName'] = '-';}   
                

        try {
                $Parts = explode('.',$ifindex);
                $Pon = self::Pon_Port($Parts[0])[1]; 
                $html['PonPort'] =  $Pon.':'.$Parts[1];
        } 
        catch (\Exception $e) 
        {$html['PonPort'] = '-';}   

        try {

                $valueSN = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.5.".$ifindex, TRUE);
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
                

                $html['FULLSN'] =  $SN_Fixed.substr($valueSN, 8, 16);
                $html['SN'] = $valueSN;
        } 
        catch (\Exception $e) 
        {$html['SN'] = '-'; $html['FULLSN'] = '';}   


        try {
                $StatusOnu = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$ifindex, TRUE);
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
                    $valueUptime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.5.".$ifindex, TRUE);
                    $valueUptime = str_replace('STRING: ','',$valueUptime);
                    $valueUptime = str_replace("\"",'',$valueUptime);
                    $valueUptime = trim($valueUptime);
                    $valueUptime = ZTE::calculateUptime($valueUptime);
                    $Uptime      = $valueUptime;
                    $html['Uptime'] = $Uptime;

            }catch (\Exception $e){$html['Uptime'] = '-';}
        }
        else
        {
            try{
                    $valueDowntime = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.6.".$ifindex, TRUE);
                    $valueDowntime = str_replace('STRING: ','',$valueDowntime);
                    $valueDowntime = str_replace("\"",'',$valueDowntime);
                    $valueDowntime = trim($valueDowntime);
                    $valueDowntime = ZTE::calculateUptime($valueDowntime);
                    $Downtime = $valueDowntime;
                    $html['Downtime'] = $Downtime;

            }catch (\Exception $e){$html['Downtime'] = '-';}
        }


        return $html;
    }

    static public function OLT_SIDE_ONT_CONTROL_DISABLE($ip,$read,$write,$ifindex) 
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
 
        try {
                $snmp_RW->set('1.3.6.1.4.1.3902.1012.3.28.2.1.1.'.$ifindex, 'i', '2'); 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }
 
        return true;
    }

    static public function OLT_SIDE_ONT_CONTROL_ENABLE($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
 
        try {
             $snmp_RW->set('1.3.6.1.4.1.3902.1012.3.28.2.1.1.'.$ifindex, 'i', '1'); 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }
 
        return true;
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
        
        $Array = [];
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

    static public function Pon_Key_Convert($value)
    {
         
        $Data = [
            [268501248, 268435456],
            [268501504, 268435712],
            [268501760, 268435968],
            [268502016, 268436224],
            [268502272, 268436480],
            [268502528, 268436736],
            [268502784, 268436992],
            [268503040, 268437248],
            [268503296, 268437504],
            [268503552, 268437760],
            [268503808, 268438016],
            [268504064, 268438272],
            [268504320, 268438528],
            [268504576, 268438784],
            [268504832, 268439040],
            [268505088, 268439296],
            
     
            [268566784, 268500992],
            [268567040, 268501248],
            [268567296, 268501504],
            [268567552, 268501760],
            [268567808, 268502016],
            [268568064, 268502272],
            [268568320, 268502528],
            [268568576, 268502784],
            [268568832, 268503040],
            [268569088, 268503296],
            [268569344, 268503552],
            [268569600, 268503808],
            [268569856, 268504064],
            [268570112, 268504320],
            [268570368, 268504576],
            [268570624, 268504832] 
         ];
    
         $Array = [];
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
}
