<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 

class OLTSIDE_VSOLUTION extends Model
{
    use HasFactory;

    static public function OLT_SIDE_SYSTEMINFO($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $Name       = '';
        $Firmware   = '';
        $Model      = '';
        $RuningTime = '';
        $Hardware   = '';

        try {$Name = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.10.12.5.1", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
     
        try {
                $Firmware   = $snmp->walk("1.3.6.1.4.1.37950.1.1.5.10.12.5.4", TRUE);
                $Model      = $snmp->get("SNMPv2-MIB::sysDescr.0", TRUE); 
                $RuningTime = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.10.12.5.8", TRUE); 
                $Hardware   = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.10.12.5.5", TRUE);

                $Model = trim(str_replace('STRING: ','',$Model));
               
                foreach ($Hardware as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));
                    $value = trim(str_replace("\"",'',$value));
                    $Hardware = $value;
                }            
    
                foreach ($RuningTime as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));
                    $value = trim(str_replace("\"",'',$value));
                    $RuningTime = $value;
                }
    
                foreach ($Firmware as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));
                    $value = trim(str_replace("\"",'',$value));
                    $Firmware = $value;
                }
    
                foreach ($Name as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));
                    $value = trim(str_replace("\"",'',$value));
                    $Name = $value;
                }
        } 
        catch (\Exception $e) 
        {}
  
        $html ['name']       = $Name;    
        $html ['firmware']   = $Firmware;    
        $html ['hardware']   = $Hardware;    
        $html ['uptime']     = $RuningTime;    
        $html ['version']    = $Model;   

        try {
                $Total      = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.27.1.2", TRUE);
                $Online     = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.27.1.3", TRUE); 

                $F_Total = 0;$F_Online = 0;
                foreach ($Online as $key => $valueOnline)
                {
                    $valueOnline = trim(str_replace('INTEGER: ','',$valueOnline));
                    $valueOnline = trim(str_replace("\"",'',$valueOnline));
                    $F_Online += $valueOnline;          
                }

                foreach ($Total as $key => $value)
                {
                    $value = trim(str_replace('INTEGER: ','',$value));
                    $value = trim(str_replace("\"",'',$value));
                    $F_Total += $value;

                    $item = [];
                    $item ['pon'] = $key;
                    $item ['ont'] = $value;
                    $html ['Chart_'.$key] = $item;
                }
                $F_Offline = $F_Total - $F_Online;

                $html ['totalOnline']   = $F_Online;    
                $html ['totalOffline']  = $F_Offline;    
                $html ['totalOnt']      = $F_Total;
        } 
        catch (\Exception $e) 
        {}

 

        return $html;
    }

    static public function OLT_SIDE_SWITCHPORTS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        
        try { 
                $Ifalias   = $snmp->walk(("1.3.6.1.4.1.37950.1.1.5.10.1.1.1.1.2"), TRUE);
                foreach ($Ifalias as $key => $value) 
                {
                    $value = trim(str_replace("STRING: ", "", $value));
                    $value = trim(str_replace("\"", "", $value));

                    @$Uplinktatus = trim(str_replace("INTEGER: ", "",$snmp->get("1.3.6.1.4.1.37950.1.1.5.10.1.1.3.1.2.".$key , TRUE)));
 
                    $item = [];
                    $item ['uplinkName']    = $value;
                    $item ['uplinkStatus']  = $Uplinktatus;
                    $html ['uplink'.$key]   = $item;
                }   
         
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);        
        }

        try {
                $PonName = $snmp->walk("1.3.6.1.4.1.37950.1.1.5.10.1.2.1.1.2", TRUE);
                foreach ($PonName as $keyZ => $valueZ) 
                {
                    $valueZ = trim(str_replace("STRING: ", "", $valueZ));
                    $valueZ = trim(str_replace("\"", "", $valueZ));

                    $PonStatus   =   trim(str_replace("INTEGER: ", "",$snmp->get("1.3.6.1.4.1.37950.1.1.5.10.1.2.3.1.2.".$keyZ , TRUE)));

                    $itemZ = [];
                    $itemZ ['ponName']      = $valueZ;
                    $itemZ ['ponStatus']    = $PonStatus;
                    $html  ['pon'.$keyZ]    = $itemZ;
                }
        } 
        catch (\Exception $e) 
        {       
            return response()->json(['error' => $e->getMessage()]);      
        }
 
        return $html;
    }
     
    static public function OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $ifindex = str_replace('.',':',$ifindex);
 
       try {
                $parts = explode(':',$ifindex); 
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.1.16.1.0', 'i', $parts[0]);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.1.16.2.0', 'i', $parts[1]);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.1.16.3.0', 's', $descr);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.1.16.4.0', 'i', 1);  
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

        try {
                $ifindex = str_replace('.',':',$ifindex);
                $parts = explode(':',$ifindex);
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.1.8.1.0', 'i', $parts[0]);  
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.1.8.2.0', 'i', $parts[1]); 
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

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
 
        $PonStatus = '';$Global_Online = 0; $Global_Total = 0; $Global_Offline = 0;
        try {$PonStatus  = $snmp->walk("1.3.6.1.4.1.37950.1.1.5.10.1.2.1.1.2", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {
                foreach ($PonStatus as $key => $value) 
                {
                    $value  =  trim(str_replace("STRING: ", "", $value));
                    $value  =  trim(str_replace("\"", "", $value));
    
                    $OperateStatus   =  $snmp->get("1.3.6.1.4.1.37950.1.1.5.10.1.2.3.1.2.".$key , TRUE);  
                    $OperateStatus  =  trim(str_replace("INTEGER: ", "", $OperateStatus));

                    $AdminStatus = $snmp->get(trim("1.3.6.1.4.1.37950.1.1.5.10.1.2.4.1.2.".$key), TRUE);  
                    $AdminStatus = trim(str_replace("INTEGER: ", "", $AdminStatus)); 
    
                    $NameOfPon = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.1.2.1.1.14.".$key), TRUE);  
                    $NameOfPon = trim(str_replace("STRING: ", "", $NameOfPon)); 
                    $NameOfPon  =  trim(str_replace("\"", "", $NameOfPon));
    
    
                    $TX  = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.13.1.1.5.".$key), TRUE);  
                    $TX  =  trim(str_replace("STRING: ", "", $TX)); 
                    $TX  = trim(str_replace("\"", "", $TX));
                     
                    $Temp  = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.13.1.1.2.".$key), TRUE);  
                    $Temp  =  trim(str_replace("STRING: ", "", $Temp)); 
                    $Temp  = trim(str_replace("\"", "", $Temp));
     
    
                    $CURR  = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.13.1.1.4.".$key), TRUE);  
                    $CURR  =  trim(str_replace("STRING: ", "", $CURR)); 
                    $CURR  = trim(str_replace("\"", "", $CURR));
     
    
                    $VOLT  = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.13.1.1.3.".$key), TRUE);  
                    $VOLT  =  trim(str_replace("STRING: ", "", $VOLT)); 
                    $VOLT  = trim(str_replace("\"", "", $VOLT));
            
                    $TotalOnline = 0;$TotalOffline = 0;$TotalX = 0;
                    try {
                            $TotalOnus  = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$key , TRUE); 
                            
                            foreach ($TotalOnus as $keyTotalOnus => $valueTotalOnus) 
                            {
                                $TotalX++;
                                $Global_Total++;
                                $valueTotalOnus = trim(str_replace("INTEGER: ", "", $valueTotalOnus)); 
                                if($valueTotalOnus == 1)
                                {
                                    $Global_Online++;
                                    $TotalOnline++;
                                }
                                else
                                {
                                    $TotalOffline++;
                                    $Global_Offline++;
                                }
                            }
                    } 
                    catch (\Exception $e) 
                    {}
 
    
                    $item = [];
                    $item ['ifindex']   = $key;
                    $item ['ponport']   = $value;
                    $item ['name']      = $NameOfPon;
                    $item ['state']     = $OperateStatus;
                    $item ['admin']     = $AdminStatus;
                    $item ['temp']      = $Temp;
                    $item ['tx']        = $TX;
                    $item ['volt']      = $VOLT;
                    $item ['current']   = $CURR;
                    $item ['Online']    = $TotalOnline;
                    $item ['Offline']   = $TotalOffline;
                    $item ['Total']     = $TotalX;
    
                    $html['PonList_'.$key] = $item;
                }
        } 
        catch (\Exception $e) 
        {return response()->json(['error' => $e->getMessage()]);}

        $html['Global_Total']   = $Global_Total;
        $html['Global_Online']  = $Global_Online;
        $html['Global_Offline'] = $Global_Offline;

        return $html;
    }

    static public function OLT_SIDE_PON_DESCRIPTION($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Pon');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
        
        if( $descr == 'N/A')$descr = ' ';
 
        try {     
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.10.1.2.1.1.14.'.$ifindex, 's', $descr); 
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
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.10.1.2.4.1.2.'.$ifindex, 'i', 1); 
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
             snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.10.1.2.4.1.2.'.$ifindex, 'i', 0);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
    }

    static public function OLT_SIDE_UPLINKS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
 
        $Ifalias = [];
        try { 
                $Ifalias   = $snmp->walk(trim("1.3.6.1.4.1.37950.1.1.5.10.1.1.1.1.2"), TRUE);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        try {
                foreach ($Ifalias as $key => $value) 
                {
                    $iFindex = trim(str_replace("STRING: ", "", $value));
                    $iFindex = trim(str_replace("\"", "", $iFindex));
      
                    try {
                            $Name = $snmp->get(trim("1.3.6.1.4.1.37950.1.1.5.10.1.1.1.1.20.".$key), TRUE);  
                            $Name = trim(str_replace("STRING: ", "", $Name)); 
                            $Name = trim(str_replace("\"", "", $Name)); 
                    }catch (\Exception $e){$Name = '';}
                    

                    try {
                            $AdminStatus = $snmp->get(trim("1.3.6.1.4.1.37950.1.1.5.10.1.1.1.1.4.".$key), TRUE);  
                            $AdminStatus = trim(str_replace("INTEGER: ", "", $AdminStatus)); 
                    }catch (\Exception $e){$AdminStatus = '';}

                    try {
                            $Type = $snmp->get(trim("1.3.6.1.4.1.37950.1.1.5.10.1.1.1.1.3.".$key), TRUE);  
                            $Type = trim(str_replace("INTEGER: ", "", $Type)); 
                            
                            if($Type == 1)$Type = 'copper';
                            else if ($Type == 0)$Type = 'fiber';
                    }catch (\Exception $e){$Type = '';}

                    try {
                            $Status = $snmp->get(trim("1.3.6.1.4.1.37950.1.1.5.10.1.1.3.1.2.".$key), TRUE);  
                            $Status = trim(str_replace("INTEGER: ", "", $Status)); 
                    }catch (\Exception $e){$Status = '';}

                    try {
                            $PortSpeed = $snmp->get(trim("1.3.6.1.4.1.37950.1.1.5.10.1.1.3.1.3.".$key), TRUE);  
                            $PortSpeed = trim(str_replace("STRING: ", "", $PortSpeed)); 
                            $PortSpeed = trim(str_replace("\"", "", $PortSpeed));
                    }catch (\Exception $e){$PortSpeed = '';}

                    
                    try {              
                            $Duplex = $snmp->get(trim("1.3.6.1.4.1.37950.1.1.5.10.1.1.1.1.7.".$key), TRUE);  
                            $Duplex = trim(str_replace("INTEGER: ", "", $Duplex)); 
            
                            if($Duplex == 1)$Duplex = 'auto';
                            else if ($Duplex == 2)$Duplex = 'full';
                            else if ($Duplex == 3)$Duplex = 'half';
                    }catch (\Exception $e){$Duplex = '';}
 
           
    
                    try {
                            $Temp = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.1.1.5.1.2.".$key), TRUE);    
                            $Temp = trim(str_replace("INTEGER: ", "", $Temp)); 
                    }catch (\Exception $e){$Temp = '-';}
              

                    try {
                            $TX = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.1.1.5.1.5.".$key), TRUE);   
                            $TX = trim(str_replace("INTEGER: ", "", $TX)); 
                    }catch (\Exception $e){$TX = '-';}
                     

                    try {
                            $RX = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.1.1.5.1.6.".$key), TRUE);    
                            $RX = trim(str_replace("INTEGER: ", "", $RX)); 
                    }catch (\Exception $e){$RX = '-';}

                    try {
                            $Volt = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.1.1.5.1.3.".$key), TRUE);   
                            $Volt = trim(str_replace("INTEGER: ", "", $Volt)); 
                    }catch (\Exception $e){$Volt = '-';}

    
                    try {
                            $Current = $snmp->get(trim(".1.3.6.1.4.1.37950.1.1.5.10.1.1.5.1.4.".$key), TRUE);   
                            $Current = trim(str_replace("INTEGER: ", "", $Current));  
                    }catch (\Exception $e){$Current = '-';}
     
                    $item = [];
                    $item ['ifindex']           = $key;
                    $item ['port']              = $iFindex;
                    $item ['name']              = $Name;
                    $item ['rx']                = $RX;
                    $item ['tx']                = $TX;
                    $item ['temp']              = $Temp;
                    $item ['volt']              = $Volt;
                    $item ['current']           = $Current;
                    $item ['duplex']            = $Duplex;
                    $item ['speed']             = $PortSpeed;
                    $item ['admin']             = $AdminStatus;
                    $item ['state']             = $Status;
                    $item ['type']              = $Type;
                    $html ['UplinkList_'.$key]  = $item;
                }    
        } 
        catch (\Exception $e) 
        { return response()->json(['error' => $e->getMessage()]);}
         

        return $html;
    }
    
    static public function OLT_SIDE_UPLINK_DESCRIPTION($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Uplink');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
        
        if( $descr == 'N/A')$descr = ' ';
 
        try {     
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.10.1.1.1.1.20.'.$ifindex, 's', $descr); 
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
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.10.1.1.1.1.4.'.$ifindex, 'i', 1); 
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
            snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.10.1.1.1.1.4.'.$ifindex, 'i', 0); 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
    }

    
    static public function OLT_SIDE_ONT_DETAILS($ip,$read,$write,$ifindex)
    { 
        $html = [];

        $snmp= new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {
                $ifDescr = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9.".$ifindex ,TRUE);
                $ifDescr =  trim(str_replace("STRING: ","",$ifDescr));
                $ifDescr =  trim(str_replace("\"","",$ifDescr));
                $html['ifAlias'] = $ifDescr;
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }


        try {
                $Status  = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$ifindex, TRUE);
      
                $Status = str_replace('INTEGER: ', '', trim($Status));   
                $position = strpos($Status, '1');
                if ($position !== false)
                {
                    $html['Status'] = 1;
                }
                else $html['Status'] = 0;
        } 
        catch (\Exception $e) 
        {$html['Status'] = '-';}
    

        try {
                $Distance = $snmp->get("1.3.6.1.4.1.37950.1.1.5.12.1.25.1.17.".$ifindex , TRUE);
                $Distance =  trim(str_replace("Gauge32: ","",$Distance));

                if($html['Status'] == 1)$html['Distance'] = $Distance;
                else $html['Distance'] = '-';
                 
        } 
        catch (\Exception $e) 
        {$html['Distance'] = '-';}
       
        try {
                $OnuTX = $snmp->get("1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.6.".$ifindex , TRUE);
                $OnuTX      = trim(str_replace("STRING: ", "", $OnuTX));  
                $OnuTX      = trim(str_replace("\"", "", $OnuTX));  
 
                if($html['Status'] == 1)$html['OnuTX'] = $OnuTX;
                else $html['OnuTX'] = '-';
       
        } 
        catch (\Exception $e) 
        {$html['OnuTX'] = '-';}
 
        try {   
                $PonZ  = explode('.',$ifindex);
                snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.5.3.1.0', 'i',$PonZ[0]); 
                $OltRX = $snmp->get("1.3.6.1.4.1.37950.1.1.5.12.1.28.1.3.".$PonZ[1] , TRUE);
                $OltRX = trim(str_replace("STRING: ", "", $OltRX));  
                $OltRX = trim(str_replace("\"", "", $OltRX)); 

                
                if($html['Status'] == 1)$html['OltRX'] = $OltRX;
                else $html['OltRX'] = '-';
        } 
        catch (\Exception $e) 
        { $html['OltRX'] = '-';}

        try {
                $Temp = $snmp->get("1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.3.".$ifindex , TRUE);
                $Temp = trim(str_replace("STRING: ", "", $Temp));  
                $Temp = trim(str_replace("\"", "", $Temp));   
                
  
                if($html['Status'] == 1)$html['Temp'] = $Temp;
                else $html['Temp'] = '-';
      
        } 
        catch (\Exception $e) 
        {$html['Temp'] = '-';}

        try {
                $Volt = $snmp->get("1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.4.".$ifindex , TRUE); 
                $Volt = trim(str_replace("STRING: ", "", $Volt));  
                $Volt = trim(str_replace("\"", "", $Volt));  
 
                if($html['Status'] == 1)$html['Volt'] = $Volt;
                else $html['Volt'] = '-';
            
        } 
        catch (\Exception $e) 
        {$html['Volt'] = '-';}     

        try {
                $Curr = $snmp->get("1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.5.".$ifindex , TRUE);
                $Curr = trim(str_replace("STRING: ", "", $Curr));  
                $Curr = trim(str_replace("\"", "", $Curr));  

                if($html['Status'] == 1)$html['Curr'] = $Curr;
                else $html['Curr'] = '-';
               
        } 
        catch (\Exception $e) 
        {$html['Curr'] = '-';}   

        try {
                $rtt  = $snmp->get("1.3.6.1.4.1.37950.1.1.5.12.1.17.1.3.".$ifindex."", TRUE);  
                $rtt  = trim(str_replace("INTEGER: ", "", $rtt));  
                $rtt  = trim(str_replace("\"", "", $rtt));  
                $html['rtt'] =  $rtt;
        } 
        catch (\Exception $e) 
        {$html['rtt'] = '-';}   
                

        try {
                $Parts = explode('.',$ifindex);
                $html['PonPort'] =  'EPON0/'.$Parts[0].':'.$Parts[1];
        } 
        catch (\Exception $e) 
        {$html['PonPort'] = '-';}   


        try {
                $OnuMac = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5.".$ifindex , TRUE);
                $OnuMac = trim(str_replace("\"", "", $OnuMac));
                $OnuMac = trim(str_replace("STRING: ", "", $OnuMac));
                $html['OnuMac'] =  trim($OnuMac);
        } 
        catch (\Exception $e) 
        {$html['OnuMac'] = '-';}   


        try {
                $OnuStatus = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$ifindex , TRUE);
                $OnuStatus = trim(str_replace('INTEGER: ','',$OnuStatus));
                $OnuStatus = trim(str_replace("\"",'',$OnuStatus));
        } 
        catch (\Exception $e) 
        {$OnuStatus = '';}

        $Uptime = '';$Downtime = '';
        if($OnuStatus == 1)
        {
       
            try {
                    $Uptime = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.18.".$ifindex , TRUE);
                    $Uptime = str_replace("STRING: ", "", $Uptime);
                    $Uptime = str_replace("\"", "", $Uptime);
                    if($Uptime !== 'N/A')$Uptime = VSOLUTION::calculateUptime($Uptime);
                    $html['Uptime'] = $Uptime;
            } 
            catch (\Exception $e){$html['Uptime'] = '';} 

           
        }
        else
        {             
            $Downtime = [];
            try {
                    $Downtime = $snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.19.".$ifindex , TRUE);
                    $Downtime = str_replace("STRING: ", "", $Downtime);
                    $Downtime = str_replace("\"", "", $Downtime);
                    if($Downtime !== 'N/A')$Downtime = VSOLUTION::calculateUptime($Downtime);
                    $html['Downtime'] = $Downtime ;
            } 
            catch (\Exception $e){$html['Downtime'] = '';}    
        }

        return $html;
    }

}
