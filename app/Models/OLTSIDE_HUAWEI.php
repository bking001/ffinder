<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OLTSIDE_HUAWEI extends Model
{
    use HasFactory;

    static public function OLT_SIDE_SYSTEMINFO($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

 
        $Name = '';
        try { 
                $Name = $snmp->get('1.3.6.1.2.1.1.5.0'); 
                $Name = trim(str_replace('STRING: ','',$Name));
                $Name = trim(str_replace("\"",'',$Name));
                $html['name'] = $Name;
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

 

        try {
            
                $Uptime     = $snmp->walk("1.3.6.1.6.3.10.2.1.3", TRUE);   
                foreach ($Uptime as $key => $value) 
                {
                    $value = str_replace('INTEGER: ','',$value);
                    $html['uptime'] = self::convertSecondsToTime((int)$value);
                }   
            
                $Version    = $snmp->get("1.3.6.1.4.1.2011.6.3.1.999.0", TRUE); 
                                        
                $Firmware   = $snmp->get("1.3.6.1.4.1.2011.6.3.1.3.0", TRUE);   
                $Hardware   = $snmp->get("1.3.6.1.4.1.2011.6.3.1.26.0", TRUE);
        
                $Hardware   = trim(str_replace('STRING: ','',$Hardware));
                $Hardware   = trim(str_replace("\"",'',$Hardware));
                
                $Firmware   = trim(str_replace('STRING: ','',$Firmware));
                $Firmware   = trim(str_replace("\"",'',$Firmware));
        
                $Version  = str_replace("STRING: ", "", $Version);
                $Version  = str_replace("\"", "", $Version);
                $position = strpos($Version, 'V');
                if ($position !== false) 
                {
                    $Version = substr($Version, 0, $position);
                    $Version = $Version.'T';
                } 
        
                $html['version']   = $Version;
                $html['firmware']  = $Firmware;
                $html['hardware']  = $Hardware;
                
        } 
        catch (\Exception $e) 
        {$html['uptime'] = '';$html['firmware'] = '';$html['hardware'] = '';$html['version'] = '';}
 
 
        $On = 0;$Off = 0;$Total = 0;
        try {
                $Status   = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15", TRUE);  
                foreach ($Status as $key => $valueX) 
                {
                    $valueX  = trim(str_replace("INTEGER: ", "", $valueX));
                    $valueX  = trim(str_replace("\"",'',$valueX));  

                    if($valueX == 1)$On++;
                    else $Off++;
                }      
        } 
        catch (\Exception $e) 
        {} 

        try { 

                $TotalOnu_EPON = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15", TRUE);  
                
                foreach ($TotalOnu_EPON as $key => $valueZ) 
                {
                    $valueZ  = trim(str_replace("INTEGER: ", "", $valueZ));
                    $valueZ  = trim(str_replace("\"",'',$valueZ));  

                    if($valueZ == 1)$On++;
                    else $Off++;
                }      
            
        }catch (\Exception $e){}
      
        $html ['totalOnline']   = $On;    
        $html ['totalOffline']  = $Off;    
        $html ['totalOnt']      = $Total = $On + $Off; 

        return $html;
    }

    static public function OLT_SIDE_PON_CHARTS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
 
        try {
                $TotalOnu        =  $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.21.1.16" , TRUE);          
        
                foreach ($TotalOnu as $key => $value)
                {
                    $value    = trim(str_replace('INTEGER: ','',$value)); 
                    $value    = trim(str_replace("\"",'',$value));   

                    if((int)$value > 0)
                    {
                        $html[] = array(
                            'ifDescr'  => self::Pon_Port($key),
                            'value'    => (int)$value,
                        );
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
                $Check = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.31.1.5", TRUE); 

                $TotalOnu_EPON = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.31.1.12", TRUE);  
                
                foreach ($TotalOnu_EPON as $key => $value)
                {
                    $value    = trim(str_replace('INTEGER: ','',$value)); 
                    $value    = trim(str_replace("\"",'',$value));   
 
                    $parts = explode('.',$key);
            
                    if((int)$value > 0)
                    {
                        $html[] = array(
                            'ifDescr'  => self::GPON_EPON_PORT($parts[0]),
                            'value'    => (int)$value,
                        );
                    }
                }
             
        }catch (\Exception $e){}

 
        return $html;
    }

    static public function OLT_SIDE_SWITCHPORTS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
 
        $Pons = '';
        try { 
              
              $Pons = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.21.1.10", TRUE);     
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
 
        try { 
                foreach ($Pons as $key => $value) 
                {
                    $value = trim(str_replace("INTEGER: ", "",$value));
                    $value = trim(str_replace("\"", "",$value));

                    $item = [];
                    $item ['name']          = self::Pon_Port($key); 
                    $item ['status']        = $value;
                    $html ['PonList_'.$key] = $item;
                }
        } 
        catch (\Exception $e) 
        {}

        try { 
                $Ports = $snmp->walk("1.3.6.1.2.1.31.1.1.1.1", TRUE);
                 
                foreach ($Ports as $key => $value) 
                {
                    $value = trim(str_replace("STRING: ", "",$value));
                    $value = trim(str_replace("\"", "",$value));

                    $OperateStatus   =  trim(str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.8.".$key , TRUE)));

          
                    if(strpos($value,'ethernet') !== false )
                    {
                        $item = [];
                        $item ['name']          = $value;
                        $item ['status']        = $OperateStatus;
                        $html ['PonList_'.$key] = $item;
                    }
 
                }
        } 
        catch (\Exception $e) 
        {}

        try { 

                $Pons_EPON = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.31.1.5", TRUE);  
            
                try { 
                        foreach ($Pons_EPON as $key => $value) 
                        {
                            $value = trim(str_replace("INTEGER: ", "",$value));
                            $value = trim(str_replace("\"", "",$value));

                            $item = [];
                            $item ['name']          = self::GPON_EPON_PORT($key); 
                            $item ['status']        = $value;
                            $html ['PonList_'.$key] = $item;
                        }
                     
                } 
                catch (\Exception $e) 
                {}
                 
        }catch (\Exception $e){return $html;}



        return $html;
    }

    static public function OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  


        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($ifindex);    
        if (strpos($OltType, 'GPON') !== false)
        {
            try {
                    $Parts = explode('.',$ifindex);

                    $VlanArray    = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.12", TRUE); 
                    $uniqueValuesArray = array_unique($VlanArray);
                    $uniqueValuesArray = array_values($uniqueValuesArray);

                    foreach ($uniqueValuesArray as $key => $valueVlan) 
                    {
                        $valueVlan = str_replace("INTEGER: ", "", $valueVlan);
                        $valueVlan = str_replace("\"", "", $valueVlan);
                    
                        if(!empty($valueVlan))
                        {    
                            try{                               
                                    $Service_Port = $snmp->get("1.3.6.1.4.1.2011.5.14.5.5.1.7.".$Parts[0].".4.".$Parts[1].".4294967295.4294967295.1.".$valueVlan, TRUE);
                                    if(!$snmp->getError()) 
                                    {
                                        $Service_Port = str_replace("INTEGER: ", "", $Service_Port);
                                        $Service_Port = trim(str_replace("\"", "", $Service_Port));    
                                        $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.17.'.$Service_Port , 's', $descr);                            
                                    }    
                            }catch (\Exception $e){}
                        }    
                    }

                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.'.$Parts[0].'.'.$Parts[1] , 's', $descr); 
                    return true;                                                           
            } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try {
                    $Parts = explode('.',$ifindex);

                    $VlanArray    = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.12", TRUE); 
                    $uniqueValuesArray = array_unique($VlanArray);
                    $uniqueValuesArray = array_values($uniqueValuesArray);

                    foreach ($uniqueValuesArray as $key => $valueVlan) 
                    {
                        $valueVlan = str_replace("INTEGER: ", "", $valueVlan);
                        $valueVlan = str_replace("\"", "", $valueVlan);
                    
                        if(!empty($valueVlan))
                        {    
                            try{                               
                                    $Service_Port = $snmp->get("1.3.6.1.4.1.2011.5.14.5.5.1.7.".$Parts[0].".4.".$Parts[1].".4294967295.4294967295.1.".$valueVlan, TRUE);
                                    if(!$snmp->getError()) 
                                    {
                                        $Service_Port = str_replace("INTEGER: ", "", $Service_Port);
                                        $Service_Port = trim(str_replace("\"", "", $Service_Port));    
                                        $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.17.'.$Service_Port , 's', $descr);                            
                                    }    
                            }catch (\Exception $e){}
                        }    
                    }

                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.'.$Parts[0].'.'.$Parts[1] , 's', $descr); 
                    return true;                                                           
            } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        else 
        {
           return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }


    }

    static public function OLT_SIDE_ONU_UNINSTALL($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($ifindex);    
        if (strpos($OltType, 'GPON') !== false)
        {
            try {
                    $Parts = explode('.',$ifindex);
                    try{
                            $VlanArray    = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.12", TRUE);
                            $uniqueValuesArray = array_unique($VlanArray);
                            $uniqueValuesArray = array_values($uniqueValuesArray);

                            foreach ($uniqueValuesArray as $key => $value) 
                            {
                                $value = str_replace("INTEGER: ", "", $value);
                                $value = str_replace("\"", "", $value);
        
                                if(!empty($value))
                                {                           
                                    try {      
                                            $Service_Port = $snmp->get("1.3.6.1.4.1.2011.5.14.5.5.1.7.".$Parts[0].".4.".$Parts[1].".4294967295.4294967295.1.".$value, TRUE);
                                            $Service_Port = str_replace("INTEGER: ", "", $Service_Port);
                                            $Service_Port = trim(str_replace("\"", "", $Service_Port));    
                                            $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.15.'.$Service_Port , 'i', 6); 
                                    }catch (\Exception $e){}
                                
                                }       
                            }

                    }catch (\Exception $e) {}
                   
                    
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.43.1.10.'.$ifindex , 'i', 6);

                    return true;
            } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try {
                    $Parts = explode('.',$ifindex);
                  
                        try{

                            $VlanArray    = $snmp->walk("1.3.6.1.4.1.2011.5.14.5.2.1.12", TRUE);
                            $uniqueValuesArray = array_unique($VlanArray);
                            $uniqueValuesArray = array_values($uniqueValuesArray);

                            foreach ($uniqueValuesArray as $key => $value) 
                            {
                                $value = str_replace("INTEGER: ", "", $value);
                                $value = str_replace("\"", "", $value);
        
                                if(!empty($value))
                                {                           
                                    try {      
                                            $Service_Port = $snmp->get("1.3.6.1.4.1.2011.5.14.5.5.1.7.".$Parts[0].".4.".$Parts[1].".4294967295.4294967295.1.".$value, TRUE);
                                            $Service_Port = str_replace("INTEGER: ", "", $Service_Port);
                                            $Service_Port = trim(str_replace("\"", "", $Service_Port));    
                                            $snmp_RW->set('1.3.6.1.4.1.2011.5.14.5.2.1.15.'.$Service_Port , 'i', 6); 
                                    }catch (\Exception $e){}
                                
                                }       
                            }

                            
                        }catch (\Exception $e) {}
                
                    
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.53.1.10.'.$ifindex , 'i', 6);

                    return true;
            } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
        else 
        {
           return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }
 
    }

    static public function OLT_SIDE_PON_PARAMETERS($ip,$read,$write)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $Pons = '';$Global_Online = 0; $Global_Total = 0; $Global_Offline = 0;
        try { 
                $Pons = $snmp->walk("1.3.6.1.2.1.31.1.1.1.1", TRUE);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
 
        try {
                $Descr       = $snmp->walk("1.3.6.1.4.1.2011.6.3.3.4.1.3" , TRUE); 
                $AdminState  = $snmp->walk("1.3.6.1.4.1.2011.6.3.3.4.1.6" , TRUE);
        } 
        catch (\Exception $e) 
        {}
 
        //try {
                $DetectEPon2key = '';
                foreach ($Pons as $key => $value) 
                {
                    $Online = 0;$Offline = 0;
                    $value =  trim(str_replace("STRING: ", "",$value));  
                    if(strpos($value, 'GPON') !== false)
                    {
                      
                        $OperateStatus   =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.21.1.10.".$key , TRUE); 
                        $PonTemp         =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.23.1.1.".$key  , TRUE);      
                        $Volt            =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.23.1.2.".$key  , TRUE);  
                        $Current         =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.23.1.3.".$key  , TRUE);   
                        $TX              =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.23.1.4.".$key  , TRUE);  
                        $Sfp             =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.21.1.13.".$key , TRUE); 
                        $TotalOnu        =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.21.1.16.".$key , TRUE);  
                        
                        $OperateStatus   =  trim(str_replace("INTEGER: ", "", $OperateStatus));
                        $PonTemp         =  trim(str_replace("INTEGER: ", "", $PonTemp));
                        $Volt            =  trim(str_replace("INTEGER: ", "", $Volt));
                        $Current         =  trim(str_replace("INTEGER: ", "", $Current));
                        $TX              =  trim(str_replace("INTEGER: ", "", $TX));
                        $Sfp             =  trim(str_replace("INTEGER: ", "", $Sfp));
                        $TotalOnu        =  trim(str_replace("INTEGER: ", "", $TotalOnu));

                        $TX      = round(($TX * 0.01),2);
                        $Volt    = round(($Volt * 0.01),2);

                        $NameOfPon = '';$Descr_Key = '';
                        foreach ($Descr as $keyZ => $valueZ) 
                        {
                            $DetectPon2key = self::GPON_EPON_PORT_FIXED($key); 
                            if($keyZ == $DetectPon2key)
                            {
                                $valueZ = trim(str_replace("STRING: ", "", $valueZ));
                                $valueZ = trim(str_replace("\"", "", $valueZ));
                                $NameOfPon = $valueZ;
                                $Descr_Key = $keyZ;
                            } 
                        }

                        
                        $NameOfPonAdminState = '';
                        foreach ($AdminState as $keyX => $valueX) 
                        {
                            $DetectPonAdminkey = self::GPON_EPON_PORT_FIXED($key); 
                            if($keyX == $DetectPonAdminkey)
                            {
                                $valueX = trim(str_replace("INTEGER: ", "", $valueX));
                                $valueX = trim(str_replace("\"", "", $valueX));
                                $NameOfPonAdminState = $valueX;
                            } 
                        }   

                        try {
                                $Status          = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$key, TRUE);  
                                foreach ($Status as $valueX) 
                                {
                                    $valueX =  trim(str_replace("INTEGER: ", "", $valueX));
                                    if($valueX == 1)$Online++;
                                    else $Offline++;
                                }
                        } 
                        catch (\Exception $e) 
                        {}
                   
    
                        $Global_Online  += $Online;
                        $Global_Offline += $Offline;

             

                        $item = [];
                        $item ['ifindex']   = $key;
                        $item ['ponport']   = $value;
                        $item ['name']      = $NameOfPon;
                        $item ['Descr_Key'] = $Descr_Key;
                        $item ['state']     = $OperateStatus;
                        $item ['admin']     = $NameOfPonAdminState;
                        $item ['sfp']       = $Sfp;
                        $item ['temp']      = $PonTemp;
                        $item ['tx']        = $TX;
                        $item ['volt']      = $Volt;
                        $item ['current']   = $Current;
                        $item ['Online']    = $Online;
                        $item ['Offline']   = $Offline;
                        $item ['Total']     = (int)$Online + (int)$Offline;

                        $html['PonList_'.$key] = $item;
                    }
                    else if(strpos($value, 'EPON') !== false)
                    {

                        $PonTemp         =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.33.1.1.".$key  , TRUE);    
                        $OperateStatus   =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.31.1.5.".$key , TRUE);                               
                        $Volt            =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.33.1.2.".$key  , TRUE);  

                        $Current         =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.33.1.3.".$key  , TRUE);   
                        $TX              =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.33.1.4.".$key  , TRUE);  
                        $Sfp             =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.32.1.1.".$key , TRUE); 
                        $TotalOnu        =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.31.1.12.".$key , TRUE);  
                        
           
                       
                        $Volt            =  trim(str_replace("INTEGER: ", "", $Volt));
                        $Current         =  trim(str_replace("INTEGER: ", "", $Current));
                        $TX              =  trim(str_replace("INTEGER: ", "", $TX));
                        $Sfp             =  trim(str_replace("INTEGER: ", "", $Sfp));
                        $TotalOnu        =  trim(str_replace("INTEGER: ", "", $TotalOnu));
                        $PonTemp         =  trim(str_replace("INTEGER: ", "", $PonTemp));
                        $OperateStatus   =  trim(str_replace("INTEGER: ", "", $OperateStatus));
                         
                        
                        $TX      = round(($TX * 0.01),2);
                        $Volt    = round(($Volt * 0.01),2);

                   
                        $NameOfPon = '';$Descr_Key = '';             
                        foreach ($Descr as $keyZ => $valueZ) 
                        {   
                            $DetectEPon2key = self::GPON_EPON_PORT_FIXED($key);    
                            if($keyZ == $DetectEPon2key)
                            {  
                                $valueZ = trim(str_replace("STRING: ", "", $valueZ));
                                $valueZ = trim(str_replace("\"", "", $valueZ));
                                $NameOfPon = $valueZ;
                                $Descr_Key = $keyZ;

                            } 
                        }

                        $NameOfPonAdminState = '';
                        foreach ($AdminState as $keyX => $valueX) 
                        {
                            $DetectPonAdminkey = self::GPON_EPON_PORT_FIXED($key); 
                            if($keyX == $DetectPonAdminkey)
                            {
                                $valueX = trim(str_replace("INTEGER: ", "", $valueX));
                                $valueX = trim(str_replace("\"", "", $valueX));
                                $NameOfPonAdminState = $valueX;
                            } 
                        }   


                        try {
                                $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$key, TRUE);     
                                foreach ($Status as $key => $valueEX) 
                                {
                                    $valueEX =  trim(str_replace("INTEGER: ", "", $valueEX));
                                    if($valueEX == 1)$Online++;
                                    else $Offline++;                                 
                                }
                        } 
                        catch (\Exception $e) 
                    {}
                        $Global_Online  += $Online;
                        $Global_Offline += $Offline;

                        $item = [];
                        $item ['ifindex']   = $key;
                        $item ['ponport']   = $value;
                        $item ['name']      = $NameOfPon;
                        $item ['Descr_Key'] = $Descr_Key;
                        $item ['state']     = $OperateStatus;
                        $item ['admin']     = $NameOfPonAdminState;
                        $item ['sfp']       = $Sfp;
                        $item ['temp']      = $PonTemp;
                        $item ['tx']        = $TX;
                        $item ['volt']      = $Volt;
                        $item ['current']   = $Current;
                        $item ['Online']    = $Online;
                        $item ['Offline']   = $Offline;
                        $item ['Total']     = (int)$Online + (int)$Offline;

                        $html['PonList_'.$key] = $item;
                    }
                }
        //} 
        //catch (\Exception $e){}
 


        $html['Global_Total']   = (int)$Global_Online + (int)$Global_Offline;
        $html['Global_Online']  = $Global_Online;
        $html['Global_Offline'] = $Global_Offline;

        return $html;
    }
    
    static public function OLT_SIDE_PON_DESCRIPTION($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Pon');

        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
       
        if($descr == 'N/A')$descr = ' ';
 
        try {         
                $snmp_RW->set('1.3.6.1.4.1.2011.6.3.3.4.1.3.'.$ifindex, 's', $descr);
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
                $snmp_RW->set('1.3.6.1.4.1.2011.6.3.3.4.1.6.'.$ifindex, 'i', '1'); 
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
                $snmp_RW->set('1.3.6.1.4.1.2011.6.3.3.4.1.6.'.$ifindex, 'i', '2'); 
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

        $Uplinks = '';
        try { 
            $Uplinks = $snmp->walk("1.3.6.1.2.1.31.1.1.1.1", TRUE);
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        try {
                foreach ($Uplinks as $key => $value) 
                {
                    $value = trim(str_replace('STRING: ','',$value));

                    if (strpos($value, "ethernet") !== false) 
                    {
                        $Name            =  $snmp->get("IF-MIB::ifAlias.".$key, TRUE);                       
                        $OperateStatus   =  $snmp->get(".1.3.6.1.2.1.2.2.1.8.".$key , TRUE);                
                        $AdminStatus     =  $snmp->get(".1.3.6.1.2.1.2.2.1.7.".$key , TRUE);                
                        $RX              =  $snmp->get(".1.3.6.1.4.1.2011.5.14.6.4.1.5.".$key , TRUE);     
                        $TX              =  $snmp->get(".1.3.6.1.4.1.2011.5.14.6.4.1.4.".$key , TRUE);      
                        $Current         =  $snmp->get(".1.3.6.1.4.1.2011.5.14.6.4.1.3.".$key , TRUE);              
                        $Volt            =  $snmp->get(".1.3.6.1.4.1.2011.5.14.6.4.1.2.".$key , TRUE);      
                        $Temp            =  $snmp->get(".1.3.6.1.4.1.2011.5.14.6.4.1.1.".$key , TRUE);      
                        $Speed           =  $snmp->get(".1.3.6.1.4.1.2011.5.14.1.4.1.3.".$key , TRUE);         
                        $Duplex          =  $snmp->get(".1.3.6.1.4.1.2011.5.14.1.4.1.1.".$key , TRUE);   
    
                        $Name            =  trim(str_replace('STRING: ','',$Name));
                        $OperateStatus   =  trim(str_replace("INTEGER: ", "",$OperateStatus ));
                        $AdminStatus     =  trim(str_replace("INTEGER: ", "",$AdminStatus ));
                        $RX              =  trim(str_replace("INTEGER: ", "", $RX));
                        $TX              =  trim(str_replace("INTEGER: ", "", $TX ));
                        $Current         =  trim(str_replace("INTEGER: ", "", $Current));
                        $Volt            =  trim(str_replace("INTEGER: ", "",  $Volt));
                        $Temp            =  trim(str_replace("INTEGER: ", "",$Temp));
                        $Speed           =  trim(str_replace("INTEGER: ", "", $Speed));
                        $Duplex          =  trim(str_replace("INTEGER: ", "", $Duplex));


                        if (trim($Temp) == 2147483647 || trim($Temp) == -2147483647)
                        {
                            $Temp = '-';
                        }
                        else
                        { 
                            $Temp = round($Temp * 0.000001 , 0)." °C";
                        }
    
                        if(trim($TX) == 2147483647 || trim($TX) == -2147483647 || trim($TX) >= 32247066)
                        {
                            $TX   = '-';
                        }
                        else
                        {
                            $TX   = round($TX * 0.000001, 1)." (Dbm)";
                        }
    
                        if(trim($RX) == 2147483647 || trim($RX) == -2147483647 || trim($RX) == -50000000)
                        {
                            $RX   = '-';
                        }
                        else
                        {
                            $RX   = round($RX * 0.000001, 1)." (Dbm)";
                        }

                        if (trim($Volt) == 2147483647 || trim($Volt) == -2147483647 || trim($Volt) > 1000000000)
                        {
                            $Volt  = '-';
                        }
                        else
                        {
                            $Volt  = round(($Volt * 0.000001),2)." A";   
                        }
     
      
                        if (trim($Current) == 2147483647 || trim($Current) == -2147483647 || trim($Current) > 1000000000)
                        {
                            $Current  = '-';
                        }
                        else
                        {
                            $Current  = round($Current * 0.000001,2)." V";           
                        }                     

                        if($Speed == 0)$Speed = 'auto';
                        else if($Speed == 10)$Speed = '10M';
                        else if($Speed == 100)$Speed = '100M';
                        else if($Speed == 1000)$Speed = '1000M';
                        else if($Speed == 2500)$Speed = '2500M';
                        else if($Speed == 10000)$Speed = '10000M';
                        else if($Speed == 100000)$Speed = '100000M';
                        else if($Speed == 11)$Speed = 'auto 10M';
                        else if($Speed == 101)$Speed = 'auto 100';
                        else if($Speed == 1001)$Speed = 'auto 1000M';
                        else if($Speed == 10001)$Speed = 'auto 10000M';
                        else if($Speed == -1)$Speed = 'invalid';
                         
     
                        if($Duplex == 1)$Duplex = 'full';
                        else if($Duplex == 2)$Duplex = 'half';
                        else if($Duplex == 3)$Duplex = 'auto';
                        else if($Duplex == 4)$Duplex = 'auto Full';
                        else if($Duplex == 5)$Duplex = 'auto Half';
                        else if($Duplex == -1)$Duplex = 'invalid';


                        $item = [];
                        $item ['ifindex']           = $key;
                        $item ['port']              = $value;
                        $item ['name']              = $Name;
                        $item ['rx']                = $RX;
                        $item ['tx']                = $TX;
                        $item ['temp']              = $Temp;
                        $item ['volt']              = $Volt;
                        $item ['current']           = $Current;
                        $item ['duplex']            = $Duplex;
                        $item ['speed']             = $Speed;
                        $item ['admin']             = $AdminStatus;
                        $item ['state']             = $OperateStatus;
                        $html['UplinkList_'.$key]   = $item;
                    }
                }    
        } 
        catch (\Exception $e) 
        {}

        // 35 °C	-5.6 (Dbm)	-4.3 (Dbm)	auto Full	auto 1000M	3.27 A	4.16 V

        return $html;
    }

    static public function OLT_SIDE_UPLINK_DESCRIPTION($ip,$read,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Uplink');
         
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  

        try {
                if($descr == 'N/A')
                { 
                    $snmp_RW->set('1.3.6.1.2.1.31.1.1.1.18.'.$ifindex, 's', '');          
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

        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($ifindex); 
        if (strpos($OltType, 'GPON') !== false)
        {
            try {
                        $ifDescr = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9.".$ifindex ,TRUE);
                        $ifDescr =  trim(str_replace("STRING: ","",$ifDescr));
                        $ifDescr =  trim(str_replace("\"","",$ifDescr));
                        $html['ifAlias'] = $ifDescr;
                } 
                catch (\Exception $e) 
                {
                    return response()->json(['error' => $e->getMessage()]);
                }


                try {
                        $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$ifindex, TRUE);
                        $xxx = current($Status);
                        $xxx = str_replace('INTEGER: ', '', trim($xxx));
                        $position = strpos($xxx, '1');
                        if ($position !== false)
                        {
                            $html['Status'] = 1;
                        }
                        else $html['Status'] = 0;
                } 
                catch (\Exception $e) 
                {$html['Status'] = '-';}
            

                try {
                        $Distance = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.46.1.20.".$ifindex , TRUE);
                        $Distance =  trim(str_replace("INTEGER: ","",$Distance));

                        if($html['Status'] == 1)$html['Distance'] = $Distance;
                        else $html['Distance'] = '-';
                        
                } 
                catch (\Exception $e) 
                {$html['Distance'] = '-';}
                
                try {
                        $OnuTX = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.3.".$ifindex , TRUE);
                        $OnuTX =  trim(str_replace("INTEGER: ","",$OnuTX));

                        if($html['Status'] == 1)$html['OnuTX'] = self::SginalFixer($OnuTX);
                        else $html['OnuTX'] = '-';
            
                } 
                catch (\Exception $e) 
                {$html['OnuTX'] = '-';}

                try {
                        $OltRX = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.6.".$ifindex , TRUE);
                        $OltRX =  trim((str_replace("INTEGER: ","",$OltRX))- 10000) / 100;

                        
                        if($html['Status'] == 1)$html['OltRX'] = $OltRX;
                        else $html['OltRX'] = '-';
                } 
                catch (\Exception $e) 
                {$html['OltRX'] = '-';}

                try {
                        $Temp = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.1.".$ifindex , TRUE);
                        $Temp =  trim(str_replace("INTEGER: ","",$Temp));

                        
                        if($html['Status'] == 1)$html['Temp'] = $Temp;
                        else $html['Temp'] = '-';
            
                } 
                catch (\Exception $e) 
                {$html['Temp'] = '-';}

                try {
                        $Volt = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.5.".$ifindex , TRUE);
                        $Volt =  trim((str_replace("INTEGER: ","",$Volt)) / 1000 , 1);

                        if($html['Status'] == 1)$html['Volt'] = $Volt;
                        else $html['Volt'] = '-';
                    
                } 
                catch (\Exception $e) 
                {$html['Volt'] = '-';}     

                try {
                        $Curr = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.2.".$ifindex , TRUE);
                        $Curr =  trim(str_replace("INTEGER: ","",$Curr));

                        if($html['Status'] == 1)$html['Curr'] = $Curr;
                        else $html['Curr'] = '-';
                    
                } 
                catch (\Exception $e) 
                {$html['Curr'] = '-';}   

                try {
                        $LineName = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.7.".$ifindex , TRUE);
                        $LineName =  trim(str_replace("STRING: ","",$LineName));
                        $LineName =  trim(str_replace("\"","",$LineName));
                        $html['LineName'] = ($LineName);
                } 
                catch (\Exception $e) 
                {$html['LineName'] = '-';}   
                        
                try {
                        $ServiceName = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.8.".$ifindex , TRUE);
                        $ServiceName =  trim(str_replace("STRING: ","",$ServiceName));
                        $ServiceName =  trim(str_replace("\"","",$ServiceName));
                        $html['ServiceName'] = ($ServiceName);
                } 
                catch (\Exception $e) 
                {$html['ServiceName'] = '-';}  

                try {
                        $Runstate = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$ifindex , TRUE);
                        $Runstate =  trim(str_replace("INTEGER: ","",$Runstate));

                        if($Runstate == 1)$Runstate = 'UP';
                        else if($Runstate == 2)$Runstate = 'DOWN';
                        else if($Runstate == 3)$Runstate = 'INVALID';

                        $html['Runstate'] = ($Runstate);
                } 
                catch (\Exception $e) 
                {$html['Runstate'] = '-';}  

                try {
                        $ConfigState = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.46.1.16.".$ifindex , TRUE);
                        $ConfigState =  trim(str_replace("INTEGER: ","",$ConfigState));

                        if($ConfigState == 1)$ConfigState = 'Initialization';
                        else if($ConfigState == 2)$ConfigState = 'Normal';
                        else if($ConfigState == 3)$ConfigState = 'Failed';
                        else if($ConfigState == 4)$ConfigState = 'Noresume';
                        else if($ConfigState == 5)$ConfigState = 'Config';
                        else if($ConfigState == -1)$ConfigState = 'Invalid';

                        
                        $html['ConfigState'] = ($ConfigState);
                } 
                catch (\Exception $e) 
                {$html['ConfigState'] = '-';}   

                try {
                        $Parts = explode('.',$ifindex);
                        $Pon = self::Pon_Port($Parts[0]);
                        $html['PonPort'] =  $Pon.':'.$Parts[1];
                } 
                catch (\Exception $e) 
                {$html['PonPort'] = '-';}   

                try { 
                    
                        $SN = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3.".$ifindex, TRUE); 

                        $SN      = str_replace("Hex-STRING: ", "", $SN);
                        $SN      = str_replace("STRING: ", "", $SN);
                        $SN      = str_replace("\"", "", $SN);   
                        $SN      = trim(str_replace(" ", "", $SN));
                        if(strlen($SN) < 15 )
                        {
                            $SN = strtoupper(bin2hex($SN)); 
                        }

                        $html['SN'] = $SN;
                } 
                catch (\Exception $e) 
                {$html['SN'] = '-';}

                try { 
                        $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$ifindex, TRUE);
                        $xxx = current($Status);
                        $xxx = str_replace('INTEGER: ', '', trim($xxx));
                        $position = strpos($xxx, '1');
                        if ($position !== false)
                        {
                            $Status = 'Online';
                        }
                        else $Status = 'Offline';
                } 
                catch (\Exception $e) 
                {$Status = '';}

                
                try { 
                        $Match = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.18.".$ifindex, TRUE);  
                        $Match =  trim(str_replace("INTEGER: ","",$Match));
                        $html['Match'] = $Match ?? '-';
                } 
                catch (\Exception $e) 
                {$html['Match'] = '-';}

                $Uptime = '';$Downtime = '';
                if($Status == 'Online')
                {
                    try { 
                            $Uptime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.22.".$ifindex, TRUE); 
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
                            $html['Uptime'] = $Uptime;
                        
                    } 
                    catch (\Exception $e) 
                    {$html['Uptime'] = '-';}
                }
                else
                {
                    try { 
                            $Downtime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.23.".$ifindex, TRUE); 
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
                            $html['Downtime'] =  $Downtime;
                    } 
                    catch (\Exception $e) 
                    {$html['Downtime'] = '-';}

                }
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try {
                    $ifDescr = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9.".$ifindex ,TRUE);
                    $ifDescr =  trim(str_replace("STRING: ","",$ifDescr));
                    $ifDescr =  trim(str_replace("\"","",$ifDescr));
                    $html['ifAlias'] = $ifDescr;
            } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }

            try {
                    $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$ifindex, TRUE);
                    $xxx = current($Status);
                    $xxx = str_replace('INTEGER: ', '', trim($xxx));
                    $position = strpos($xxx, '1');
                    if ($position !== false)
                    {
                        $html['Status'] = 1;
                    }
                    else $html['Status'] = 0;
            } 
            catch (\Exception $e) 
            {$html['Status'] = '-';}
        

            try {
                    $Distance = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.57.1.19.".$ifindex , TRUE);
                    $Distance =  trim(str_replace("INTEGER: ","",$Distance));

                    if($html['Status'] == 1)$html['Distance'] = $Distance;
                    else $html['Distance'] = '-';
                    
            } 
            catch (\Exception $e) 
            {$html['Distance'] = '-';}
            
            try {
                    $OnuTX = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.4.".$ifindex , TRUE);
                    $OnuTX =  trim(str_replace("INTEGER: ","",$OnuTX));

                    if($html['Status'] == 1)$html['OnuTX'] = self::SginalFixer($OnuTX);
                    else $html['OnuTX'] = '-';
        
            } 
            catch (\Exception $e) 
            {$html['OnuTX'] = '-';}

            try {
                    $OltRX = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.1.".$ifindex , TRUE);
                    $OltRX =  trim((str_replace("INTEGER: ","",$OltRX))- 10000) / 100;

                    
                    if($html['Status'] == 1)$html['OltRX'] = $OltRX;
                    else $html['OltRX'] = '-';
            } 
            catch (\Exception $e) 
            {$html['OltRX'] = '-';}

            try {
                    $Temp = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.2.".$ifindex , TRUE);
                    $Temp =  trim(str_replace("INTEGER: ","",$Temp));

                    
                    if($html['Status'] == 1)$html['Temp'] = $Temp;
                    else $html['Temp'] = '-';
        
            } 
            catch (\Exception $e) 
            {$html['Temp'] = '-';}

            try {
                    $Volt = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.6.".$ifindex , TRUE);
                    $Volt =  trim((str_replace("INTEGER: ","",$Volt)) / 1000 , 1);

                    if($html['Status'] == 1)$html['Volt'] = $Volt;
                    else $html['Volt'] = '-';
                
            } 
            catch (\Exception $e) 
            {$html['Volt'] = '-';}     

            try {
                    $Curr = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.3.".$ifindex , TRUE);
                    $Curr =  trim(str_replace("INTEGER: ","",$Curr));

                    if($html['Status'] == 1)$html['Curr'] = $Curr;
                    else $html['Curr'] = '-';
                
            } 
            catch (\Exception $e) 
            {$html['Curr'] = '-';}   

            try {
                    $LineName = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.7.".$ifindex , TRUE);
                    $LineName =  trim(str_replace("STRING: ","",$LineName));
                    $LineName =  trim(str_replace("\"","",$LineName));
                    $html['LineName'] = ($LineName);
            } 
            catch (\Exception $e) 
            {$html['LineName'] = '-';}   
                    
            try {
                    $ServiceName = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.8.".$ifindex , TRUE);
                    $ServiceName =  trim(str_replace("STRING: ","",$ServiceName));
                    $ServiceName =  trim(str_replace("\"","",$ServiceName));
                    $html['ServiceName'] = ($ServiceName);
            } 
            catch (\Exception $e) 
            {$html['ServiceName'] = '-';}  

            try {
                    $Runstate = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$ifindex , TRUE);
                    $Runstate =  trim(str_replace("INTEGER: ","",$Runstate));

                    if($Runstate == 1)$Runstate = 'UP';
                    else if($Runstate == 2)$Runstate = 'DOWN';
                    else if($Runstate == 3)$Runstate = 'INVALID';

                    $html['Runstate'] = ($Runstate);
            } 
            catch (\Exception $e) 
            {$html['Runstate'] = '-';}  

            try {
                    $ConfigState = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.57.1.16.".$ifindex , TRUE);
                    $ConfigState =  trim(str_replace("INTEGER: ","",$ConfigState));

                    if($ConfigState == 1)$ConfigState = 'Initialization';
                    else if($ConfigState == 2)$ConfigState = 'Normal';
                    else if($ConfigState == 3)$ConfigState = 'Failed';
                    else if($ConfigState == 4)$ConfigState = 'Noresume';
                    else if($ConfigState == 5)$ConfigState = 'Config';
                    else if($ConfigState == -1)$ConfigState = 'Invalid';

                    
                    $html['ConfigState'] = ($ConfigState);
            } 
            catch (\Exception $e) 
            {$html['ConfigState'] = '-';}   

            try {

                    $Parts = explode('.',$ifindex);
                    $Pon = OLTSIDE_HUAWEI::GPON_EPON_PORT($Parts[0]);
                    $html['PonPort'] =  $Pon.':'.$Parts[1];
            } 
            catch (\Exception $e) 
            {$html['PonPort'] = '-';}   

            try { 
                
                    $SN = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3.".$ifindex, TRUE); 
                    $SN      = str_replace("Hex-STRING: ", "", $SN);
                    $SN      = str_replace("STRING: ", "", $SN);
                    $SN      = trim(str_replace("\"", "", $SN));   
                    $SN      = trim(str_replace(" ", ":", $SN));
                    if(strlen($SN) < 10 )
                    {
                        $SN = strtoupper(bin2hex($SN)); 
                    }

                    $html['SN'] = $SN;
            } 
            catch (\Exception $e) 
            {$html['SN'] = '-';}

            try { 
                    $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15.".$ifindex, TRUE);
                    $xxx = current($Status);
                    $xxx = str_replace('INTEGER: ', '', trim($xxx));
                    $position = strpos($xxx, '1');
                    if ($position !== false)
                    {
                        $Status = 'Online';
                    }
                    else $Status = 'Offline';
            } 
            catch (\Exception $e) 
            {$Status = '';}

        
            try { 
                        $Match = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.18.".$ifindex, TRUE);  
                        $Match =  trim(str_replace("INTEGER: ","",$Match));
                        $html['Match'] = $Match ?? '-';
                } 
                catch (\Exception $e) 
                {$html['Match'] = '-';}

            $Uptime = '';$Downtime = '';
            if($Status == 'Online')
            {
                try { 
                        $Uptime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.23.".$ifindex, TRUE); 
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
                        $html['Uptime'] = $Uptime;
                    
                } 
                catch (\Exception $e) 
                {$html['Uptime'] = '-';}
            }
            else
            {
                try { 
                        $Downtime = $snmp->get(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.24.".$ifindex, TRUE); 
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
                        $html['Downtime'] =  $Downtime;
                } 
                catch (\Exception $e) 
                {$html['Downtime'] = '-';}

            }

        }
        else 
        {
           return response()->json(['error' => 'ოელტეს ტიპი ვერ დადგინდა GPON , EPON']);
        }

       
        return $html;
    }

    static public function OLT_SIDE_ONT_CONTROL_DISABLE($ip,$read,$write,$ifindex) 
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
 
        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($ifindex); 
        if (strpos($OltType, 'GPON') !== false)
        {       
            try {
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.46.1.1.'.$ifindex, 'i', '2'); 
            } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp_RW->getError()]);
            }
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try {
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.57.1.1.'.$ifindex, 'i', '2'); 
            } 
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

    static public function OLT_SIDE_ONT_CONTROL_ENABLE($ip,$read,$write,$ifindex)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $snmp_RW  = new \SNMP(\SNMP::VERSION_2c, $ip, $write);  
 
        $OltType = OLTSIDE_HUAWEI::GPON_EPON_PORT($ifindex); 
        if (strpos($OltType, 'GPON') !== false)
        {       
            try {
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.46.1.1.'.$ifindex, 'i', '1'); 
            } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $snmp_RW->getError()]);
            }
        }
        else if (strpos($OltType, 'EPON') !== false)
        {
            try {
                    $snmp_RW->set('1.3.6.1.4.1.2011.6.128.1.1.2.57.1.1.'.$ifindex, 'i', '1'); 
            } 
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

    static public function Pon_Port_Custom($value)
    {
        $Data = [
           [4194304000, '0.0.65535.0'],
           [4194304256, '0.0.65535.1'],
           [4194304512, '0.0.65535.2'],
           [4194304768, '0.0.65535.3'],
           [4194305024, '0.0.65535.4'],
           [4194305280, '0.0.65535.5'],
           [4194305536, '0.0.65535.6'],
           [4194305792, '0.0.65535.7'],
           [4194306048, '0.0.65535.8'],
           [4194306304, '0.0.65535.9'],
           [4194306560, '0.0.65535.10'],
           [4194306816, '0.0.65535.11'],
           [4194307072, '0.0.65535.12'],
           [4194307328, '0.0.65535.13'],
           [4194307584, '0.0.65535.14'],
           [4194307840, '0.0.65535.15'],
           [4194312192, '0.1.65535.0'],
           [4194312448, '0.1.65535.1'],
           [4194312704, '0.1.65535.2'],
           [4194312960, '0.1.65535.3'],
           [4194313216, '0.1.65535.4'],
           [4194313472, '0.1.65535.5'],
           [4194313728, '0.1.65535.6'],
           [4194313984, '0.1.65535.7'],
           [4194314240, '0.1.65535.8'],
           [4194314496, '0.1.65535.9'],
           [4194314752, '0.1.65535.10'],
           [4194315008, '0.1.65535.11'],
           [4194315264, '0.1.65535.12'],
           [4194315520, '0.1.65535.13'],
           [4194315776, '0.1.65535.14'],
           [4194316032, '0.1.65535.15'],
           [4194320384, '0.2.65535.0'],
           [4194320640, '0.2.65535.1'],
           [4194320896, '0.2.65535.2'],
           [4194321152, '0.2.65535.3'],
           [4194321408, '0.2.65535.4'],
           [4194321664, '0.2.65535.5'],
           [4194321920, '0.2.65535.6'],
           [4194322176, '0.2.65535.7'],
           [4194322432, '0.2.65535.8'],
           [4194322688, '0.2.65535.9'],
           [4194322944, '0.2.65535.10'],
           [4194323200, '0.2.65535.11'],
           [4194323456, '0.2.65535.12'],
           [4194323712, '0.2.65535.13'],
           [4194323968, '0.2.65535.14'],
           [4194324224, '0.2.65535.15'],
           [4194328576, '0.3.65535.0'],
           [4194328832, '0.3.65535.1'],
           [4194329088, '0.3.65535.2'],
           [4194329344, '0.3.65535.3'],
           [4194329600, '0.3.65535.4'],
           [4194329856, '0.3.65535.5'],
           [4194330112, '0.3.65535.6'],
           [4194330368, '0.3.65535.7'],
           [4194330624, '0.3.65535.8'],
           [4194330880, '0.3.65535.9'],
           [4194331136, '0.3.65535.10'],
           [4194331392, '0.3.65535.11'],
           [4194331648, '0.3.65535.12'],
           [4194331904, '0.3.65535.13'],
           [4194332160, '0.3.65535.14'],
           [4194332416, '0.3.65535.15'],
           [4194336768, '0.4.65535.0'],
           [4194337024, '0.4.65535.1'],
           [4194337280, '0.4.65535.2'],
           [4194337536, '0.4.65535.3'],
           [4194337792, '0.4.65535.4'],
           [4194338048, '0.4.65535.5'],
           [4194338304, '0.4.65535.6'],
           [4194338560, '0.4.65535.7'],
           [4194338816, '0.4.65535.8'],
           [4194339072, '0.4.65535.9'],
           [4194339328, '0.4.65535.10'],
           [4194339584, '0.4.65535.11'],
           [4194339840, '0.4.65535.12'],
           [4194340096, '0.4.65535.13'],
           [4194340352, '0.4.65535.14'],
           [4194340608, '0.4.65535.15'],
           [4194344960, '0.5.65535.0'],
           [4194345216, '0.5.65535.1'],
           [4194345472, '0.5.65535.2'],
           [4194345728, '0.5.65535.3'],
           [4194345984, '0.5.65535.4'],
           [4194346240, '0.5.65535.5'],
           [4194346496, '0.5.65535.6'],
           [4194346752, '0.5.65535.7'],
           [4194347008, '0.5.65535.8'],
           [4194347264, '0.5.65535.9'],
           [4194347520, '0.5.65535.10'],
           [4194347776, '0.5.65535.11'],
           [4194348032, '0.5.65535.12'],
           [4194348288, '0.5.65535.13'],
           [4194348544, '0.5.65535.14'],
           [4194348800, '0.5.65535.15'],
           [4194353152, '0.6.65535.0'],
           [4194353408, '0.6.65535.1'],
           [4194353664, '0.6.65535.2'],
           [4194353920, '0.6.65535.3'],
           [4194354176, '0.6.65535.4'],
           [4194354432, '0.6.65535.5'],
           [4194354688, '0.6.65535.6'],
           [4194354944, '0.6.65535.7'],
           [4194355200, '0.6.65535.8'],
           [4194355456, '0.6.65535.9'],
           [4194355712, '0.6.65535.10'],
           [4194355968, '0.6.65535.11'],
           [4194356224, '0.6.65535.12'],
           [4194356480, '0.6.65535.13'],
           [4194356736, '0.6.65535.14'],
           [4194356992, '0.6.65535.15']
           
       ];
    
       foreach ($Data as $item) 
       {
           if ($value == $item[0]) 
           {
               return $item[1];          
           }
       }
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
        [4194356992, 'GPON 0/6/15'],
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

    static public function GPON_EPON_PORT_FIXED($ifIndex)
    {
        $board_type = ( $ifIndex & bindec('11111110000000000000000000000000') ) >> 25 ;
        switch($board_type) 
        {
            case "126":  //EPON
                $shelf_no       = ( $ifIndex & bindec('00000001111110000000000000000000') ) >> 19 ;
                $slot_no        = ( $ifIndex & bindec('00000000000001111110000000000000') ) >> 13 ;
                $port_no        = ( $ifIndex & bindec('00000000000000000001111100000000') ) >> 8  ;

                return $shelf_no.'.'.$slot_no.'.65535.'.$port_no;
            case "125":  //GPON
                $shelf_no       = ( $ifIndex & bindec('00000001111110000000000000000000') ) >> 19 ;
                $slot_no        = ( $ifIndex & bindec('00000000000001111110000000000000') ) >> 13 ;
                $port_no        = ( $ifIndex & bindec('00000000000000000001111100000000') ) >> 8  ;
                
                return $shelf_no.'.'.$slot_no.'.65535.'.$port_no;
        }
    }

    static public function convertSecondsToTime($seconds) 
    {
        $timeUnits = [
            // 'year'  => 31536000, // 365 days
            // 'month' => 2592000,  // 30 days approximation
            // 'week'  => 604800,   // 7 days
            'day'   => 86400,    // 24 hours
            'hour'  => 3600,     // 60 minutes
            'minute'=> 60,       // 60 seconds
            'second'=> 1         // 1 second
        ];
    
        $timeString = '';
    
        foreach ($timeUnits as $unit => $value) {
            if ($seconds >= $value) {
                $unitValue = floor($seconds / $value);
                $seconds %= $value;
                $timeString .= $unitValue . ' ' . $unit . ($unitValue > 1 ? 's' : '') . ', ';
            }
        }
    
        // Remove trailing comma and space
        $timeString = rtrim($timeString, ', ');
    
        return $timeString;
    }
}
