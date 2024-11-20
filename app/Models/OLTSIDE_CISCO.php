<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OLTSIDE_CISCO extends Model
{
    use HasFactory;

     
    static public function OLT_SIDE_SYSTEMINFO($ip,$read,$write)
    {
        $html = [];
 
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  
    
        $Name       = '';
        $Model      = '';
        $Uptime     = '';

        try {
                $Name  = $snmp->get('1.3.6.1.2.1.1.5.0');     
                $Name = trim(str_replace('STRING: ','',$Name));
                $Name = trim(str_replace("\"",'',$Name)); 
                $Name = trim(str_replace(".airlink.ge",'',$Name)); 
                 
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
     
        try {
                $Uptime     = $snmp->walk("1.3.6.1.2.1.1.3", TRUE);                                            
                $Model      = $snmp->get('1.3.6.1.2.1.47.1.1.1.1.13.1001'); 
        
                $Model = trim(str_replace('STRING: ','',$Model));
                $Model = trim(str_replace("\"",'',$Model));


                foreach ($Uptime as $key => $value) 
                {
                    if (strpos($value, "Timeticks") !== false) 
                    {
                        $position = strpos($value, ')');
                        if ($position !== false) 
                        { 
                            $Uptime = substr($value, $position + strlen(')'));
                        }    
                    }
                    else
                    {
                        $Uptime = $value;   
                    }
                
                }
        } 
        catch (\Exception $e) 
        {}
  
        $html ['name']       = $Name;     
        $html ['uptime']     = $Uptime;    
        $html ['version']    = $Model;   
 
        $F_Online = 0;$F_Offline = 0;$F_Total = 0;
        try {
                $Link = $snmp->walk(".1.3.6.1.2.1.2.2.1.8", TRUE); 
                foreach ($Link as $key => $value) 
                {
                    $value = trim(str_replace("INTEGER: ",'',$value));   

                    if(strpos(trim(str_replace('','',$snmp->get("1.3.6.1.2.1.2.2.1.2.".$key, TRUE))),'GigabitEthernet1')!== false)    
                    {
                        if(strpos($value,'up')!== false)
                        {
                            $F_Online++;
                            $F_Total++;
                        }
                        else 
                        {
                            $F_Offline++;
                            $F_Total++;
                        }
                    }
                }        
        } 
        catch (\Exception $e) 
        {}

        $html ['totalOnline']   = $F_Online;    
        $html ['totalOffline']  = $F_Offline;    
        $html ['totalOnt']      = $F_Total;

        return $html;
    }
    
    static public function OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$write,$ifindex,$descr)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        
        $snmp_RW = new \SNMP(\SNMP::VERSION_2c, $ip, $write);    

        try {
                $snmp_RW->set('1.3.6.1.2.1.31.1.1.1.18.'.$ifindex, 's', $descr); 
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        return true;
    }



}
