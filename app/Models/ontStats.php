<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\HUAWEI;
use App\Models\ZTE;

class ontStats extends Model
{
    use HasFactory;

    static public function Update_onts_BDCOM($ip,$read,$Type,$name)
    {

        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $port = '';
        try {$port = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);} 
        catch (\Exception $e) 
        {}

        $Sec_Index_By_Onu_Mac = '';
        try {$Sec_Index_By_Onu_Mac  = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.3", TRUE);} 
        catch (\Exception $e){}
 
        try {
                foreach ($port as $key => $value) 
                {
                    $value = str_replace("STRING: ", "", $value);
        
                    if(strpos($value,':') !== false)
                    {
                        $Description = '';
                        try {$Description = str_replace("STRING: ", "",$snmp->get("IF-MIB::ifAlias.".$key, TRUE)); if(!$Description)$Description = 'N/A';} 
                        catch (\Exception $e){$Description = 'N/A';}
            
                        $MacOnu = '';
                        try {
                                $MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$key, TRUE));				
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
            
                        }catch (\Exception $e){$MacOnu = '';}   

                        DB::table('ontList')->updateOrInsert([
                            'oltAddress'    => $ip,
                            'oltType'       => $Type,
                            'oltName'       => $name,
                            'onuDescr'      => trim($Description) ?? '-',
                            'ifindex'       => $key,
                            'ponPort'       => $value,
                            'onuMac'        => $MacOnu,
                            'last_update'   => now()
                        ]);
            
                    }
                }
        }catch (\Exception $e){}

        return $html;
    }

    static public function Update_onts_VSOLUTION($ip,$read,$Type,$name)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

      
        $Descr = '';
        try { $Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
 
        
        $OnuMac = [];
        try {$OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5" , TRUE);} 
        catch (\Exception $e){}

        try {
                foreach ($Descr as $key => $value) 
                {
                    $value = str_replace("STRING: ", "", $value);
                    $value = str_replace("\"", "", $value);
                    $value = trim($value);
                    $MacOnu = '';

                    foreach ($OnuMac as $keyZ => $valueZ) 
                    {
                        if($key === $keyZ)
                        {
                            $valueZ = str_replace("STRING: ", "", $valueZ);
                            $valueZ = str_replace("\"", "", $valueZ);
                            $MacOnu = $valueZ;
                        }
                    }

                    DB::table('ontList')->updateOrInsert([
                        'oltAddress'    => $ip,
                        'oltType'       => $Type,
                        'oltName'       => $name,
                        'onuDescr'      => $value ?? '-',
                        'ifindex'       => $key,
                        'ponPort'       => 'EPON0/'.str_replace('.',':',$key),
                        'onuMac'        => $MacOnu,
                        'last_update'   => now()
                    ]);
                }
        }catch (\Exception $e){}

        return $html;
    }

    static public function Update_onts_HSGQ($ip,$token,$Type,$name)
    {
        $html = [];

        try {
                for ($counter = 1; $counter < 9; $counter++) 
                {
                    $ArraySecond =  HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$counter,$token);
                    $SecDataArray = json_decode($ArraySecond, true); 
                
                    foreach ($SecDataArray['data'] as $key => $item) 
                    {                         
                        DB::table('ontList')->updateOrInsert([
                            'oltAddress'    => $ip,
                            'oltType'       => $Type,
                            'oltName'       => $name,
                            'onuDescr'      => trim($item['onu_name']) ?? '-',
                            'ifindex'       => $item['port_id'].'.'.$item['onu_id'],
                            'ponPort'       => 'EPON0/'.$item['port_id'].':'.$item['onu_id'],
                            'onuMac'        => $item['macaddr'],
                            'last_update'   => now()
                        ]);
                    }
                    
                }           

        }catch (\Exception $e){}

        return $html;
    }
     
    
    static public function Update_onts_HUAWEI($ip,$read,$Type,$name)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $PonList = '';
        try {$PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9", TRUE);} 
        catch (\Exception $e) 
        {}
        
        try { $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3", TRUE); } 
        catch (\Exception $e) 
        {$SN = '';}
 
        try {
                foreach ($PonList as $key => $value) 
                {
                    $value = str_replace("STRING:","",$value);
                    $value = str_replace("\"", "",$value); 
                    $value = trim($value);
                    $MacOnu = '';
                    foreach ($SN as $keyX => $valueX) 
                    {
                    if($key === $keyX)
                    {
                            $valueX      = str_replace("Hex-STRING: ", "", $valueX);
                            $valueX      = str_replace("STRING: ", "", $valueX);
                            $valueX      = str_replace("\"", "", $valueX);   
                            $valueX      = trim(str_replace(" ", "", $valueX));
                            if(strlen($valueX) < 15 )
                            {
                                $valueX = strtoupper(bin2hex($valueX)); 
                            }
                            $MacOnu =  $valueX;
                    }
                    }

                    $ponPort = explode('.',$key);
            
        
                    DB::table('ontList')->updateOrInsert([
                        'oltAddress'    => $ip,
                        'oltType'       => $Type,
                        'oltName'       => $name,
                        'onuDescr'      => $value ?? '-',
                        'ifindex'       => $key,
                        'ponPort'       => HUAWEI::Pon_Port($ponPort[0]).':'.$ponPort[1],
                        'onuMac'        => $MacOnu,
                        'last_update'   => now()
                    ]);
                }
        }catch (\Exception $e){}


        // ეპონის მხარე 

        $EPON_PonList = '';
        try { $EPON_PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9", TRUE);} 
        catch (\Exception $e) 
        {}

        try { $EPON_SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3", TRUE); } 
        catch (\Exception $e) 
        {$EPON_SN = '';}
        
        try {
                foreach ($EPON_PonList as $EPON_key => $EPON_value) 
                {
                    $EPON_value = str_replace("STRING:","",$EPON_value);
                    $EPON_value = str_replace("\"", "",$EPON_value); 
                    $EPON_value = trim($EPON_value);  
                    $EPON_MacOnu = '';
                    foreach ($EPON_SN as $EPON_keyX => $EPON_valueX) 
                    { 
                        if($EPON_key === $EPON_keyX)
                        {
                                $EPON_valueX      = str_replace("Hex-STRING: ", "", $EPON_valueX);
                                $EPON_valueX      = str_replace("STRING: ", "", $EPON_valueX);
                                $EPON_valueX      = str_replace("\"", "", $EPON_valueX);   
                                $EPON_valueX      = trim(str_replace(" ", "", $EPON_valueX));  
                                if(strlen($EPON_valueX) < 12 && strlen($EPON_valueX) > 0)
                                {
                                    $EPON_valueX = strtoupper(bin2hex($EPON_valueX)); 
                                }

                                $EPON_valueX = str_split($EPON_valueX, 2);
                                $EPON_MacOnu = implode(':', $EPON_valueX);    
 
                        }
                    }

                    $ponPort = explode('.',$EPON_key);

 
                    DB::table('ontList')->updateOrInsert([
                        'oltAddress'    => $ip,
                        'oltType'       => $Type,
                        'oltName'       => $name,
                        'onuDescr'      => $EPON_value ?? '-',
                        'ifindex'       => $EPON_key,
                        'ponPort'       => HUAWEI::GPON_EPON_PORT($ponPort[0]).':'.$ponPort[1],
                        'onuMac'        => $EPON_MacOnu,
                        'last_update'   => now()
                    ]);
                }
        }catch (\Exception $e){}

        return $html; 
    }
     
    static public function Update_onts_ZTE($ip,$read,$Type,$name)
    {
        $html = [];

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $OnuDesc = '';
        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3", TRUE);} 
        catch (\Exception $e){}

        try {
                foreach ($OnuDesc as $key => $value) 
                {
                    $value = str_replace('STRING: ','',$value);
                    $value = str_replace("$",'',$value);
                    $value = str_replace("\"",'',$value);
                    

                    $valueSN = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.5.".$key, TRUE);
                    $valueSN = str_replace('Hex-STRING: ','',$valueSN);
                    $valueSN = str_replace('STRING: ','',$valueSN);
                    $valueSN = str_replace(' ','',$valueSN);
                    $valueSN = str_replace("\"",'',$valueSN);
                    

                    if(strlen($valueSN) < 10 )
                    {  
                        $valueSN  = bin2hex($valueSN);
                    }

                    $ponPort = explode('.',$key);
                    if(isset($value) && !empty($value))$value = trim($value);
            
                    DB::table('ontList')->updateOrInsert([
                        'oltAddress'    => $ip,
                        'oltType'       => $Type,
                        'oltName'       => $name,
                        'onuDescr'      => $value ?? '-',
                        'ifindex'       => $key,
                        'ponPort'       => ZTE::Pon_Port($ponPort[0])[1].':'.$ponPort[1],
                        'onuMac'        => $valueSN,
                        'last_update'   => now()
                    ]);

                }
        }catch (\Exception $e){}


        return $html;
    }
    
    static public function ClonesCount()
    {
        $Count = 0;
        $html = [];

         DB::table('ontListView')->truncate();

        $duplicateOnuMacs = DB::table('ontList')
        ->select('onuDescr')
        ->groupBy('onuDescr')
        ->havingRaw('COUNT(*) > 1')
        ->pluck('onuDescr');

        $duplicateRows = DB::table('ontList')
            ->whereIn('onuDescr', $duplicateOnuMacs)
            ->get();

        foreach ($duplicateRows as $row)                                            
        {
            if( trim($row->onuDescr) !== 'N/A' && trim($row->onuDescr) !== 'NULL' && strpos(trim($row->onuDescr), 'ONU') === false)
            {
                DB::table('ontListView')->updateOrInsert([
                    'secID'      => $row->id,
                    'oltName'    => $row->oltName,
                    'oltAddress' => $row->oltAddress,
                    'oltType'    => $row->oltType,
                    'onuDescr'   => $row->onuDescr,
                    'ifindex'    => $row->ifindex,
                    'ponPort'    => $row->ponPort,
                    'onuMac'     => $row->onuMac,
                    'last_update'=> $row->last_update,
                    'ByType'     => 'DESCRIPTION'
                ]);
                $Count++;
            }
            
        }
 
      
        $duplicateOnuMacs = DB::table('ontList')
            ->select('onuMac')
            ->groupBy('onuMac')
            ->havingRaw('COUNT(*) > 1')
            ->pluck('onuMac');                                      

        $duplicateRows = DB::table('ontList')
            ->whereIn('onuMac', $duplicateOnuMacs)
            ->get();
            
        foreach ($duplicateRows as $row) 
        { 
            if( $row->onuMac != 'N/A')
            {
                DB::table('ontListView')->updateOrInsert([
                    'secID'      => $row->id,
                    'oltName'    => $row->oltName,
                    'oltAddress' => $row->oltAddress,
                    'oltType'    => $row->oltType,
                    'onuDescr'   => $row->onuDescr,
                    'ifindex'    => $row->ifindex,
                    'ponPort'    => $row->ponPort,
                    'onuMac'     => $row->onuMac,
                    'last_update'=> $row->last_update,
                    'ByType'     => 'MAC'
                ]);
                
                $Count++;
            }
        }

        DB::table('ontColoneCountNum')->Update([
            'count'    => $Count
        ]);

        $html ['Count'] =  $Count;
        return $html;
    }

    static public function NaCount()
    {
        $Count = 0;
        $html = [];

        DB::table('NAontListView')->truncate();

        $duplicateRows = DB::table('ontList')->get();

        foreach ($duplicateRows as $row)                                            
        {   
            if( trim($row->onuDescr) == 'N/A' || trim($row->onuDescr) == 'NULL' || trim($row->onuDescr) == 'ONT_NO_DESCRIPTION' || trim($row->onuDescr) == ' ' || strpos(trim($row->onuDescr), 'ONU') !== false)
            {
                DB::table('NAontListView')->updateOrInsert([
                    'secID'      => $row->id, 
                    'oltName'    => $row->oltName,
                    'oltAddress' => $row->oltAddress,
                    'oltType'    => $row->oltType,
                    'onuDescr'   => $row->onuDescr,
                    'ifindex'    => $row->ifindex,
                    'ponPort'    => $row->ponPort,
                    'onuMac'     => $row->onuMac,
                    'last_update'=> $row->last_update,
                ]);
                $Count++;
            }
            
        }
 
        DB::table('ontNACountNum')->Update([
            'count'    => $Count
        ]);

        $html ['Count'] =  $Count;
        return $html;
    }
}
