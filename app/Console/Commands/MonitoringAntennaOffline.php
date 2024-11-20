<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\TaskMonitoring;
use Illuminate\Support\Facades\DB;
use App\Models\airsoftModel;

class MonitoringAntennaOffline extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:monitoring-antenna-offline';

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
        ->where('type', 158)
        ->where(function($query) {
            $query->where('taskStatus', 1)
                  ->orWhere('taskStatus', 2)
                  ->orWhere('taskStatus', 9);
        })
        ->get();
 
        foreach ($Dbm as $key => $value) 
        {
           $data      = airsoftModel::ab_search($value->user_id);         
           $json      = json_decode($data, true);   
           $AntennaIp = $json['user_ip'];
           $secIp     = $json['sector_ip'];
          
           if(!empty($AntennaIp))
           {       
                $creds = DB::table('parameters')->where('type','antenna')->first();  
                TaskMonitoring::ANTENNA_OFFLINE($secIp,$creds->username,$creds->password,$value->user_id,$value->task_id,$AntennaIp);
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
