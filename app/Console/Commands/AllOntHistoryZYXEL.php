<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AllOntHistoryZYXEL extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:all-ont-history-z-y-x-e-l';

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
        
        $Type = DB::table('devices')->where('Type','ZYXEL')->get();   
        
        foreach ($Type as $olt) 
        {
            $snmp    = new \SNMP(\SNMP::VERSION_2c, $olt->Address, $olt->snmpRcomunity);

            try { $Name = $snmp->walk(".1.3.6.1.2.1.31.1.1.1.18", TRUE);} 
            catch (\Exception $e) 
            {
                continue;
            }


            foreach ($Name as $key => $value) 
            {

                $value = str_replace('STRING: ','', $value);

                try { 
                        $Link = $snmp->get(".1.3.6.1.2.1.2.2.1.8.".$key, TRUE);  
                }catch (\Exception $e){$Link = '-';}

                try { 
                        $PortErrors = $snmp->get("1.3.6.1.2.1.2.2.1.14.".$key, TRUE);
                }catch (\Exception $e){$PortErrors = '-';}

 

                $RealStatus = '-';
                if (strpos($Link, 'up') !== false)
                {
                    $RealStatus = 'UP';
                }
                else
                {
                    $RealStatus = 'DOWN';
                }
                

                try{

                        $currentMonthName = '_'.Carbon::now()->format('F');
                        if(isset($value) && !empty($value))$descr = trim($value);
                        DB::table($currentMonthName)->insert([
                            'descr'         => $descr,
                            'olt'           => $olt->Address,                   
                            'ponPort'       => $key,                    
                            'onuStatus'     => $RealStatus,
                            'error'         => (int)$PortErrors,
                            'last_update'   => now(),
                        ]);
                }catch (\Exception $e){}
            }
 
               

        }
    }
}
