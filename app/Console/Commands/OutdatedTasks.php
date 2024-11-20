<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class OutdatedTasks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:outdated-tasks';

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
        $subWeeks = Carbon::now()->subWeeks(2);
 
        $Outdated = DB::table('TaskMonitoring')  
        ->Where('created', '<', $subWeeks)
        ->whereIn('taskStatus', [1, 2, 9])
        ->get();
  
 
        foreach ($Outdated as $key => $value)   // აირსოფტიდან ერთ თვეზე მეტი დროის მქონე თასქის წაშლა
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
                   'taskStatus' => 4,
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
