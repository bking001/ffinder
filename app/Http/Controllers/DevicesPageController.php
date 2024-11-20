<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Models\PrivilegesModel;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Log;

class DevicesPageController extends Controller
{
    public function getDataAndView(Request $request)
    {
        PrivilegesModel::PrivCheck('Devices');
        $data = DB::table('devices')->orderBy('Type')->paginate(8)->appends(request()->query());
        return view('devices', ['data' => $data]);
    }

    public function parametersUpdate(Request $request) 
    {
        PrivilegesModel::PrivCheck('Devices');
            try {
                    foreach ($request->all() as $item)
                    {
                            DB::table('devices')
                            ->where('id', $item['id'])
                            ->update([
                                'Type'          => $item['type'],
                                'Address'       => $item['Address'],
                                'device_name'   => $item['name'],
                                'mast'          => $item['point'],
                                'coordinates'   => $item['coord'],
                                'Username'      => $item['username'],
                                'Pass'          => $item['password'],
                                'snmpRcomunity' => $item['Read'],
                                'snmpWcomunity' => $item['write']
                            ]);
                            break;
                    }
                    return true;
              } catch (\Throwable $e) {
                    return $e->getMessage();
                }
    }

    public function GlobalEdit(Request $request) 
    {
        PrivilegesModel::PrivCheck('Devices');

        if ($request->has('BDCOM_CHECKBOX'))
        {
            if ($request->input('Read_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'BDCOM')
                ->update([
                    'snmpRcomunity' =>  $request->input('Read_Comunity')
                ]);
            }
            if ($request->input('Write_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'BDCOM')
                ->update([
                    'snmpWcomunity' =>  $request->input('Write_Comunity')
                ]);
            }
            if ($request->input('Username') !== null)
            {
                DB::table('devices')
                ->where('Type', 'BDCOM')
                ->update([
                    'Username' =>  $request->input('Username')
                ]);
            }
            if ($request->input('Password') !== null)
            {
                DB::table('devices')
                ->where('Type', 'BDCOM')
                ->update([
                    'Pass' =>  $request->input('Password')
                ]);
            }
        }

        if ($request->has('HUAWEI_CHECKBOX'))
        {
            if ($request->input('Read_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'HUAWEI')
                ->update([
                    'snmpRcomunity' =>  $request->input('Read_Comunity')
                ]);
            }
            if ($request->input('Write_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'HUAWEI')
                ->update([
                    'snmpWcomunity' =>  $request->input('Write_Comunity')
                ]);
            }
            if ($request->input('Username') !== null)
            {
                DB::table('devices')
                ->where('Type', 'HUAWEI')
                ->update([
                    'Username' =>  $request->input('Username')
                ]);
            }
            if ($request->input('Password') !== null)
            {
                DB::table('devices')
                ->where('Type', 'HUAWEI')
                ->update([
                    'Pass' =>  $request->input('Password')
                ]);
            }
        }

        if ($request->has('ZTE_CHECKBOX'))
        {
            if ($request->input('Read_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'ZTE')
                ->update([
                    'snmpRcomunity' =>  $request->input('Read_Comunity')
                ]);
            }
            if ($request->input('Write_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'ZTE')
                ->update([
                    'snmpWcomunity' =>  $request->input('Write_Comunity')
                ]);
            }
            if ($request->input('Username') !== null)
            {
                DB::table('devices')
                ->where('Type', 'ZTE')
                ->update([
                    'Username' =>  $request->input('Username')
                ]);
            }
            if ($request->input('Password') !== null)
            {
                DB::table('devices')
                ->where('Type', 'ZTE')
                ->update([
                    'Pass' =>  $request->input('Password')
                ]);
            }
        }

        if ($request->has('VSOLUTION_CHECKBOX'))
        {
            if ($request->input('Read_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'VSOLUTION')
                ->update([
                    'snmpRcomunity' =>  $request->input('Read_Comunity')
                ]);
            }
            if ($request->input('Write_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'VSOLUTION')
                ->update([
                    'snmpWcomunity' =>  $request->input('Write_Comunity')
                ]);
            }
            if ($request->input('Username') !== null)
            {
                DB::table('devices')
                ->where('Type', 'VSOLUTION')
                ->update([
                    'Username' =>  $request->input('Username')
                ]);
            }
            if ($request->input('Password') !== null)
            {
                DB::table('devices')
                ->where('Type', 'VSOLUTION')
                ->update([
                    'Pass' =>  $request->input('Password')
                ]);
            }
        }

        if ($request->has('HSGQ_CHECKBOX'))
        {
            if ($request->input('Read_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'HSGQ')
                ->update([
                    'snmpRcomunity' =>  $request->input('Read_Comunity')
                ]);
            }
            if ($request->input('Write_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'HSGQ')
                ->update([
                    'snmpWcomunity' =>  $request->input('Write_Comunity')
                ]);
            }
            if ($request->input('Username') !== null)
            {
                DB::table('devices')
                ->where('Type', 'HSGQ')
                ->update([
                    'Username' =>  $request->input('Username')
                ]);
            }
            if ($request->input('Password') !== null)
            {
                DB::table('devices')
                ->where('Type', 'HSGQ')
                ->update([
                    'Pass' =>  $request->input('Password')
                ]);
            }
        }

        if ($request->has('ZYXEL_CHECKBOX'))
        {
            if ($request->input('Read_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'ZYXEL')
                ->update([
                    'snmpRcomunity' =>  $request->input('Read_Comunity')
                ]);
            }
            if ($request->input('Write_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'ZYXEL')
                ->update([
                    'snmpWcomunity' =>  $request->input('Write_Comunity')
                ]);
            }
            if ($request->input('Username') !== null)
            {
                DB::table('devices')
                ->where('Type', 'ZYXEL')
                ->update([
                    'Username' =>  $request->input('Username')
                ]);
            }
            if ($request->input('Password') !== null)
            {
                DB::table('devices')
                ->where('Type', 'ZYXEL')
                ->update([
                    'Pass' =>  $request->input('Password')
                ]);
            }
        }

        if ($request->has('CISCO_CHECKBOX'))
        {
            if ($request->input('Read_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'CISCO_CATALYST')
                ->update([
                    'snmpRcomunity' =>  $request->input('Read_Comunity')
                ]);
            }
            if ($request->input('Write_Comunity') !== null)
            {
                DB::table('devices')
                ->where('Type', 'CISCO_CATALYST')
                ->update([
                    'snmpWcomunity' =>  $request->input('Write_Comunity')
                ]);
            }
            if ($request->input('Username') !== null)
            {
                DB::table('devices')
                ->where('Type', 'CISCO_CATALYST')
                ->update([
                    'Username' =>  $request->input('Username')
                ]);
            }
            if ($request->input('Password') !== null)
            {
                DB::table('devices')
                ->where('Type', 'CISCO_CATALYST')
                ->update([
                    'Pass' =>  $request->input('Password')
                ]);
            }
        }


        return Redirect::route('Devices')->with('status', 'Devices Updated successfully');
    }
     
    public function DefaultCreds(Request $request) 
    {
        PrivilegesModel::PrivCheck('Devices');

        $validator = validator()->make($request->only('SelectedDeviceType'), [
            'SelectedDeviceType' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $SelectedDeviceType = $request->input('SelectedDeviceType');

        try {
            $mostCountedUsername = DB::table('devices')
            ->select('username', 'Pass', 'snmpRcomunity', 'snmpWcomunity', DB::raw('count(*) as count'))
            ->where('Type', $SelectedDeviceType)
            ->groupBy('username', 'Pass', 'snmpRcomunity', 'snmpWcomunity')
            ->orderByDesc('count')
            ->first();
            return response()->json($mostCountedUsername);
        }
        catch (\Throwable $e) {
            return response()->json(['error' => $e->getMessage()]);
        }
   
    }
     

    public function Create_Device(Request $request): RedirectResponse
    {  
        PrivilegesModel::PrivCheck('Devices');
        try {
                DB::table('devices')->insert([
                    'Type'          => $request->input('type'),
                    'Address'       => $request->input('IP'),
                    'device_name'   => $request->input('Device_Name'),
                    'mast'          => $request->input('Device_Point'),
                    'coordinates'   => $request->input('Device_Coordinates'),
                    'Username'      => $request->input('Username'),
                    'Pass'          => $request->input('Password'),
                    'snmpRcomunity' => $request->input('Read_Comunity'),
                    'snmpWcomunity' => $request->input('Write_Comunity'),
                ]);

                 return Redirect::route('Devices')->with('status', 'Device  Created successfully');
          } catch (\Throwable $e) {
                return Redirect::route('Devices')->with('error', 'Error Creating device ' . $e->getMessage());
            }
    }

    public function Delete_Device(Request $request) 
    {
        PrivilegesModel::PrivCheck('Devices');
        try {
            foreach ($request->all() as $item)
            {
                    DB::table('devices')
                    ->where('id', $item['id'])
                    ->delete();
                    break;
            }
                return true;
          } catch (\Throwable $e) {
                return $e->getMessage();
            }
    }

    public function search(Request $request)
    {
        PrivilegesModel::PrivCheck('Devices');
        try {
                $param   = $request->input('default_search');
                $columns = Schema::getColumnListing('devices');
                $query   = DB::table('devices')->where(function ($query) use ($columns, $param)
                {
                    foreach ($columns as $column)
                    {
                        $query->orWhere($column, 'LIKE', '%' . $param . '%');
                    }
                });
                $results = $query->paginate(8);

                return view('devices', ['data' => $results]);
            } catch (\Throwable $e)
            {
                abort(500,"Error");
            }
    }

    public function masts(Request $request)
    {
        PrivilegesModel::PrivCheck('Devices');
        $data = DB::table('andzebi')->orderByDesc('id')->paginate(8)->appends(request()->query());
        return view('masts', ['data' => $data]);
    }

    public function mast_search(Request $request)
    {
        PrivilegesModel::PrivCheck('Devices');
        try {
                $param   = $request->input('default_search');
                $columns = Schema::getColumnListing('andzebi');
                $query   = DB::table('andzebi')
                ->where(function ($query) use ($columns, $param)
                {
                    foreach ($columns as $column)
                    {
                        $query->orWhere($column, 'LIKE', '%' . $param . '%');
                    }
                });
                $results = $query->paginate(10);

               
                return view('masts', ['data' => $results]);
            } catch (\Throwable $e)
            {
                abort(500,"Error");
            }
    }

    public function mast_add(Request $request): RedirectResponse
    {
        PrivilegesModel::PrivCheck('Devices');
        try {
                DB::table('andzebi')->insert([
                    'saxeli' => $request->input('Mast_Name'),
                ]);

                return Redirect::route('mast_table')->with('status', 'Mast  Created successfully');
          } catch (\Throwable $e) {
                return Redirect::route('mast_table')->with('error', 'Error Creating device ' . $e->getMessage());
            }
    }

    public function mast_Delete(Request $request) 
    {
        PrivilegesModel::PrivCheck('Devices');
        try {
           
                DB::table('andzebi')
                ->where('id', $request->input('id'))
                ->delete();

                return true;
            } catch (\Throwable $e) {
                  return $e->getMessage();
              }
    }

    public function mast_update(Request $request) 
    {
        PrivilegesModel::PrivCheck('Devices');

            try {          
                    DB::table('andzebi')
                    ->where('id', $request->input('id'))
                    ->update(['saxeli'  => $request->input('saxeli')]);                    
               
                    return true;
              } catch (\Throwable $e) {
                    return $e->getMessage();
                }
    }

}
