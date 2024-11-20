<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TaskMonitoring;
use App\Models\airsoftModel;
use Illuminate\Support\Facades\DB;

class MonitoringOpticDbm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monitoring-optic-dbm';

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
       
        $Dbm = DB::table('TaskMonitoring')  
        ->where('type', 160)
        ->where(function($query) {
            $query->where('taskStatus', 1)
                  ->orWhere('taskStatus', 2);
        })
        ->get();
 
         
        foreach ($Dbm as $key => $value) 
        {
           $data      = airsoftModel::ab_search($value->user_id);          
           $json      = json_decode($data, true);
           $sector_ip = $json['sector_ip'];
          
           if(!empty($sector_ip))
           {
                $Type = DB::table('devices')->where('Address',$sector_ip)->first();
    
                if(!empty($Type))
                {
                    if($Type->Type == 'BDCOM')
                    {
                        TaskMonitoring::BDCOM_DBM($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'HSGQ')
                    {
                        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  
                        TaskMonitoring::HSGQ_DBM($Type->Address,$HSGQtoken->password,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'VSOLUTION')
                    {
                        TaskMonitoring::VSOLUTION_DBM($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'ZTE')
                    {
                        TaskMonitoring::ZTE_DBM($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'HUAWEI')
                    {
                        TaskMonitoring::HUAWEI_DBM($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                }
           }
        }



        foreach ($Dbm as $key => $value)   // აირსოფტიდან თასკის სტატუსის შემოწმება / დაარქივება
        {   
            if(TaskMonitoring::Task_Health_Check($value->task_id) !== '0')
            {
                $data = DB::table('TaskMonitoring')
                ->where('task_id',$value->task_id )
                ->first();
       
                if ($data) 
                {
                   DB::table('TaskArchive')->insert([
                       'oltName'       => $data->oltName,
                       'oltType'       => $data->oltType,
                       'user_id'       => $data->user_id,
                       'task_id'       => $data->task_id,
                       'staff'         => $data->staff,
                       'type'          => $data->type,
                       'taskStatus' => 3,
                       'created' => $data->created,
                       'last_update' => $data->last_update,
                   ]);
       
       
                   DB::table('TaskMonitoring')
                   ->where('task_id',$value->task_id)
                   ->delete();
               }
            }
        }
    }
}
