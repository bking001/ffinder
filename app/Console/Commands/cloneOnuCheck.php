<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\ontStats;
use Illuminate\Support\Facades\Log;


class cloneOnuCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:clone-onu-check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Doubled Onus';

    /**
     * Execute the console command.
     */
    public function handle()
    {

   
        DB::table('ontList')->truncate();

        try {
                $credentials = DB::table('devices')->get();
                foreach ($credentials as $key => $value) 
                {
                    if($value->Type == 'BDCOM')
                    { 
                        ontStats::Update_onts_BDCOM($value->Address,$value->snmpRcomunity,$value->Type,$value->device_name);
                    }
                    else if ($value->Type == 'VSOLUTION')
                    {
                        ontStats::Update_onts_VSOLUTION($value->Address,$value->snmpRcomunity,$value->Type,$value->device_name);    
                    }
                    else if ($value->Type == 'HSGQ')
                    {
                        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  
                        ontStats::Update_onts_HSGQ($value->Address,$HSGQtoken->password,$value->Type,$value->device_name);    
                    }
                    else if ($value->Type == 'HUAWEI')
                    {
                        ontStats::Update_onts_HUAWEI($value->Address,$value->snmpRcomunity,$value->Type,$value->device_name);    
                    }
                    else if ($value->Type == 'ZTE')
                    {
                        ontStats::Update_onts_ZTE($value->Address,$value->snmpRcomunity,$value->Type,$value->device_name);    
                    }
                }

                ontStats::ClonesCount(); 
                ontStats::NaCount();
 
        } catch (\Exception $e) 
        {
            Log::error('Error updating PONS statistics: ' . $e->getMessage());
        }
    }
}
