<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\airsoftModel;
use App\Models\TaskMonitoring;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\PrivilegesModel;
use Illuminate\Support\Facades\Schema;
use Carbon\Carbon;

use App\Models\Install\antenna;
 

class airsoft extends Controller
{
   
    public function search(Request $request)
    {
        $id = $request->input('ab_nom');

        $username = $request->user()->name;
        $userIp = $request->ip();
        Log::channel('actions')->info('[Search] '.$id .'\n[User] '.$username.'\n[Address] '.$userIp);

        $request->validate([
            'ab_nom' => 'required|integer',
        ]);

        return response()->json(airsoftModel::ab_search($id));
    }

    public function macvendoor(Request $request)
    {
        $mac = $request->input('MacData');
        // $request->validate([
        //     'MacData' => 'required|regex:/^([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2})$/',
        // ]);
        return response()->json(airsoftModel::MacCalculateForAirsoft($mac));
    }

    public function comments(Request $request)
    {
        $id = $request->input('ClientID');
        
        $request->validate([
            'ClientID' => 'required|integer',
        ]);


        return response()->json(airsoftModel::Comments($id));
    }

    public function SendComment(Request $request)
    {
        $validator = validator()->make($request->only('Client','id','CommentType','CommentData'), [
            'Client'      => 'required|string',
            'id'          => 'required|integer',
            'CommentType' => 'required|integer',
            'CommentData' => 'required|string'
        ]);


        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $name        = $request->user()->name;  
        $Client      = $request->input('Client');
        $id          = $request->input('id');
        $CommentType = $request->input('CommentType');
        $CommentData = $request->input('CommentData');

        $username = $request->user()->name;
        $userIp = $request->ip();
        
        Log::channel('actions')->notice('[AIRSOFT COMMENT] '.$Client 
        .'\n[თანამშრომელი] '.$username
        .'\n[თანამშრომლის აიპი] '.$userIp
        .'\n[აბონენტი] '.$Client
        .'\n[კომენტარი] '.$CommentData
        );


        return response()->json(airsoftModel::Comments_Add($Client,$id,$CommentType,$CommentData,$name));
    }

    public function tasks(Request $request)
    {
        $id = $request->input('ClientID');
        $request->validate([
            'ClientID' => 'required|integer',
        ]);
        return response()->json(airsoftModel::Tasks($id));
    }

    public function coordinates(Request $request)
    {  
        $request->validate([
            'ClientID' => 'required|integer',
        ]);
        $id = $request->input('ClientID');   
        return response()->json(airsoftModel::coordinates($id));
    }

    public function PONcoordinates(Request $request)
    {   
        $validator = validator()->make($request->only('ClientID'), [
            'ClientID' => 'required|array|min:0',
        ]);
        
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $html = [];

        $clientIdsArray = $request->input('ClientID');     
        $clientIdsString = $clientIdsArray[0];  
 
        $clientIds = explode(',', $clientIdsString);  

        if (!is_array($clientIds)) {
            $clientIds = explode(',', $clientIds);
        }
   
        foreach ($clientIds as $key => $id)
        {
            try { 
                    $Parts = explode('|',$id);
                    $item    = [];
                    $item [] = airsoftModel::coordinates((int)$Parts[0]);

                    $decodedItem = json_decode($item[0], true);
                    $coordinates = trim(str_replace("\"",'',$decodedItem['coordinates']));
                    $address     = trim(str_replace("\"",'',$decodedItem['address']));
                    $user_id     = trim(str_replace("\"",'',$decodedItem['user_id']));

                    if($coordinates !== "0" && !empty($coordinates) && $coordinates !== '')
                    {
                        $Temp = [];
                        $Temp ['user_id'] = $user_id;
 
                        $Temp ['ponPort'] = $Parts[1];
                        if($Parts[4] == 1)
                        {
                            $Temp ['status'] = 'Online';
                            $Temp ['time']   = $Parts[2];
                        }
                        else
                        {
                            $Temp ['status'] = 'Offline';
                            $Temp ['time']   = $Parts[3];
                        }
                     
                        $Temp ['address'] = $address;
                        $Temp ['coordinates'] = 'https://www.google.com/maps/place/'.urlencode($coordinates);  
                        $html['Coordinates_'.$key] = $Temp;
                    }

            }catch (\Exception $e){}
 
        }


        return response()->json($html);
    }

    public function SWICHcoordinates(Request $request)
    {   
        $validator = validator()->make($request->only('ClientID'), [
            'ClientID' => 'required|array|min:0',
        ]);
        
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $html = [];

        $clientIdsArray = $request->input('ClientID');     
        $clientIdsString = $clientIdsArray[0];  
 
        $clientIds = explode(',', $clientIdsString);  

        if (!is_array($clientIds)) {
            $clientIds = explode(',', $clientIds);
        }
      
        foreach ($clientIds as $key => $id)
        {
            try { 
                    $Parts = explode('|',$id);
                    $item    = [];
                    $item [] = airsoftModel::coordinates((int)$Parts[0]);

                    $decodedItem = json_decode($item[0], true);
                    $coordinates = trim(str_replace("\"",'',$decodedItem['coordinates']));
                    $address     = trim(str_replace("\"",'',$decodedItem['address']));
                    $user_id     = trim(str_replace("\"",'',$decodedItem['user_id']));

                    if($coordinates !== "0" && !empty($coordinates) && $coordinates !== '')
                    {
                        $Temp = [];
                        $Temp ['user_id'] = $user_id;
                        $Temp ['ponPort'] = $Parts[1];
 
                     
                        $Temp ['address'] = $address;
                        $Temp ['coordinates'] = 'https://www.google.com/maps/place/'.urlencode($coordinates);  
                        $html['Coordinates_'.$key] = $Temp;
                    }

            }catch (\Exception $e){}
 
        }


        return response()->json($html);
    }


    ///////////////////////////////////////////////////////////////////////// T A S K    M O N I T O R I N G

    public static function Monitoring_TEST()
    {
        // $Data = antenna::sectorCustomerSearch('2808937');

        // // stations($SectorID,$token,$url)
        
        // foreach ($Data as $key => $value) 
        // {
        //     $dd = antenna::stations($value['id'],'69527706-d1bf-47fa-afa9-a3d1c13e3f80','https://uisp.airlink.ge');

        //     dd(json_decode($dd,true));
        //     return (antenna::stations($value['id'],'69527706-d1bf-47fa-afa9-a3d1c13e3f80','https://uisp.airlink.ge'));
        // }
       
     
        return TaskMonitoring::ANTENNA_OFFLINE('10.180.29.7','UBNT','swisch2015','2819453','236200','172.18.247.5');
        
       // return TaskMonitoring::ANTENNA_OFFLINE('172.18.225.2','ubnt','swisch2015','2808937','235300');
        //return TaskMonitoring::HSGQ_DBM('10.180.62.10','be94a85a6ba599dc5a31001505333044','2815016','234421'); 
    }

    public static function Monitoring_View()
    {

        $data       = DB::table('TaskMonitoring')
        ->where('taskStatus', 1)
        ->orWhere('taskStatus', 2)
        ->orWhere('taskStatus', 9)
        ->orderBy('taskStatus', 'desc')
        ->paginate(10,['*'],'active')->appends(request()->query());

 
        $once       = DB::table('TaskMonitoring')
        ->where('taskStatus', 1)
        ->orWhere('taskStatus', 2)
        ->orWhere('taskStatus', 9)
        ->get();

        $collection = collect($once);

        if(!empty($once))
        {
            foreach ($data as $key => $value)
            {
                if(isset($value->oltName) && isset($value->task_id))
                { 
                    $taskIdToUpdate = $value->task_id;
                    $countOfOltName = $collection->where('oltName', $value->oltName)->count();; 

                    $data->getCollection()->transform(function ($item) use ($taskIdToUpdate, $countOfOltName) 
                    {
                        if ($item->task_id == $taskIdToUpdate) 
                        {
                            $item->count = $countOfOltName;
                        }
                        return $item;
                    });
                }    
            }
        }
 
        

        return view('tasks', ['data' => $data]);
    }

    public static function SameTasks(Request $request)
    {
        $validator = validator()->make($request->only('oltName'), [
            'oltName'  => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $oltName = $request->input('oltName');


        $data       = DB::table('TaskMonitoring')
        ->where('oltName', $oltName)
        ->orderBy('taskStatus', 'desc')
        ->paginate(10,['*'],'active')->appends(request()->query());

 
        $once       = DB::table('TaskMonitoring')
        ->where('taskStatus', 1)
        ->orWhere('taskStatus', 2)
        ->orWhere('taskStatus', 9)
        ->get();

        $collection = collect($once);

        if(!empty($once))
        {
            foreach ($data as $key => $value)
            {
                if(isset($value->oltName) && isset($value->task_id))
                { 
                    $taskIdToUpdate = $value->task_id;
                    $countOfOltName = $collection->where('oltName', $value->oltName)->count();; 

                    $data->getCollection()->transform(function ($item) use ($taskIdToUpdate, $countOfOltName) 
                    {
                        if ($item->task_id == $taskIdToUpdate) 
                        {
                            $item->count = $countOfOltName;
                        }
                        return $item;
                    });
                }    
            }
        }
 
        

        return view('tasks', ['data' => $data]);
    }

    public static function archived_Tasks()
    {
        $archived   = DB::table('TaskArchive')  
        ->orderBy('last_update', 'desc')
        ->paginate(10,['*'],'archive')->appends(request()->query());

        return view('archivedTasks', ['archived' => $archived]);
    }

    static public function Task_History(Request $request)
    {
         $validator = validator()->make($request->only('task_id'), [
             'task_id'  => 'required|numeric',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
 
         $task_id = $request->input('task_id');
 
         $data = DB::table('TaskCronHistory')
         ->where('task_id',$task_id )
         ->orderBy('last_update', 'desc')
         ->get();
 
         return response()->json($data);
    }

    static public function Task_Stop(Request $request)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        
         $validator = validator()->make($request->only('task_id'), [
             'task_id'  => 'required|numeric',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
 
        $task_id = $request->input('task_id');
 

        $data = DB::table('TaskMonitoring')
         ->where('task_id',$task_id )
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
            ->where('task_id',$task_id )
            ->delete();

        }
         

         $username = $request->user()->name;
         $userIp   = $request->ip();
         Log::channel('actions')->error('[Task Stop] '.$task_id .'\n[User] '.$username.'\n[Address] '.$userIp);
      
 
         return response()->json($data);
    }
 
    static public function Task_Restore(Request $request)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

         $validator = validator()->make($request->only('task_id'), [
             'task_id'  => 'required|numeric',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
 
         $task_id = $request->input('task_id');
 
         $data = DB::table('TaskArchive')
         ->where('task_id',$task_id )
         ->first();


        if ($data) 
        {

            DB::table('TaskMonitoring')->insert([
                'oltName'       => $data->oltName,
                'oltType'       => $data->oltType,
                'user_id'       => $data->user_id,
                'task_id'       => $data->task_id,
                'staff'         => $data->staff,
                'type'          => $data->type,
                'taskStatus' => 1,
                'created' => $data->created,
                'last_update' => $data->last_update,
            ]);


            DB::table('TaskArchive')
            ->where('task_id',$task_id )
            ->delete();

        }



         $username = $request->user()->name;
         $userIp   = $request->ip();
         Log::channel('actions')->error('[Task Restore] '.$task_id .'\n[User] '.$username.'\n[Address] '.$userIp);
 
         return response()->json($data);
    }
     
    static public function Task_Search(Request $request)
    {   

        $param   = $request->input('default_search');


        if($param == 'FIXED')
        {
            $data = DB::table('TaskMonitoring')
            ->where('taskStatus','2')
            ->paginate(10);

            return view('searchedTasks', ['data' => $data]);
   
        }
        else if($param == 'CHANGED')
        {
            $data = DB::table('TaskMonitoring')
            ->where('taskStatus','9')
            ->paginate(10);

             return view('searchedTasks', ['data' => $data]);;
        }
        else if($param == 'NOT FIXED')
        {
            $data = DB::table('TaskMonitoring')
            ->where('taskStatus','1')
            ->paginate(10);

             return view('searchedTasks', ['data' => $data]);;
        }
        else if($param == 'ანტენა არაა კავშირზე')
        {
            $data = DB::table('TaskMonitoring')
            ->where('type','158')
            ->paginate(10);

             return view('searchedTasks', ['data' => $data]);
        }
        else if($param == 'ONU / ONT არაა კავშირზე')
        {
            $data = DB::table('TaskMonitoring')
            ->where('type','157')
            ->paginate(10);

             return view('searchedTasks', ['data' => $data]);
        }
        else if($param == 'მაღალი DBM ONU-ზე (-27>)')
        {
            $data = DB::table('TaskMonitoring')
            ->where('type','160')
            ->paginate(10);

             return view('searchedTasks', ['data' => $data]);
        }
        else if($param == 'მაღალი DBM ანტენაზე (-70>)')
        {
            $data = DB::table('TaskMonitoring')
            ->where('type','161')
            ->paginate(10);

             return view('searchedTasks', ['data' => $data]);
        }
        else if($param == 'ლინკი არ დგება')
        {
            $data = DB::table('TaskMonitoring')
            ->where('type','159')
            ->paginate(10);

            return view('searchedTasks', ['data' => $data]);
        }



        $columns = Schema::getColumnListing('TaskMonitoring');
        $query   = DB::table('TaskMonitoring')->where(function ($query) use ($columns, $param)
        {
            foreach ($columns as $column)
            {
                $query->orWhere($column, 'LIKE', '%' . $param . '%');
            }
        });
        $data = $query->paginate(10,['*'],'active');

        $once       = DB::table('TaskMonitoring')
        ->where('taskStatus', 1)
        ->orWhere('taskStatus', 2)
        ->orWhere('taskStatus', 9)
        ->get();

        $collection = collect($once);

        if(!empty($once))
        {
            foreach ($data as $key => $value)
            {
                if(isset($value->oltName) && isset($value->task_id))
                { 
                    $taskIdToUpdate = $value->task_id;
                    $countOfOltName = $collection->where('oltName', $value->oltName)->count();; 

                    $data->getCollection()->transform(function ($item) use ($taskIdToUpdate, $countOfOltName) 
                    {
                        if ($item->task_id == $taskIdToUpdate) 
                        {
                            $item->count = $countOfOltName;
                        }
                        return $item;
                    });
                }    
            }
        }

        return view('searchedTasks', ['data' => $data]);
    }

    static public function ArchiveTask_Search(Request $request)
    {   

        $param   = $request->input('default_search');


        if($param == 'ანტენა არაა კავშირზე')
        {
            $data = DB::table('TaskArchive')
            ->where('type','158')
            ->paginate(10);

             return view('searchedArchivedTasks', ['data' => $data]);
        }
        else if($param == 'ONU / ONT არაა კავშირზე')
        {
            $data = DB::table('TaskArchive')
            ->where('type','157')
            ->paginate(10);

             return view('searchedArchivedTasks', ['data' => $data]);
        }
        else if($param == 'მაღალი DBM ONU-ზე (-27>)')
        {
            $data = DB::table('TaskArchive')
            ->where('type','160')
            ->paginate(10);

             return view('searchedArchivedTasks', ['data' => $data]);
        }
        else if($param == 'მაღალი DBM ანტენაზე (-70>)')
        {
            $data = DB::table('TaskArchive')
            ->where('type','161')
            ->paginate(10);

             return view('searchedArchivedTasks', ['data' => $data]);
        }
        else if($param == 'ლინკი არ დგება')
        {
            $data = DB::table('TaskArchive')
            ->where('type','159')
            ->paginate(10);

             return view('searchedArchivedTasks', ['data' => $data]);
        }



        $columns = Schema::getColumnListing('TaskArchive');
        $query   = DB::table('TaskArchive')->where(function ($query) use ($columns, $param)
        {
            foreach ($columns as $column)
            {
                $query->orWhere($column, 'LIKE', '%' . $param . '%');
            }
        });
        $data = $query->paginate(10,['*'],'active');

        return view('searchedArchivedTasks', ['data' => $data]);
    }

    /////////////////////////////////////////////////////////////////////////  A L L    O N T    H I S T O R Y

    static public function AllOntHistory(Request $request)
    {
        $validator = validator()->make($request->only('client'), [
             'client'           => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $client = urldecode($request->input('client'));

        $currentMonthName = '_'.Carbon::now()->format('F');

        $data = DB::table($currentMonthName)
        ->where('descr',$client )
        ->orderBy('last_update', 'desc')
        ->take(12) 
        ->get();

        $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

        if(!empty($data))
        {
            $TempArray = [];
            foreach ($data as $key => $value) 
            {
                if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                {
                    $TempArray [] = $value->dbmRX;
                    $sum += $value->dbmRX;
                }
 
            }
 
            if(!empty($TempArray))
            {
                $max   = min($TempArray);
                $min   = max($TempArray);
                $delta = round($max - $min,2);
    
                $count       = count($TempArray);
                $middle      = round($sum / $count,2);
            }
 
        }

  
        $response = [
            'data'      => $data,
            'min'       => $min,
            'max'       => $max,
            'middle'    => $middle,
            'delta'     => $delta,
        ];
      
 
        return response()->json($response);
    }

    static public function AllAntennaHistory(Request $request)
    {
        $validator = validator()->make($request->only('client'), [
             'client'           => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $client = urldecode($request->input('client'));

        $currentMonthName = '_'.Carbon::now()->format('F');

        $data = DB::table($currentMonthName)
        ->where('descr',$client )
        ->orderBy('last_update', 'desc')
        ->take(12) 
        ->get();

        $tx_min     = 0;
        $tx_max     = 0;
        $tx_middle  = 0;
        $tx_delta   = 0;
        $tx_sum     = 0;
        $tx_count   = 1;

        
        $rx_min     = 0;
        $rx_max     = 0;
        $rx_middle  = 0;
        $rx_delta   = 0;
        $rx_sum     = 0;
        $rx_count   = 1;

        if(!empty($data))
        {
            $txSignalArray = [];
            $rxSignalArray = [];
            foreach ($data as $key => $value) 
            {
                if($value->txSignal !== '-' && is_numeric($value->txSignal))
                {
                    $txSignalArray [] = $value->txSignal;         
                    $tx_sum += $value->txSignal;           
                }
                if($value->rxSignal !== '-' && is_numeric($value->rxSignal))
                {
                    $rxSignalArray [] = $value->rxSignal;
                    $rx_sum += $value->rxSignal;
                }
 
            }
 
            if(!empty($txSignalArray))
            {
                $tx_max    = min($txSignalArray);
                $tx_min    = max($txSignalArray);
                $tx_delta  = round($tx_max - $tx_min,2);
    
                $tx_count  = count($txSignalArray);
                $tx_middle = round($tx_sum / $tx_count,2);
            }

            if(!empty($rxSignalArray))
            {
                $rx_max    = min($rxSignalArray);
                $rx_min    = max($rxSignalArray);
                $rx_delta  = round($rx_max - $rx_min,2);
    
                $rx_count  = count($rxSignalArray);
                $rx_middle = round($rx_sum / $rx_count,2);
            }
 
        }

  
        $response = [
            'data'      => $data,
            'tx_min'       => $tx_min,
            'tx_max'       => $tx_max,
            'tx_middle'    => $tx_middle,
            'tx_delta'     => $tx_delta,

            'rx_min'       => $rx_min,
            'rx_max'       => $rx_max,
            'rx_middle'    => $rx_middle,
            'rx_delta'     => $rx_delta,
        ];
      
 
        return response()->json($response);
    }

    /////////////////////////////////////////////////////////////////////////   Pon Ont History
    static public function PonOntHistory(Request $request)
    {
        $validator = validator()->make($request->only('clients'), [
             'clients'           => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }

        $twoDaysAgo         = Carbon::now()->subDays(1)->toDateString();
        $currentMonthName   = '_'.Carbon::now()->format('F');
  
        $data = DB::table($currentMonthName)
        ->whereIn('descr', $clients)
        ->where('last_update', '>=', $twoDaysAgo)
        ->orderBy('last_update', 'desc')
        ->get();
 
        $html = [];

        $globalMin = PHP_INT_MAX; // Initialize with a very high value
        $globalMax = PHP_INT_MIN; // Initialize with a very low value


        try{

            foreach ($clients as $keyZ => $value) 
            {            
                $User = $value;

                if(!empty($User))
                {
                    $middle = [];
                    $Tmp    = [];
                    $total  = 0;
                    $PonPort = '';
                    foreach ($data as $key => $value) 
                    {   
                        if($value->descr == $User)
                        {
                            $PonPort = $value->ponPort;
                            if(is_numeric($value->dbmRX))
                            {
                                $total += $value->dbmRX;
                                $Tmp [] = $value->dbmRX;
                            }
                        }                 
                    }
    
                    $middle ['client']  = $User;
                    $middle ['ponPort'] = $PonPort;
                    if (count($Tmp) > 0)
                    {
                        // Update global min and max
                        $globalMin = min($globalMin, min($Tmp));
                        $globalMax = max($globalMax, max($Tmp));


                        $middle ['minValue'] = max($Tmp) ?? '-';
                        $middle ['maxValue'] = min($Tmp) ?? '-';
                        $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                        $middle ['middle']   = round($total / count($Tmp),2);
                    }
                    else 
                    {
                        $middle ['minValue'] =  '-';
                        $middle ['maxValue'] =  '-';
                        $middle ['middle']   = '-';
                        $middle ['delta']    = '-';
                    }
     
                    $html [] = $middle;
                }
            }
    
        }catch (\Exception $e){};
 
        // Final global max and min values
        $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
        $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
        if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
        {
            $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
        }
        else
        {
            $ponDelta = '-';
        }
                      

        $ponMiddle      = 0;
        $ponMiddleCount = 0;

        try{
                foreach ($html  as $key => $value) 
                {
                    if($value['middle'] !== '-'  && is_numeric($value['middle']))
                    {
                        $ponMiddle += $value['middle']; 
                        $ponMiddleCount++;
                    }         
                }

                if($ponMiddle && $ponMiddleCount)
                $PonMiddle = round($ponMiddle / $ponMiddleCount,2);

        }catch (\Exception $e){$ponMiddle = 0;};
    

        $response = [
            'data'          => $data,
            'clients'       => $clients,
            'html'          => $html,
            'PonMiddle'     => $PonMiddle ?? 0,
            'PonMax'        => $finalGlobalMin,
            'PonMin'        => $finalGlobalMax,
            'PonDelta'      => $ponDelta,
        ];
 
        return response()->json($response);
    }

    static public function Pon_January(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '01') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_January')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle  = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_February(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '02') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_February')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle  = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_March(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '03') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_March')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle  = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_April(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '04') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_April')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle  = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_May(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '05') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_May')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle  = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_June(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '06') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_June')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle  = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_July(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'          => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '07') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
         
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_July')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

 
            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
 
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle  = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_August(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '08') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_August')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
   
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => round($ponMiddle, 2) ?? 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_September(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '09') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_September')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_October(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '10') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_October')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
   
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
 
     
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_November(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '11') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_November')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    static public function Pon_December(Request $request)
    {
        $validator = validator()->make($request->only('clients','startDateValue','endtDateValue'), [
            'clients'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

         
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 
        $clients = urldecode($request->input('clients'));
        $clients = explode(',', $clients);
        $clients = array_map('trim', $clients);

        if (empty($clients)) {
            return response()->json(['error' => 'No clients specified']);
        }


         // Parse the dates using Carbon
         $startDate  = Carbon::parse($startDateValue);
         $endDate    = Carbon::parse($endtDateValue);
 
         $months = collect();
         $MonthFound = false;
         while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
         {
             $months->push($startDate->format('m'));
      
             if ($startDate->format('m') == '12') 
             {
                 $MonthFound = true;
             }
         
             $startDate->addMonth();
         }
 
         $data = [];
         $html = [];
         $response = [];
 
         if ($MonthFound == true) 
         {
            $data = DB::table('_December')
            ->whereIn('descr',$clients )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $globalMin = PHP_INT_MAX; // Initialize with a very high value
            $globalMax = PHP_INT_MIN; // Initialize with a very low value
    
    
            try{
    
                foreach ($clients as $keyZ => $value) 
                {            
                    $User = $value;
    
                    if(!empty($User))
                    {
                        $middle = [];
                        $Tmp    = [];
                        $total  = 0;
                        $PonPort = '';
                        foreach ($data as $key => $value) 
                        {   
                            if($value->descr == $User)
                            {
                                $PonPort = $value->ponPort;
                                if(is_numeric($value->dbmRX))
                                {
                                    $total += $value->dbmRX;
                                    $Tmp [] = $value->dbmRX;
                                }
                            }                 
                        }
        
                        $middle ['client']  = $User;
                        $middle ['ponPort'] = $PonPort;
                        if (count($Tmp) > 0)
                        {
                            // Update global min and max
                            $globalMin = min($globalMin, min($Tmp));
                            $globalMax = max($globalMax, max($Tmp));
    
    
                            $middle ['minValue'] = round(max($Tmp),2) ?? '-';
                            $middle ['maxValue'] = round(min($Tmp),2) ?? '-';
                            $middle ['delta']    = round(max($Tmp) - min($Tmp),2) ?? '-';
                            $middle ['middle']   = round($total / count($Tmp),2);
                        }
                        else 
                        {
                            $middle ['minValue'] =  '-';
                            $middle ['maxValue'] =  '-';
                            $middle ['middle']   = '-';
                            $middle ['delta']    = '-';
                        }
         
                        $html [] = $middle;
                    }
                }
        
            }catch (\Exception $e){};
     
            // Final global max and min values
            $finalGlobalMin = $globalMin === PHP_INT_MAX ? '-' : $globalMin;
            $finalGlobalMax = $globalMax === PHP_INT_MIN ? '-' : $globalMax;
            if($finalGlobalMin !== '-' && $finalGlobalMax !== '-')
            {
                $ponDelta = round((float)$finalGlobalMax - (float)$finalGlobalMin,2);
            }
            else
            {
                $ponDelta = '-';
            }
                          
    
            $ponMiddle      = 0;
            $ponMiddleCount = 0;
    
            try{
                    foreach ($html  as $key => $value) 
                    {
                        if($value['middle'] !== '-'  && is_numeric($value['middle']))
                        {
                            $ponMiddle += $value['middle']; 
                            $ponMiddleCount++;
                        }         
                    }
    
                    if($ponMiddle && $ponMiddleCount)
                    $ponMiddle = round($ponMiddle / $ponMiddleCount,2);
    
            }catch (\Exception $e){$ponMiddle = 0;};
        
    
            $response = [
                'data'          => $data,
                'clients'       => $clients,
                'html'          => $html,
                'PonMiddle'     => is_numeric($ponMiddle) ? round($ponMiddle, 2) : 0,
                'PonMax'        => is_numeric($finalGlobalMin) ? round($finalGlobalMin, 2) : 0,
                'PonMin'        => is_numeric($finalGlobalMax) ? round($finalGlobalMax, 2) : 0,
                'PonDelta'      => $ponDelta,
            ];


         }
 
 
        return response()->json($response);
    }

    /////////////////////////////////////////////////////////////////////////    All Ont History

    static public function January(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '01') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data     = [];
        $response = [];

        if ($MonthFound == true) 
        {
            $data = DB::table('_January')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function February(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '02') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data = []; $response = [];

        if ($MonthFound == true) 
        {
            $data = DB::table('_February')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function March(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '03') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data = [];$response = [];

        if ($MonthFound == true) 
        {
            $data = DB::table('_March')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function April(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '04') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data = [];$response = [];

        if ($MonthFound == true) 
        {
            $data = DB::table('_April')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function May(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '05') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data = [];$response = [];

        if ($MonthFound == true) 
        {
            $data = DB::table('_May')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function June(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

       
        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '06') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data = [];$response = [];

        if ($MonthFound == true)
        {
            $data = DB::table('_June')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function July(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 

        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '07') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }
 
        $data = [];$response = [];


        if ($MonthFound == true) 
        {
            $data = DB::table('_July')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function August(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '08') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data = [];$response = [];

        if ($MonthFound == true)
        {
            $data = DB::table('_August')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function September(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');
 

        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

       
        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '09') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }


        $data = [];$response = [];

        if ($MonthFound == true)
        {
            $data = DB::table('_September')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function October(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '10') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data = [];$response = [];

        if ($MonthFound == true) 
        {
            $data = DB::table('_October')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function November(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

       
        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '11') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data = [];$response = [];

        if ($MonthFound == true)
        {
            $data = DB::table('_November')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }

    static public function December(Request $request)
    {
        $validator = validator()->make($request->only('client','startDateValue','endtDateValue'), [
            'client'           => 'required|string',
            'startDateValue'   => 'required|string',
            'endtDateValue'    => 'required|string',
       ]);

 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $client         = urldecode($request->input('client'));
        $startDateValue = $request->input('startDateValue');
        $endtDateValue  = $request->input('endtDateValue');


        // Parse the dates using Carbon
        $startDate  = Carbon::parse($startDateValue);
        $endDate    = Carbon::parse($endtDateValue);

        $months = collect();
        $MonthFound = false;
        while ($startDate->format('Y-m') <= $endDate->format('Y-m')) 
        {
            $months->push($startDate->format('m'));
     
            if ($startDate->format('m') == '12') 
            {
                $MonthFound = true;
            }
        
            $startDate->addMonth();
        }

        $data = [];$response = [];

        if ($MonthFound == true) 
        {
            $data = DB::table('_December')
            ->where('descr',$client )
            ->whereDate('last_update', '>=', $startDateValue)
            ->whereDate('last_update', '<=', $endtDateValue)
            ->orderBy('last_update', 'desc')
            ->get();

            $min = 0;$max = 0;$middle = 0;$delta = 0;$sum = 0;$count = 1;

            if(!empty($data))
            {
                $TempArray = [];
                foreach ($data as $key => $value) 
                {
                    if($value->dbmRX !== '-' && is_numeric($value->dbmRX))
                    {
                        $TempArray [] = $value->dbmRX;
                        $sum += $value->dbmRX;
                    }
     
                }
     
                if(!empty($TempArray))
                {
                    $max   = min($TempArray);
                    $min   = max($TempArray);
                    $delta = round($max - $min,2);
        
                    $count       = count($TempArray);
                    $middle      = round($sum / $count,2);
                }
     
            }
       
            $response = [
                'data'      => $data,
                'min'       => $min,
                'max'       => $max,
                'middle'    => $middle,
                'delta'     => $delta,
            ];
          
     
            return response()->json($response);
        } 

        return response()->json($response);
    }
    /////////////////////////////////////////////////////////////////////////

}
