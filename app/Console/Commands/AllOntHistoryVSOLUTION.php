<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AllOntHistoryVSOLUTION extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-ont-history-v-s-o-l-u-t-i-o-n';

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
        
        $Type = DB::table('devices')->where('Type','VSOLUTION')->get();   

        foreach ($Type as $olt) 
        {
            $snmp = new \SNMP(\SNMP::VERSION_2c, $olt->Address, $olt->snmpRcomunity);

            $iface = [];
            try { $Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9" , TRUE);} 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    continue;
                }
            }

            $Reason = [];
            try {$Reason = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.15", TRUE);}catch (\Exception $e){}
    
            $OnuMac = [];
            try {$OnuMac = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.5" , TRUE);}catch (\Exception $e) {}
    
            $Dmb = [];
            try {$Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7" , TRUE);}catch (\Exception $e){}
    
            $OnuStatus = [];
            try {$OnuStatus = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4" , TRUE);}catch (\Exception $e){}

            $Distance = [];
            try { $Distance = $snmp->walk("1.3.6.1.4.1.37950.1.1.5.12.1.25.1.17" , TRUE);}catch (\Exception $e){}
    

            foreach ($Distance as $key => $value) 
            {  
                $iface[$key]['IfId'] = $key;
                $value=explode('Gauge32: ', $value);
                $value=end($value);
                $value=trim($value);
                $value = str_replace("\"", "", $value);
                $iface[$key]['Distance'] = $value;
            }
            foreach ($Descr as $key => $value) 
            {
                $iface[$key]['IfId']=$key;
                $value=explode(' ', $value);
                $value=end($value);
                $value=trim($value);
                $value = str_replace("\"", "", $value);
                if($value == 'NULL')$value = 'N/A';
                $iface[$key]['IfDescr'] = $value;
            }
            foreach ($Dmb as $key => $value) 
            {  
                $iface[$key]['IfId'] = $key;
                $value=explode('STRING: ', $value);
                $value=end($value);
                $value=trim($value);
                $value = str_replace("\"", "", $value);

                $dBmValue = '';
                $startPos = strpos($value, '(');
                $endPos   = strpos($value, 'dBm');
                if ($startPos !== false && $endPos !== false)
                {
                    $dbmSubstring = substr($value, $startPos + 1, $endPos - $startPos - 1);
                    $dBmValue = $dbmSubstring;
                }

                $iface[$key]['Dmb'] = $dBmValue;
            }
            foreach ($OnuMac as $key => $value) 
            {
                $iface[$key]['IfId']=$key;
                $value=explode(' ', $value);
                $value=end($value);
                $value=trim($value);
                $value = str_replace("\"", "", $value);
                $iface[$key]['OnuMac'] = $value;
            }
            foreach ($OnuStatus as $key => $value) 
            {   
                $iface[$key]['Status']=$key;
                $value=explode(' ', $value);
                $value=end($value);
                $value=trim($value);
                $value = str_replace("\"", "", $value);
                $value = preg_replace('/\(\d+\)/', '', $value);
                if($value)$iface[$key]['Status'] = $value;
                else $iface[$key]['Status'] = '';
            }
            foreach ($Reason as $key => $value) 
            {  
                $iface[$key]['IfId'] = $key;
                $value=explode('INTEGER: ', $value);
                $value=end($value);
                $value=trim($value);
                $value = str_replace("\"", "", $value);
                if($value == 1 || $value == 2){$value = 'Power Off';}
                else if($value == 0 ){$value = 'Wire Down';}
                else $value = 'N/A';
                $iface[$key]['Reason'] = $value;
            }

    
            foreach ($iface as $key => $value)
            {
                $DevicesData = DB::table('devices')->get();  

                if(isset($value['Status']) && isset($value['IfId']))
                {
                    $PonPort = explode('.',$value['IfId']);

                    $LastStatus = '-';
                    if (strpos($value['Status'], '1') !== false)
                    {
                        $LastStatus = 'Online';
                    }
                    else 
                    {
                        $LastStatus = 'Offline';
                    }

                    $currentMonthName = '_'.Carbon::now()->format('F');
                    if(isset($value['IfDescr']) && !empty($value['IfDescr']))$descr = trim($value['IfDescr']);
                    DB::table($currentMonthName)->insert([
                        'descr'         => $descr ?? '-',
                        'onuMac'        => $value['OnuMac'],
                        'olt'           => $olt->Address,                   
                        'ponPort'       => 'EPON0/'.$PonPort[0].':'.$PonPort[1],                    
                        'onuStatus'     => $LastStatus,
                        'reason'        => $value['Reason'],
                        'dbmRX'         => $value['Dmb'], 
                        'distance'      => (int)$value['Distance'],
                        'last_update'   => now(),  
                    ]);

                    
                    $DeviceAddress = $olt->Address;
                     
                    $foundDevice = $DevicesData->filter(function ($device) use ($DeviceAddress) {
                        return $device->Address === $DeviceAddress;
                    })->first();


                    DB::table('AllStatsResult')->updateOrInsert(
                        [
                            'descr' => $value['IfDescr'],
                            'olt'           => $olt->Address,   
                        ], 
                        [
                            'Type'          => $foundDevice->Type?? 'Unknow',
                            'device_name'   => $foundDevice->device_name?? 'Unknow',
                            'onuMac'        => $value['OnuMac'],
                            'olt'           => $olt->Address,                   
                            'ponPort'       => 'EPON0/'.$PonPort[0].':'.$PonPort[1],                    
                            'onuStatus'     => $LastStatus,
                            'reason'        => $value['Reason'],
                            'dbmRX'         => $value['Dmb'], 
                            'distance'      => (int)$value['Distance'],
                            'last_update'   => now(),  
                        ]
                    );
                }
            }

        }
    }
}
