<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\airsoftModel;


class TaskFromCrm extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:task-from-crm';

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
        $creds = DB::table('parameters')->where('type','airsoft')->first();   

        $link  =  $creds->url.'/restapi/finder.php';
        $Token = $creds->password;

        $params = [
                        'request' => 'undontasks',
                  ];

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'API-Key:'.$Token,
                'Content-Type: text/plain'
            ),
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {return 'Error: ' . curl_error($ch);}
        curl_close($ch);
 
        $StaffResurl = airsoftModel::airsoft_users_list(); 
        $StaffList   = json_decode($StaffResurl, true);    

        $data = json_decode($response, true);     
      
        
        foreach ($data as $key => $value) 
        {
            if($value['user_provider'] == 111 || $value['user_provider'] == 112 || $value['user_provider'] == 155)
            {
                if($value['type'] == 157 || $value['type'] == 158 || $value['type'] == 159 || $value['type'] == 160 || $value['type'] == 161 ) // || $value['type'] == 162
                {
                    $existingRecord = DB::table('TaskMonitoring')
                    ->where('task_id', $value['task_id'])
                    ->first();
                   
                    $StaffName = $value['staff'];
                    foreach ($StaffList as $Staffkey => $Staffvalue) 
                    {
                        if($value['staff'] == $Staffvalue['id'])
                        {
                            $StaffName = $Staffvalue['name'];
                        }
                    }

                    
                    if (!$existingRecord) 
                    {   
                        DB::table('TaskMonitoring')->insert([
                            'task_id'    => $value['task_id'],
                            'user_id'    => $value['user_id'],
                            'staff'      => $StaffName,
                            'type'       => $value['type'],
                            'created'    => now(),
                            'taskStatus' => 1,
                        ]);
                    } 

                    
                }
            }
       
        }
    }
}
