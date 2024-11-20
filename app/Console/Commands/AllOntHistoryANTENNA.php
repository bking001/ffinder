<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\UISP;
use Carbon\Carbon;
 

class AllOntHistoryANTENNA extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-ont-history-a-n-t-e-n-n-a';

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
        $Token = DB::table('parameters')->where('type','UISP')->first();  
        
        $url = $Token->url.'/nms/api/v2.1/devices';
        $headers = [
            'Accept: application/json',
            'x-auth-token:'.$Token->password ,
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

 
        $Data   = json_decode($response, true);     
 
        if($Data)
        {
            $currentMonthName = '_'.Carbon::now()->format('F');

            foreach ($Data as $key => $value)
            {  
                $id = '';$SectorMac = '';$SectorIP = '';$ssid = '';$frequency = '';$channelWidth = '';

                $id             = $value['identification']['id'];
                $SectorMac      = $value['identification']['mac'] ?? '-';                    
                $SectorIP       = $value['ipAddress'] ?? '-';
                $ssid           = $value['attributes']['ssid'] ?? '-';
                $frequency      = $value['overview']['frequency'] ?? '-';
                $channelWidth   = $value['overview']['channelWidth'] ?? '-';
                $SectorName     = $value['identification']['name'] ?? '-';


                $Station  = UISP::stations($Token->url,$id,$Token->password);
                $StationData   = json_decode($Station, true);  

                foreach ($StationData as $Skey => $Svalue) 
                {
                    $descr = '';$radio = '';$txSignal = '';$rxSignal = '';$antennaMac = '';
                    
                    $descr      = $Svalue['name'];
                    $radio      = $Svalue['radio'];
                    $txSignal   = $Svalue['txSignal'];
                    $rxSignal   = $Svalue['rxSignal'];
                    $antennaMac = $Svalue['mac'];
 
                    $RealStatus = '-';
                    if($Svalue['connected'] == true)
                    {
                        $RealStatus = 'connected';
                    }
                    else
                    {
                        $RealStatus = 'disconnected';
                    }
                    if(isset($descr) &&  !empty($descr))$descr = trim($descr);

                    DB::table($currentMonthName)->insert([
                        'descr'         => $descr ?? '-',
                        'onuStatus'     => $RealStatus,
                        'onuMac'        => $antennaMac ?? '-',
                        'olt'           => $SectorIP ?? '-',                   
                        'sectorMac'     => $SectorMac ?? '-',
                        'ssid'          => $ssid ?? '-',
                        'txSignal'      => $txSignal ?? '-',
                        'rxSignal'      => $rxSignal ?? '-',
                        'channel'       => $channelWidth ?? '-',
                        'frequency'     => $frequency ?? '-',
                        'ghz'           => $radio ?? '-',
                        'last_update'   => now(),
                    ]);

                    
                    
                //     // DB::table('AllStatsResult')->updateOrInsert(
                //     //     ['descr' => $descr], 
                //     //     [
                //     //         'Type'          => 'ANTENNA',
                //     //         'device_name'   => $SectorName ?? 'Unknow',
                //     //         'onuStatus'     => $RealStatus,
                //     //         'onuMac'        => $antennaMac ?? '-',
                //     //         'olt'           => $SectorIP ?? '-',                   
                //     //         'sectorMac'     => $SectorMac ?? '-',
                //     //         'ssid'          => $ssid ?? '-',
                //     //         'txSignal'      => $txSignal ?? '-',
                //     //         'rxSignal'      => $rxSignal ?? '-',
                //     //         'channel'       => $channelWidth ?? '-',
                //     //         'frequency'     => $frequency ?? '-',
                //     //         'ghz'           => $radio ?? '-',
                //     //         'last_update'   => now(),
                //     //     ]
                //     // );
                }
            
            }
        }
            
    }
}
