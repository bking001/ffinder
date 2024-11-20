<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\PonsStatsModel;
use Illuminate\Support\Facades\Log;


class UpdatePons extends Command
{

    protected $signature = 'app:update-pons';
    protected $description = 'Command description';

    public function handle()
    {
        try {
                $credentials = DB::table('devices')->get();

                foreach ($credentials as $key => $value) 
                {
                    if ($value->Type == 'BDCOM')
                    {
                        PonsStatsModel::Update_BDCOM($value->Address, $value->snmpRcomunity, $value->Type, $value->device_name, $value->mast);
                    }
                    else if ($value->Type == 'HUAWEI')
                    {
                        PonsStatsModel::Update_HUAWEI($value->Address, $value->snmpRcomunity, $value->Type, $value->device_name, $value->mast);
                    }
                    else if ($value->Type == 'ZTE')
                    {
                        PonsStatsModel::Update_ZTE($value->Address, $value->snmpRcomunity, $value->Type, $value->device_name, $value->mast);
                    }
                    else if ($value->Type == 'VSOLUTION')
                    {
                        PonsStatsModel::Update_VSOLUTION($value->Address, $value->snmpRcomunity, $value->Type, $value->device_name, $value->mast);
                    }
                    else if ($value->Type == 'HSGQ')
                    {
                        $token = DB::table('parameters')->where('type','hsgq')->first();  
                        PonsStatsModel::Update_HSGQ($value->Address, $token->password, $value->Type, $value->device_name, $value->mast);
                    } 
                }
        } catch (\Exception $e) 
        {
            Log::error('Error updating PONS statistics: ' . $e->getMessage());
            $this->error('Error updating PONS statistics: ' . $e->getMessage());
        }

        try{

                $deviceAddresses = DB::table('devices')->pluck('Address')->toArray();

                $ponAddresses = DB::table('PonStatistic')->pluck('Address')->toArray();

                // Find addresses in PonStatistic that are not in devices
                $addressesToDelete = array_diff($ponAddresses, $deviceAddresses);

                if (!empty($addressesToDelete)) {
                    DB::table('PonStatistic')->whereIn('Address', $addressesToDelete)->delete();
                }
            } catch (\Exception $e) {
                Log::error('Error updating PONS statistics: ' . $e->getMessage());
                $this->error('Error updating PONS statistics: ' . $e->getMessage());
            }

    }
}
