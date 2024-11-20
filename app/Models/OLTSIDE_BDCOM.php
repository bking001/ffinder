<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 
use App\Models\sshModel;


class OLTSIDE_BDCOM extends Model
{
    use HasFactory;

    static public function OLT_SIDE_SYSTEMINFO($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $Olt_Version = '';
        try {$Olt_Version = trim(str_replace('STRING: ','',$snmp->get("SNMPv2-MIB::sysDescr.0", TRUE)));} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        if (strpos($Olt_Version, 'P3310C') !== false){$html ['version'] = 'P3310C';$html ['pic'] = 'oltpics/P3608B.svg';}
        else if (strpos($Olt_Version, 'P3608B') !== false){$html ['version'] = 'P3608B';$html ['pic'] = 'oltpics/P3608B.png';}
        else if (strpos($Olt_Version, 'P3310D') !== false){$html ['version'] = 'P3310D';$html ['pic'] = 'oltpics/P3310D.png';}
        else if (strpos($Olt_Version, 'P3616-2TE') !== false){$html ['version'] = 'P3616-2TE';$html ['pic'] = 'oltpics/P3616-2TE.png';}
        else if (strpos($Olt_Version, 'P3608-2TE') !== false){$html ['version'] = 'P3608-2TE';$html ['pic'] = 'oltpics/P3608-2TE.png';}
        else if (strpos($Olt_Version, 'P3600-16E') !== false){$html ['version'] = 'P3600-16E';$html ['pic'] = 'oltpics/P3600-16E.png';}

         
        try {
                $Uptime      = $snmp->get('SNMPv2-MIB::sysUpTime.0');    
                $Name        = $snmp->get('1.3.6.1.2.1.1.5.0');           
                $Versions    = $snmp->get('1.3.6.1.2.1.1.1.0');

                $Name = trim(str_replace('STRING: ','',$Name));
                $html ['name'] = trim(str_replace("\"",'',$Name));

                $Versions = trim(str_replace('STRING: ','',$Versions));
                $Versions = trim(str_replace("\"",'',$Versions));
                $Firmware = '';$Hardware = '';
                if (strpos($Versions, 'Version ') !== false)
                {
                    if (strpos($Olt_Version, 'P3600-16E')!== false)
                    {
                        $Versions = explode('Version ',$Versions);
                        $V2  = explode('hardware',$Versions[1]);
                        $Firmware = $V2[0]; 
                        $V3  = explode('version: ',$Versions[1]);
                        $Hardware = $V3[1];
                    }
                    else
                    {
                        $Versions = explode('Version ',$Versions);
                        $V2  = explode(' ',$Versions[1]);
                        $Firmware = $V2[0];
                        $V3  = explode(',',$Versions[2]);
                        $Hardware = $V3[0];
                    }
                }

                $html ['firmware'] =  $Firmware;
                $html ['hardware'] =  $Hardware;

                $FixedTime = '';  
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
                                             
                    $timeComponents = OLTSIDE_BDCOM::uptimeToTimeComponents(trim($Uptime));
                    
                    if ($timeComponents)
                    {
                        $html ['uptime'] = "{$timeComponents['days']} days, ".$timeComponents['hours'].":".$timeComponents['minutes'].":".$timeComponents['seconds'];
                    } 
                    else 
                    {
                        $html ['uptime'] = "Invalid uptime format.";
                    }
                }
        } 
        catch (\Exception $e) 
        {}
 
        try {
                $activeOnuNum    = $snmp->walk("1.3.6.1.4.1.3320.101.6.1.1.21" , TRUE); 
                $inactiveOnuNum  = $snmp->walk("1.3.6.1.4.1.3320.101.6.1.1.22" , TRUE);
                $On = 0;$Off = 0;$Total = 0;
                foreach ($activeOnuNum as $key => $value)
                {
                    $On +=  str_replace('INTEGER: ','',$value);    
                }

                foreach ($inactiveOnuNum as $key => $value)
                {
                    $Off +=  str_replace('INTEGER: ','',$value);    
                }
        } 
        catch (\Exception $e) 
        {$On = '';$Off = '';$Total = '';}



        $html ['totalOnline']   = $On;    
        $html ['totalOffline']  = $Off;    
        $html ['totalOnt']      = $Total = $On + $Off; 

        return $html;
    }

    static public function OLT_SIDE_PON_CHARTS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $TotalOnus = '';
        try {$TotalOnus = $snmp->walk(".1.3.6.1.4.1.3320.101.6.1.1.21", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
  
        try {
                foreach ($TotalOnus as $key => $value)
                {
                    $value    = trim(str_replace('INTEGER: ','',$value));

                    
                            $inactiveOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.22.".$key , TRUE);
                            $inactiveOnuNum  = trim(str_replace('INTEGER: ','',$inactiveOnuNum));          
                            
                            $ifDescr    = $snmp->get(".1.3.6.1.2.1.2.2.1.2.".$key, TRUE);
                            $ifDescr    = trim(str_replace('STRING: ','',$ifDescr));
                            $value      = $value + $inactiveOnuNum;
                    
                    $html[] = array(
                        'key'      => $key,
                        'ifDescr'  => $ifDescr,
                        'value'    => $value,
                    );
                }
            } 
            catch (\Exception $e) 
            {}


        return $html;
    }
     
    static public function OLT_SIDE_SWITCHPORTS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        
        $iface = [];
        $IfDesc = '';
        try { 
                $IfDesc   = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE); 

                foreach ($IfDesc as $key => $value)
                {
                    $value = trim(str_replace("STRING: ", "", $value));
                    if(strpos($value, 'GigaEthernet') !== false || strpos($value, 'TGigaEthernet' ) !== false || ( strpos($value, 'EPON0/' ) !== false &&  strpos($value, ':' ) == false))
                    {
                        $iface[$key]['IfId']   = $key;              
                        $iface[$key]['IfDesc'] = $value;
                    }
                }
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($iface as $key => $value) 
        {
            $OperateStatus = trim(str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.8.".$key , TRUE)));

            $item = [];
            $item ['name']          = $value['IfDesc']; 
            $item ['status']        = $OperateStatus;
            $item ['ifindex']       = $value['IfId'];
            $html ['PonList_'.$key] = $item;
        }

        return $html;
    }

    static public function OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
   
        try {
                    $MacOnu = $snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$ifindex, TRUE);
                    $MacOnu = trim(str_replace("Hex-STRING: ", "",$MacOnu));
                    $MacOnu = trim(str_replace("STRING: ", "",$MacOnu));
                    $MacOnu = trim(str_replace("\"", "",$MacOnu));

                    if(strlen($MacOnu) < 15 )
                    {  
                        $MacOnu     = ltrim($MacOnu);
                        $inputMac   = bin2hex($MacOnu);    
                        $macArray   = str_split($inputMac, 2);
                        $MacOnu     = implode(':', $macArray);      

                        // $inputMac  = bin2hex($MacOnu);
                        // $MacOnu    = substr($inputMac, 0, 4) . '.' . substr($inputMac, 4, 4) . '.' . substr($inputMac, 8, 4);
                    }    
                    else 
                    {
                        $MacOnu = str_replace(" ", ":",$MacOnu); 
                    }                                            
                      
                    $Lvl2Index = OLTSIDE_BDCOM::ReverceMac(strtoupper($MacOnu));     
                    $PonIfIndex = '';
            
                    $Ab_Name = $snmp->get("1.3.6.1.2.1.2.2.1.2.".$ifindex, TRUE);
                    $Ab_Name = str_replace('STRING:','',$Ab_Name);
                    $Ab_Name = trim($Ab_Name);
                    $Ab_Name = explode(':',$Ab_Name);
                    $Ab_Name = $Ab_Name[0];
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp->getError()]);
        }

 
        try {
                $PonName = $snmp->walk("1.3.6.1.2.1.2.2.1.2", TRUE);
                foreach ($PonName as $key => $value) 
                {
                    $value = str_replace('STRING:','',$value);
                    $value = trim($value);
                    if($Ab_Name == $value)
                    {
                        $PonIfIndex =  $key;
                    }
                }
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp->getError()]);
        }


        try {$snmp_RW->set('1.3.6.1.4.1.3320.101.11.1.1.4.'.$PonIfIndex.'.'.$Lvl2Index, 's', $descr); } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }


        try {
                $credentials = DB::table('devices')->where('Address',$ip)->first();
                $commandArray = [
                    "ena",   
                    "AIRLINK2014",
                    "write all"
                ];
                sshModel::SSH($ip,22,$credentials->Username,$credentials->Pass,$commandArray);

        }catch (\Exception $e){}
         
        
        return true;
    }

    static public function OLT_SIDE_ONU_UNINSTALL($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
 
        try {
                $MacOnu = $snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$ifindex, TRUE); 
                $MacOnu = str_replace("Hex-STRING: ", "",$MacOnu);
                $MacOnu = trim(str_replace("STRING: ", "",$MacOnu));
                $MacOnu = trim(str_replace("\"", "",$MacOnu));         
              
                if(strlen($MacOnu) < 15 )  
                {  
                    $MacOnu     = ltrim($MacOnu);
                    $inputMac   = bin2hex($MacOnu);    
                    $macArray   = str_split($inputMac, 2);
                    $MacOnu     = implode(':', $macArray);      

                    //$inputMac  = bin2hex($MacOnu);
                    //$MacOnu    = substr($inputMac, 0, 2) . ':' . substr($inputMac, 2, 2) . ':' . substr($inputMac, 4, 2). ':' . substr($inputMac, 6, 2).  ':'. substr($inputMac, 8, 2).  ':'. substr($inputMac, 10, 2);
                }
                else 
                {
                    $MacOnu = str_replace(" ", ":",$MacOnu); 
                }   

                $MacOnu = OLTSIDE_BDCOM::ReverceMac(strtoupper($MacOnu));          
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }


        try {
                $PonName  = $snmp->get(".1.3.6.1.2.1.2.2.1.2.".$ifindex, TRUE);
                $PonName  = trim(str_replace("STRING: ", "",$PonName));
                $PonName  = explode(':',$PonName);
                $PonIfIndex = '';
                $List =  $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);
                foreach ($List as $key => $value) 
                { 
                    if(str_replace("STRING: ", "", $value) == strtoupper($PonName[0]))
                    {
                        $PonIfIndex = $key;  
                    }
                }
           
                if(!empty($PonIfIndex))
                {
                    $snmp_RW->set('1.3.6.1.4.1.3320.101.11.1.1.2.'.$PonIfIndex.'.'.$MacOnu, 'i', '0');    

                                
                    try {
                            $credentials = DB::table('devices')->where('Address',$ip)->first();
                            $commandArray = [
                                "ena",   
                                "AIRLINK2014",
                                "write all"
                            ];
                            sshModel::SSH($ip,22,$credentials->Username,$credentials->Pass,$commandArray);

                    }catch (\Exception $e){}

                    return true;  
                }
                else 
                {
                    return response()->json(['error' => 'PonIfIndex  Is Empty']);
                }
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp_RW->getError()]);
        }

    }
     
    static public function OLT_SIDE_PON_PARAMETERS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $PonState = '';$PonTemp = '';$Volt = '';$Olt_TX = '';$AdminState = '';
        try {$PonState      = $snmp->walk("1.3.6.1.4.1.3320.101.107.1.2" , TRUE);   } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {
                $PonTemp       = $snmp->walk("1.3.6.1.4.1.3320.101.107.1.6" , TRUE);    
                $Volt          = $snmp->walk("1.3.6.1.4.1.3320.101.107.1.7" , TRUE);          
                $Olt_TX        = $snmp->walk("1.3.6.1.4.1.3320.101.107.1.3" , TRUE);     
                $AdminState    = $snmp->walk("1.3.6.1.4.1.3320.101.6.1.1.9" , TRUE);        
        } 
        catch (\Exception $e) 
        {}

 
        asort($PonState);
        foreach ($PonState as $key => $value)
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 1)$value = 'UP';
            else if($value == 2)$value = 'DOWN';
            $iface[$key]['PonState']=$value;
        }
        foreach ($PonTemp as $key => $value)
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = round((str_replace("\"", "", $value)) / 256,1)." °C";  
            $iface[$key]['PonTemp']=$value;
        }
        foreach ($Volt as $key => $value)
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);  
            $value = round(trim(str_replace("\"", "", $value)/10000),1)." V"; 
            $iface[$key]['Volt']=$value;
        }
        foreach ($Olt_TX as $key => $value)
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = round(str_replace("\"", "", $value)/10,1)." (DBm)";
            $iface[$key]['Olt_TX']=$value;
        }
        foreach ($AdminState as $key => $value)
        {
            $iface[$key]['IfId']=$key;
            $value=explode(' ', $value);
            $value=end($value);
            $value=trim($value);
            $value = str_replace("\"", "", $value);
            if($value == 1)$value = 'UP';
            else if($value == 2)$value = 'DOWN';
            $iface[$key]['AdminState']=$value;
        }

        $TmpBody = '';$SelectHtml = '';
        $Global_Total= 0;$Global_Online= 0;$Global_Offline= 0;
        foreach ($iface as $Unikey => $Univalue) 
        {
                try {
                        $Current       = $snmp->get("1.3.6.1.4.1.3320.101.107.1.8.".$Unikey , TRUE);         
                        $SFP           = $snmp->get("1.3.6.1.4.1.3320.101.107.1.9.".$Unikey , TRUE); 

                        $SFP = str_replace("INTEGER: ", "", $SFP); 
                        if($SFP == 1)$SFP = 'Present';
                        else if($SFP == 2)$SFP = 'Absent';
        
                        $Current = round((str_replace("INTEGER: ", "", $Current)/500),1)." A";
        
                        $PonName  = trim(str_replace("STRING: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.2.".$Unikey , TRUE)));   
                        $activeOnuNum    = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.21.".$Unikey , TRUE);                     
                        $inactiveOnuNum  = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.22.".$Unikey , TRUE);                    
                        $Online  = str_replace('INTEGER: ','',$activeOnuNum);      
                        $Offline = str_replace('INTEGER: ','',$inactiveOnuNum);     
           
                        $ifDescr = trim(str_replace("STRING: ", "",$snmp->get(".1.3.6.1.2.1.31.1.1.1.18.".$Unikey , TRUE)));    
                        $SelectHtml .= '<option value="' . $Unikey . '">' . $PonName . '</option>';     
                } 
                catch (\Exception $e) 
                {}

                $Total = $Online + $Offline;
                $Global_Total   += $Total;
                $Global_Online  += $Online;
                $Global_Offline += $Offline;

                $item = [];
                $item ['ifindex']   = $Unikey;
                $item ['ponport']   = $PonName;
                $item ['name']      = $ifDescr;
                $item ['state']     = $Univalue['PonState'];
                $item ['admin']     = $Univalue['AdminState'];
                $item ['sfp']       = $SFP;
                $item ['temp']      = $Univalue['PonTemp'];
                $item ['rx']        = $Univalue['Olt_TX'];
                $item ['volt']      = $Univalue['Volt'];
                $item ['current']   = $Current;
                $item ['Online']    = $Online;
                $item ['Offline']   = $Offline;
                $item ['Total']     = $Total;

                $html['PonList_'.$Unikey] = $item;
        }

        $html['Global_Total']   = $Global_Total;
        $html['Global_Online']  = $Global_Online;
        $html['Global_Offline'] = $Global_Offline;

        return $html;
    }

    static public function OLT_SIDE_PON_TURNON($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Pon');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                $snmp_RW->set('1.3.6.1.4.1.3320.101.6.1.1.9.'.$ifindex, 'i', '1');
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
                $snmp_RW->set('1.3.6.1.4.1.3320.101.6.1.1.9.'.$ifindex, 'i', '2');
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        return true;
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

    static public function OLT_SIDE_UPLINKS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $iface = [];
        try { 
                $IfDesc = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);
                foreach ($IfDesc as $key => $value)
                {
                    $value = trim(str_replace("STRING: ", "", $value));
                    if(strpos($value, 'GigaEthernet') !== false || strpos($value, 'TGigaEthernet') !== false)
                    {
                        $iface[$key]['IfId']   = $key;              
                        $iface[$key]['IfDesc'] = $value;
                    }
                }
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

            
        try {
                foreach ($iface as $key => $value) 
                {
                    $Name            =  $snmp->get(".1.3.6.1.2.1.31.1.1.1.18.".$key , TRUE);       
                    $OperateStatus   =  $snmp->get(".1.3.6.1.2.1.2.2.1.8.".$key , TRUE);            
                    $AdminStatus     =  $snmp->get(".1.3.6.1.2.1.2.2.1.7.".$key , TRUE);           
                    $TX              =  $snmp->get(".1.3.6.1.4.1.3320.9.63.1.7.1.2.".$key , TRUE);  
                    $RX              =  $snmp->get(".1.3.6.1.4.1.3320.9.63.1.7.1.3.".$key , TRUE);  
                    $Temp            =  $snmp->get(".1.3.6.1.4.1.3320.9.63.1.7.1.4.".$key , TRUE);  
                    $Volt            =  $snmp->get(".1.3.6.1.4.1.3320.9.63.1.7.1.5.".$key , TRUE);  
                    $Current         =  $snmp->get(".1.3.6.1.4.1.3320.9.63.1.7.1.6.".$key , TRUE);  
                    $Duplex          =  $snmp->get(".1.3.6.1.4.1.3320.9.63.1.3.1.4.".$key , TRUE);  
                    $Speed           =  $snmp->get(".1.3.6.1.4.1.3320.9.63.1.3.1.3.".$key , TRUE);           
                    $Is_SFP          =  $snmp->get(".1.3.6.1.4.1.3320.9.63.1.7.1.7.".$key , TRUE);  // .1.3.6.1.4.1.3320.9.63.1.7.1.20
                     
                    $Name            =  trim(str_replace("STRING: ", "", $Name));
                    $OperateStatus   =  trim(str_replace("INTEGER: ", "", $OperateStatus));
                    $AdminStatus     =  trim(str_replace("INTEGER: ", "", $AdminStatus));
                    $TX              =  trim(str_replace("INTEGER: ", "", $TX));
                    $RX              =  trim(str_replace("INTEGER: ", "", $RX));
                    $Temp            =  trim(str_replace("INTEGER: ", "", $Temp));
                    $Volt            =  trim(str_replace("INTEGER: ", "", $Volt));
                    $Current         =  trim(str_replace("INTEGER: ", "", $Current));
                    $Duplex          =  trim(str_replace("INTEGER: ", "", $Duplex));
                    $Speed           =  trim(str_replace("INTEGER: ", "", $Speed));
                    $Is_SFP          =  trim(str_replace("STRING: ", "", $Is_SFP));


                    $Olt_Version = $snmp->get("SNMPv2-MIB::sysDescr.0", TRUE);  
                    $P3600_16E  = stripos($Olt_Version, 'P3600-16E');
                    if(!empty($P3600_16E))
                    { 
                        $RX       = round($RX /100 , 1)." (Dbm)";
                        $TX       = round($TX / 100, 1)." (Dbm)";
                        $InputBitSec     =  trim(str_replace("Counter64: ", "", $snmp->get(".1.3.6.1.4.1.3320.9.64.4.1.1.6.".$key , TRUE)));
                        $OutputBitSec    =  trim(str_replace("Counter64: ", "", $snmp->get(".1.3.6.1.4.1.3320.9.64.4.1.1.8.".$key , TRUE)));
                        $InputPacketSec  =  trim(str_replace("Counter64: ", "", $snmp->get(".1.3.6.1.4.1.3320.9.64.4.1.1.7.".$key , TRUE)));
                        $OutputPacketSec =  trim(str_replace("Counter64: ", "", $snmp->get(".1.3.6.1.4.1.3320.9.64.4.1.1.9.".$key , TRUE)));
                    }
                    else
                    {
                        $RX       = round($RX /100 , 1)." (Dbm)";
                        $TX       = round($TX, 1)." (Dbm)";
                        $InputBitSec     =  trim(str_replace("Counter64: ", "", $snmp->get(".1.3.6.1.4.1.3320.9.64.4.1.1.6.".$key , TRUE)));
                        $OutputBitSec    =  trim(str_replace("Counter64: ", "", $snmp->get(".1.3.6.1.4.1.3320.9.64.4.1.1.8.".$key , TRUE)));
                        $InputPacketSec  =  trim(str_replace("Counter64: ", "", $snmp->get(".1.3.6.1.4.1.3320.9.64.4.1.1.7.".$key , TRUE)));
                        $OutputPacketSec =  trim(str_replace("Counter64: ", "", $snmp->get(".1.3.6.1.4.1.3320.9.64.4.1.1.9.".$key , TRUE)));
                    }
    
    
                    if($Duplex == 1)$Duplex = 'Auto';
                    else if($Duplex == 2)$Duplex = 'Full-Duplex';
                    else if($Duplex == 1)$Duplex = 'Half-Duplex';
    
                    if($Speed == 1)$Speed = 'Auto';
                    else if($Speed == 2)$Speed = 'speed-10M';
                    else if($Speed == 3)$Speed = 'speed-100M';
                    else if($Speed == 4)$Speed = 'speed-1000M';
                    else if($Speed == 5)$Speed = 'speed-10000M';
    
                    $Current  = round($Current / 500 , 2)." V";
                    $Volt     = round($Volt / 10000,2)." A";
                    $Temp     = round($Temp /256 , 2)." °C";

                 
                    $Is_SFP = str_replace("\"", "",$Is_SFP);  
                    if(empty($Is_SFP))
                    {
                        $Temp = '-';
                        $TX = '-';
                        $RX = '-';
                        $Volt = '-';
                        $Current = '-';
                    }

                    $item = [];
                    $item ['ifindex']           = $key;
                    $item ['port']              = $value['IfDesc'];
                    $item ['name']              = $Name;
                    $item ['rx']                = $RX;
                    $item ['tx']                = $TX;
                    $item ['temp']              = $Temp;
                    $item ['volt']              = $Volt;
                    $item ['current']           = $Current;
                    $item ['InputBitSec']       = $InputBitSec.' bits/sec , '.$InputPacketSec.'  packets/sec';
                    $item ['OutputBitSec']      = $OutputBitSec.' bits/sec , '.$OutputPacketSec.'  packets/sec';
                    $item ['duplex']            = $Duplex;
                    $item ['speed']             = $Speed;
                    $item ['admin']             = $AdminStatus;
                    $item ['state']             = $OperateStatus;
                    $html['UplinkList_'.$key]   = $item;
                }    
        } 
        catch (\Exception $e) 
        {}


        return $html;
    }
     
    static public function OLT_SIDE_UPLINK_DESCRIPTION($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Uplink');
         
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                if($descr == 'N/A')
                {
                    $snmp_RW->set('1.3.6.1.2.1.31.1.1.1.18.'.$ifindex, 's', ' ')  ;          
                }
                else
                {
                    $snmp_RW->set('1.3.6.1.2.1.31.1.1.1.18.'.$ifindex, 's', $descr);
                }
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

        $ifAlias  = '';

        try {
                $ifDescr = $snmp->get("1.3.6.1.2.1.2.2.1.2.".$ifindex ,TRUE);
                $ifDescr =  trim(str_replace("STRING: ","",$ifDescr));
                $html['ifAlias'] = $ifDescr;
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }

        try {
                $Distance = $snmp->get("1.3.6.1.4.1.3320.101.10.1.1.27.".$ifindex , TRUE);
                $Distance =  trim(str_replace("INTEGER: ","",$Distance));
                $html['Distance'] = $Distance.' m';
        } 
        catch (\Exception $e) 
        {$html['Distance'] = '-';}

             
        try {
                $Temperature  = $snmp->get("1.3.6.1.4.1.3320.101.10.5.1.2.".$ifindex , TRUE);
                $Temperature  =  round(trim(str_replace("INTEGER: ","",$Temperature))/256,2);  
                $html['Temperature'] = $Temperature.' °C';
        } 
        catch (\Exception $e) 
        {$html['Temperature'] = '-';}


        try {
                $Onu_TX = $snmp->get(".1.3.6.1.4.1.3320.101.10.5.1.6.".$ifindex, TRUE);
                $Onu_TX =  trim(str_replace("INTEGER: ","",$Onu_TX))/10;
                $html['Onu_TX'] = $Onu_TX.' (DBm)';
        } 
        catch (\Exception $e) 
        {$html['Onu_TX'] = '-';}    


        try {
                $Volt = $snmp->get(".1.3.6.1.4.1.3320.101.10.5.1.3.".$ifindex, TRUE); // ONU PON port optical module volt  Unit is 100uV  /10000 იყოფა
                $Volt =  round(trim(str_replace("INTEGER: ","",$Volt))/10000,1);  
                $html['Volt'] = $Volt.' V';
        } 
        catch (\Exception $e) 
        {$html['Volt'] = '-';}    

         
        try {
                $Current = $snmp->get(".1.3.6.1.4.1.3320.101.10.5.1.4.".$ifindex, TRUE); // Unit is 2uA. bias current(mA) /500 იყოფა
                $Current =  trim(str_replace("INTEGER: ","",$Current))/500;
                $html['Current'] = $Current.' A';
        } 
        catch (\Exception $e) 
        {$html['Current'] = '-';}            
           

        try {
                $Olt_RX = $snmp->get("SNMPv2-SMI::enterprises.3320.101.108.1.3.".$ifindex, TRUE);  // walk აჭედინებს
                $Olt_RX =  self::convertToDecimal(trim(str_replace("INTEGER: ","",$Olt_RX)));
                if($Olt_RX > -100)$html['Olt_RX'] = $Olt_RX.' (DBm)';
                else $html['Olt_RX'] = '-';
        } 
        catch (\Exception $e) 
        {$html['Olt_RX'] = '-';}            
              

        try {
                $ifAlias = $snmp->get("IF-MIB::ifAlias.".$ifindex, TRUE);
                $ifAlias =  trim(str_replace("STRING: ","",$ifAlias));
                $html['ifDescr'] = $ifAlias;
        } 
        catch (\Exception $e) 
        {$html['ifDescr'] = '-';}   
       
        try {
                $sequence = $snmp->walk("1.3.6.1.4.1.3320.101.11.4.1.3", TRUE); //   ინდექსის საპოპვნელად RTT სთვის
                foreach ($sequence as $key => $value_sequence) 
                {            
                    $value_sequence = str_replace('STRING: ','',$value_sequence);
                    $value_sequence = str_replace("\"","",$value_sequence);
                        
                    if(($value_sequence) == $html['ifDescr'])
                    {
                        $Fixed_Sequence = $key;
                    }
                }
                if(!empty($Fixed_Sequence))
                {
                    $Rtt = $snmp->get("1.3.6.1.4.1.3320.101.11.4.1.7.".$Fixed_Sequence , TRUE);  // /1000  ms   sec

                    $Rtt = str_replace('INTEGER: ','',$Rtt);
                    $Rtt = str_replace("\"","",$Rtt);
                    $html['Rtt'] = $Rtt." ms";
                    
                }
        } 
        catch (\Exception $e) 
        {$html['Rtt'] = '-';}            
        
        $MacOnu = '';
        try {
                $MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$ifindex, TRUE));				
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

                $html['MacOnu'] = $MacOnu;
        } 
        catch (\Exception $e){$MacOnu = '';}

        try {
                $OperStatus = trim(str_replace("INTEGER: ", "",$snmp->get("1.3.6.1.2.1.2.2.1.8.".$ifindex, TRUE)));
                $OperStatus = preg_replace('/\(\d+\)/', '', $OperStatus);
                $OperStatus = trim(str_replace("\"",'',$OperStatus));
        }catch (\Exception $e){$OperStatus  = '';} 

        $Downtime = ''; $Uptime = '';
        if($OperStatus == 'up')
        {
            try {
                    $Uptime   	  = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.80.".$ifindex, TRUE)); 
                    $Uptime 	  = BDCOM::secondsToNormalTime($Uptime);
                    $html['Uptime'] = $Uptime;
            } 
            catch (\Exception $e){$html['Uptime'] = '';}
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

                        if(strtoupper($valueEX) == strtoupper($MacOnu)) $ReasonSecondKey = $Zkey;
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

                    $html['Downtime'] = $Downtime;
            } 
            catch (\Exception $e){$html['Downtime'] = '';}
        }

        return $html;
    }
     
    static public function convertToDecimal($integerValue) 
    {
        $dbmValue = $integerValue / 10;
        return $dbmValue;
    }

    static public function ReverceMac($hexSequence) 
    {
        // Split the hexadecimal values
        $hexArray = explode(':', $hexSequence);

        // Convert each hexadecimal value to decimal
        $decimalArray = array_map([self::class, 'hexToDecimal'], $hexArray);

        // Join the decimal values with dots
        $result = implode('.', $decimalArray);

        return $result;
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

    static public function secondsToNormalTime($seconds) 
	{                                 
		$days = floor($seconds / (3600 * 24));
		$seconds %= (3600 * 24);
		
		$hours = floor($seconds / 3600);
		$seconds %= 3600;
		
		$minutes = floor($seconds / 60);
		$seconds %= 60;

		$result = $days.' d, '.$hours.' h, '.$minutes.' m';

		return $result;
	}
}
