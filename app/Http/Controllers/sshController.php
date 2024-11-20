<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB; 

use App\Models\sshModel;

class sshController extends Controller
{
    static public function CommandQuery(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip'), [
            'ip' => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip = $request->input('ip');
        $credentials = DB::table('devices')->where('Address',$ip)->first();
 
 
        $commandArray = [
            "ena",   
            "AIRLINK2014",
            "conf t",
            "interface gpon-onu_1/1/11:1",
            "show pon onu-profile gpon remote router cfg"
        ];
        
        return sshModel::SSH($ip,22,$credentials->Username,$credentials->Pass,$commandArray,false);
    }
}
 