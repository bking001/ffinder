<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

use App\Models\OLTSIDE_BDCOM;
use App\Models\OLTSIDE_VSOLUTION;
use App\Models\OLTSIDE_HSGQ;
use App\Models\OLTSIDE_HUAWEI;
use App\Models\OLTSIDE_ZTE;

use App\Models\ontStats;
use App\Models\PonsStatsModel;

use App\Models\AllOntStatsModel;
use App\Exports\UsersExport;
use Maatwebsite\Excel\Facades\Excel;

class PonsPageController extends Controller
{
    public static function getPonStats()
    {
        $data = DB::table('PonStatistic')->orderBy('mast')->paginate(12)->appends(request()->query());
        $count = $data->count();
        return view('Pons', ['data' => $data, 'count' => $count]);
    }

    public static function getPonsSelect()
    {
 
        $uniqueDeviceNames = DB::table('PonStatistic')
        ->distinct()
        ->pluck('mast')
        ->filter(function ($value) {
            // Filter out empty or null values
            return !empty($value);
        });

        return response()->json([$uniqueDeviceNames]);
    }

    public function getMastOrder(Request $request)
    {
        $validator = validator()->make($request->only('mast'), [
            'mast' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $mast = $request->input('mast');
      
        $data = DB::table('PonStatistic')->where('mast',$mast)->orderBy('mast')->paginate(12)->appends(request()->query());
        $count = $data->count();


        return view('Pons', ['data' => $data, 'count' => $count]);
    }
     

    public function search(Request $request)
    {
        try {
            $param = $request->input('default_search');
            $terms = explode(',', $param);
            $columns = Schema::getColumnListing('PonStatistic');

            $query = DB::table('PonStatistic')->where(function ($query) use ($columns, $terms) {
                foreach ($terms as $term) {
                    $term = trim($term); // Trim any extra whitespace
                    $query->where(function ($query) use ($columns, $term) {
                        foreach ($columns as $column) {
                            $query->orWhere($column, 'LIKE', '%' . $term . '%');
                        }
                    });
                }
            });

            $query->orderBy('mast');
            $results = $query->paginate(12);

            return view('Pons', ['data' => $results, 'param' => $param]);
        } catch (\Throwable $e) {
            abort(500, 'Error');
        }
    }

    public static function getPonArray(Request $request)
    {
        $validator = validator()->make($request->only('id'), [
            'id' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $id = $request->input('id');

        $data = DB::table('PonStatistic')->where('id', $id)->first();

        return response()->json($data);
    }

    public static function PonStatsUpdate(Request $request)
    {
        $validator = validator()->make($request->only('ip'), [
            'ip' => 'required|ipv4',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip = $request->input('ip');

        $credentials = DB::table('devices')->where('Address', $ip)->first();

        if ($credentials->Type == 'BDCOM') {
            return PonsStatsModel::Update_BDCOM($ip, $credentials->snmpRcomunity, $credentials->Type, $credentials->device_name, $credentials->mast);
        } elseif ($credentials->Type == 'HUAWEI') {
            return PonsStatsModel::Update_HUAWEI($ip, $credentials->snmpRcomunity, $credentials->Type, $credentials->device_name, $credentials->mast);
        } elseif ($credentials->Type == 'ZTE') {
            return PonsStatsModel::Update_ZTE($ip, $credentials->snmpRcomunity, $credentials->Type, $credentials->device_name, $credentials->mast);
        } elseif ($credentials->Type == 'VSOLUTION') {
            return PonsStatsModel::Update_VSOLUTION($ip, $credentials->snmpRcomunity, $credentials->Type, $credentials->device_name, $credentials->mast);
        } elseif ($credentials->Type == 'HSGQ') {
            $token = DB::table('parameters')->where('type', 'hsgq')->first();
            return PonsStatsModel::Update_HSGQ($ip, $token->password, $credentials->Type, $credentials->device_name, $credentials->mast);
        } else {
            return response()->json(['error' => $ip . ' ეს ოელტე არ მოიძებნა ბაზაში']);
        }
    }

    //////////////////////////////////////////////////////////////////////////////////////  O N T  S T A T S

    public static function getOnuStats()
    {
        $data = DB::table('AllStatsResult')->paginate(10)->appends(request()->query());

        $highDBM = DB::table('AllStatsResult')
            ->where('dbmRX', '<', -27)
            ->count();

        $Online = DB::table('AllStatsResult')
            ->whereIn('onuStatus', ['Online', 'Working'])
            ->count();

        $Offline = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->count();

        $los = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->whereIn('reason', ['wire down', 'LOS', 'LOSi/LOBi', 'LOFI', 'LOAMI', 'LOSi', 'LOFi', 'Laser out'])
            ->count();

        $query = '1';

        return view('onustats', ['query' => $query, 'data' => $data, 'los' => $los, 'highDbm' => $highDBM, 'Online' => $Online, 'Offline' => $Offline]);
    }

    public static function getOnuStatsllOnline()
    {
        $data = DB::table('AllStatsResult')
            ->whereIn('onuStatus', ['Online', 'Working'])
            ->paginate(10)
            ->appends(request()->query());

        $highDBM = DB::table('AllStatsResult')
            ->where('dbmRX', '<', -27)
            ->count();

        $Online = DB::table('AllStatsResult')
            ->whereIn('onuStatus', ['Online', 'Working'])
            ->count();

        $Offline = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->count();

        $los = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->whereIn('reason', ['wire down', 'LOS', 'LOSi/LOBi', 'LOFI', 'LOAMI', 'LOSi', 'LOFi', 'Laser out'])
            ->count();

        $query = "`onuStatus` IN ('Online', 'Working')";

        return view('onustats', ['query' => $query, 'data' => $data, 'los' => $los, 'highDbm' => $highDBM, 'Online' => $Online, 'Offline' => $Offline]);
    }

    public static function getOnuStatAllOffline()
    {
        $data = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline'])
            ->paginate(10)
            ->appends(request()->query());

        $highDBM = DB::table('AllStatsResult')
            ->where('dbmRX', '<', -27)
            ->count();

        $Online = DB::table('AllStatsResult')
            ->whereIn('onuStatus', ['Online', 'Working'])
            ->count();

        $Offline = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->count();

        $los = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->whereIn('reason', ['wire down', 'LOS', 'LOSi/LOBi', 'LOFI', 'LOAMI', 'LOSi', 'LOFi', 'Laser out'])
            ->count();

        $query = "onuStatus = 'Offline'";

        return view('onustats', ['query' => $query, 'data' => $data, 'los' => $los, 'highDbm' => $highDBM, 'Online' => $Online, 'Offline' => $Offline]);
    }

    public static function getOnuStatsAllHighDbm()
    {
        $data = DB::table('AllStatsResult')
            ->where('dbmRX', '<', -27)
            ->paginate(10)
            ->appends(request()->query());

        $highDBM = DB::table('AllStatsResult')
            ->where('dbmRX', '<', -27)
            ->count();

        $Online = DB::table('AllStatsResult')
            ->whereIn('onuStatus', ['Online', 'Working'])
            ->count();

        $Offline = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->count();

        $los = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->whereIn('reason', ['wire down', 'LOS', 'LOSi/LOBi', 'LOFI', 'LOAMI', 'LOSi', 'LOFi', 'Laser out'])
            ->count();

        $query = '`dbmRX` < -27';

        return view('onustats', ['query' => $query, 'data' => $data, 'los' => $los, 'highDbm' => $highDBM, 'Online' => $Online, 'Offline' => $Offline]);
    }

    public static function getOnuStatsAllLos()
    {
        $data = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->whereIn('reason', ['wire down', 'LOS', 'LOSi/LOBi', 'LOFI', 'LOAMI', 'LOSi', 'LOFi', 'Laser out'])
            ->paginate(10)
            ->appends(request()->query());

        $highDBM = DB::table('AllStatsResult')
            ->where('dbmRX', '<', -27)
            ->count();

        $Online = DB::table('AllStatsResult')
            ->whereIn('onuStatus', ['Online', 'Working'])
            ->count();

        $Offline = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->count();

        $los = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->whereIn('reason', ['wire down', 'LOS', 'LOSi/LOBi', 'LOFI', 'LOAMI', 'LOSi', 'LOFi', 'Laser out'])
            ->count();

        $query = "`onuStatus` IN ('Offline', 'Los') AND `reason` IN ('wire down', 'LOS', 'LOSi/LOBi', 'LOFI', 'LOAMI', 'LOSi', 'LOFi', 'Laser out')";

        return view('onustats', ['query' => $query, 'data' => $data, 'los' => $los, 'highDbm' => $highDBM, 'Online' => $Online, 'Offline' => $Offline]);
    }

    public static function OnuStat_Search(Request $request)
    {
        try {
            $param = $request->input('default_search');
            $terms = explode(',', $param);
            $columns = Schema::getColumnListing('AllStatsResult');

            $query = DB::table('AllStatsResult')->where(function ($query) use ($columns, $terms) {
                foreach ($terms as $term) {
                    $term = trim($term); // Trim any extra whitespace
                    $query->where(function ($query) use ($columns, $term) {
                        foreach ($columns as $column) {
                            $query->orWhere($column, 'LIKE', '%' . $term . '%');
                        }
                    });
                }
            });
 
            $data = $query->paginate(10);

            $highDBM = DB::table('AllStatsResult')
                ->where('dbmRX', '<', -27)
                ->count();

            $Online = DB::table('AllStatsResult')
                ->whereIn('onuStatus', ['Online', 'Working'])
                ->count();

            $Offline = DB::table('AllStatsResult')
                ->where('onuStatus', ['Offline', 'Los'])
                ->count();

            $los = DB::table('AllStatsResult')
                ->where('onuStatus', 'Offline')
                ->whereIn('reason', ['wire down', 'LOS', 'LOSi/LOBi', 'LOFI', 'LOAMI', 'LOSi', 'LOFi', 'Laser out'])
                ->count();

            $query = '';

            $firstTerm = true;
            foreach ($terms as $term) {
                $term = trim($term); // Trim any extra whitespace

                if (!$firstTerm) {
                    $query .= ' AND ';
                }

                $query .= '(';

                $firstColumn = true;
                foreach ($columns as $column) {
                    if (!$firstColumn) {
                        $query .= ' OR ';
                    }
                    $query .= "`$column` LIKE '%$term%'";
                    $firstColumn = false;
                }

                $query .= ')';
                $firstTerm = false;
            }

 

            return view('onustats', ['query' => $query, 'data' => $data, 'los' => $los, 'highDbm' => $highDBM, 'Online' => $Online, 'Offline' => $Offline]);
        } catch (\Throwable $e) {
            abort(500, 'Error');
        }
    }

    public static function OnuStat_AdvancedSearch(Request $request)
    {
        $validator = validator()->make($request->only('address_search', 'status_search', 'type_search', 'hihgdbm_search', 'lowdbm_search', 'low_distance_search', 'high_distance_search'), [
            'address_search' => 'sometimes|nullable|string',
            'status_search' => 'sometimes|nullable|string',
            'type_search' => 'sometimes|nullable|string',
            'hihgdbm_search' => 'sometimes|nullable|string',
            'lowdbm_search' => 'sometimes|nullable|string',
            'low_distance_search' => 'sometimes|nullable|integer',
            'high_distance_search' => 'sometimes|nullable|integer',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $address_search = $request->input('address_search');
        $status_search = $request->input('status_search');
        $type_search = $request->input('type_search');
        $hihgdbm_search = $request->input('hihgdbm_search');
        $lowdbm_search = $request->input('lowdbm_search');
        $low_distance_search = $request->input('low_distance_search');
        $high_distance_search = $request->input('high_distance_search');

        $RealQuery = DB::table('AllStatsResult');

        $query = "";

        if (!empty($type_search)) {
            $RealQuery->where('Type', 'like', "%{$type_search}%");
            if (!empty($query)) $query .= "  AND ";
            $query .= " `Type` LIKE '%{$type_search}%' ";
        }

        if (!empty($address_search)) {
            $RealQuery->where('olt', 'like', "%{$address_search}%");
            if (!empty($query)) $query .= "  AND ";
            $query .= " `olt` LIKE '%{$address_search}%' ";
        }

        if (!empty($status_search)) {
            if (!empty($query)) $query .= "  AND ";
            if ($status_search == 'Online') {
                $RealQuery->whereIn('onuStatus', ['Online', 'Working']);
                $query .= " `onuStatus` IN ('Online', 'Working') ";
            } else {
                $RealQuery->where('onuStatus', 'like', "%{$status_search}%");
                $query .= " `onuStatus` LIKE '%{$status_search}%' ";
            }
        }

        if (!empty($hihgdbm_search)) {
            $RealQuery->where('dbmRX', '<=', -abs($hihgdbm_search)); 
            if (!empty($query)) $query .= "  AND ";
            $query .= " `dbmRX` <= " . -abs($hihgdbm_search);
        }

        if (!empty($lowdbm_search)) {
            $RealQuery->where('dbmRX', '>=', -abs($lowdbm_search));  
            if (!empty($query)) $query .= "  AND ";
            $query .= " `dbmRX` >= " . -abs($lowdbm_search);
        }

        if (!empty($low_distance_search)) {
            $RealQuery->where('distance', '>', 0)->where('distance', '<=', (int) $low_distance_search);
            if (!empty($query)) $query .= "  AND ";
            $query .= " `distance` > 0 AND `distance` <= " . (int) $low_distance_search;
        }

        if (!empty($high_distance_search)) {
            $RealQuery->where('distance', '>', 0)->where('distance', '>=', (int) $high_distance_search);
            if (!empty($query)) $query .= "  AND ";
            $query .= " `distance` > 0 AND `distance` >= " . (int) $high_distance_search;
        }

  

        $data = $RealQuery->paginate(10)->appends($request->query());

        $highDBM = DB::table('AllStatsResult')
            ->where('dbmRX', '<', -27)
            ->count();

        $Online = DB::table('AllStatsResult')
            ->whereIn('onuStatus', ['Online', 'Working'])
            ->count();

        $Offline = DB::table('AllStatsResult')
            ->where('onuStatus', ['Offline', 'Los'])
            ->count();

        $los = DB::table('AllStatsResult')
            ->where('onuStatus', 'Offline')
            ->whereIn('reason', ['wire down', 'LOS', 'LOSi/LOBi', 'LOFI', 'LOAMI', 'LOSi', 'LOFi', 'Laser out'])
            ->count();

       

        return view('onustats', ['query' => $query, 'data' => $data, 'los' => $los, 'highDbm' => $highDBM, 'Online' => $Online, 'Offline' => $Offline, 'address_search' => $address_search, 'status_search' => $status_search, 'type_search' => $type_search, 'lowdbm_search' => $lowdbm_search, 'hihgdbm_search' => $hihgdbm_search, 'low_distance_search' => $low_distance_search, 'high_distance_search' => $high_distance_search]);
    }

    public static function OnuStat_Export_Exel(Request $request)
    {
        $validator = validator()->make($request->only('query'), [
            'query' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $query = $request->input('query');

        return Excel::download(new UsersExport($query), 'Finder-' . now() . '.xlsx');
    }

    public static function OnuStat_Export_Csv(Request $request)
    {
        $validator = validator()->make($request->only('queryCsv'), [
            'queryCsv' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $query = $request->input('queryCsv');

     
        return Excel::download(new UsersExport($query), 'Finder-' . now().'.csv', \Maatwebsite\Excel\Excel::CSV);
    }
     
    //////////////////////////////////////////////////////////////////////////////////////  O N T

    public static function duplicatedOnts(Request $request)
    {
        $credentials = DB::table('devices')->get();

        foreach ($credentials as $key => $value) {
            if ($value->Type == 'BDCOM') {
                //ontStats::Update_onts_BDCOM($value->Address,$value->snmpRcomunity,$value->Type,$value->device_name);
            } elseif ($value->Type == 'VSOLUTION') {
                //ontStats::Update_onts_VSOLUTION($value->Address,$value->snmpRcomunity,$value->Type,$value->device_name);
            } elseif ($value->Type == 'HSGQ') {
                //$HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();
                //ontStats::Update_onts_HSGQ($value->Address,$HSGQtoken->password,$value->Type,$value->device_name);
            } elseif ($value->Type == 'HUAWEI') {
                //ontStats::Update_onts_HUAWEI($value->Address,$value->snmpRcomunity,$value->Type,$value->device_name);
            } elseif ($value->Type == 'ZTE') {
                //ontStats::Update_onts_ZTE($value->Address,$value->snmpRcomunity,$value->Type,$value->device_name);
            }
        }
    }

    public static function Clones_Delete(Request $request)
    {
        $validator = validator()->make($request->only('Type', 'ip', 'ifindex', 'descr', 'onuMac'), [
            'Type' => 'required|string',
            'ip' => 'required|ipv4',
            'ifindex' => 'required|string',
            'descr' => 'required|string',
            'onuMac' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $Type = $request->input('Type');
        $ip = $request->input('ip');
        $ifindex = $request->input('ifindex');
        $descr = $request->input('descr');
        $onuMac = $request->input('onuMac');

        $username = $request->user()->name;
        $userIp = $request->ip();

        $DB = DB::table('devices')->where('Address', $ip)->first();

        if ($Type == 'BDCOM') {
            $res = OLTSIDE_BDCOM::OLT_SIDE_ONU_UNINSTALL($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex);
            if ($res == true) {
                Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

                DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

                ontStats::ClonesCount();

                return true;
            } else {
                return $res;
            }
        } elseif ($Type == 'VSOLUTION') {
            $res = OLTSIDE_VSOLUTION::OLT_SIDE_ONU_UNINSTALL($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex);
            if ($res == true) {
                Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

                DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

                ontStats::ClonesCount();

                return true;
            } else {
                return $res;
            }
        } elseif ($Type == 'HSGQ') {
            $HSGQtoken = DB::table('parameters')->where('type', 'hsgq')->first();
            OLTSIDE_HSGQ::OLT_SIDE_ONU_UNINSTALL($ip, $HSGQtoken->password, $ifindex);

            Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

            DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

            ontStats::ClonesCount();

            return true;
        } elseif ($Type == 'HUAWEI') {
            $res = OLTSIDE_HUAWEI::OLT_SIDE_ONU_UNINSTALL($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex);
            if ($res == true) {
                Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

                DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

                ontStats::ClonesCount();

                return true;
            } else {
                return $res;
            }
        } elseif ($Type == 'ZTE') {
            $res = OLTSIDE_ZTE::OLT_SIDE_ONU_UNINSTALL($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex);
            if ($res == true) {
                Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

                DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

                ontStats::ClonesCount();

                return true;
            } else {
                return $res;
            }
        }
    }

    public static function Clones_Description_Edit(Request $request)
    {
        $validator = validator()->make($request->only('Type', 'ip', 'ifindex', 'OLDdescr', 'NEWdescr', 'onuMac'), [
            'Type' => 'required|string',
            'ip' => 'required|ipv4',
            'ifindex' => 'required|string',
            'OLDdescr' => 'required|string',
            'NEWdescr' => 'required|string',
            'onuMac' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $Type = $request->input('Type');
        $ip = $request->input('ip');
        $ifindex = $request->input('ifindex');
        $OLDdescr = $request->input('OLDdescr');
        $NEWdescr = $request->input('NEWdescr');
        $onuMac = $request->input('onuMac');

        $username = $request->user()->name;
        $userIp = $request->ip();

        $DB = DB::table('devices')->where('Address', $ip)->first();

        if ($Type == 'BDCOM') {
            OLTSIDE_BDCOM::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex, $NEWdescr);
        } elseif ($Type == 'VSOLUTION') {
            OLTSIDE_VSOLUTION::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex, $NEWdescr);
        } elseif ($Type == 'HSGQ') {
            $HSGQtoken = DB::table('parameters')->where('type', 'hsgq')->first();
            OLTSIDE_HSGQ::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $HSGQtoken->password, $ifindex, $NEWdescr);
        } elseif ($Type == 'HUAWEI') {
            OLTSIDE_HUAWEI::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex, $NEWdescr);
        } elseif ($Type == 'ZTE') {
            OLTSIDE_ZTE::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex, $NEWdescr);
        }

        Log::channel('actions')->warning('[Onu Description Edit] ' . $NEWdescr . '\n[OLT] ' . $ip . '\n[OLD DESCRIPTION] ' . $OLDdescr . '\n[User] ' . $username . '\n[Address] ' . $userIp);

        DB::table('ontList')
            ->whereRaw('TRIM(onuMac) = ?', [trim($onuMac)])
            ->whereRaw('TRIM(onuDescr) = ?', [trim($OLDdescr)])
            ->update(['onuDescr' => $NEWdescr]);

        DB::table('ontListView')
            ->whereRaw('TRIM(onuMac) = ?', [trim($onuMac)])
            ->whereRaw('TRIM(onuDescr) = ?', [trim($OLDdescr)])
            ->update(['onuDescr' => $NEWdescr]);

        ontStats::ClonesCount();

        return true;
    }

    public static function duplicatedCount(Request $request)
    {
        return ontStats::ClonesCount();
    }

    public static function duplicatedGet(Request $request)
    {
        $data = DB::table('ontListView')->orderBy('onuDescr')->orderBy('onuMac')->paginate(12)->appends(request()->query());
        $count = $data->count();

        return view('clones', ['data' => $data, 'count' => $count]);
    }

    public function Clones_Search(Request $request)
    {
        try {
            $param = $request->input('default_search');
            $terms = explode(',', $param);
            $columns = Schema::getColumnListing('ontListView');

            $query = DB::table('ontListView')->where(function ($query) use ($columns, $terms) {
                foreach ($terms as $term) {
                    $term = trim($term); // Trim any extra whitespace
                    $query->where(function ($query) use ($columns, $term) {
                        foreach ($columns as $column) {
                            $query->orWhere($column, 'LIKE', '%' . $term . '%');
                        }
                    });
                }
            });

            $query->orderBy('onuDescr');
            //$query->orderBy('onuMac');
            $results = $query->paginate(12);

            return view('clones', ['data' => $results, 'param' => $param]);
        } catch (\Throwable $e) {
            abort(500, 'Error');
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////// N / A

    public static function naOntGet(Request $request)
    {
        $data = DB::table('NAontListView')->orderBy('onuDescr')->paginate(12)->appends(request()->query());
        $count = $data->count();

        return view('na', ['data' => $data, 'count' => $count]);
    }

    public static function naCount(Request $request)
    {
        return ontStats::NaCount();
    }

    public static function NA_Search(Request $request)
    {
        try {
            $param = $request->input('default_search');
            $terms = explode(',', $param);
            $columns = Schema::getColumnListing('NAontListView');

            $query = DB::table('NAontListView')->where(function ($query) use ($columns, $terms) {
                foreach ($terms as $term) {
                    $term = trim($term); // Trim any extra whitespace
                    $query->where(function ($query) use ($columns, $term) {
                        foreach ($columns as $column) {
                            $query->orWhere($column, 'LIKE', '%' . $term . '%');
                        }
                    });
                }
            });

            $query->orderBy('onuDescr');
            $results = $query->paginate(12);

            return view('na', ['data' => $results, 'param' => $param]);
        } catch (\Throwable $e) {
            abort(500, 'Error');
        }
    }

    public static function NA_Description_Edit(Request $request)
    {
        $validator = validator()->make($request->only('Type', 'ip', 'ifindex', 'OLDdescr', 'NEWdescr', 'onuMac'), [
            'Type' => 'required|string',
            'ip' => 'required|ipv4',
            'ifindex' => 'required|string',
            'OLDdescr' => 'required|string',
            'NEWdescr' => 'required|string',
            'onuMac' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $Type = $request->input('Type');
        $ip = $request->input('ip');
        $ifindex = $request->input('ifindex');
        $OLDdescr = $request->input('OLDdescr');
        $NEWdescr = $request->input('NEWdescr');
        $onuMac = $request->input('onuMac');

        $username = $request->user()->name;
        $userIp = $request->ip();

        $DB = DB::table('devices')->where('Address', $ip)->first();

        if ($Type == 'BDCOM') {
            OLTSIDE_BDCOM::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex, $NEWdescr);
        } elseif ($Type == 'VSOLUTION') {
            OLTSIDE_VSOLUTION::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex, $NEWdescr);
        } elseif ($Type == 'HSGQ') {
            $HSGQtoken = DB::table('parameters')->where('type', 'hsgq')->first();
            OLTSIDE_HSGQ::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $HSGQtoken->password, $ifindex, $NEWdescr);
        } elseif ($Type == 'HUAWEI') {
            OLTSIDE_HUAWEI::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex, $NEWdescr);
        } elseif ($Type == 'ZTE') {
            OLTSIDE_ZTE::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex, $NEWdescr);
        }

        Log::channel('actions')->warning('[Onu Description Edit] ' . $NEWdescr . '\n[OLT] ' . $ip . '\n[OLD DESCRIPTION] ' . $OLDdescr . '\n[User] ' . $username . '\n[Address] ' . $userIp);

        DB::table('ontList')
            ->whereRaw('TRIM(onuMac) = ?', [trim($onuMac)])
            ->whereRaw('TRIM(onuDescr) = ?', [trim($OLDdescr)])
            ->update(['onuDescr' => $NEWdescr]);

        ontStats::naCount();

        return true;
    }

    public static function NA_Delete(Request $request)
    {
        $validator = validator()->make($request->only('Type', 'ip', 'ifindex', 'descr', 'onuMac'), [
            'Type' => 'required|string',
            'ip' => 'required|ipv4',
            'ifindex' => 'required|string',
            'descr' => 'required|string',
            'onuMac' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $Type = $request->input('Type');
        $ip = $request->input('ip');
        $ifindex = $request->input('ifindex');
        $descr = $request->input('descr');
        $onuMac = $request->input('onuMac');

        $username = $request->user()->name;
        $userIp = $request->ip();

        $DB = DB::table('devices')->where('Address', $ip)->first();

        if ($Type == 'BDCOM') {
            $res = OLTSIDE_BDCOM::OLT_SIDE_ONU_UNINSTALL($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex);
            if ($res == true) {
                Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

                DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

                ontStats::naCount();

                return true;
            } else {
                return $res;
            }
        } elseif ($Type == 'VSOLUTION') {
            $res = OLTSIDE_VSOLUTION::OLT_SIDE_ONU_UNINSTALL($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex);
            if ($res == true) {
                Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

                DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

                ontStats::naCount();

                return true;
            } else {
                return $res;
            }
        } elseif ($Type == 'HSGQ') {
            $HSGQtoken = DB::table('parameters')->where('type', 'hsgq')->first();
            OLTSIDE_HSGQ::OLT_SIDE_ONU_UNINSTALL($ip, $HSGQtoken->password, $ifindex);

            Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

            DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

            ontStats::naCount();

            return true;
        } elseif ($Type == 'HUAWEI') {
            $res = OLTSIDE_HUAWEI::OLT_SIDE_ONU_UNINSTALL($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex);
            if ($res == true) {
                Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

                DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

                ontStats::naCount();

                return true;
            } else {
                return $res;
            }
        } elseif ($Type == 'ZTE') {
            $res = OLTSIDE_ZTE::OLT_SIDE_ONU_UNINSTALL($ip, $DB->snmpRcomunity, $DB->snmpWcomunity, $ifindex);
            if ($res == true) {
                Log::channel('actions')->error('[Onu Uninstall] ' . $descr . '\n[OLT] ' . $ip . '\n[User] ' . $username . '\n[Address] ' . $userIp);

                DB::table('ontList')->where(DB::raw('TRIM(onuMac)'), $onuMac)->where(DB::raw('TRIM(onuDescr)'), $descr)->delete();

                ontStats::naCount();

                return true;
            } else {
                return $res;
            }
        }
    }

    ////////////////////////////////////////////////////////////////////////////////////// D E S C R I P T I O N

    public static function GlobalDescriptionSearch(Request $request)
    {
        $validator = validator()->make($request->only('Descr'), [
            'Descr' => 'required|string',
        ]);

        if ($validator->fails()) {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $user = $request->input('Descr');

        $username = $request->user()->name;
        $userIp = $request->ip();

        Log::channel('actions')->info('[Global Search By Description] ' . $user . '\n[User] ' . $username . '\n[Address] ' . $userIp);

        $List = DB::table('ontList')
            ->whereRaw('TRIM(onuDescr) = ?', [trim($user)])
            ->get();

        return $List;
    }
}
