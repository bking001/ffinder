<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


use App\Models\OLTSIDE_HUAWEI;
use App\Models\OLTSIDE_ZTE;
use App\Models\OLTSIDE_HSGQ;
 


class PonsStatsModel extends Model
{
    use HasFactory;

    static public function Update_BDCOM($ip,$read,$Type,$name,$mastName)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $TotalOnus = '';$OnuCount = 0;$PonCount = 0;$ActivePon = 0;$FreePon = 0;
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
                    
                    $OnuCount += $value;
                    $PonCount ++;
                 

                    try {
                            $PonTemp = $snmp->get("1.3.6.1.4.1.3320.101.107.1.6.".$key , TRUE);  
                            $PonTemp = trim(str_replace('INTEGER: ','',$PonTemp)); 
                            $PonTemp = round((str_replace("\"", "", $PonTemp)) / 256,1)." °C"; 
                    }
                    catch (\Exception $e){$PonTemp = '';}

                    try {
                            $Volt = $snmp->get("1.3.6.1.4.1.3320.101.107.1.7.".$key , TRUE);  
                            $Volt = trim(str_replace('INTEGER: ','',$Volt)); 
                            $Volt = round(trim(str_replace("\"", "", $Volt)/10000),1)." V"; 
                    }
                    catch (\Exception $e){$Volt = '';}
                       
                    try {
                            $Olt_TX = $snmp->get("1.3.6.1.4.1.3320.101.107.1.3.".$key , TRUE);  
                            $Olt_TX = trim(str_replace('INTEGER: ','',$Olt_TX)); 
                            $Olt_TX = round(str_replace("\"", "", $Olt_TX)/10,1)." (DBm)";
                    }
                    catch (\Exception $e){$Olt_TX = '';}

                    try {
                            $Current       = $snmp->get("1.3.6.1.4.1.3320.101.107.1.8.".$key , TRUE);  
                            $Current = trim(str_replace('INTEGER: ','',$Current)); 
                            $Current = round((str_replace("INTEGER: ", "", $Current)/500),1)." A";
                    }
                    catch (\Exception $e){$Current = '';}

                    try {
                            $SFP = $snmp->get("1.3.6.1.4.1.3320.101.107.1.9.".$key , TRUE);  
                            $SFP = trim(str_replace('INTEGER: ','',$SFP)); 
                            
                            if($SFP == 1)$SFP = 'Present';
                            else if($SFP == 2)$SFP = 'Absent';
                    }
                    catch (\Exception $e){$SFP = '';}

      
                    try {
                            $AdminState = $snmp->get("1.3.6.1.4.1.3320.101.6.1.1.9.".$key, TRUE);  
                            $AdminState = trim(str_replace('INTEGER: ','',$AdminState)); 
                            if($AdminState == 1)$AdminState = 'UP';
                            else if($AdminState == 2)$AdminState = 'DOWN';
                    }
                    catch (\Exception $e){$AdminState = '';}

                    if($value == 0)
                    {
                        $FreePon++;
                        $html[] = array(
                            'AdminState'=> $AdminState,
                            'Current'   => $Current,
                            'SFP'       => $SFP,
                            'Olt_TX'    => $Olt_TX,
                            'Volt'      => $Volt,
                            'PonTemp'   => $PonTemp,
                            'ifDescr'   => $ifDescr,
                            'value'     => $value,
                            'status'    => 'inactive',
                        );
                    }
                    else
                    {
                        $ActivePon++;
                        $html[] = array(
                            'AdminState'=> $AdminState,
                            'Current'   => $Current,
                            'SFP'       => $SFP,
                            'Olt_TX'    => $Olt_TX,
                            'Volt'      => $Volt,
                            'PonTemp'   => $PonTemp,
                            'ifDescr'   => $ifDescr,
                            'value'     => $value,
                            'status'    => 'active',
                        );
                    }

 
                }
        } 
        catch (\Exception $e){}


        $data = [
                    'Type'          => $Type,
                    'Address'       =>  $ip,
                    'device_name'   => $name,
                    'mast'          => $mastName,
                    'TotalOnu'      => (int) $OnuCount,
                    'PonCount'      => (int) $PonCount,
                    'ActivePon'     => (int) $ActivePon,
                    'FreePon'       => (int) $FreePon,
                    'PonsArray'     => json_encode([$html]),
                    'last_update'   => now()
        ];
        

        DB::table('PonStatistic')->updateOrInsert(['Address' => $ip], $data);

        return $html;
    }

    static public function Update_HUAWEI($ip,$read,$Type,$name,$mastName)
    {
        $html = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  


        $TotalOnu = 0;$OnuCount = 0;$PonCount = 0;$ActivePon = 0;$FreePon = 0;
        try {
                $TotalOnu   =  $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.21.1.16" , TRUE);          
        
                foreach ($TotalOnu as $key => $value)
                {
                    $value    = trim(str_replace('INTEGER: ','',$value)); 
                    $value    = trim(str_replace("\"",'',$value));   

                    $OnuCount += (int)$value;
                    $PonCount ++;


                    try {
                            $PonTemp = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.23.1.1.".$key , TRUE);  
                            $PonTemp = trim(str_replace('INTEGER: ','',$PonTemp)); 
                            $PonTemp = $PonTemp." °C";

                    }catch (\Exception $e){$PonTemp = '';}


                    try {
                            $Volt = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.23.1.2.".$key , TRUE);  
                            $Volt = trim(str_replace('INTEGER: ','',$Volt)); 
                            $Volt = round(($Volt * 0.01),2);
                            $Volt=  $Volt." V";

                    }catch (\Exception $e){$Volt = '';}
    

                    try {
                            $TX = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.23.1.4.".$key , TRUE);  
                            $TX = trim(str_replace('INTEGER: ','',$TX)); 
                            $TX = round(($TX * 0.01),2);
                            $TX = $TX." (DBm)";

                    }catch (\Exception $e){$TX = '';}

                                    
                    try {
                            $Sfp = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.21.1.13.".$key , TRUE);  
                            $Sfp = trim(str_replace('INTEGER: ','',$Sfp)); 

                            if($Sfp == 1)$Sfp = 'Present';
                            else $Sfp = 'Absent';

                    }catch (\Exception $e){$Sfp = '';}


                    try {
                            $Current = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.23.1.3.".$key , TRUE);  
                            $Current = trim(str_replace('INTEGER: ','',$Current)); 
                            $Current= $Current." A";

                    }catch (\Exception $e){$Current = '';}


                    try {
                            $LVL2Key = OLTSIDE_HUAWEI::Pon_Port_Custom($key);     
                            $AdminState = $snmp->get("1.3.6.1.4.1.2011.6.3.3.4.1.6.".$LVL2Key , TRUE);  
                            $AdminState = trim(str_replace('INTEGER: ','',$AdminState)); 
                            $AdminState = trim(str_replace("\"", "", $AdminState));
                            if($AdminState == 1)$AdminState = 'UP';
                            else if($AdminState == 2)$AdminState = 'DOWN';

                    }catch (\Exception $e){$AdminState = '';}

       

                    if($value == 0)
                    {
                        $FreePon++;
                        $html[] = array(
                            'AdminState'=> $AdminState,
                            'Current'   => $Current,
                            'SFP'       => $Sfp,
                            'Olt_TX'    => $TX,
                            'Volt'      => $Volt,
                            'PonTemp'   => $PonTemp,
                            'ifDescr'  => OLTSIDE_HUAWEI::Pon_Port($key),
                            'value'    => (int)$value,
                            'status'    => 'inactive'
                        );
                   
                    }
                    else
                    {
                        $ActivePon++;
                        $html[] = array(
                            'AdminState'=> $AdminState,
                            'Current'   => $Current,
                            'SFP'       => $Sfp,
                            'Olt_TX'    => $TX,
                            'Volt'      => $Volt,
                            'PonTemp'   => $PonTemp,
                            'ifDescr'  => OLTSIDE_HUAWEI::Pon_Port($key),
                            'value'    => (int)$value,
                            'status'    => 'active',
                        );
                   
                    }

                }
        } 
        catch (\Exception $e) 
        {return response()->json(['error' => $snmp->getError()]);}

                $data = [
                    'Type'          => $Type,
                    'Address'       =>  $ip,
                    'device_name'   => $name,
                    'mast'          => $mastName,
                    'TotalOnu'      => (int) $OnuCount,
                    'PonCount'      => (int) $PonCount,
                    'ActivePon'     => (int) $ActivePon,
                    'FreePon'       => (int) $FreePon,
                    'PonsArray'     => json_encode([$html]),
                    'last_update'   => now()
                ];

        DB::table('PonStatistic')->updateOrInsert(['Address' => $ip], $data);

        return $html;

    }

    static public function Update_ZTE($ip,$read,$Type,$name,$mastName)
    {
        $html = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
    
     
      

        $TotalOnu = 0;$OnuCount = 0;$PonCount = 0;$ActivePon = 0;$FreePon = 0;
        try {
                $TotalOnu =  $snmp->walk("1.3.6.1.4.1.3902.1012.3.13.1.1.13" , TRUE);
                foreach ($TotalOnu as $key => $value) 
                {
                    $value = str_replace('INTEGER: ','',$value);
                    $value = trim($value);

                    $OnuCount += (int)$value;
                    $PonCount ++;

                    $OrigKey = $key;
                    $key = OLTSIDE_ZTE::Pon_Key_Convert($key);
                    $key = $key[1];

                    try {
                            $PonTemp = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.12.".$key , TRUE);  
                            $PonTemp = trim(str_replace('INTEGER: ','',$PonTemp)); 
                            $PonTemp = round($PonTemp * 0.001,1)." °C"; 
                    }
                    catch (\Exception $e){$PonTemp = '';}
 
                    try {
                            $Volt = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.10.".$key , TRUE);  
                            $Volt = trim(str_replace('INTEGER: ','',$Volt)); 
                            $Volt = round($Volt * 0.001,2)." V";
                    }
                    catch (\Exception $e){$Volt = '';}
                    
                    try {
                            $TX = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.4.".$key , TRUE);  
                            $TX = trim(str_replace('INTEGER: ','',$TX)); 
                            $TX = round($TX/1000,3)." (DBm)";
                    }
                    catch (\Exception $e){$TX = '';}

                    try {
                            $Current = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.9.".$key , TRUE);  
                            $Current = trim(str_replace('INTEGER: ','',$Current)); 
                            $Current = round($Current * 0.001,1)." A";
                    }
                    catch (\Exception $e){$Current = '';}

                    try {
                            $SFP = $snmp->get("1.3.6.1.4.1.3902.1015.3.1.13.1.13.".$key , TRUE);  
                            $SFP = trim(str_replace('STRING: ','',$SFP)); 
                            
                           
                            if(strlen($SFP) > 5)$SFP = 'Present';
                             else $SFP = 'Absent';
                    }
                    catch (\Exception $e){$SFP = '';}

    
                    try {
                            $AdminState = $snmp->get("1.3.6.1.2.1.2.2.1.7.".$key, TRUE);  
                            $AdminState = trim(str_replace('STRING: ','',$AdminState)); 

                            if(strpos($AdminState, 'up') !== false) $AdminState = 'UP';
                            else $AdminState = 'DOWN';
                    }
                    catch (\Exception $e){$AdminState = '';}
        


                    if($value == 0)
                    {
                        $FreePon++;
                        $html[] = array(
                            'AdminState'=> $AdminState,
                            'Current'   => $Current,
                            'SFP'       => $SFP,
                            'Olt_TX'    => $TX,
                            'Volt'      => $Volt,
                            'PonTemp'   => $PonTemp,
                            'key'       => $key,
                            'ifDescr'   => 'GPON '.OLTSIDE_ZTE::Pon_Port($OrigKey)[1],
                            'value'     => (int)$value,
                            'status'    => 'inactive'
                        );
                    }
                    else
                    {
                        $ActivePon++;
                        $html[] = array(
                            'AdminState'=> $AdminState,
                            'Current'   => $Current,
                            'SFP'       => $SFP,
                            'Olt_TX'    => $TX,
                            'Volt'      => $Volt,
                            'PonTemp'   => $PonTemp,
                            'key'       => $key,
                            'ifDescr'   => 'GPON '.OLTSIDE_ZTE::Pon_Port($OrigKey)[1],
                            'value'     => (int)$value,
                            'status'    => 'active',
                        );
                    }
           
                }
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp->getError()]);
        }

                $data = [
                    'Type'          => $Type,
                    'Address'       =>  $ip,
                    'device_name'   => $name,
                    'mast'          => $mastName,
                    'TotalOnu'      => (int) $OnuCount,
                    'PonCount'      => (int) $PonCount,
                    'ActivePon'     => (int) $ActivePon,
                    'FreePon'       => (int) $FreePon,
                    'PonsArray'     => json_encode([$html]),
                    'last_update'   => now()
                ];

        DB::table('PonStatistic')->updateOrInsert(['Address' => $ip], $data);

        return $html;

    }

    static public function Update_VSOLUTION($ip,$read,$Type,$name,$mastName)
    {
        $html = [];
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
    

        $TotalOnu = 0;$OnuCount = 0;$PonCount = 0;$ActivePon = 0;$FreePon = 0;
        try {
                $Total = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.27.1.2", TRUE);

                foreach ($Total as $key => $value)
                {
                    $value = trim(str_replace('INTEGER: ','',$value));
                    $value = trim(str_replace("\"",'',$value));
                   
                    $OnuCount += (int)$value;
                    $PonCount ++;
        

                    try {
                            $PonTemp = $snmp->get("1.3.6.1.4.1.37950.1.1.5.10.13.1.1.2.".$key , TRUE);  
                            $PonTemp = trim(str_replace('STRING: ','',$PonTemp)); 
                            $PonTemp = trim(str_replace("\"", "", $PonTemp));
                    }
                    catch (\Exception $e){$PonTemp = '';}
 
                    try {
                            $Volt = $snmp->get("1.3.6.1.4.1.37950.1.1.5.10.13.1.1.3.".$key , TRUE);   
                            $Volt = trim(str_replace('STRING: ','',$Volt)); 
                            $Volt = trim(str_replace("\"", "", $Volt)); 
                    }
                    catch (\Exception $e){$Volt = '';}
                    
                    try {
                            $TX = $snmp->get("1.3.6.1.4.1.37950.1.1.5.10.13.1.1.5.".$key , TRUE);  
                            $TX = trim(str_replace('STRING: ','',$TX)); 
                            $TX = trim(str_replace("\"", "", $TX));
                    }
                    catch (\Exception $e){$TX = '';}

                    try {
                            $Current = $snmp->get("1.3.6.1.4.1.37950.1.1.5.10.13.1.1.4.".$key , TRUE);  
                            $Current = trim(str_replace('STRING: ','',$Current)); 
                            $Current = trim(str_replace("\"", "", $Current));
                    }
                    catch (\Exception $e){$Current = '';}


                    try {
                            $AdminState = $snmp->get("1.3.6.1.4.1.37950.1.1.5.10.1.2.4.1.2.".$key, TRUE);  
                            $AdminState = trim(str_replace('INTEGER: ','',$AdminState)); 
 
                            if($AdminState == 1) $AdminState = 'UP';
                            else $AdminState = 'DOWN';
                    }
                    catch (\Exception $e){$AdminState = '';}
        





                    if($value == 0)
                    {
                        $FreePon++;
                        $html[] = array(
                            'AdminState'=> $AdminState,
                            'Current'   => $Current,
                            'SFP'       => '',
                            'Olt_TX'    => $TX,
                            'Volt'      => $Volt,
                            'PonTemp'   => $PonTemp,
                            'ifDescr'  => $key,
                            'value'    => (int)$value,
                            'status'    => 'inactive'
                        );
                    }
                    else
                    {
                        $ActivePon++;
                        $html[] = array(
                            'AdminState'=> $AdminState,
                            'Current'   => $Current,
                            'SFP'       => '',
                            'Olt_TX'    => $TX,
                            'Volt'      => $Volt,
                            'PonTemp'   => $PonTemp,
                            'ifDescr'  => 'EPON0/'.$key,
                            'value'    => (int)$value,
                            'status'    => 'active',
                        );
                    }

                    
                }
    
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $snmp->getError()]);
        }

                $data = [
                    'Type'          => $Type,
                    'Address'       =>  $ip,
                    'device_name'   => $name,
                    'mast'          => $mastName,
                    'TotalOnu'      => (int) $OnuCount,
                    'PonCount'      => (int) $PonCount,
                    'ActivePon'     => (int) $ActivePon,
                    'FreePon'       => (int) $FreePon,
                    'PonsArray'     => json_encode([$html]),
                    'last_update'   => now()
                ];

        DB::table('PonStatistic')->updateOrInsert(['Address' => $ip], $data);

        return $html;

    }

    static public function Update_HSGQ($ip,$token,$Type,$name,$mastName)
    {
        $html = [];
 
        $OnuCount = 0;$PonCount = 0;$ActivePon = 0;$FreePon = 0;

        try {
                $ArrayFirst = OLTSIDE_HSGQ::API('http://'.$ip.'/board?info=pon',$token);
                $dataArray  = json_decode($ArrayFirst, true);
 
                foreach ($dataArray['data'] as $key => $item) 
                {    

                    $value = (int)($item['online']+$item['offline']);

                    $OnuCount += (int)$value;
                    $PonCount ++;

                    $Array2   = OLTSIDE_HSGQ::API('http://'.$ip.'/ponmgmt?form=optical_poninfo&port_id='.$item['port_id'],$token);
                    $SecData2 = json_decode($Array2, true);   

                    if($item['admin_status'] == 1)
                    {
                        $adminsT = 'UP';
                    }
                    else 
                    {
                        $adminsT = 'DOWN';
                    }
                    
                    if($SecData2['data']['module_state'] == 1)$SFP = 'Present';
                    else if($SecData2['data']['module_state'] == 2)$SFP = 'Absent';

    
                     
                   

                    if($value == 0)
                    {
                        $FreePon++;
                        $html[] = array(
                            'AdminState'=> $adminsT,
                            'Current'   => $SecData2['data']['transmit_bias'],
                            'SFP'       => $SFP,
                            'Olt_TX'    => $SecData2['data']['transmit_power'],
                            'Volt'      => $SecData2['data']['work_voltage'],
                            'PonTemp'   => str_replace('C',' °C',$SecData2['data']['work_temprature']),
                            'ifDescr'  => 'EPON0/'.$item['port_id'],
                            'value'    =>  $value,
                            'status'   => 'inactive'
                        );
                    }
                    else
                    {
                        $ActivePon++;
                        $html[] = array(
                            'AdminState'=> $adminsT,
                            'Current'   => $SecData2['data']['transmit_bias'],
                            'SFP'       => $SFP,
                            'Olt_TX'    => $SecData2['data']['transmit_power'],
                            'Volt'      => $SecData2['data']['work_voltage'],
                            'PonTemp'   => str_replace('C',' °C',$SecData2['data']['work_temprature']),
                            'ifDescr'  => 'EPON0/'.$item['port_id'],
                            'value'    =>  $value,
                            'status'   => 'active',
                        );
                    }
  
                }
        
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }

 
 

                $data = [
                    'Type'          => $Type,
                    'Address'       => $ip,
                    'device_name'   => $name,
                    'mast'          => $mastName,
                    'TotalOnu'      => (int) $OnuCount,
                    'PonCount'      => (int) $PonCount,
                    'ActivePon'     => (int) $ActivePon,
                    'FreePon'       => (int) $FreePon,
                    'PonsArray'     => json_encode([$html]),
                    'last_update'   => now()
                ];

        DB::table('PonStatistic')->updateOrInsert(['Address' => $ip], $data);

        return $html;

    }
     
}
