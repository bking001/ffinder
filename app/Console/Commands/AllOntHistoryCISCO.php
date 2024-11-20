<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AllOntHistoryCISCO extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-ont-history-c-i-s-c-o';

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
        
        $Type = DB::table('devices')->where('Type','CISCO_CATALYST')->get();   
  
        foreach ($Type as $olt) 
        {
            $snmp    = new \SNMP(\SNMP::VERSION_2c, $olt->Address, $olt->snmpRcomunity);

            try {$Name = ($snmp->walk("1.3.6.1.2.1.2.2.1.2", TRUE));} 
            catch (\Exception $e) 
            {
                if (strpos($e->getMessage(), 'No response') !== false) 
                {
                    continue;
                }
            }

            foreach ($Name as $key => $value) 
            {
                $value = str_replace('STRING: ','', $value);
                if(strpos($value,'GigabitEthernet') !== FALSE)
                {
                    $Status     = str_replace('INTEGER: ','',$snmp->get("1.3.6.1.2.1.2.2.1.8.".$key, TRUE));
                    $Alias      = str_replace('STRING: ','',$snmp->get("1.3.6.1.2.1.31.1.1.1.18.".$key, TRUE));
                    $Speed      = str_replace('Gauge32: ','',$snmp->get("1.3.6.1.2.1.31.1.1.1.15.".$key, TRUE));
                    $InError    = str_replace('Counter32: ','',$snmp->get("1.3.6.1.2.1.2.2.1.14.".$key, TRUE));
                    $OutError   = str_replace('Counter32: ','',$snmp->get("1.3.6.1.2.1.2.2.1.20.".$key, TRUE));
              
                    $RealStatus = '-';
                    if (strpos($Status, 'up') !== false)
                    {
                        $RealStatus = 'UP';
                    }
                    else
                    {
                        $RealStatus = 'DOWN';
                    }
          
                    try{

                        $currentMonthName = '_'.Carbon::now()->format('F');
                        if(isset($Alias))$Alias = trim($Alias);
                        
                        DB::table($currentMonthName)->insert([
                            'descr'         => $Alias ?? '-',
                            'olt'           => $olt->Address,                   
                            'ponPort'       => $value,                    
                            'onuStatus'     => $RealStatus,
                            'speed'         => $Speed,
                            'error'         => (int)$InError + (int)$OutError,
                            'last_update'   => now(),
                        ]);

                    }catch (\Exception $e){}
                }
 
            }
             
        }

        
    }
}
