<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\HSGQ;
use Carbon\Carbon;

class AllOntHistoryHSGQ extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-ont-history-h-s-g-q';

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
        
        $Type = DB::table('devices')->where('Type','HSGQ')->get();   
        $hsgq = DB::table('parameters')->where('type','HSGQ')->first();   

        $DevicesData = DB::table('devices')->get();  

        foreach ($Type as $olt) 
        {
            for ($i=1; $i < 9; $i++) 
            { 
                $ArraySecond =  HSGQ::API('https://'.$olt->Address.'/onu_allow_list?port_id='.$i,$hsgq->password);   
                $SecDataArray = json_decode($ArraySecond, true); 

                if(isset($SecDataArray['data']))
                {
                    foreach ($SecDataArray['data'] as $key => $item) 
                    {
 
                        $currentMonthName = '_'.Carbon::now()->format('F');

                        DB::table($currentMonthName)->insert([
                            'descr'         => trim($item['onu_name']),
                            'onuMac'        => $item['macaddr'],
                            'olt'           => $olt->Address,                   
                            'ponPort'       => 'EPON0/'.$item['port_id'].':'.$item['onu_id'],                    
                            'onuStatus'     => $item['status'],
                            'reason'        => $item['last_down_reason'],
                            'distance'      => $item['distance'],
                            'dbmRX'         => $item['receive_power'],
                            'last_update'   => now(),
                        ]);

                        $DeviceAddress = $olt->Address;
                     
                        $foundDevice = $DevicesData->filter(function ($device) use ($DeviceAddress) {
                            return $device->Address === $DeviceAddress;
                        })->first();

                        

                        DB::table('AllStatsResult')->updateOrInsert(
                            [
                                'descr' => $item['onu_name'],
                                'olt'         => $olt->Address,
                            ], 
                            [
                                'Type'          => $foundDevice->Type?? 'Unknow',
                                'device_name'   => $foundDevice->device_name?? 'Unknow',
                                'onuMac'        => $item['macaddr'],
                                'olt'           => $olt->Address,                   
                                'ponPort'       => 'EPON0/'.$item['port_id'].':'.$item['onu_id'],                    
                                'onuStatus'     => $item['status'],
                                'reason'        => $item['last_down_reason'],
                                'distance'      => $item['distance'],
                                'dbmRX'         => $item['receive_power'],
                                'last_update'   => now(),
                            ]
                        );
                    }
                } 
            }

        }
    }
}
