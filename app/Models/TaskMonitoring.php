<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\HSGQ;
use App\Models\BDCOM;
use App\Models\HUAWEI;
use App\Models\airsoftModel;
use phpseclib3\Net\SSH2;
use App\Models\ubiquiti;
use App\Models\phpAPImodel;
use App\Models\Install\antenna;
 
use App\Models\UISP;


class TaskMonitoring extends Model
{
    use HasFactory;


    public static function Monitoring()
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
                if($value['type'] == 157 || $value['type'] == 158 || $value['type'] == 159 || $value['type'] == 160 || $value['type'] == 161 || $value['type'] == 162)
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

                    if ($existingRecord) 
                    {
                        DB::table('TaskMonitoring')
                            ->where('task_id', $value['task_id'])
                            ->update([
                                'task_id'     => $value['task_id'],
                                'user_id'     => $value['user_id'],
                                'staff'       => $StaffName,
                                'type'        => $value['type'],
                            ]);
                    } 
                    else 
                    {
                        DB::table('TaskMonitoring')->insert([
                            'task_id'    => $value['task_id'],
                            'user_id'    => $value['user_id'],
                            'staff'      => $StaffName,
                            'type'       => $value['type'],
                            'taskStatus' => 1,
                        ]);
                    }

                    var_export($value); 
                }
            }
       
        }

    }

    public static function Monitoring_Los()
    {
 
        $Los = DB::table('TaskMonitoring')
        ->where('type', 157)
        ->where(function($query) {
            $query->where('taskStatus', 1)
                  ->orWhere('taskStatus', 2);
        })
        ->get();

 
        foreach ($Los as $key => $value) 
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
                        TaskMonitoring::BDCOM_LOS($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'HSGQ')
                    {
                        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  
                        TaskMonitoring::HSGQ_LOS($Type->Address,$HSGQtoken->password,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'VSOLUTION')
                    {
                        TaskMonitoring::VSOLUTION_LOS($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'ZTE')
                    {
                        TaskMonitoring::ZTE_LOS($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'HUAWEI')
                    {
                        TaskMonitoring::HUAWEI_LOS($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                }
           }
        }
 
   
        foreach ($Los as $key => $value)   // აირსოფტიდან თასკის სტატუსის შემოწმება / დაარქივება
        {   
            if(self::Task_Health_Check($value->task_id) !== '0')
            {
                DB::table('TaskMonitoring')
                ->where('task_id', $value->task_id)
                ->update([
                    'last_update' => now(),
                    'taskStatus'   => 3,
                ]);
            }
        }
        
    }

    public static function Monitoring_Optic_Dbm()
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
            if(self::Task_Health_Check($value->task_id) !== '0')
            {
                DB::table('TaskMonitoring')
                ->where('task_id', $value->task_id)
                ->update([
                    'last_update' => now(),
                    'taskStatus'   => 3,
                ]);
            }
        }
    }

    public static function Monitoring_Antenna_Dbm()
    {

        $Dbm = DB::table('TaskMonitoring')  
        ->where('type', 161)
        ->where(function($query) {
            $query->where('taskStatus', 1)
                  ->orWhere('taskStatus', 2);
        })
        ->get();
 
        foreach ($Dbm as $key => $value) 
        {
           $data      = airsoftModel::ab_search($value->user_id);          
           $json      = json_decode($data, true);
           $sector_ip = $json['user_ip'];

           if(!empty($sector_ip))
           {
                $creds = DB::table('parameters')->where('type','antenna')->first();  
                TaskMonitoring::ANTENNA_DBM($sector_ip,$creds->username,$creds->password,$value->user_id,$value->task_id);
           }

        }
        
        foreach ($Dbm as $key => $value)   // აირსოფტიდან თასკის სტატუსის შემოწმება / დაარქივება
        {   
            if(self::Task_Health_Check($value->task_id) !== '0')
            {
                DB::table('TaskMonitoring')
                ->where('task_id', $value->task_id)
                ->update([
                    'last_update' => now(),
                    'taskStatus'   => 3,
                ]);
            }
        }
         
    }

    public static function Monitoring_Link_Down()
    {
 
        $Dbm = DB::table('TaskMonitoring')
        ->where('type', 159)
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
                        TaskMonitoring::BDCOM_LINK($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'HSGQ')
                    {
                        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  
                        TaskMonitoring::HSGQ_LINK($Type->Address,$HSGQtoken->password,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'VSOLUTION')
                    {
                        TaskMonitoring::VSOLUTION_LINK($Type->Address,$Type->snmpRcomunity,$Type->snmpWcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'ZTE')
                    {
                        TaskMonitoring::ZTE_LINK($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'HUAWEI')
                    {
                        TaskMonitoring::HUAWEI_LINK($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'CISCO')
                    {
                        TaskMonitoring::CISCO_LINK($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }
                    else if($Type->Type == 'ZYXEL')
                    {
                        TaskMonitoring::ZYXEL_LINK($Type->Address,$Type->snmpRcomunity,$value->user_id,$value->task_id);
                    }   
                }
                else
                {            
                    TaskMonitoring::SECTOR_LINK($sector_ip,$value->user_id,$value->task_id);
                }
           }
        }

        foreach ($Dbm as $key => $value)   // აირსოფტიდან თასკის სტატუსის შემოწმება / დაარქივება
        {   
            if(self::Task_Health_Check($value->task_id) !== '0')
            {
                DB::table('TaskMonitoring')
                ->where('task_id', $value->task_id)
                ->update([
                    'last_update' => now(),
                    'taskStatus'   => 3,
                ]);
            }
        }
    }

    public static function Monitoring_Antenna_Offline()
    {
 
        $Dbm = DB::table('TaskMonitoring')
        ->where('type', 158)
        ->where(function($query) {
            $query->where('taskStatus', 1)
                  ->orWhere('taskStatus', 2);
        })
        ->get();
 
  
        foreach ($Dbm as $key => $value) 
        {
           $data      = airsoftModel::ab_search($value->user_id);         
           $json      = json_decode($data, true);
           $sector_ip = $json['user_ip'];
          
           if(!empty($sector_ip))
           {       
                $creds = DB::table('parameters')->where('type','antenna')->first();  
                TaskMonitoring::ANTENNA_OFFLINE($sector_ip,$creds->username,$creds->password,$value->user_id,$value->task_id);
           }
        }

        foreach ($Dbm as $key => $value)   // აირსოფტიდან თასკის სტატუსის შემოწმება / დაარქივება
        {   
            if(self::Task_Health_Check($value->task_id) !== '0')
            {
                DB::table('TaskMonitoring')
                ->where('task_id', $value->task_id)
                ->update([
                    'last_update' => now(),
                    'taskStatus'   => 3,
                ]);
            }
        }
    }

    public static function Monitoring_IP()
    {
 
        $Mikrotik = DB::table('TaskMonitoring')
        ->where('type', 162)
        ->where(function($query) {
            $query->where('taskStatus', 1)
                  ->orWhere('taskStatus', 2);
        })
        ->get();
 
        foreach ($Mikrotik as $key => $value) 
        {
           $data      = airsoftModel::ab_search($value->user_id);          
           $json      = json_decode($data, true);    
           $sector_ip = $json['antenna_ip'];
          
           if(!empty($sector_ip))
           {
                $Type = DB::table('parameters')->where('type','mikrotik')->first();    
    
                if(!empty($Type))
                {
                    return TaskMonitoring::IP_PROBLEM($sector_ip,$Type->username,$Type->password,$value->user_id,$value->task_id);
                }
           }
        }
        
        foreach ($Mikrotik as $key => $value)   // აირსოფტიდან თასკის სტატუსის შემოწმება / დაარქივება
        {   
            if(self::Task_Health_Check($value->task_id) !== '0')
            {
                DB::table('TaskMonitoring')
                ->where('task_id', $value->task_id)
                ->update([
                    'last_update' => now(),
                    'taskStatus'   => 3,
                ]);
            }
        }
    }

    public static function Task_Health_Check($id)
    {
        $creds = DB::table('parameters')->where('type','airsoft')->first();   

        $link  =  $creds->url.'/restapi/finder.php';
        $Token = $creds->password;

 
        $params = [
                        'request' => 'task_status',
                        'id'      => $id
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
 
        $data = json_decode($response, true);   

        return $data['status'];
    }

    ////////////////////////////////////////////////////////////////////   I P    P R O B L E M 
    static public function IP_PROBLEM($ip,$username,$password,$user,$task_id,$created)
    {
        $API = new phpAPImodel();
        $API->debug = false;	
   
        if ($API->connect($ip, $username, $password))
        {
            $CommandInfo  = '/ip/dhcp-server/lease/print';
            $API->write($CommandInfo,false);
            $API->write('?comment='.$user); 
            $READ_ONU  	 = $API->read(false);
            $OnuInfo     = $API->parseResponse($READ_ONU);

            $expire="";$status="";
 
            foreach ($OnuInfo as  $value) 
            {
              if ($value['comment'] == $user) 
              {
                $expire 		= $value['expires-after'] ?? '';
                $status 		= $value['status'] ?? '';
                break;
              }
            }


            $Arp  	  = '/ip/arp/print';
            $API->write($Arp,false);
            $API->write('?comment='.$user);
            $READ_ARP    = $API->read(false);
            $ArpInfo     = $API->parseResponse($READ_ARP);
            $interface = '';
     
            foreach ($ArpInfo as  $value) 
            {												 					
              if ($value['comment'] == $user) 
              {   
                $interface = trim($value['interface']);  
                break;
              }
            }

            $InterfaceLeaseTime = 0;

            $CommandInfo  = '/ip/dhcp-server/print';
            $API->write($CommandInfo,false);
            $API->write('?interface='.$interface); 
            $READ_ONU  	 = $API->read(false);
            $LeaseTime   = $API->parseResponse($READ_ONU);

            foreach ($LeaseTime as  $value) 
            {												 					
              if ($value['interface'] == $interface) 
              {   
                $InterfaceLeaseTime = trim($value['lease-time']);  
                break;
              }
            }

            if($status == 'bound')
            {
                if (self::convertMikroTikTimeToSeconds($expire) < self::convertMikroTikTimeToSeconds($InterfaceLeaseTime) / 2)
                {
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltType'   => 'MIKROTIK',
                        'last_update' => now(),
                        'oltName'   => $ip,
                    ]);

                    DB::table('TaskCronHistory')->insert([
                        'task_id'     => $task_id,
                        'user_id'     => $user,
                        'oltName'     => $ip,
                        'onu_status'  => 'Bound',
                        'dbm'         => $expire,
                        'last_update' => now(),
                    ]);
                }
                else
                {
 
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltName'   => $ip,
                        'oltType'   => 'MIKROTIK',
                        'last_update' => now(),
                        'taskStatus'   => 2,
                    ]);


                    DB::table('TaskCronHistory')->insert([
                        'task_id'     => $task_id,
                        'user_id'     => $user,
                        'oltName'     => $ip,
                        'onu_status'  => 'Bound',
                        'dbm'         => $expire,
                        'last_update' => now(),
                    ]);
                }
            }
            else
            {
                DB::table('TaskCronHistory')->insert([
                    'task_id'     => $task_id,
                    'user_id'     => $user,
                    'oltName'     => $ip,
                    'onu_status'  => 'Waiting',
                    'dbm'         => ' ',
                    'last_update' => now(),
                ]);
            }
 
        }

    }

    ////////////////////////////////////////////////////////////////////   A N T E N N A   O F F L I N E
    static public function ANTENNA_OFFLINE($SectorIP,$username,$password,$user,$task_id,$AntennaIp)
    {
        $Find = false;
        $ubiquiti = new ubiquiti($AntennaIp, $username, $password, false, '80', 5);
	    $String   = $ubiquiti->status(true);
       
        try{
                if (isset($String['wireless']) && is_array($String['wireless'])) 
                {
                    $Signal     = $String['wireless']['signal'];
                }
                
        }catch (\Exception $e){$Signal = '-';}
       
        // if(isset($String) && empty($String))
        // {    
        //     $creds = DB::table('parameters')->where('type','uisp')->first();   
        //     $url   =  $creds->url;
        //     $Token = $creds->password;
           
        //     $Data = antenna::sectorCustomerSearch($user);   
          
        //     if (!empty($Data) && is_array($Data))
        //     {
        //         foreach ($Data as $key => $value) 
        //         {  
        //             $AntennaData =  antenna::stations($value['id'],$Token, $url);   

        //             if (!empty($AntennaData))
        //             {
        //                 $AntennaData   = json_decode($AntennaData, true);
        //                 dd($AntennaData);
        //                 foreach ($AntennaData as $key => $Xvalue) 
        //                 {                        
        //                     if(isset($Xvalue['name']) && $Xvalue['name'] == $user)
        //                     {    
        //                         if(isset($Xvalue['connected']) && $Xvalue['connected'] == true)
        //                         {
        //                             $Find = true;

        //                             DB::table('TaskMonitoring')
        //                             ->where('task_id', $task_id)
        //                             ->update([
        //                                 'oltName'   => $SectorIP,
        //                                 'sector_ip' => $AntennaIp,
        //                                 'oltType'   => 'ANTENNA',
        //                                 'last_update' => now(),
        //                                 'taskStatus'   => 2,
        //                             ]);

                                    
        //                             DB::table('TaskCronHistory')->insert([
        //                                 'task_id'     => $task_id,
        //                                 'user_id'     => $user,
        //                                 'oltName'     => $SectorIP,
        //                                 'sector_ip'   => $AntennaIp,
        //                                 'onu_status'  => 'Online',
        //                                 'dbm'         => $Xvalue['rxSignal'],
        //                                 'last_update' => now(),
        //                             ]);
        //                         }
        //                         else
        //                         {
        //                             $Find = true;
                                    
        //                             DB::table('TaskMonitoring')
        //                             ->where('task_id', $task_id)
        //                             ->update([
        //                                 'oltType'   => 'ANTENNA',
        //                                 'last_update' => now(),
        //                                 'oltName'     => $SectorIP,
        //                                 'sector_ip'   => $AntennaIp,
        //                                 'taskStatus'   => 1,
        //                             ]);
                        
        //                             DB::table('TaskCronHistory')->insert([
        //                                 'task_id'     => $task_id,
        //                                 'user_id'     => $user,
        //                                 'oltName'     => $SectorIP,
        //                                 'sector_ip'   => $AntennaIp,
        //                                 'onu_status'  => 'Offline',
        //                                 'dbm'         => ' ',
        //                                 'last_update' => now(),
        //                             ]);
        //                         }
        //                     }                     
        //                 }                        
        //             }
        //         }
        //     }
        // }
 
        // if($Find == false) 
        // {
            if(isset($String) && !empty($String))
            {
                if($Signal > -70)
                {
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltName'   => $SectorIP,
                        'sector_ip'   => $AntennaIp,
                        'oltType'   => 'ANTENNA',
                        'last_update' => now(),
                        'taskStatus'   => 2,
                    ]);
                }
                else
                {
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltName'   => $SectorIP,
                        'sector_ip'   => $AntennaIp,
                        'oltType'   => 'ANTENNA',
                        'last_update' => now(),
                        'taskStatus'   => 9,
                    ]);
                }
    
                DB::table('TaskCronHistory')->insert([
                    'task_id'     => $task_id,
                    'user_id'     => $user,
                    'oltName'     => $SectorIP,
                    'sector_ip'   => $AntennaIp,
                    'onu_status'  => 'Online',
                    'dbm'         => $Signal,
                    'last_update' => now(),
                ]);
            }
            else
            {
                DB::table('TaskMonitoring')
                ->where('task_id', $task_id)
                ->update([
                    'oltType'   => 'ANTENNA',
                    'last_update' => now(),
                    'oltName'   => $SectorIP,
                    'sector_ip'   => $AntennaIp,
                    'taskStatus'   => 1,
                ]);
    
                DB::table('TaskCronHistory')->insert([
                    'task_id'     => $task_id,
                    'user_id'     => $user,
                    'oltName'     => $SectorIP,
                    'sector_ip'   => $AntennaIp,
                    'onu_status'  => 'Offline',
                    'dbm'         => ' ',
                    'last_update' => now(),
                ]);
            }
        // }
    }
    ////////////////////////////////////////////////////////////////////  L O S
    static public function HUAWEI_LOS($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        foreach ($PonList as $key => $value) 
        {
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("\"", "", $value);
            if (strpos($value, $user) !== false) 
            {
                $Signal = '-';
                

                try {
                        $Status  = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.46.1.15.".$key, TRUE);
                        $xxx = current($Status);
                        $xxx = str_replace('INTEGER: ', '', trim($xxx));

                        $ONTstatus = 'Unknow';
                        $position = strpos($xxx, '1');
                        if ($position !== false)
                        { 
                            $ONTstatus = 'Online';

                            try {
                                $Signal =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$key, TRUE); 
                                $Signal = str_replace('INTEGER: ', '', trim($Signal));
                                $Signal = HUAWEI::SginalFixer($Signal);    
        
                            }catch (\Exception $e){$Signal = '-';}  

                            if((float)($Signal) > -27 && $Signal !== '-')
                            {
                                DB::table('TaskMonitoring')
                                ->where('task_id', $task_id)
                                ->update([
                                    'oltName'   => $ip,
                                    'oltType'   => 'HUAWEI',
                                    'last_update' => now(),
                                    'taskStatus'   => 2,
                                ]);
                            }
                            else
                            {
                                DB::table('TaskMonitoring')
                                ->where('task_id', $task_id)
                                ->update([
                                    'oltName'   => $ip,
                                    'oltType'   => 'HUAWEI',
                                    'last_update' => now(),
                                    'taskStatus'   => 9,
                                ]);
                            }

                        }
                        else
                        {
                            $ONTstatus = 'Offline';

                            DB::table('TaskMonitoring')
                            ->where('task_id', $task_id)
                            ->update([
                                'oltType'   => 'HUAWEI',
                                'last_update' => now(),
                                'oltName'   => $ip,
                                'taskStatus'   => 1,
                            ]);
                        }

                        
                        DB::table('TaskCronHistory')->insert([
                            'task_id'     => $task_id,
                            'user_id'     => $user,
                            'oltName'     => $ip,
                            'onu_status'  => $ONTstatus,
                            'dbm'         => $Signal,
                            'last_update' => now(),
                        ]);

                }catch (\Exception $e){}    
            }

        }

    }

    static public function BDCOM_LOS($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$ifAlias = $snmp->walk("IF-MIB::ifAlias", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        foreach ($ifAlias as $key => $value) 
        {
            $value = trim(str_replace('STRING:','',$value));
            if (strpos($value, $user) !== false)
            {
                 
                try {
                        $Onu_Status         = $snmp->walk("1.3.6.1.4.1.3320.101.10.1.1.26.".$key, TRUE);
                        foreach ($Onu_Status as $Zkey => $value) 
                        {                       
                            $value = trim(str_replace('INTEGER: ','',$value));

                            $Signal = '-';
                            $ONTstatus = 'Unknow';

                            if (trim($value) == 3)
                            {
                                try {
                                        $dbm  = $snmp->walk("1.3.6.1.4.1.3320.101.10.5.1.5.".$key, TRUE);
                                        foreach ($dbm as $key => $value) 
                                        {
                                            $value  = trim(str_replace('INTEGER: ','',($value)));
                                            $Signal =  BDCOM::convertToDecimal(trim($value));
                                        }
                                }catch (\Exception $e){$Signal = '-';}   

                                $ONTstatus = 'Online';


                                if ((float)($Signal) > -27 && (float)$Signal !== -6553.5)
                                {
                                    DB::table('TaskMonitoring')
                                    ->where('task_id', $task_id)
                                    ->update([
                                        'oltType'   => 'BDCOM',
                                        'oltName'   => $ip,
                                        'last_update' => now(),
                                        'taskStatus'   => 2,
                                    ]);
                                }
                                else
                                {
                                    DB::table('TaskMonitoring')
                                    ->where('task_id', $task_id)
                                    ->update([
                                        'oltType'   => 'BDCOM',
                                        'oltName'   => $ip,
                                        'last_update' => now(),
                                        'taskStatus'   => 9,
                                    ]);
                                }

                            }
                            else
                            {
                                $ONTstatus = 'Offline';
                                DB::table('TaskMonitoring')
                                ->where('task_id', $task_id)
                                ->update([
                                    'oltType'   => 'BDCOM',
                                    'last_update' => now(),
                                    'oltName'   => $ip,
                                    'taskStatus'   => 1,
                                ]);
                            }

                            DB::table('TaskCronHistory')->insert([
                                'task_id'     => $task_id,
                                'user_id'     => $user,
                                'oltName'     => $ip,
                                'onu_status'  => $ONTstatus,
                                'dbm'         => $Signal,
                                'last_update' => now(),
                            ]);
                        }
                }catch (\Exception $e){$html ['onuStatus'] = '';}    
            }   
        }

    }

    static public function ZTE_LOS($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($OnuDesc as $key => $value) 
        {
            $value  = str_replace("$$$$", "", $value);
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("\"", "", $value);
            if (strpos($value, $user) !== false)
            {
                $RealDbm = '-';

                try {
                        $StatusOnu     = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.2.1.4.".$key,TRUE);
                        $Status = '';
                        foreach ($StatusOnu as $keyx => $dataStatus) 
                        {
                            if (strpos($dataStatus, ':') !== false)
                            {
                                $data   = explode(':', $dataStatus);  
                                $Status = trim($data[1]);  
                                break;
                            }
                            else 
                            {    
                                $Status = trim($dataStatus);  
                                break;
                            }
                        }

                        $ONTstatus = 'Unknow';

                        try {
                                $Dbm = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$key, TRUE);
                                
                                foreach ($Dbm as $key2 => $data) 
                                {   
                                    if (strpos($data, ':') !== false)
                                    {
                                        $data    = explode(':', $data);  
                                        if( trim($data[1]) > 30000 &&  trim($data[1]) != 65535 )
                                        { 
                                            $RealDbm = (trim($data[1]) - 65536) *0.002-30;
                                            break; 
                                        }
                                        else
                                        {
                                            $RealDbm = trim($data[1]) *0.002-30;
                                            break; 
                                        }      
                                    
                                    }
                                    else 
                                    {    
                                        if( trim($data) > 30000 &&  trim($data) != 65535)
                                        {
                                            $RealDbm = (trim($data) - 65536) *0.002-30;
                                            break; 
                                        }
                                        else
                                        {
                                            $RealDbm = trim($data) *0.002-30;
                                            break; 
                                        }
                                    }      
                                }
                                $RealDbm = round($RealDbm, 2);
                        }catch (\Exception $e){$RealDbm = '-';}   

                        if ($Status == '3')
                        {
                            if ((float)($RealDbm) > -27 && $RealDbm < 0)
                            {
                                $ONTstatus = 'Online';
                                DB::table('TaskMonitoring')
                                ->where('task_id', $task_id)
                                ->update([
                                    'oltType'   => 'ZTE',
                                    'last_update' => now(),
                                    'oltName'   => $ip,
                                    'taskStatus'   => 2,
                                ]);
                            }
                            else
                            {
                                if($RealDbm > 0)$RealDbm = '-';
                                
                                $ONTstatus = 'Online';
                                DB::table('TaskMonitoring')
                                ->where('task_id', $task_id)
                                ->update([
                                    'oltType'   => 'ZTE',
                                    'last_update' => now(),
                                    'oltName'   => $ip,
                                    'taskStatus'   => 9,
                                ]);
                            }
                            
                        }
                        else
                        {
                            $RealDbm   = '-';
                            $ONTstatus = 'Offline';
                            DB::table('TaskMonitoring')
                            ->where('task_id', $task_id)
                            ->update([
                                'oltType'   => 'ZTE',
                                'last_update' => now(),
                                'oltName'   => $ip,
                                'taskStatus'   => 1,
                            ]);
                        }

                        DB::table('TaskCronHistory')->insert([
                            'task_id'     => $task_id,
                            'user_id'     => $user,
                            'oltName'     => $ip,
                            'onu_status'  => $ONTstatus,
                            'dbm'         => $RealDbm,
                            'last_update' => now(),
                        ]);

                }catch (\Exception $e){}    

            }
        }

    }
     
    static public function VSOLUTION_LOS($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9" , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($Descr as $key => $value) 
        {
            $RealKey = $key;
            $value = trim(str_replace('STRING:','',$value));
            $value = trim(str_replace("\"", "", $value));
            if (strpos($value, $user) !== false)
            {   
                $Dmb = '-';
                $OnuStatus = '';
                try {
                        $OnuStatus  = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$key , TRUE);
                        foreach ($OnuStatus as $key => $value) 
                        {
                            $ONTstatus = 'Unknow';
                            $value = trim(str_replace('INTEGER: ','',$value));
                            if($value == 1)
                            {
                                $ONTstatus = 'Online';
                             
                            
                                try {
                                        $Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$RealKey , TRUE);    
                                        foreach ($Dmb as $key => $value) 
                                        {
                                            $value = trim(str_replace('STRING: ','',$value));
                                            $value = trim(str_replace("\"","",$value));
                                            $startPos = strpos($value, '(');
                                            $endPos   = strpos($value, 'dBm');
                                            if ($startPos !== false && $endPos !== false)
                                            {
                                                $dbmSubstring = substr($value, $startPos + 1, $endPos - $startPos - 1);
                                                $int = (float)abs((float)trim($dbmSubstring));   
                                            }
                                             
                                            $Dmb = trim($dbmSubstring);

                                           
                                        }

                                }catch (\Exception $e){$Dmb = '-';}  
                             
                  

                                if ((float)($Dmb) > -27)
                                {
                                    DB::table('TaskMonitoring')
                                    ->where('task_id', $task_id)
                                    ->update([
                                        'oltType'   => 'VSOLUTION',
                                        'oltName'   => $ip,
                                        'last_update' => now(),
                                        'taskStatus'   => 2,
                                    ]);
                                }
                                else
                                {
                                    DB::table('TaskMonitoring')
                                    ->where('task_id', $task_id)
                                    ->update([
                                        'oltType'   => 'VSOLUTION',
                                        'oltName'   => $ip,
                                        'last_update' => now(),
                                        'taskStatus'   => 9,
                                    ]);
                                }
                           
                            }
                            else
                            {
                                $ONTstatus = 'Offline';
                                DB::table('TaskMonitoring')
                                ->where('task_id', $task_id)
                                ->update([
                                    'oltType'   => 'VSOLUTION',
                                    'last_update' => now(),
                                    'oltName'   => $ip,
                                    'taskStatus'   => 1,
                                ]);
                            }

                            DB::table('TaskCronHistory')->insert([
                                'task_id'     => $task_id,
                                'user_id'     => $user,
                                'oltName'     => $ip,
                                'onu_status'  => $ONTstatus,
                                'dbm'         => $Dmb,
                                'last_update' => now(),
                            ]);
    
                        }
                }catch (\Exception $e){}    
            }
        }

    }

    static public function HSGQ_LOS($ip,$token,$user,$task_id)
    {
        HSGQ::API('https://'.$ip.'/onu_allow_list?t=1709042982000',$token);
        $ArrayFirst =  HSGQ::API('http://'.$ip.'/onutable',$token);     
        $dataArray = json_decode($ArrayFirst, true);
      
        foreach ($dataArray['data'] as $item) 
        {
            if (strpos($item['onu_name'], $user) !== false){$Port_id = $item['port_id'];break;}
        }

        if(!empty($Port_id))
        { 
            $Dbm = '-';
            $ArraySecond =   HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$Port_id,$token);
            $SecDataArray = json_decode($ArraySecond, true);
            foreach ($SecDataArray['data'] as $item) 
            {
                if (strpos($item['onu_name'], $user) !== false) 
                {     
                    $ONTstatus = 'Unknow';
                    if($item['status'] == 'Online')
                    {
                        $ONTstatus = 'Online';

                        if (is_numeric($item['receive_power']))$Dbm = round($item['receive_power'],2);
                        else   $Dbm  = $item['receive_power'];

                        if ((float)($Dbm) > -27 && $Dbm !== '-')
                        {
                            DB::table('TaskMonitoring')
                            ->where('task_id', $task_id)
                            ->update([
                                'oltType'   => 'HSGQ',
                                'oltName'   => $ip,
                                'last_update' => now(),
                                'taskStatus'   => 2,
                            ]);
                        }
                        else
                        {
                            DB::table('TaskMonitoring')
                            ->where('task_id', $task_id)
                            ->update([
                                'oltType'   => 'HSGQ',
                                'oltName'   => $ip,
                                'last_update' => now(),
                                'taskStatus'   => 9,
                            ]);
                        }
 
                    }
                    else
                    {
                        $ONTstatus = 'Offline';
                        DB::table('TaskMonitoring')
                        ->where('task_id', $task_id)
                        ->update([
                            'oltType'   => 'HSGQ',
                            'last_update' => now(),
                            'oltName'   => $ip,
                            'taskStatus'   => 1,
                        ]);
                    }

                    DB::table('TaskCronHistory')->insert([
                        'task_id'     => $task_id,
                        'user_id'     => $user,
                        'oltName'     => $ip,
                        'onu_status'  => $ONTstatus,
                        'dbm'         => $Dbm,
                        'last_update' => now(),
                    ]);
                }
            }
        }    

    }
    ////////////////////////////////////////////////////////////////////    D B M

    static public function BDCOM_DBM($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$ifAlias = $snmp->walk("IF-MIB::ifAlias", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        foreach ($ifAlias as $key => $value) 
        {
            $ONTstatus = '';
            $value = trim(str_replace('STRING:','',$value));
            if (strpos($value, $user) !== false)
            {   
                try {
                        $Dbm                = $snmp->walk("1.3.6.1.4.1.3320.101.10.5.1.5.".$key, TRUE);
                    }catch (\Exception $e){$Dbm = '';}   
                   
                    if(!empty($Dbm))
                    { 
                        foreach ($Dbm as $key => $value) 
                        {  
                            $value = trim(str_replace('INTEGER: ','',($value)));
                            $Dbm   =  BDCOM::convertToDecimal(trim($value));
                      
                            if ((float)($Dbm) > -27)
                            {   
                                $ONTstatus = 'Online';
                                DB::table('TaskMonitoring')
                                ->where('task_id', $task_id)
                                ->update([
                                    'oltType'   => 'BDCOM',
                                    'oltName'   => $ip,
                                    'last_update' => now(),
                                    'taskStatus'   => 2,
                                ]); 
                            }
                            else if ((float)($Dbm) < -27 && (float)$Dbm > -40)
                            { 
                                $ONTstatus = 'Online';
                                DB::table('TaskMonitoring')
                                ->where('task_id', $task_id)
                                ->update([
                                    'oltType'   => 'BDCOM',
                                    'last_update' => now(),
                                    'oltName'   => $ip,
                                    'taskStatus'   => 1,
                                ]);
                            }
                            else
                            { 
                                $ONTstatus = 'Offline';
                                DB::table('TaskMonitoring')
                                ->where('task_id', $task_id)
                                ->update([
                                    'oltType'   => 'BDCOM',
                                    'last_update' => now(),
                                    'oltName'   => $ip,
                                ]);
                            }

                            if((float)$Dbm == -6553.5)$Dbm = '-';
                            DB::table('TaskCronHistory')->insert([
                                'task_id'     => $task_id,
                                'user_id'     => $user,
                                'oltName'     => $ip,
                                'onu_status'  => $ONTstatus,
                                'dbm'         => $Dbm,
                                'last_update' => now(),
                            ]);


                        }
                    }
                    else 
                    {
                        DB::table('TaskCronHistory')->insert([
                            'task_id'     => $task_id,
                            'user_id'     => $user,
                            'oltName'     => $ip,
                            'onu_status'  => 'Offline',
                            'dbm'         => '-',
                            'last_update' => now(),
                        ]);
                    }

             
            }   
        }

    }

    static public function ZTE_DBM($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  


        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($OnuDesc as $key => $value) 
        {
            $value  = str_replace("$$$$", "", $value);
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("\"", "", $value);
            if (strpos($value, $user) !== false)
            {
                $RealDbm = '-';

                try {
                        $Dbm = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.50.12.1.1.10.".$key, TRUE);
                        
                        foreach ($Dbm as $key2 => $data) 
                        {   
                            if (strpos($data, ':') !== false)
                            {
                                $data    = explode(':', $data);  
                                if( trim($data[1]) > 30000 &&  trim($data[1]) != 65535 )
                                { 
                                    $RealDbm = (trim($data[1]) - 65536) *0.002-30;
                                    break; 
                                }
                                else
                                {
                                    $RealDbm = trim($data[1]) *0.002-30;
                                    break; 
                                }      
                            
                            }
                            else 
                            {    
                                if( trim($data) > 30000 &&  trim($data) != 65535)
                                {
                                    $RealDbm = (trim($data) - 65536) *0.002-30;
                                    break; 
                                }
                                else
                                {
                                    $RealDbm = trim($data) *0.002-30;
                                    break; 
                                }
                            }      
                        }
                        $RealDbm = round($RealDbm, 2);
                }catch (\Exception $e){$RealDbm = '-';}    
 
                if ((float)($RealDbm) > -27 && $RealDbm < 0)
                {
                    $ONTstatus = 'Online';
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltType'   => 'ZTE',
                        'oltName'   => $ip,
                        'last_update' => now(),
                        'taskStatus'   => 2,
                    ]); 
                }
                else if ((float)($RealDbm) < -27 && $RealDbm < 0)
                {
                    $ONTstatus = 'Online';
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltType'   => 'ZTE',
                        'last_update' => now(),
                        'oltName'   => $ip,
                        'taskStatus'   => 1,
                    ]);
                }
                else
                {
                    $ONTstatus = 'Offline';
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltType'   => 'ZTE',
                        'last_update' => now(),
                        'oltName'   => $ip,
                    ]);
                }

                DB::table('TaskCronHistory')->insert([
                    'task_id'     => $task_id,
                    'user_id'     => $user,
                    'oltName'     => $ip,
                    'onu_status'  => $ONTstatus,
                    'dbm'         => $RealDbm,
                    'last_update' => now(),
                ]);

            }
        }

      

    }

    static public function VSOLUTION_DBM($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  


        try {$Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9" , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($Descr as $key => $value) 
        {
            $value = trim(str_replace('STRING:','',$value));
            $value = trim(str_replace("\"", "", $value));
            if (strpos($value, $user) !== false)
            {
                $Dmb = '-';

                try {
                        $Dmb = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.2.1.8.1.7.".$key , TRUE); 
                        foreach ($Dmb as $key => $value) 
                        {
                            $value = trim(str_replace('STRING: ','',$value));
                            $value = trim(str_replace("\"","",$value));
                            $startPos = strpos($value, '(');
                            $endPos   = strpos($value, 'dBm');
                            if ($startPos !== false && $endPos !== false)
                            {
                                $dbmSubstring = substr($value, $startPos + 1, $endPos - $startPos - 1);
                                $int = (float)abs((float)trim($dbmSubstring));   
                            }

                            $Dmb = trim($dbmSubstring);
                        }
                }catch (\Exception $e){$Dmb = '-';}  

            }
        }
      
        if ((float)($Dmb) > -27)
        {
            $ONTstatus = 'Online';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'VSOLUTION',
                'oltName'   => $ip,
                'last_update' => now(),
                'taskStatus'   => 2,
            ]); 
        }
        else if ((float)($Dmb) < -27)
        {
            $ONTstatus = 'Online';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'VSOLUTION',
                'last_update' => now(),
                'oltName'   => $ip,
                'taskStatus'   => 1,
            ]);
        }
        else
        {
            $ONTstatus = 'Offline';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'VSOLUTION',
                'last_update' => now(),
                'oltName'   => $ip,
            ]);
        }

        DB::table('TaskCronHistory')->insert([
            'task_id'     => $task_id,
            'user_id'     => $user,
            'oltName'     => $ip,
            'onu_status'  => $ONTstatus,
            'dbm'         => $Dmb,
            'last_update' => now(),
        ]);

    }

    static public function HUAWEI_DBM($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($PonList as $key => $value) 
        {
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("\"", "", $value);
            if (strpos($value, $user) !== false) 
            {
                $Dmb = '-';
                try {
                    $Signal =  $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.51.1.4.".$key, TRUE); 
                    $Signal = str_replace('INTEGER: ', '', trim($Signal));
                    $Dmb    = HUAWEI::SginalFixer($Signal);    

                }catch (\Exception $e){$Dmb = '-';}    
            }
        }
    
        if ((float)($Dmb) > -27 && $Dmb !== '-') // && $Dmb !== '-'
        {
            $ONTstatus = 'Online';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'HUAWEI',
                'oltName'   => $ip,
                'last_update' => now(),
                'taskStatus'   => 2,
            ]); 
        }
        else if ((float)($Dmb) < -27 && $Dmb !== '-')
        {
            $ONTstatus = 'Online';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'HUAWEI',
                'last_update' => now(),
                'oltName'   => $ip,
                'taskStatus'   => 1,
            ]);
        }
        else
        {
            $ONTstatus = 'Offline';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'HUAWEI',
                'last_update' => now(),
                'oltName'   => $ip,
            ]);
        }

        DB::table('TaskCronHistory')->insert([
            'task_id'     => $task_id,
            'user_id'     => $user,
            'oltName'     => $ip,
            'onu_status'  => $ONTstatus,
            'dbm'         => $Dmb,
            'last_update' => now(),
        ]);

    }

    static public function HSGQ_DBM($ip,$token,$user,$task_id)
    {
         
        HSGQ::API('https://'.$ip.'/onu_allow_list?t=1709042982000',$token);
        $ArrayFirst =  HSGQ::API('http://'.$ip.'/onutable',$token);     
        $dataArray = json_decode($ArrayFirst, true);
      
        foreach ($dataArray['data'] as $item) 
        {
            if (strpos($item['onu_name'], $user) !== false){$Port_id = $item['port_id'];break;}
        }

        if(!empty($Port_id))
        { 
            $Dbm = '-';
            $ArraySecond =   HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$Port_id,$token);   
            $SecDataArray = json_decode($ArraySecond, true);
            foreach ($SecDataArray['data'] as $item) 
            {
                if (strpos($item['onu_name'], $user) !== false) 
                {     
                    $ONTstatus = 'Unknow';
                    if($item['status'] == 'Online')
                    {
                        if (is_numeric($item['receive_power']))$Dbm = round($item['receive_power'],2);
                        else   $Dbm  = $item['receive_power'];
                    }
 
                }
            }
        }   
     
        if ((float)($Dbm) > -27 && $Dbm !== '-')
        {   
            $ONTstatus = 'Online';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'HSGQ',
                'oltName'   => $ip,
                'last_update' => now(),
                'taskStatus'   => 2,
            ]); 
        }
        else if ((float)($Dbm) < -27 && $Dbm !== '-')
        {    
            $ONTstatus = 'Online';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'HSGQ',
                'last_update' => now(),
                'oltName'   => $ip,
                'taskStatus'   => 1,
            ]);
        }
        else
        {    
            $ONTstatus = 'Offline';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'HSGQ',
                'last_update' => now(),
                'oltName'   => $ip,
            ]);
        }

        DB::table('TaskCronHistory')->insert([
            'task_id'     => $task_id,
            'user_id'     => $user,
            'oltName'     => $ip,
            'onu_status'  => $ONTstatus,
            'dbm'         => $Dbm,
            'last_update' => now(),
        ]);

    }

    static public function ANTENNA_DBM($SectorIP,$username,$password,$user,$task_id,$AntennaIp)
    {
          
        $ubiquiti = new ubiquiti($AntennaIp, $username, $password, false, '80', 5);
	    $String   = $ubiquiti->status(true);
 
        $Signal = ' ';
        if (isset($String['wireless']) && is_array($String['wireless'])) 
        {
            $Signal     = $String['wireless']['signal'];
        }
       
        if(isset($String) && !empty($String) && $Signal < -70)
        {
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltName'   => $SectorIP,
                'sector_ip' => $AntennaIp,
                'oltType'   => 'ANTENNA',
                'last_update' => now(),
                'taskStatus'   => 1,
            ]);

             
            DB::table('TaskCronHistory')->insert([
                'task_id'     => $task_id,
                'user_id'     => $user,
                'oltName'     => $SectorIP,
                'sector_ip'   => $AntennaIp,
                'onu_status'  => 'Online',
                'dbm'         => $Signal,
                'last_update' => now(),
            ]);
        }
        else if(isset($String) && !empty($String) && $Signal > -70)
        {
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltName'   => $SectorIP,
                'sector_ip' => $AntennaIp,
                'oltType'   => 'ANTENNA',
                'last_update' => now(),
                'taskStatus'   => 2,
            ]);

             

            DB::table('TaskCronHistory')->insert([
                'task_id'     => $task_id,
                'user_id'     => $user,
                'oltName'   => $SectorIP,
                'sector_ip' => $AntennaIp,
                'onu_status'  => 'Online',
                'dbm'         => $Signal,
                'last_update' => now(),
            ]);
        }
        else
        {
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'ANTENNA',
                'last_update' => now(),
                'oltName'     => $SectorIP,
                'sector_ip'   => $AntennaIp,
            ]);

            DB::table('TaskCronHistory')->insert([
                'task_id'     => $task_id,
                'user_id'     => $user,
                'oltName'     => $SectorIP,
                'sector_ip'   => $AntennaIp,
                'onu_status'  => 'Offline',
                'dbm'         => ' ',
                'last_update' => now(),
            ]);
        }
  

    }

    ////////////////////////////////////////////////////////////////////   L I N K   D O W N

    static public function BDCOM_LINK($ip,$read,$user,$task_id)
    {
        $ONTstatus = ' ';
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$ifAlias = $snmp->walk("IF-MIB::ifAlias", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }


        foreach ($ifAlias as $key => $value) 
        {
            $value = trim(str_replace('STRING:','',$value));
            if (strpos($value, $user) !== false)
            {   
            
                try {
                        $PortStatus = $snmp->walk("1.3.6.1.4.1.3320.101.12.1.1.8.".$key, TRUE);  
                        if (!empty($PortStatus) && is_array($PortStatus))
                        {
                            foreach ($PortStatus as $key => $value) 
                            {    
                                $size = count($PortStatus);   
                                if($size <= 4)
                                {                                       
                                    $PortStatusEx = trim(str_replace('INTEGER: ', '', $value));                                   
                                }
                                else
                                {
                                    $PortStatusEx = 0;
                                }
                            }
                        }
                        else
                        {
                            $PortStatusEx = 'This ONU has no uni ports';
                        }
                
                }
                catch (\Exception $e){$PortStatusEx  = '-';}  

                if ((float)($PortStatusEx) == 1)
                {
                    $ONTstatus = 'Link Up';
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltType'   => 'BDCOM',
                        'oltName'   => $ip,
                        'last_update' => now(),
                        'taskStatus'   => 2,
                    ]); 
                }
                else if ((float)($PortStatusEx) == 2)
                {
                    $ONTstatus = 'Link Down';
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltType'   => 'BDCOM',
                        'last_update' => now(),
                        'oltName'   => $ip,
                        'taskStatus'   => 1,
                    ]);
                }
                else
                {
                    $ONTstatus = 'Offline';
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltType'   => 'BDCOM',
                        'last_update' => now(),
                        'oltName'   => $ip,
                    ]);
                }
        
                DB::table('TaskCronHistory')->insert([
                    'task_id'     => $task_id,
                    'user_id'     => $user,
                    'oltName'     => $ip,
                    'onu_status'  => $ONTstatus,
                    'last_update' => now(),
                ]);

                if ((float)($PortStatus) == 1)break;
            }   
        }

    }

    static public function ZTE_LINK($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  


        try {$OnuDesc = $snmp->walk(".1.3.6.1.4.1.3902.1012.3.28.1.1.3", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($OnuDesc as $key => $value) 
        {
            $value  = str_replace("$$$$", "", $value);
            $value  = str_replace("STRING: ", "", $value);
            $value  = str_replace("\"", "", $value);
            if (strpos($value, $user) !== false)
            {
                try {
                        $OnuSideLinks  = $snmp->walk("1.3.6.1.4.1.3902.1012.3.50.14.1.1.7.".$key, TRUE); 
                }catch (\Exception $e){$OnuSideLinks  = '-';}    

                foreach ($OnuSideLinks as $keyZ => $value) 
                {
                    $value  = str_replace("INTEGER: ", "", $value);
                    $State = '-';
                    if($value == 1)     $State = 'Link Down';
                    else if($value == 2)$State = 'Half-10';
                    else if($value == 3)$State = 'Full-10';
                    else if($value == 4)$State = 'Half-100';
                    else if($value == 5)$State = 'Full-100';
                    else if($value == 6)$State = 'Full-1000';
     
                    if ($State !== 'Link Down')break;
                }
            }
        }
 

            if ($State !== 'Link Down')
            {
                DB::table('TaskMonitoring')
                ->where('task_id', $task_id)
                ->update([
                    'oltType'   => 'BDCOM',
                    'oltName'   => $ip,
                    'last_update' => now(),
                    'taskStatus'   => 2,
                ]); 
            }
            else
            {
                DB::table('TaskMonitoring')
                ->where('task_id', $task_id)
                ->update([
                    'oltType'   => 'BDCOM',
                    'last_update' => now(),
                    'oltName'   => $ip,
                    'taskStatus'   => 1,
                ]);
            }

            DB::table('TaskCronHistory')->insert([
                'task_id'     => $task_id,
                'user_id'     => $user,
                'oltName'     => $ip,
                'dbm'         => ' ',
                'onu_status'  => $State,
                'last_update' => now(),
            ]);


    }

    static public function VSOLUTION_LINK($ip,$read,$write,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        
        try {$Descr = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.9" , TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }
        $valuePortState = '-';
        foreach ($Descr as $key => $value) 
        {
            $value = trim(str_replace('STRING:','',$value));
            $value = trim(str_replace("\"", "", $value));
            if (strpos($value, $user) !== false)
            {
                $Pon = explode('.',$key);
                $Port = $Pon[1];
                $OnuStatus = 0;
                try {
                    $OnuStatus  = trim(str_replace('INTEGER: ','',$snmp->get(".1.3.6.1.4.1.37950.1.1.5.12.1.25.1.4.".$key , TRUE))); 
                }
                catch (\Exception $e){}    

                if($OnuStatus == 1)
                {         
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.5.3.1.0', 'i',$Pon[0]);   
                    snmp3_set($ip, $write, 'noAuthNoPriv', null, null, null, null,'1.3.6.1.4.1.37950.1.1.5.12.5.3.2.0', 'i',$Port);
                    uSleep(500);
                    $PortState = 0;
                    try {
                            $PortState  = $snmp->walk(".1.3.6.1.4.1.37950.1.1.5.12.5.1.9.1.4",TRUE); 
                    }
                    catch (\Exception $e){}   

                        if(!empty($PortState))
                        {
                            foreach ($PortState as $keyPortState => $valuePortState) 
                            {   
                                $valuePortState = trim(str_replace('INTEGER: ','',$valuePortState));   
                                $valuePortState = trim(str_replace("\"",'',$valuePortState));      
                                
                                
                                if ($valuePortState == 1)break;
                            }
                        }
                }
            }
        }

            $Link = '';
            if ($valuePortState == 1)
            {
                $Link = 'Link Up';
                DB::table('TaskMonitoring')
                ->where('task_id', $task_id)
                ->update([
                    'oltType'   => 'VSOLUTION',
                    'oltName'   => $ip,
                    'last_update' => now(),
                    'taskStatus'   => 2,
                ]); 
            }
            else
            {
                $Link = 'Link Down';
                DB::table('TaskMonitoring')
                ->where('task_id', $task_id)
                ->update([
                    'oltType'   => 'VSOLUTION',
                    'last_update' => now(),
                    'oltName'   => $ip,
                    'taskStatus'   => 1,
                ]);
            }

            DB::table('TaskCronHistory')->insert([
                'task_id'     => $task_id,
                'user_id'     => $user,
                'oltName'     => $ip,
                'dbm'         => ' ',
                'onu_status'  => $Link,
                'last_update' => now(),
            ]);

    }

    static public function HUAWEI_LINK($ip,$read,$user,$task_id)
    {
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        try {$PonList = $snmp->walk(".1.3.6.1.4.1.2011.6.128.1.1.2.43.1.9", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        if(!empty($PonList))
        {
            $status = '-';
            foreach ($PonList as $key => $value) 
            {
                $value  = str_replace("STRING: ", "", $value);
                $value  = str_replace("\"", "", $value);
                if (strpos($value, $user) !== false) 
                {
    
                    try {$PortStatus = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.22.".$key, TRUE);}
                    catch (\Exception $e){$PortStatus = '';} 
    
                    try {$PortCount  = $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.62.1.21.".$key, TRUE);}
                    catch (\Exception $e){$PortCount = '';}    
    
                    $newArray = [];
                    if (is_array($PortCount))
                    {
                        foreach ($PortCount as $Zkey => $Zvalue) 
                        {
                            if (strpos($Zvalue, '-1') === false) 
                            {
                                $newArray[$Zkey] = 
                                [
                                    'Status' => $PortStatus[$Zkey],
                                ];
                                if($Zkey === 4)break;
                            }
                        }
                    }
                    
                    $sizeArray = count($newArray); 
    
                    if($sizeArray)
                    {
                        $Port_Number = 1;
                        $html ['shutdown'] = 0;
                     
                        foreach ($newArray as $key => $value) 
                        {   
     
                            $status = trim(str_replace('INTEGER: ','',$value['Status']));
    
    
                            if ($status == 1)break;
                        } 
                    }
    
                }   
            }
         
                $State = '-';
    
                if ($status == 1)
                {
                    $State = 'Link Up';
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltType'   => 'HUAWEI',
                        'oltName'   => $ip,
                        'last_update' => now(),
                        'taskStatus'   => 2,
                    ]); 
                }
                else
                {
                    $State = 'Link Down';
                    DB::table('TaskMonitoring')
                    ->where('task_id', $task_id)
                    ->update([
                        'oltType'   => 'HUAWEI',
                        'last_update' => now(),
                        'oltName'   => $ip,
                        'taskStatus'   => 1,
                    ]);
                }
    
                DB::table('TaskCronHistory')->insert([
                    'task_id'     => $task_id,
                    'user_id'     => $user,
                    'oltName'     => $ip,
                    'dbm'         => ' ',
                    'onu_status'  => $State,
                    'last_update' => now(),
                ]);
        }
    }

    static public function HSGQ_LINK($ip,$token,$user,$task_id)
    {
         
        HSGQ::API('https://'.$ip.'/onu_allow_list?t=1709042982000',$token);
        $ArrayFirst =  HSGQ::API('http://'.$ip.'/onutable',$token);     
        $dataArray = json_decode($ArrayFirst, true);
      
        foreach ($dataArray['data'] as $item) 
        {
            if (strpos($item['onu_name'], $user) !== false){$Port_id = $item['port_id'];$OntID = $item['onu_id'];break;}
        }

        if(!empty($Port_id))
        { 
            $State = '-';
            $ArraySecond  = HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$Port_id,$token);
            $SecDataArray = json_decode($ArraySecond, true);
            foreach ($SecDataArray['data'] as $item) 
            {
                if (strpos($item['onu_name'], $user) !== false) 
                {     
                    $ONTstatus = 'Unknow';
                    if($item['status'] == 'Online')
                    {
                        $ArraySecond =  HSGQ::API('https://'.$ip.'/onumgmt?form=port_cfg&port_id='.$Port_id.'&onu_id='.$OntID.'',$token);
                        $SecDataArray   = json_decode($ArraySecond, true);

                        foreach ($SecDataArray['data'] as $key => $item) 
                        {
                            $State = $item['status'];

                            if ($State == 1)break;
                        }
                    }
 
                }
            }
        }   
    
        if (isset($State) && $State == 1)
        {
            $ONTstatus = 'Link Up';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'HSGQ',
                'oltName'   => $ip,
                'last_update' => now(),
                'taskStatus'   => 2,
            ]); 
        }
        else
        {
            $ONTstatus = 'Link Down';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'HSGQ',
                'last_update' => now(),
                'oltName'   => $ip,
                'taskStatus'   => 1,
            ]);
        }

        DB::table('TaskCronHistory')->insert([
            'task_id'     => $task_id,
            'user_id'     => $user,
            'oltName'     => $ip,
            'onu_status'  => $ONTstatus,
            'dbm'         => '',
            'last_update' => now(),
        ]);

    }

    static public function SECTOR_LINK($ip,$user,$task_id)
    {
        $commandArray = [
            "wstalist",
        ];

        $creds = DB::table('parameters')->where('type','antenna_ssh')->first();  

        $res = TaskMonitoring::SSH_SECTOR($ip,22,$creds->username,$creds->password,$user,$commandArray,true);     
        if($res == 'SSH Login failed')
        {
            $res = TaskMonitoring::SSH_SECTOR($ip,22,$creds->username,$creds->password.' ',$user,$commandArray,true);     
        }
 

        if($res == 'SSH Login failed')
        {
            $ONTstatus = '-';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'SSH Login failed',
                'last_update' => now(),
                'oltName'   => $ip,
            ]);
        }
        else if($res == 'SSH No Connection')
        {
            $ONTstatus = '-';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'SSH No Connection',
                'last_update' => now(),
                'oltName'   => $ip,
            ]);
        }
        else if ($res > 0)
        {
            $ONTstatus = 'Link Up';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'ANTENNA',
                'oltName'   => $ip,
                'last_update' => now(),
                'taskStatus'   => 2,
            ]); 
        }
        else
        {
            $ONTstatus = 'Link Down';
            DB::table('TaskMonitoring')
            ->where('task_id', $task_id)
            ->update([
                'oltType'   => 'ANTENNA',
                'last_update' => now(),
                'oltName'   => $ip,
                'taskStatus'   => 1,
            ]);
        }

        DB::table('TaskCronHistory')->insert([
            'task_id'     => $task_id,
            'user_id'     => $user,
            'oltName'     => $ip,
            'onu_status'  => $ONTstatus,
            'dbm'         => '',
            'last_update' => now(),
        ]);
    }

    static public function SSH_SECTOR($server,$port,$username,$password,$user,$commandArray,$Readmode = true)
    {
      
        $ssh = new SSH2($server, $port);
        if (!$ssh->login($username, $password)) {
            return 'SSH Login failed';
        }
 
        if(!$ssh->isConnected())
        {
            return 'SSH No Connection';
        }

        $ssh->setTimeout(1);

        $result = '';
        $Started = false;
    
        foreach ($commandArray as  $Command) 
        {        
            $ssh->write($Command."\n");   

            if($Readmode)
            {
                $sms = $ssh->read('OK!');

                $lines = explode("\n", $sms);     
                foreach ($lines as $line) 
                { 
                    $line = str_replace(["\r","\"","\t"],'',$line);
                    $line = trim($line);
                    if(!empty($line) && $line !== $Command)
                    {
                        if(strpos($line,$user) !== false)$Started = true;       
                        
                        if($Started == true && strpos($line,'speed') !== false) 
                        {
                            $result = $line;   
                            $result = str_replace(["\"","speed: ",","],'',$result); 
                            break;
                        }
                        
                    } 
                    
                }
            }
 
        }

        return  $result;  
    }

    static public function CISCO_LINK($ip,$read,$user,$task_id)
    {
      
        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

 
        try {$Name = ($snmp->walk("1.3.6.1.2.1.2.2.1.2", TRUE));} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        try {
                foreach ($Name as $key => $value) 
                {
                    $value = str_replace('STRING: ','', $value);
                    if(strpos($value,'GigabitEthernet') !== FALSE)
                    {
                        $Status = str_replace('INTEGER: ','',$snmp->get("1.3.6.1.2.1.2.2.1.8.".$key, TRUE));
                    }
                }
        } 
        catch (\Exception $e) 
        {$Status = '-';}

    
         
            if (strpos($Status,'up') !== false)
            {
                $State = 'Link Up';
                DB::table('TaskMonitoring')
                ->where('task_id', $task_id)
                ->update([
                    'oltType'   => 'CISCO',
                    'oltName'   => $ip,
                    'last_update' => now(),
                    'taskStatus'   => 2,
                ]); 
            }
            else
            {
                $State = 'Link Down';
                DB::table('TaskMonitoring')
                ->where('task_id', $task_id)
                ->update([
                    'oltType'   => 'CISCO',
                    'last_update' => now(),
                    'oltName'   => $ip,
                    'taskStatus'   => 1,
                ]);
            }

            DB::table('TaskCronHistory')->insert([
                'task_id'     => $task_id,
                'user_id'     => $user,
                'oltName'     => $ip,
                'dbm'         => ' ',
                'onu_status'  => $State,
                'last_update' => now(),
            ]);


    }
   
    static public function ZYXEL_LINK($ip,$read,$user,$task_id)
    {

        $snmp = new \SNMP(\SNMP::VERSION_2c, $ip, $read); 
        
        try { $Name = $snmp->walk(".1.3.6.1.2.1.31.1.1.1.18", TRUE); } 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        $Link = '-';
        foreach ($Name as $key => $value) 
        {   
            $value = trim(str_replace('STRING: ','',$value));
            $pos = strpos($value, $user);
            if ($pos !== false) 
            {
                try { 
                     $Link = $snmp->get(".1.3.6.1.2.1.2.2.1.8.".$key, TRUE);  
                     $Link = trim(str_replace('INTEGER; ','',$Link));
                } 
                catch (\Exception $e){$Link = '-';}   
            }
        } 
   
 
            if (strpos($Link,'up') !== false)
            {
                $State = 'Link Up';
                DB::table('TaskMonitoring')
                ->where('task_id', $task_id)
                ->update([
                    'oltType'   => 'ZYXEL',
                    'oltName'   => $ip,
                    'last_update' => now(),
                    'taskStatus'   => 2,
                ]); 
            }
            else
            {
                $State = 'Link Down';
                DB::table('TaskMonitoring')
                ->where('task_id', $task_id)
                ->update([
                    'oltType'   => 'ZYXEL',
                    'last_update' => now(),
                    'oltName'   => $ip,
                    'taskStatus'   => 1,
                ]);
            }

            DB::table('TaskCronHistory')->insert([
                'task_id'     => $task_id,
                'user_id'     => $user,
                'oltName'     => $ip,
                'dbm'         => ' ',
                'onu_status'  => $State,
                'last_update' => now(),
            ]);


    }

    //////////////////////////////////////////////////////////////////////////
    static public function convertMikroTikTimeToSeconds($timeStr) 
    {
        $time = 0;
        preg_match_all('/(\d+)([smhdw])/i', $timeStr, $matches, PREG_SET_ORDER);
        foreach ($matches as $match) {
            $value = (int)$match[1];
            $unit = strtolower($match[2]);  
            switch ($unit) {   
                case 's': $time += $value; break;
                case 'm': $time += $value * 60; break;
                case 'h': $time += $value * 3600; break;
                case 'd': $time += $value * 86400; break;
                case 'w': $time += $value * 604800; break;
            }
        }
        return $time;
    }
}
