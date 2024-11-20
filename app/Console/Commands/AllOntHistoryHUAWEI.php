<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\HUAWEI;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AllOntHistoryHUAWEI extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-ont-history-h-u-a-w-e-i';

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
        
        $Type = DB::table('devices')->where('Type','HUAWEI')->get();   
        $DevicesData = DB::table('devices')->get();  

        foreach ($Type as $olt) 
        { 
            $snmp    = new \SNMP(\SNMP::VERSION_2c, $olt->Address, $olt->snmpRcomunity);

            $iface = [];$Eiface = [];

            try { $PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9", TRUE);
                    foreach ($PonList as $key => $value)
                    {
                        $iface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $iface[$key]['PonList'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    continue;
                }
            }
                    

            if(isset($PonList))
            { 
                    try { 
                            $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.43.1.3", TRUE); 
                            foreach ($SN as $key => $value)
                            {
                                $iface[$key]['IfId'] = $key;
                                $value      = str_replace("Hex-STRING: ", "", $value);
                                $value      = str_replace("STRING: ", "", $value);
                                $value      = str_replace("\"", "", $value);   
                                $value      = trim(str_replace(" ", "", $value));
                                if(strlen($value) < 15 )
                                {
                                    $value = strtoupper(bin2hex($value)); 
                                }

                                $iface[$key]['SN']       = $value;
                            }
                    } 
                    catch (\Exception $e){$SN = '';}

                    try { 
                            $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.24", TRUE);
                            foreach ($Reason as $key => $value)
                            {
                                $iface[$key]['IfId'] = $key;
                                $value = explode(' ', $value);
                                $value = end($value);
                                $value = trim($value);
                                $value = str_replace("\"", "", $value);
                                $iface[$key]['Reason'] = $value;
                            }
                    }catch (\Exception $e) {$Reason = '';}

                    try {
                            $Distance = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.46.1.20" , TRUE);
                
                            foreach ($Distance as $key => $value)
                            {
                                $iface[$key]['IfId'] = $key;
                                $value = explode(' ', $value);
                                $value = end($value);
                                $value = trim($value);
                                $value = str_replace("\"", "", $value);
                                $iface[$key]['Distance'] = $value;
                            }

                    }catch (\Exception $e){$Distance = '-';}

                    try { 
                            $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15", TRUE);
                            foreach ($Status as $key => $value)
                            {
                                $iface[$key]['IfId'] = $key;
                                $value = explode(' ', $value);
                                $value = end($value);
                                $value = trim($value);
                                $value = str_replace("\"", "", $value);
                                $iface[$key]['Status'] = $value;


                                try { 
                                        $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$key, TRUE);  
                                        $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                                        $Signal  = HUAWEI::SginalFixer($Signal);
                                        $iface[$key]['Signal'] = $Signal;
                                } 
                                catch (\Exception $e){$Signal = '';}
                            }
                    }catch (\Exception $e){$Status = '';}
                 
             
                    foreach ($iface as $key => $value) 
                    {
 
                        $ReadyReason = $iface[$key]['Reason'] ?? '-';
                        if ($ReadyReason == 1) {
                            $FinalReason = 'LOS';
                        } elseif ($ReadyReason == 2) {
                            $FinalReason = 'LOSi/LOBi';  
                        } elseif ($ReadyReason == 3) {
                            $FinalReason = 'LOFI';  
                        } elseif ($ReadyReason == 4) {
                            $FinalReason = 'SFI';
                        } elseif ($ReadyReason == 5) {
                            $FinalReason = 'LOAI';
                        } elseif ($ReadyReason == 6) {
                            $FinalReason = 'LOAMI';
                        } elseif ($ReadyReason == 7) {
                            $FinalReason = 'Deactive ONT Fails';
                        } elseif ($ReadyReason == 8) {
                            $FinalReason = 'Deactive ONT Success';
                        } elseif ($ReadyReason == 9) {
                            $FinalReason = 'Reset ONT';
                        } elseif ($ReadyReason == 10) {
                            $FinalReason = 'Re-register ONT';
                        } elseif ($ReadyReason == 11) {
                            $FinalReason = 'Pop Up Fail';
                        } elseif ($ReadyReason == 13) {
                            $FinalReason = 'Dying-Gasp';
                        } elseif ($ReadyReason == 15) {
                            $FinalReason = 'LOKI';
                        } elseif ($ReadyReason == 18) {
                            $FinalReason = 'Deactived ONT Due to the Ring';
                        } elseif ($ReadyReason == 30) {
                            $FinalReason = 'Shut Down ONT Optical Module';
                        } elseif ($ReadyReason == 31) {
                            $FinalReason = 'Reset ONT by ONT Command';
                        } elseif ($ReadyReason == 32) {
                            $FinalReason = 'Reset ONT by ONT Reset Button';
                        } elseif ($ReadyReason == 33) {
                            $FinalReason = 'Reset ONT by ONT Software';
                        } elseif ($ReadyReason == 34) {
                            $FinalReason = 'Deactived ONT Due to Broadcast Attack';
                        } elseif ($ReadyReason == 35) {
                            $FinalReason = 'Operator Check Fail';
                        } elseif ($ReadyReason == 37) {
                            $FinalReason = 'Rogue ONT Detected by Itself';
                        } elseif ($ReadyReason == -1) {
                            $FinalReason = '-';
                        } else {
                            $FinalReason = 'Unknown Reason';
                        }
 
                        $RealStatus = '-';
                        if($iface[$key]['Status'] == 1)
                        {
                            $RealStatus = 'Online';
                        }
                        else
                        {
                            $RealStatus = 'Offline';
                        }
                    
                        $PonPort = explode('.',$iface[$key]['IfId']);

                        try {
                            
                            $currentMonthName = '_'.Carbon::now()->format('F');
                            if(isset($iface[$key]['PonList']) && !empty($iface[$key]['PonList']))$descr = trim($iface[$key]['PonList']);

                            DB::table($currentMonthName)->insert([
                                'descr'         => $descr ?? '-',
                                'onuMac'        => $iface[$key]['SN'] ?? '-',
                                'olt'           => $olt->Address,                   
                                'ponPort'       => HUAWEI::GPON_EPON_PORT($PonPort[0]).':'.$PonPort[1],                    
                                'onuStatus'     => $RealStatus ?? '-',
                                'reason'        => $FinalReason,
                                'distance'      => $iface[$key]['Distance'] ?? '-',
                                'dbmRX'         => $iface[$key]['Signal'] ?? '-',
                                'last_update'   => now(),
                            ]);

                            $DeviceAddress = $olt->Address;
                            
                            $foundDevice = $DevicesData->filter(function ($device) use ($DeviceAddress) {
                                return $device->Address === $DeviceAddress;
                            })->first();

                            DB::table('AllStatsResult')->updateOrInsert(
                                [
                                    'descr' =>  $iface[$key]['PonList'],
                                    'olt'   => $olt->Address,
                                ], 
                                [
                                    'Type'          => $foundDevice->Type?? 'Unknow',
                                    'device_name'   => $foundDevice->device_name ?? 'Unknow',
                                    'descr'         => $iface[$key]['PonList'] ?? '-',
                                    'onuMac'        => $iface[$key]['SN'] ?? '-',
                                    'olt'           => $olt->Address,                   
                                    'ponPort'       => HUAWEI::GPON_EPON_PORT($PonPort[0]).':'.$PonPort[1],                    
                                    'onuStatus'     => $RealStatus ?? '-',
                                    'reason'        => $FinalReason,
                                    'distance'      => $iface[$key]['Distance'] ?? '-',
                                    'dbmRX'         => $iface[$key]['Signal'] ?? '-',
                                    'last_update'   => now(),
                                ]
                            );
                            
   
                        }catch (\Exception $e){}
                    }

            }
 
            try { 
                    $EPonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9", TRUE);
                    foreach ($EPonList as $key => $value)
                    {
                        $Eiface[$key]['IfId'] = $key;
                        $value = explode(' ', $value);
                        $value = end($value);
                        $value = trim($value);
                        $value = str_replace("\"", "", $value);
                        $Eiface[$key]['PonList'] = $value;
                    }
            } 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    continue;
                }
           
            }

            if(isset($EPonList))
            {
                try { 
                        $SN = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3", TRUE); 
                        foreach ($SN as $key => $value)
                        {
                            $Eiface[$key]['IfId'] = $key;
                            $value      = str_replace("Hex-STRING: ", "", $value);
                            $value      = str_replace("STRING: ", "", $value);
                            $value      = trim(str_replace("\"", "", $value));   
                            $value      = trim(str_replace(" ", ":", $value));
                            if(strlen($value) < 10 )
                            {
                                $value = strtoupper(bin2hex($value)); 
                            }
                                
                            $Eiface[$key]['SN']       = $value;
                        }
                }catch (\Exception $e){$SN = '';}

                try { 
                        $Status = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15", TRUE);
                        foreach ($Status as $key => $value)
                        {
                            $Eiface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $Eiface[$key]['Status'] = $value;


                            
                            try { 
                                    $Signal  = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5.".$key, TRUE);  
                                    $Signal  = trim(str_replace("INTEGER: ", "", $Signal));
                                    $Signal  = HUAWEI::SginalFixer($Signal);
                                    $Eiface[$key]['Signal'] = $Signal;
                                   
                            }catch (\Exception $e) 
                            {$Signal = '';}

                        }
                }catch (\Exception $e){$Status = '';}

                try { 
                        $Reason = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25", TRUE);
                        foreach ($Reason as $key => $value)
                        {
                            $Eiface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $Eiface[$key]['Reason'] = $value;
                        }
                }catch (\Exception $e){$Reason = '';}
 

                try {
                        $Distance = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.57.1.19" , TRUE);
            
                        foreach ($Distance as $key => $value)
                        {
                            $Eiface[$key]['IfId'] = $key;
                            $value = explode(' ', $value);
                            $value = end($value);
                            $value = trim($value);
                            $value = str_replace("\"", "", $value);
                            $Eiface[$key]['Distance'] = $value;
                        }

                }catch (\Exception $e){$Distance = '-';}

                foreach ($Eiface as $key => $value) 
                {

                    $ReadyReason = $Eiface[$key]['Reason'];
                    if ($ReadyReason == 1) {
                        $FinalReason = 'LOS';
                    } elseif ($ReadyReason == 2) {
                        $FinalReason = 'LOSi/LOBi';  
                    } elseif ($ReadyReason == 3) {
                        $FinalReason = 'LOFI';  
                    } elseif ($ReadyReason == 4) {
                        $FinalReason = 'SFI';
                    } elseif ($ReadyReason == 5) {
                        $FinalReason = 'LOAI';
                    } elseif ($ReadyReason == 6) {
                        $FinalReason = 'LOAMI';
                    } elseif ($ReadyReason == 7) {
                        $FinalReason = 'Deactive ONT Fails';
                    } elseif ($ReadyReason == 8) {
                        $FinalReason = 'Deactive ONT Success';
                    } elseif ($ReadyReason == 9) {
                        $FinalReason = 'Reset ONT';
                    } elseif ($ReadyReason == 10) {
                        $FinalReason = 'Re-register ONT';
                    } elseif ($ReadyReason == 11) {
                        $FinalReason = 'Pop Up Fail';
                    } elseif ($ReadyReason == 13) {
                        $FinalReason = 'Dying-Gasp';
                    } elseif ($ReadyReason == 15) {
                        $FinalReason = 'LOKI';
                    } elseif ($ReadyReason == 18) {
                        $FinalReason = 'Deactived ONT Due to the Ring';
                    } elseif ($ReadyReason == 30) {
                        $FinalReason = 'Shut Down ONT Optical Module';
                    } elseif ($ReadyReason == 31) {
                        $FinalReason = 'Reset ONT by ONT Command';
                    } elseif ($ReadyReason == 32) {
                        $FinalReason = 'Reset ONT by ONT Reset Button';
                    } elseif ($ReadyReason == 33) {
                        $FinalReason = 'Reset ONT by ONT Software';
                    } elseif ($ReadyReason == 34) {
                        $FinalReason = 'Deactived ONT Due to Broadcast Attack';
                    } elseif ($ReadyReason == 35) {
                        $FinalReason = 'Operator Check Fail';
                    } elseif ($ReadyReason == 37) {
                        $FinalReason = 'Rogue ONT Detected by Itself';
                    } elseif ($ReadyReason == -1) {
                        $FinalReason = '-';
                    } else {
                        $FinalReason = 'Unknown Reason';
                    }

                    $RealStatus = '-';
                    if($Eiface[$key]['Status'] == 1)
                    {
                        $RealStatus = 'Online';
                    }
                    else
                    {
                        $RealStatus = 'Offline';
                    }
                
                    $PonPort = explode('.',$Eiface[$key]['IfId']);

                    try{
                            $currentMonthName = '_'.Carbon::now()->format('F');
                            if(isset($Eiface[$key]['PonList']) && !empty($Eiface[$key]['PonList']))$Epondescr = trim($Eiface[$key]['PonList']);
                            DB::table($currentMonthName)->insert([
                                'descr'         => $Epondescr ?? '-',
                                'onuMac'        => $Eiface[$key]['SN'] ?? '-',
                                'olt'           => $olt->Address,                   
                                'ponPort'       => HUAWEI::GPON_EPON_PORT($PonPort[0]).':'.$PonPort[1],                    
                                'onuStatus'     => $RealStatus ?? '-',
                                'reason'        => $FinalReason,
                                'distance'      => $Eiface[$key]['Distance'] ?? '-',
                                'dbmRX'         => $Eiface[$key]['Signal'] ?? '-',
                                'last_update'   => now(),
                            ]);

                            
                            $DeviceAddress = $olt->Address;
                            
                            $foundDevice = $DevicesData->filter(function ($device) use ($DeviceAddress) {
                                return $device->Address === $DeviceAddress;
                            })->first();

                            DB::table('AllStatsResult')->updateOrInsert(
                                [
                                    'descr' => $Eiface[$key]['PonList'],
                                    'olt'   => $olt->Address,      
                                ], 
                                [
                                    'Type'          => $foundDevice->Type?? 'Unknow',
                                    'device_name'   => $foundDevice->device_name?? 'Unknow',
                                    'onuMac'        => $Eiface[$key]['SN'] ?? '-',
                                    'olt'           => $olt->Address,                   
                                    'ponPort'       => HUAWEI::GPON_EPON_PORT($PonPort[0]).':'.$PonPort[1],                    
                                    'onuStatus'     => $RealStatus ?? '-',
                                    'reason'        => $FinalReason,
                                    'distance'      => $Eiface[$key]['Distance'] ?? '-',
                                    'dbmRX'         => $Eiface[$key]['Signal'] ?? '-',
                                    'last_update'   => now(),
                                ]
                            );

                    }catch (\Exception $e){}


                }

            }
         
 
        }
    }
}
