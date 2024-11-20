<?php

namespace App\Http\Controllers;
 
use Illuminate\Support\Facades\DB; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;


use App\Models\OLTSIDE_BDCOM;
use App\Models\OLTSIDE_HUAWEI;
use App\Models\OLTSIDE_ZTE;
use App\Models\OLTSIDE_VSOLUTION;
use App\Models\OLTSIDE_HSGQ;
use App\Models\OLTSIDE_ZYXEL;
use App\Models\OLTSIDE_CISCO;


class OLTSIDEController extends Controller
{

    static public function NameSearch(REQUEST $request)
    {
        $validator = validator()->make($request->only('name'), [
            'name'    => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $name     = $request->input('name');


        $data = DB::table('devices')->get();

        $ResultArray = [];
        foreach ($data as $key => $value) 
        {
            if ((strpos(strtoupper($value->mast),  strtoupper($name)) !== false) || (strpos(strtoupper($value->device_name),  strtoupper($name)) !== false))
            {
                $ResultArray[$key]['name'] = $value->mast.' '.$value->device_name;
                $ResultArray[$key]['ip']   = $value->Address;
            }         
        }

        return $ResultArray;
    }    

    /////////////////////////////////////////////////////////////////////////////// HUAWEI
    static public function huawei_SystemInfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->info('[OLT Search] '.$ip .'\n[User] '.$username.'\n[Address] '.$userIp);
      
        return  OLTSIDE_HUAWEI::OLT_SIDE_SYSTEMINFO($ip,$read,$write);
    }

    static public function huawei_PonCharts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));


        return  OLTSIDE_HUAWEI::OLT_SIDE_PON_CHARTS($ip,$read,$write);
    }

    static public function huawei_SwitchPorts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));


        return  OLTSIDE_HUAWEI::OLT_SIDE_SWITCHPORTS($ip,$read,$write);
    }

    static public function huawei_OnuDescription(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write' , 'ifindex' , 'user' , 'descr'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
            'descr'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');
        $descr    = $request->input('descr');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Onu Description Edit] '.$descr .'\n[OLT] '.$ip.'\n[OLD DESCRIPTION] '.$user.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_HUAWEI::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$read,$write,$ifindex,$descr);
    }

    static public function huawei_OnuUninstall(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write' , 'ifindex' , 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->error('[Onu Uninstall] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_HUAWEI::OLT_SIDE_ONU_UNINSTALL($ip,$read,$write,$ifindex);
    }

    static public function huawei_PonParameters(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));

        return  OLTSIDE_HUAWEI::OLT_SIDE_PON_PARAMETERS($ip,$read,$write);
    }

    static public function huawei_PonTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HUAWEI::OLT_SIDE_PON_TURNON($ip,$read,$write,$ifindex);
    }
     
    static public function huawei_ShutDown(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HUAWEI::OLT_SIDE_PON_TURNOFF($ip,$read,$write,$ifindex);
    }
     
    static public function huawei_PonDescriptionEdit(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'descr', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'descr'     => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HUAWEI::OLT_SIDE_PON_DESCRIPTION($ip,$read,$write,$ifindex,$descr);
    }

    static public function huawei_Uplinks(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));

        return  OLTSIDE_HUAWEI::OLT_SIDE_UPLINKS($ip,$read,$write);
    }

    static public function huawei_UplinksTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HUAWEI::OLT_SIDE_UPLINK_TURNON($ip,$read,$write,$ifindex);
    }
     
    static public function huawei_UplinksShutDown(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HUAWEI::OLT_SIDE_UPLINK_TURNOFF($ip,$read,$write,$ifindex);
    }
     
    static public function huawei_UplinksDescriptionEdit(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'descr', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'descr'     => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HUAWEI::OLT_SIDE_UPLINK_DESCRIPTION($ip,$read,$write,$ifindex,$descr);
    }

    static public function huawei_Details(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'ifindex'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'ifindex'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $ifindex  = $request->input('ifindex');

        return  OLTSIDE_HUAWEI::OLT_SIDE_ONT_DETAILS($ip,$read,$ifindex);
    }

    static public function huawei_OnuControlOff(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Onu Control Disabled] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HUAWEI::OLT_SIDE_ONT_CONTROL_DISABLE($ip,$read,$write,$ifindex);
    }

    static public function huawei_OnuControlOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Onu Control Enable] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HUAWEI::OLT_SIDE_ONT_CONTROL_ENABLE($ip,$read,$write,$ifindex);
    }

    /////////////////////////////////////////////////////////////////////////////// BDCOM

    static public function bdcom_SystemInfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->info('[OLT Search] '.$ip .'\n[User] '.$username.'\n[Address] '.$userIp);
      
        return  OLTSIDE_BDCOM::OLT_SIDE_SYSTEMINFO($ip,$read,$write);
    }

    static public function bdcom_PonCharts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));


        return  OLTSIDE_BDCOM::OLT_SIDE_PON_CHARTS($ip,$read,$write);
    }

    static public function bdcom_SwitchPorts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));


        return  OLTSIDE_BDCOM::OLT_SIDE_SWITCHPORTS($ip,$read,$write);
    }

    static public function bdcom_OnuDescription(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write' , 'ifindex' , 'user' , 'descr'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
            'descr'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');
        $descr    = $request->input('descr');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Onu Description Edit] '.$descr .'\n[OLT] '.$ip.'\n[OLD DESCRIPTION] '.$user.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_BDCOM::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$read,$write,$ifindex,$descr);
    }

    static public function bdcom_OnuUninstall(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write' , 'ifindex' , 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->error('[Onu Uninstall] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_BDCOM::OLT_SIDE_ONU_UNINSTALL($ip,$read,$write,$ifindex);
    }

    static public function bdcom_PonParameters(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));

  
        return  OLTSIDE_BDCOM::OLT_SIDE_PON_PARAMETERS($ip,$read,$write);
    }

    static public function bdcom_PonTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_BDCOM::OLT_SIDE_PON_TURNON($ip,$read,$write,$ifindex);
    }
     
    static public function bdcom_ShutDown(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_BDCOM::OLT_SIDE_PON_TURNOFF($ip,$read,$write,$ifindex);
    }
     
    static public function bdcom_PonDescriptionEdit(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'descr', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'descr'     => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_BDCOM::OLT_SIDE_PON_DESCRIPTION($ip,$read,$write,$ifindex,$descr);
    }
     
    static public function bdcom_Uplinks(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));

        return  OLTSIDE_BDCOM::OLT_SIDE_UPLINKS($ip,$read,$write);
    }

    static public function bdcom_UplinksTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_BDCOM::OLT_SIDE_UPLINK_TURNON($ip,$read,$write,$ifindex);
    }
     
    static public function bdcom_UplinksShutDown(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_BDCOM::OLT_SIDE_UPLINK_TURNOFF($ip,$read,$write,$ifindex);
    }
     
    static public function bdcom_UplinksDescriptionEdit(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'descr', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'descr'     => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_BDCOM::OLT_SIDE_UPLINK_DESCRIPTION($ip,$read,$write,$ifindex,$descr);
    }

    static public function bdcom_Details(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'ifindex'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'ifindex'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $ifindex  = $request->input('ifindex');

        return  OLTSIDE_BDCOM::OLT_SIDE_ONT_DETAILS($ip,$read,$ifindex);
    }
     
    
    /////////////////////////////////////////////////////////////////////////////// VSOLUTION

    static public function vsolution_SystemInfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->info('[OLT Search] '.$ip .'\n[User] '.$username.'\n[Address] '.$userIp);
      
        return  OLTSIDE_VSOLUTION::OLT_SIDE_SYSTEMINFO($ip,$read,$write);
    }
     
    static public function vsolution_SwitchPorts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));


        return  OLTSIDE_VSOLUTION::OLT_SIDE_SWITCHPORTS($ip,$read,$write);
    }

    static public function vsolution_OnuDescription(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write' , 'ifindex' , 'user' , 'descr'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
            'descr'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');
        $descr    = $request->input('descr');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Onu Description Edit] '.$descr .'\n[OLT] '.$ip.'\n[OLD DESCRIPTION] '.$user.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_VSOLUTION::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$read,$write,$ifindex,$descr);
    }

    static public function vsolution_OnuUninstall(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write' , 'ifindex' , 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->error('[Onu Uninstall] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_VSOLUTION::OLT_SIDE_ONU_UNINSTALL($ip,$read,$write,$ifindex);
    }

    static public function vsolution_PonParameters(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));

  
        return  OLTSIDE_VSOLUTION::OLT_SIDE_PON_PARAMETERS($ip,$read,$write);
    }

    static public function vsolution_PonTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_VSOLUTION::OLT_SIDE_PON_TURNON($ip,$read,$write,$ifindex);
    }
     
    static public function vsolution_ShutDown(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_VSOLUTION::OLT_SIDE_PON_TURNOFF($ip,$read,$write,$ifindex);
    }
     
    static public function vsolution_PonDescriptionEdit(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'descr', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'descr'     => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_VSOLUTION::OLT_SIDE_PON_DESCRIPTION($ip,$read,$write,$ifindex,$descr);
    }
     
    static public function vsolution_Uplinks(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));

        return  OLTSIDE_VSOLUTION::OLT_SIDE_UPLINKS($ip,$read,$write);
    }

    static public function vsolution_UplinksTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_VSOLUTION::OLT_SIDE_UPLINK_TURNON($ip,$read,$write,$ifindex);
    }
     
    static public function vsolution_UplinksShutDown(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_VSOLUTION::OLT_SIDE_UPLINK_TURNOFF($ip,$read,$write,$ifindex);
    }
     
    static public function vsolution_UplinksDescriptionEdit(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'descr', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'descr'     => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_VSOLUTION::OLT_SIDE_UPLINK_DESCRIPTION($ip,$read,$write,$ifindex,$descr);
    }

    static public function vsolution_Details(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');

        return  OLTSIDE_VSOLUTION::OLT_SIDE_ONT_DETAILS($ip,$read,$write,$ifindex);
    }
    /////////////////////////////////////////////////////////////////////////////// ZTE

    static public function zte_SystemInfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->info('[OLT Search] '.$ip .'\n[User] '.$username.'\n[Address] '.$userIp);
      
        return  OLTSIDE_ZTE::OLT_SIDE_SYSTEMINFO($ip,$read,$write);
    }

    static public function zte_PonCharts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));


        return  OLTSIDE_ZTE::OLT_SIDE_PON_CHARTS($ip,$read,$write);
    }

    static public function zte_SwitchPorts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));


        return  OLTSIDE_ZTE::OLT_SIDE_SWITCHPORTS($ip,$read,$write);
    }

    static public function zte_OnuDescription(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write' , 'ifindex' , 'user' , 'descr'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
            'descr'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');
        $descr    = $request->input('descr');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Onu Description Edit] '.$descr .'\n[OLT] '.$ip.'\n[OLD DESCRIPTION] '.$user.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_ZTE::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$read,$write,$ifindex,$descr);
    }

    static public function zte_OnuUninstall(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write' , 'ifindex' , 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->error('[Onu Uninstall] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_ZTE::OLT_SIDE_ONU_UNINSTALL($ip,$read,$write,$ifindex);
    }

    static public function zte_PonParameters(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));

  
        return  OLTSIDE_ZTE::OLT_SIDE_PON_PARAMETERS($ip,$read,$write);
    }

    static public function zte_PonTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_ZTE::OLT_SIDE_PON_TURNON($ip,$read,$write,$ifindex);
    }
     
    static public function zte_ShutDown(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_ZTE::OLT_SIDE_PON_TURNOFF($ip,$read,$write,$ifindex);
    }
     
    static public function zte_PonDescriptionEdit(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'descr', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'descr'     => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_ZTE::OLT_SIDE_PON_DESCRIPTION($ip,$read,$write,$ifindex,$descr);
    }

    static public function zte_Uplinks(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));

        return  OLTSIDE_ZTE::OLT_SIDE_UPLINKS($ip,$read,$write);
    }

    static public function zte_UplinksTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_ZTE::OLT_SIDE_UPLINK_TURNON($ip,$read,$write,$ifindex);
    }
     
    static public function zte_UplinksShutDown(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_ZTE::OLT_SIDE_UPLINK_TURNOFF($ip,$read,$write,$ifindex);
    }
     
    static public function zte_UplinksDescriptionEdit(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'descr', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'descr'     => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_ZTE::OLT_SIDE_UPLINK_DESCRIPTION($ip,$read,$write,$ifindex,$descr);
    }

    static public function zte_Details(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'ifindex'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'ifindex'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $ifindex  = $request->input('ifindex');

        return  OLTSIDE_ZTE::OLT_SIDE_ONT_DETAILS($ip,$read,$ifindex);
    }

    static public function zte_OnuControlOff(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Onu Control Disabled] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_ZTE::OLT_SIDE_ONT_CONTROL_DISABLE($ip,$read,$write,$ifindex);
    }

    static public function zte_OnuControlOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write', 'ifindex', 'user'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $write    = Crypt::decrypt($request->input('write'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Onu Control Enable] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_ZTE::OLT_SIDE_ONT_CONTROL_ENABLE($ip,$read,$write,$ifindex);
    }
    /////////////////////////////////////////////////////////////////////////////// HSGQ
    
    static public function hsgq_SystemInfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->info('[OLT Search] '.$ip .'\n[User] '.$username.'\n[Address] '.$userIp);
      
        return  OLTSIDE_HSGQ::OLT_SIDE_SYSTEMINFO($ip,$read,$write);
    }

    static public function hsgq_SwitchPorts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token'), [
            'ip'    => 'required|ipv4',
            'token' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $token  = Crypt::decrypt($request->input('token'));


        return  OLTSIDE_HSGQ::OLT_SIDE_SWITCHPORTS($ip,$token);
    }

    static public function hsgq_PonCharts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token', 'read'), [
            'ip'    => 'required|ipv4',
            'token' => 'required|string',
            'read'  => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $token  = Crypt::decrypt($request->input('token'));
        $read   = Crypt::decrypt($request->input('read'));

        return  OLTSIDE_HSGQ::OLT_SIDE_PON_CHARTS($ip,$token,$read);
    }

    static public function hsgq_OnuDescription(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token', 'ifindex','descr','user'), [
            'ip'      => 'required|ipv4',
            'token'   => 'required|string',
            'ifindex' => 'required|numeric',
            'descr'   => 'required|string',
            'user'    => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $token    = Crypt::decrypt($request->input('token'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Onu Description Edit] '.$descr .'\n[OLT] '.$ip.'\n[OLD DESCRIPTION] '.$user.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_HSGQ::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$token,$ifindex,$descr);
    }

    static public function hsgq_OnuUninstall(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token', 'ifindex' , 'user'), [
            'ip'        => 'required|ipv4',
            'token'     => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $token    = Crypt::decrypt($request->input('token'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->error('[Onu Uninstall] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  OLTSIDE_HSGQ::OLT_SIDE_ONU_UNINSTALL($ip,$token,$ifindex);
    }

    static public function hsgq_PonParameters(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token'), [
            'ip'        => 'required|ipv4',
            'token'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $token    = Crypt::decrypt($request->input('token'));

  
        return  OLTSIDE_HSGQ::OLT_SIDE_PON_PARAMETERS($ip,$token);
    }

    static public function hsgq_PonTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token', 'ifindex', 'user'), [
            'ip'         => 'required|ipv4',
            'token'      => 'required|string',
            'ifindex'    => 'required|numeric',
            'user'       => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip        = $request->input('ip');
        $token     = Crypt::decrypt($request->input('token'));
        $ifindex   = $request->input('ifindex');
        $user      = $request->input('user');


        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HSGQ::OLT_SIDE_PON_TURNON($ip,$token,$ifindex);
    }
     
    static public function hsgq_ShutDown(REQUEST $request)
    {
        
        $validator = validator()->make($request->only('ip', 'token', 'ifindex', 'user'), [
            'ip'         => 'required|ipv4',
            'token'      => 'required|string',
            'ifindex'    => 'required|numeric',
            'user'       => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip        = $request->input('ip');
        $token     = Crypt::decrypt($request->input('token'));
        $ifindex   = $request->input('ifindex');
        $user      = $request->input('user');


        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HSGQ::OLT_SIDE_PON_TURNOFF($ip,$token,$ifindex);
    }
     
    static public function hsgq_PonDescriptionEdit(REQUEST $request)
    {
      
        $validator = validator()->make($request->only('ip', 'token', 'ifindex', 'descr', 'user' ,'admin','neg','deuplex','erate','flag','control','irate','mtu','pvid','speed'), [
            'ip'        => 'required|ipv4',
            'token'      => 'required|string',
            'ifindex'   => 'required|numeric',
            'descr'     => 'required|string',
            'user'      => 'required|string',

            'admin'      => 'required|numeric',
            'neg'        => 'required|numeric',
            'deuplex'    => 'required|numeric',
            'erate'      => 'required|numeric',
            'flag'       => 'required|numeric',
            'control'    => 'required|numeric',
            'irate'      => 'required|numeric',
            'mtu'        => 'required|numeric',
            'pvid'       => 'required|numeric',
            'speed'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip        = $request->input('ip');
        $token     = Crypt::decrypt($request->input('token'));
        $ifindex   = $request->input('ifindex');
        $descr     = $request->input('descr');
        $user      = $request->input('user');

        $admin     = $request->input('admin');
        $neg       = $request->input('neg');
        $deuplex   = $request->input('deuplex');
        $erate     = $request->input('erate');
        $flag      = $request->input('flag');
        $control   = $request->input('control');
        $irate     = $request->input('irate');
        $mtu       = $request->input('mtu');
        $pvid      = $request->input('pvid');
        $speed     = $request->input('speed');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Pon Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HSGQ::OLT_SIDE_PON_DESCRIPTION($ip,$token,$ifindex,$descr,$admin,$neg,$deuplex,$erate,$flag,$control,$irate,$mtu,$pvid,$speed);
    }

    static public function hsgq_Uplinks(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read','token'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'token'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $read     = Crypt::decrypt($request->input('read'));
        $token    = Crypt::decrypt($request->input('token'));

        return  OLTSIDE_HSGQ::OLT_SIDE_UPLINKS($ip,$read,$token);
    }

    static public function hsgq_UplinksDescriptionEdit(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token','ifindex', 'descr', 'user', 'admin', 'neg', 'deuplex', 'erate', 'flag', 'control', 'irate', 'mtu', 'pvid', 'speed'), [
            'ip'        => 'required|ipv4',
            'token'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'descr'     => 'required|string',
            'user'      => 'required|string',

            'admin'     => 'required|string',
            'neg'       => 'required|string',
            'deuplex'   => 'required|string',
            'erate'     => 'required|string',
            'flag'      => 'required|string',
            'control'   => 'required|string',
            'irate'     => 'required|string',
            'mtu'       => 'required|string',
            'pvid'      => 'required|string',
            'speed'     => 'required|string',
        ]);

      

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $token    = Crypt::decrypt($request->input('token'));
        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $admin    = $request->input('admin');
        $neg      = $request->input('neg');
        $deuplex  = $request->input('deuplex');
        $erate    = $request->input('erate');
        $flag     = $request->input('flag');
        $control  = $request->input('control');
        $irate    = $request->input('irate');
        $mtu      = $request->input('mtu');
        $pvid     = $request->input('pvid');
        $speed    = $request->input('speed');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Description] '.$user .'\n[OLT] '.$ip.'\n[Description] '.$descr.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HSGQ::OLT_SIDE_UPLINK_DESCRIPTION($ip,$token,$ifindex,$descr,$admin,$neg,$deuplex,$erate,$flag,$control,$irate,$mtu,$pvid,$speed);
    }

    static public function hsgq_UplinksTurnOn(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token', 'ifindex', 'user', 'descr', 'neg', 'deuplex', 'erate', 'flag', 'control', 'irate', 'mtu', 'pvid', 'speed'), [
            'ip'        => 'required|ipv4',
            'token'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',

            'descr'     => 'required|string',
            'neg'       => 'required|string',
            'deuplex'   => 'required|string',
            'erate'     => 'required|string',
            'flag'      => 'required|string',
            'control'   => 'required|string',
            'irate'     => 'required|string',
            'mtu'       => 'required|string',
            'pvid'      => 'required|string',
            'speed'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $token    = Crypt::decrypt($request->input('token'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $descr    = $request->input('descr');
        $neg      = $request->input('neg');
        $deuplex  = $request->input('deuplex');
        $erate    = $request->input('erate');
        $flag     = $request->input('flag');
        $control  = $request->input('control');
        $irate    = $request->input('irate');
        $mtu      = $request->input('mtu');
        $pvid     = $request->input('pvid');
        $speed    = $request->input('speed');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Turn ON] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HSGQ::OLT_SIDE_UPLINK_TURNON($ip,$token,$ifindex,$descr,$neg,$deuplex,$erate,$flag,$control,$irate,$mtu,$pvid,$speed);
    }
     
    static public function hsgq_UplinksShutDown(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token', 'ifindex', 'user', 'descr', 'neg', 'deuplex', 'erate', 'flag', 'control', 'irate', 'mtu', 'pvid', 'speed'), [
            'ip'        => 'required|ipv4',
            'token'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'user'      => 'required|string',

            'descr'     => 'required|string',
            'neg'       => 'required|string',
            'deuplex'   => 'required|string',
            'erate'     => 'required|string',
            'flag'      => 'required|string',
            'control'   => 'required|string',
            'irate'     => 'required|string',
            'mtu'       => 'required|string',
            'pvid'      => 'required|string',
            'speed'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $token    = Crypt::decrypt($request->input('token'));
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('user');

        $descr    = $request->input('descr');
        $neg      = $request->input('neg');
        $deuplex  = $request->input('deuplex');
        $erate    = $request->input('erate');
        $flag     = $request->input('flag');
        $control  = $request->input('control');
        $irate    = $request->input('irate');
        $mtu      = $request->input('mtu');
        $pvid     = $request->input('pvid');
        $speed    = $request->input('speed');


        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->warning('[Uplink Shutdown] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);
  
        return  OLTSIDE_HSGQ::OLT_SIDE_UPLINK_TURNOFF($ip,$token,$ifindex,$descr,$neg,$deuplex,$erate,$flag,$control,$irate,$mtu,$pvid,$speed);
    }

    static public function hsgq_Details(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token', 'ifindex'), [
            'ip'        => 'required|ipv4',
            'token'     => 'required|string',
            'ifindex'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $token    = Crypt::decrypt($request->input('token'));
        $ifindex  = $request->input('ifindex');

        return  OLTSIDE_HSGQ::OLT_SIDE_ONT_DETAILS($ip,$token,$ifindex);
    }
    /////////////////////////////////////////////////////////////////////////////// ZYXEL

    static public function zyxel_SystemInfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->info('[OLT Search] '.$ip .'\n[User] '.$username.'\n[Address] '.$userIp);
      
        return  OLTSIDE_ZYXEL::OLT_SIDE_SYSTEMINFO($ip,$read,$write);
    }
     
    static public function zyxel_OnuDescriptionEdit(REQUEST $request)
    {
        
        $validator = validator()->make($request->only('ip', 'read', 'write','ifindex','descr','user'), [
            'ip'      => 'required|ipv4',
            'read'    => 'required|string',
            'write'   => 'required|string',
            'ifindex' => 'required|numeric',
            'user'    => 'required|string',
            'descr'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));

        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Description Edit] '.$descr .'\n[OLT] '.$ip.'\n[OLD DESCRIPTION] '.$user.'\n[User] '.$username.'\n[Address] '.$userIp);

        return  OLTSIDE_ZYXEL::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$write,$ifindex,$descr);
    }
     
    /////////////////////////////////////////////////////////////////////////////// CISCO

    static public function cisco_SystemInfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->info('[OLT Search] '.$ip .'\n[User] '.$username.'\n[Address] '.$userIp);
      
        return  OLTSIDE_CISCO::OLT_SIDE_SYSTEMINFO($ip,$read,$write);
    }

    static public function cisco_OnuDescriptionEdit(REQUEST $request)
    {
        
        $validator = validator()->make($request->only('ip', 'read', 'write','ifindex','descr','user'), [
            'ip'      => 'required|ipv4',
            'read'    => 'required|string',
            'write'   => 'required|string',
            'ifindex' => 'required|numeric',
            'user'    => 'required|string',
            'descr'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));

        $ifindex  = $request->input('ifindex');
        $descr    = $request->input('descr');
        $user     = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Description Edit] '.$descr .'\n[OLT] '.$ip.'\n[OLD DESCRIPTION] '.$user.'\n[User] '.$username.'\n[Address] '.$userIp);

        return  OLTSIDE_CISCO::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$write,$ifindex,$descr);
    }

}
