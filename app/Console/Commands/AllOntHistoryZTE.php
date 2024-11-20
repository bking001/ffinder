<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\ZTE;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class AllOntHistoryZTE extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-ont-history-z-t-e';

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
        
        $Type = DB::table('devices')->where('Type','ZTE')->get();    

        foreach ($Type as $olt) 
        {
            $snmp    = new \SNMP(\SNMP::VERSION_2c, $olt->Address, $olt->snmpRcomunity);

            try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3", TRUE);} 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    continue;
                }
            }

            if(isset($OnuDesc))
            {
                $DevicesData = DB::table('devices')->get();  

                foreach ($OnuDesc as $key => $value) 
                { 
                    $Description = str_replace('STRING: ','',$value);
                    $Description = str_replace("\"",'',$Description);
                    $Description = str_replace("$$$$",'',$Description);
                    $Description = trim($Description);
                    
    
                    try{

                        $dbm = $snmp->get(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$key.'.1', TRUE); 
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
                        $dbm = round($dbm,2);
                        if((int)$dbm > 0 )$dbm = '-';

                    }catch (\Exception $e){ $dbm = '-';}

                    try{
                        
                        $StatusOnu = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$key, TRUE);
                        $StatusOnu = str_replace('INTEGER: ','',$StatusOnu);
                        $StatusOnu = str_replace("\"",'',$StatusOnu);
                        $StatusOnu = trim($StatusOnu);

                        $xxx     = 'Unknow';
                        if ($StatusOnu == '0')$xxx = 'Logging';
                        else  if ($StatusOnu == '1')$xxx = 'Los';
                        else  if ($StatusOnu == '2')$xxx = 'syncMib';
                        else  if ($StatusOnu == '3')$xxx = 'Working';
                        else  if ($StatusOnu == '4')$xxx = 'Dyinggasp';
                        else  if ($StatusOnu == '5')$xxx = 'AuthFailed';
                        else  if ($StatusOnu == '6')$xxx = 'Offline';

                    }catch (\Exception $e){ $xxx = '-';}

                    try{

                        $valueSN = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.1.1.5.".$key, TRUE);
                        $valueSN = str_replace('Hex-STRING: ','',$valueSN);
                        $valueSN = str_replace('STRING: ','',$valueSN);
                        $valueSN = str_replace(' ','',$valueSN);
                        $valueSN = str_replace("\"",'',$valueSN);
            
                        if(strlen($valueSN) < 10 )
                        {  
                            $valueSN  = bin2hex($valueSN);
                        }
        
                    }catch (\Exception $e){ $valueSN = '-';}


                    try{
                    
                        $valueReason = $snmp->get(".1.3.6.1.4.1.3902.1012.3.28.2.1.7.".$key, TRUE);
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
                   
                    }catch (\Exception $e){ $valueReason = '-';}


                    try{

                        $Distance = $snmp->get("1.3.6.1.4.1.3902.1012.3.11.4.1.2.".$key , TRUE);
                        $Distance =  trim(str_replace("INTEGER: ","",$Distance));

                    }catch (\Exception $e){ $Distance = '-';}
 
                    try{

                        $PonPort = explode('.',$key);
                        $Gpon = ZTE::Pon_Port($PonPort[0]);
                        $OntPort =  $Gpon[1].':'.$PonPort[1];
                    }catch (\Exception $e){ $OntPort = '-';}

                    $currentMonthName = '_'.Carbon::now()->format('F');

                    DB::table($currentMonthName)->insert([
                        'descr'         => $Description,
                        'onuMac'        => $valueSN,
                        'olt'           => $olt->Address,                   
                        'ponPort'       => $OntPort,                    
                        'onuStatus'     => $xxx,
                        'reason'        => $valueReason,
                        'distance'      => $Distance,
                        'dbmRX'         => $dbm,
                        'last_update'   => now(),
                    ]);

                    $DeviceAddress = $olt->Address;
                     
                    $foundDevice = $DevicesData->filter(function ($device) use ($DeviceAddress) {
                        return $device->Address === $DeviceAddress;
                    })->first();

 
                    
                    DB::table('AllStatsResult')->updateOrInsert(
                        [
                            'descr' => $Description,
                            'olt'   => $olt->Address, 
                        ], 
                        [
                            
                            'Type'          => $foundDevice->Type?? 'Unknow',
                            'device_name'   => $foundDevice->device_name?? 'Unknow',
                            'onuMac'        => $valueSN,
                            'olt'           => $olt->Address,                   
                            'ponPort'       => $OntPort,                    
                            'onuStatus'     => $xxx,
                            'reason'        => $valueReason,
                            'distance'      => $Distance,
                            'dbmRX'         => $dbm,
                            'last_update'   => now(),
                        ]
                    );
                }
            }
 
        }
    }
}
