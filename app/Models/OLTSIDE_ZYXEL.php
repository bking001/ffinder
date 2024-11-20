<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OLTSIDE_ZYXEL extends Model
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
                $Name = $snmp->get('1.3.6.1.2.1.1.5.0');                             
                $Name = trim(str_replace('STRING: ','',$Name));
                $Name = trim(str_replace("\"",'',$Name));
        } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
     
        try {
                $Version = $snmp->get("SNMPv2-MIB::sysDescr.0", TRUE);
                $Version = trim(str_replace('STRING: ','',$Version));
                $Version = trim(str_replace("\"",'',$Version));      
                $Uptime  = $snmp->get("1.3.6.1.2.1.1.3.0", TRUE);  
        } 
        catch (\Exception $e) 
        {}
  
        $html ['name']       = $Name;     
        $html ['uptime']     = $Uptime;    
        $html ['version']    = $Version;   
 
        $F_Online = 0;$F_Offline = 0;$F_Total = 0;
        try {
                $Link = $snmp->walk(".1.3.6.1.2.1.2.2.1.8", TRUE); 
                foreach ($Link as $key => $value) 
                {
                    $value = trim(str_replace("INTEGER: ",'',$value));   

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
