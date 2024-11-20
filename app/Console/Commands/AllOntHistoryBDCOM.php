<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\BDCOM;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AllOntHistoryBDCOM extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-ont-history-b-d-c-o-m';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        set_time_limit(360);

        $Type = DB::table('devices')->where('Type','BDCOM')->get();   
        $DevicesData = DB::table('devices')->get();  

        foreach ($Type as $olt) 
        {
            $snmp    = new \SNMP(\SNMP::VERSION_2c, $olt->Address, $olt->snmpRcomunity);

            try {$ifDescr = $snmp->walk(".1.3.6.1.2.1.2.2.1.2", TRUE);} 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    continue;
                }
            }
    
            $Sec_Index_By_Onu_Mac = '';
            try {$Sec_Index_By_Onu_Mac  = $snmp->walk("1.3.6.1.4.1.3320.101.11.1.1.3", TRUE);}  
            catch (\Exception $e){}
     
            foreach ($ifDescr as $key => $value) 
            {
                $value = str_replace("STRING: ", "", $value);
                if(strpos($value,'EPON') !== false && strpos($value,':') !== false)
                {
                     
                    try{ $Status      = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.2.1.2.2.1.8.".$key, TRUE)); }catch (\Exception $e){ $Status = '-';}
                    try{ $Description = str_replace("STRING: ", "",$snmp->get("IF-MIB::ifAlias.".$key, TRUE)); }catch (\Exception $e){ $Description = '-';}
                    try{ $distance    = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.27.".$key, TRUE)); }catch (\Exception $e){ $distance = '-';}
                    try{
                            $Onu_RX      = BDCOM::convertToDecimal(str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.5.1.5.".$key, TRUE)));
                            if ((int)$Onu_RX > 0)$Onu_RX = '-';
                            else if ((int)$Onu_RX == -6553.5)$Onu_RX = '-';
    
                    }catch (\Exception $e){$Onu_RX = '-';}
     
                    try{
                            $MacOnu = str_replace("Hex-STRING: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.10.1.1.3.".$key, TRUE));				
                            $MacOnu = str_replace("STRING:","",$MacOnu);
                            $MacOnu = trim(str_replace("\"", "",$MacOnu));  
                            // $MacOnu = str_replace(" ", ":",$MacOnu);   
  
                            if(strlen($MacOnu) < 10 )
                            {    
                                $MacOnu    = ltrim($MacOnu);
                                $inputMac  = bin2hex($MacOnu);    
                                $macArray  = str_split($inputMac, 2);
                                $MacOnu    = implode(':', $macArray);          
                            }
                            else
                            {
                                $MacOnu    = str_replace(" ", "",$MacOnu);
                                $macArray  = str_split($MacOnu, 2);
                                $MacOnu    = implode(':', $macArray);      
                            }


                    }catch (\Exception $e){ $MacOnu = '-';}
  
                    $Onu_StatusX = '';
                    try{

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
        
                        
                            if(strtoupper($valueEX) == strtoupper($MacOnu))
                            {
                                try {																		 
                                        $Deregreason = str_replace("INTEGER: ", "",$snmp->get(".1.3.6.1.4.1.3320.101.11.1.1.11.".$Zkey, TRUE));
                                        if(trim($Deregreason) == '8')$Onu_StatusX = "wire down"; 
                                        else if(trim($Deregreason) == '9')$Onu_StatusX = "power off"; 
                                        else if(trim($Deregreason) == '2')$Onu_StatusX = "normal";
                                        else if(trim($Deregreason) == '7')$Onu_StatusX = "llid admin down";
                                        else if(trim($Deregreason) == '255')$Onu_StatusX = "unknow";
                                        else if(trim($Deregreason) == '0')$Onu_StatusX = "unknow";
                                } 
                                catch (\Exception $e){$Onu_StatusX = '';}
                            }
                        }
        
                    }catch (\Exception $e){$Onu_RX = '-';}

                    $LastStatus = '-';
                    if (strpos($Status, 'up') !== false)
                    {
                        $LastStatus = 'Online';
                    }
                    else 
                    {
                        $LastStatus = 'Offline';
                    }
 
                    $currentMonthName = '_'.Carbon::now()->format('F');
 
                    DB::table($currentMonthName)->insert([
                        'descr'         => trim($Description),
                        'onuMac'        => $MacOnu,
                        'olt'           => $olt->Address,                   
                        'ponPort'       => $value,                    
                        'onuStatus'     => $LastStatus,
                        'reason'        => $Onu_StatusX,
                        'distance'      => (int)$distance,
                        'dbmRX'         => $Onu_RX,
                        'last_update'   => now(),
                    ]);

                    $DeviceAddress = $olt->Address;
                     
                    $foundDevice = $DevicesData->filter(function ($device) use ($DeviceAddress) {
                        return $device->Address === $DeviceAddress;
                    })->first();

                    DB::table('AllStatsResult')->updateOrInsert(
                        [
                            'descr' => $Description,
                            'olt'   => $olt->Address
                        ], 
                        [
                            'Type'        => $foundDevice->Type?? 'Unknow',
                            'device_name' => $foundDevice->device_name?? 'Unknow',
                            'onuMac'      => $MacOnu,
                            'olt'         => $olt->Address,
                            'ponPort'     => $value,
                            'onuStatus'   => $LastStatus,
                            'reason'      => $Onu_StatusX,
                            'distance'    => (int)$distance,
                            'dbmRX'       => $Onu_RX,
                            'last_update' => now(),
                        ]
                    );
                    
                }
            } 
  
        }

 
    }
}
