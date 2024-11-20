<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB; 
use App\Models\BDCOM;
use App\Models\HUAWEI;
use App\Models\ZTE;
use App\Models\HSGQ;
use App\Models\VSOLUTION;
use App\Models\ZYXEL;
use App\Models\CISCO;
use App\Models\SECTOR;
use App\Models\UISP;
use App\Models\mikrotikRouter;
use App\Models\FILTER;
use App\Models\PrivilegesModel;
use App\Models\sshModel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use App\Models\Install\_huawei;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
 

class OLT_SECTOR extends Controller
{
    static public function type(REQUEST $request)
    {
        $validator = validator()->make($request->only('device_ip'), [
            'device_ip' => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->get('device_ip');
            return response()->json(['error' => $errors[0]]);
        }

        $Type = DB::table('devices')->where('Address',$request->only('device_ip'))->first();

        if (!$Type)return response()->json(['error' => 'IP address does not exist in the database']);
     
        $username = $request->user()->name;
        $userIp   = $request->ip();

        // sshModel::CustomEncrypt($Type->Username)

        if(PrivilegesModel::SafePrivCheck('Priv_Onu'))
        {
            if($Type->Type == 'HSGQ')
            {
                $Token = DB::table('parameters')->where('type','hsgq')->first();
                $data = [
                    'Token'         => Crypt::encrypt($Token->password),
                    'Type'          => $Type->Type,
                    'Address'       => $Type->Address,
                    'Username'      => $Type->Username,
                    'Pass'          => $Type->Pass,
                    'snmpRcomunity' => Crypt::encrypt($Type->snmpRcomunity),
                    'snmpWcomunity' => Crypt::encrypt($Type->snmpWcomunity),
                    'User'          => $username,
                    'UserIP'        => $userIp,
                ];
            }
            else 
            {
                $data = [
                    'Type'          => $Type->Type,
                    'Address'       => $Type->Address,
                    'Username'      => $Type->Username,
                    'Pass'          => $Type->Pass,
                    'snmpRcomunity' => Crypt::encrypt($Type->snmpRcomunity),
                    'snmpWcomunity' => Crypt::encrypt($Type->snmpWcomunity),
                    'User'          => $username,
                    'UserIP'        => $userIp,
                ];
            }
        }
        else
        {
            if($Type->Type == 'HSGQ')
            {
                $Token = DB::table('parameters')->where('type','hsgq')->first();
                $data = [
                    'Token'         => Crypt::encrypt($Token->password),
                    'Type'          => $Type->Type,
                    'Address'       => $Type->Address,
                    'Username'      => 'no permission',
                    'Pass'          => 'no permission',
                    'snmpRcomunity' => Crypt::encrypt($Type->snmpRcomunity),
                    'snmpWcomunity' => Crypt::encrypt($Type->snmpWcomunity),
                    'User'          => $username,
                    'UserIP'        => $userIp,
                ];
            }
            else 
            {
                $data = [
                    'Type'          => $Type->Type,
                    'Address'       => $Type->Address,
                    'Username'      => 'no permission',
                    'Pass'          => 'no permission',
                    'snmpRcomunity' => Crypt::encrypt($Type->snmpRcomunity),
                    'snmpWcomunity' => Crypt::encrypt($Type->snmpWcomunity),
                    'User'          => $username,
                    'UserIP'        => $userIp,
                ];
            }
        }

        return response()->json($data);
    }

    static public function filterSearch(REQUEST $request)
    {
 
        $params = [
            'request' => 'debug',
            'search' => [],
        ];

        $parameterNames = [
            'user_name',
            'user_lastname',
            'personal_id',
            'company_name',
            'old_contract_num',
            'user_id',
            'disabled',
            'legal',
            'is_fiber',
            'tv',
            'phone',
            'address',
            'misamarti',
            'user_ip',
            'antenna_ip',
            'sector_ip',
            'town',
            'subregion',
            'tariff',
            'status',
            'provider',
            'legal_status',
            'mac',
            'tvmac',
            'activate_date',
            'activate_date_end',
            'power_provider',
            'expired',
            'is_vip',
            'media_converter',
            'do_not_disable',
            'as_temporary',
            'discount',
        ];
 
        $Empty = true;
        foreach ($parameterNames as $paramName)
         {        
                   
            if ($request->has($paramName))
            { 
                if(strlen($request->input($paramName)) > 0  )
                {

                    if($paramName == 'is_fiber') 
                    {
                        if($request->input($paramName) == 2)
                        {
                            $Empty = false;
                            $params['search'][$paramName] = '0';
                        }
                        else
                        {
                            $Empty = false;
                            $params['search'][$paramName] = '1';
                        }
                    }
                    else if($paramName == 'tv') 
                    {
                        if($request->input($paramName) == 2)
                        {
                            $Empty = false;
                            $params['search'][$paramName] = '0';
                        }
                        else
                        {
                            $Empty = false;
                            $params['search'][$paramName] = '1';
                        }
                    }
                    else if($paramName == 'disabled') 
                    {
                        if($request->input($paramName) == 2)
                        {
                            $Empty = false;
                            $params['search'][$paramName] = '0';
                        }
                        else
                        {
                            $Empty = false;
                            $params['search'][$paramName] = '1';
                        }
                    }
                    else
                    {
                        $Empty = false;
                        $params['search'][$paramName] = $request->input($paramName);
                    }
                    
                }
            }
        }

        if ($Empty)
        {
            return response()->json(['error' => 'No Filter Parameters Found']);
        }
     
    
        return  FILTER::SEARCH($params);
    }
     
    static public function GetTarriff(REQUEST $request) 
    {
        return  FILTER::Tarriff();
    }

    static public function GetRegions(REQUEST $request) 
    {
        return  FILTER::Regions();
    }

    static public function SUBRegions(REQUEST $request) 
    {
        $validator = validator()->make($request->only('SubRegion'), [
            'SubRegion'    => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $id     = $request->input('SubRegion');

        return  FILTER::SubRegions($id);
    }
     
    //////////////////////////////////////////////////////////////////// BDCOM
    static public function bdcom_client(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

      
        return  BDCOM::Client_Side_OnuInfo($ip,$read,$write,$user);
    }
    
    static public function bdcom_onuPorts(REQUEST $request) 
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');
        
        return  BDCOM::Client_Side_OnuPorts($ip,$read,$write,$user);
    }

    static public function bdcom_onuMacs(Request $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

        
        return  BDCOM::Client_Side_OnuMacs($ip,$read,$write,$user);
    }
    
    static public function bdcom_Onu_Restart(Request $request)
    {

        $validator = validator()->make($request->only('ip', 'read', 'write','ifindex','user','comment'), [
            'ip'       => 'required|ipv4',
            'read'     => 'required|string',
            'write'    => 'required|string',
            'ifindex'  => 'required|numeric',
            'user'     => 'required|string',
            'comment'  => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

            $ip         = $request->input('ip');
            $read       = Crypt::decrypt($request->input('read'));
            $write      = Crypt::decrypt($request->input('write'));
            $ifindex    = $request->input('ifindex');
            $user       = $request->input('user');
            $comment    = $request->input('comment');

            $username = $request->user()->name;
            $userIp   = $request->ip();
            Log::channel('actions')->warning('[Onu Restart] '.$user .'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);
         
    
        return  BDCOM::OnuRestart($ip,$read,$write,$ifindex,$user);
    }

    static public function bdcom_Onu_PortAdminStatusOFF(Request $request)
    {
      
        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'portIndex' => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');


        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Admin Statu Off] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  BDCOM::OnuAdminPortOff($ip,$read,$write,$ifindex,$portIndex,$user);
    }

    static public function bdcom_Onu_PortAdminStatusON(Request $request)
    {

        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'portIndex' => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
      
        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Admin Statu On] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        return  BDCOM::OnuAdminPortON($ip,$read,$write,$ifindex,$portIndex,$user);
    }

    static public function bdcom_Onu_PortVlanChange(Request $request)
    {
        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portindex','mode','vlan','comment'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'portindex' => 'required|numeric',
            'mode'      => 'required|numeric',
            'vlan'      => 'required|numeric',
            'comment'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portindex');
        $user       = $request->input('user');
        $mode       = $request->input('mode');
        $vlan       = $request->input('vlan');
        $comment    = $request->input('comment');

        $Translated = '';
        if($mode == 0 || $mode == 4 || $mode == 254) $Translated = 'transparent';
        else if($mode == 1 ) $Translated = 'tag';
        else if($mode == 2 ) $Translated = 'translation';
        else if($mode == 3 ) $Translated = 'stacking';
        
        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Vlan Change] '.$user .'\n[Port] '.$portIndex.'\n[Vlan Mode] '.$Translated.'\n[Vlan] '.$vlan.'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);
 

        return  BDCOM::OnuPortVlanChange($ip,$read,$write,$ifindex,$portIndex,$user,$vlan,$mode);
    }

    static public function bdcom_clientside_pon_select(Request $request)
    {
        $validator = validator()->make($request->only('ip' ,'read'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));

        return  BDCOM::ClientSidePonStatus($ip,$read);
    }
     
    static public function bdcom_clientside_pon_data(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
           
        return  BDCOM::ClientSidePonData($ip,$pon,$read);
    }

    static public function bdcom_clientside_pon_PonAllOnline(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
           
        return  BDCOM::ClientSidePonAllOnline($ip,$pon,$read);
    }

    static public function bdcom_clientside_pon_PonAllOffline(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
           
        return  BDCOM::ClientSidePonAllOfflinea($ip,$pon,$read);
    }

    static public function bdcom_clientside_pon_PonAllWireDown(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
           
        return  BDCOM::ClientSidePonAllWireDown($ip,$pon,$read);
    }

    static public function bdcom_clientside_pon_PonAllPowerOff(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
           
        return  BDCOM::ClientSidePonAllPowerOff($ip,$pon,$read);
    }
     
    //////////////////////////////////////////////////////////////////// HUAWEI
    static public function huawei_client_Onuinfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

        return  HUAWEI::Client_Side_OnuInfo($ip,$read,$write,$user);
    }

    static public function huawei_client_OnuPorts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

        return  HUAWEI::Huawei_Client_Side_OnuPorts($ip,$read,$write,$user);
    }

    static public function huawei_client_OnuMacs(Request $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');
   
        return  HUAWEI::Huawei_Client_Side_OnuMacs($ip,$read,$write,$user);
    }
     
    static public function huawei_Onu_Restart(Request $request)
    {

        $validator = validator()->make($request->only('ip', 'read', 'write','ifindex','user','comment'), [
            'ip'       => 'required|ipv4',
            'read'     => 'required|string',
            'write'    => 'required|string',
            'ifindex'  => 'required|numeric',
            'user'     => 'required|string',
            'comment'  => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

            $ip         = $request->input('ip');
            $read       = Crypt::decrypt($request->input('read'));
            $write      = Crypt::decrypt($request->input('write'));
            $ifindex    = $request->input('ifindex');
            $user       = $request->input('user');
            $comment    = $request->input('comment');

            $username = $request->user()->name;
            $userIp   = $request->ip();
            Log::channel('actions')->warning('[Onu Restart] '.$user .'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);
         
    
        return  HUAWEI::OnuRestart($ip,$read,$write,$ifindex,$user);
    }
    
    static public function huawei_Onu_PortAdminStatusOFF(Request $request)
    {
      
        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'portIndex' => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');


        $username = $request->user()->name;
        $userIp   = $request->ip();  
        
        Log::channel('actions')->warning('[Onu Port Admin Status Off] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  HUAWEI::huawei_Onu_PortAdminStatus_OFF($ip,$read,$write,$ifindex,$portIndex,$user);
    }

    static public function huawei_Onu_PortAdminStatusON(Request $request)
    {

        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'portIndex' => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
      
        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Admin Statu On] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        return  HUAWEI::huawei_Onu_PortAdminStatus_ON($ip,$read,$write,$ifindex,$portIndex,$user);
    }

    static public function huawei_clientside_pon_select(Request $request)
    {
        $validator = validator()->make($request->only('ip' ,'read'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));

        return  HUAWEI::ClientSidePonSelect($ip,$read);
    }
     
    static public function huawei_clientside_pon_data(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        
        return  HUAWEI::ClientSidePonData($ip,$pon,$read,$write);
    }

    static public function huawei_clientside_pon_PonAllOnline(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        
        return  HUAWEI::ClientSidePonAllOnline($ip,$pon,$read,$write);
    }

    static public function huawei_clientside_pon_PonAllOffline(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));   

        return  HUAWEI::ClientSidePonAllOffline($ip,$pon,$read,$write);
    }

    static public function huawei_clientside_pon_PonAllWireDown(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        
        return  HUAWEI::ClientSidePonAllWireDown($ip,$pon,$read,$write);
    }

    static public function huawei_clientside_pon_PonAllPowerOff(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));

        return  HUAWEI::ClientSidePonAllPowerOff($ip,$pon,$read,$write);
    }

    static public function huawei_epon_reconfigure(Request $request)
    {
        $validator = validator()->make($request->only('ip','read','ifindex'), [
            'ip'        => 'required|ipv4', 
            'read'      => 'required|string',
            'ifindex'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $ifindex    = $request->input('ifindex');

        $html = [];

        $snmp= new \SNMP(\SNMP::VERSION_2c, $ip, $read);  

        $ServiceName = $snmp->get("1.3.6.1.4.1.2011.6.128.1.1.2.53.1.8.".$ifindex , TRUE);
        $ServiceName =  trim(str_replace("STRING: ","",$ServiceName));
        $ServiceName =  trim(str_replace("\"","",$ServiceName));
     
        if(!empty($ServiceName))
        {
            $html = _huawei::HUAWEI_EPON_SERVICE_PROFILE_READ($ip,$read,$ServiceName);

            if(!empty($html))
            {
                $html = array_unique(array_column($html, 'vlan'));
            }
        }

        return $html;
    }

    static public function huawei_epon_reconfigure_finish(Request $request)
    {
        
        PrivilegesModel::PrivCheck('Priv_Onu');

        $validator = validator()->make($request->only('ip','read','ifindex','user','ponPort','vlan'), [
            'ip'        => 'required|ipv4', 
            'read'      => 'required|string',
            'ifindex'   => 'required|string',
            'user'      => 'required|string',
            'ponPort'   => 'required|string',
            'vlan'      => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $user       = $request->input('user');
        $ponPort    = $request->input('ponPort');
        $vlan       = $request->input('vlan');

        $response = _huawei::HuaweiePON_FTP($user,'ROUTER',$ifindex,$vlan,PortCount: 4); 

        $OnuLastIndex = explode('.',$ifindex);

        $OntIndex = _huawei::GPON_EPON_PORT($ifindex).'  '.$OnuLastIndex[1];     
        $OntIndex = str_replace('EPON ','',$OntIndex);
        $OntIndex = explode('/',$OntIndex);
        $OntIndex = $OntIndex[0].'/'.$OntIndex[1].' '.$OntIndex[2];    
 

        $commandArray = [];    
        $commandArray = 
        [
                          '',
                          'ena',   
                          'diagnose',
                          'ont-load stop',
                          'ont-load info configuration '.$user.'.xml.gz ftp 10.196.3.97 finderftp NQnWKdAUmZN5I65',
                          'ont-load select '.$OntIndex,
                          'ont-load start',
                          '',
                          'display ont-load result '.$OntIndex
        ];
     
        $credentials = DB::table('devices')->where('Address',$ip)->first();
        sshModel::SSH_EPON_CUSTOM($ip,22,$credentials->Username,$credentials->Pass,$commandArray,true); 
                               
        if(Storage::disk('ftp')->exists($user.'.xml.gz'))
        {
            Storage::disk('ftp')->delete($user.'.xml.gz');
        }      

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Reconfigure] '.$user .'\n[User] '.$username.'\n[Address] '.$userIp.'\n[Olt] '.$ip.'\n[Client] '.$user.'\n[ponPort] '.$ponPort.'\n[vlan] '.$vlan);
     

        if (is_array($response) && isset($response['error'])) {
            return response()->json(['error' => $response['error']]);
        }
 
        
        return response()->json(['success' => $response]);
    }
     

    static public function huawei_epon_test(Request $request)
    {
        // https://mibs.observium.org/mib/HUAWEI-XPON-MIB/

       
 

        $snmp    = new \SNMP(\SNMP::VERSION_2c, '10.196.254.253', 'public');  
        //$snmp_RW = new \SNMP(\SNMP::VERSION_2c, '10.196.254.253', 'Tools-RW'); 


        // unregister onus   1.3.6.1.4.1.2011.6.128.1.1.2.58.1.2
        // pon names   1.3.6.1.2.1.31.1.1.1.1
        // onu descriptions   1.3.6.1.4.1.2011.6.128.1.1.2.53.1.9
        // onus status   1.3.6.1.4.1.2011.6.128.1.1.2.57.1.15  // 1 - online , 2 - offline
        // onu dbm    1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5
        // onu version software 1.3.6.1.4.1.2011.6.128.1.1.2.55.1.5
        // hwEponDeviceOntMatchStatus  1.3.6.1.4.1.2011.6.128.1.1.2.57.1.18
        // onu reset  1.3.6.1.4.1.2011.6.128.1.1.2.57.1.2
        // onu rtt  1.3.6.1.4.1.2011.6.128.1.1.2.57.1.20
        // onu vendor id  1.3.6.1.4.1.2011.6.128.1.1.2.55.1.1
        // dereg erason   1.3.6.1.4.1.2011.6.128.1.1.2.57.1.25 //  13 - power , 2 - оптика
        // onu distance   1.3.6.1.4.1.2011.6.128.1.1.2.57.1.19
        // onus macs   1.3.6.1.4.1.2011.6.128.1.1.2.53.1.3
        // pon port status  1.3.6.1.4.1.2011.6.128.1.1.2.31.1.5
        // pon optic model 1.3.6.1.4.1.2011.6.128.1.1.2.32.1.1
        // pon sfp vendor name  1.3.6.1.4.1.2011.6.128.1.1.2.32.1.11
        // pon sfp module connector  1.3.6.1.4.1.2011.6.128.1.1.2.32.1.3
        // onu line profile names 1.3.6.1.4.1.2011.6.128.1.1.2.53.1.7
        // onu service profile names 1.3.6.1.4.1.2011.6.128.1.1.2.53.1.8
        // onu model  1.3.6.1.4.1.2011.6.128.1.1.2.55.1.13  .. 14 ..15
        // savarudo uptime   1.3.6.1.4.1.2011.6.128.1.1.2.61.1.4
        // onu port status  1.3.6.1.4.1.2011.6.128.1.1.2.81.1.31.4227866624.
        // onu port admin 1.3.6.1.4.1.2011.6.128.1.1.2.81.1.7
        // savraudod line profilshi chandsma   1.3.6.1.4.1.2011.6.128.1.1.3.41.1.2    }} 1.3.6.1.4.1.2011.6.128.1.1.3.41.1.5  createAndGo(4) -create a new instance of a conceptual row 
        // 4227866624  0/1/0

        $MacOnu =  $snmp->walk("1.3.6.1.4.1.2011.6.128.1.1.2.104.1.5", TRUE);
        //return OLTSIDE_HUAWEI::GPON_EPON_PORT(4227866624);
 
        return   $MacOnu;
    }
 
    //////////////////////////////////////////////////////////////////// ZTE
    static public function zte_client_Onuinfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

      
        return  ZTE::Client_Side_OnuInfo($ip,$read,$write,$user);
    }

    static public function zte_client_onuPorts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

      
        return  ZTE::Client_Side_OnuPorts($ip,$read,$write,$user);
    }
     
    static public function zte_client_OnuRestart(Request $request)
    {

        $validator = validator()->make($request->only('ip', 'read', 'write','ifindex','user','comment'), [
            'ip'       => 'required|ipv4',
            'read'     => 'required|string',
            'write'    => 'required|string',
            'ifindex'  => 'required|numeric',
            'user'     => 'required|string',
            'comment'  => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

            $ip         = $request->input('ip');
            $read       = Crypt::decrypt($request->input('read'));
            $write      = Crypt::decrypt($request->input('write'));
            $ifindex    = $request->input('ifindex');
            $user       = $request->input('user');
            $comment    = $request->input('comment');


            $username = $request->user()->name;
            $userIp   = $request->ip();
            Log::channel('actions')->warning('[Onu Restart] '.$user .'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);
         
    
        return  ZTE::OnuRestart($ip,$read,$write,$ifindex,$user);
    }
     
    static public function zte_Onu_PortAdminStatusOFF(Request $request)
    {
   
        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'portIndex' => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');


        $username = $request->user()->name;
        $userIp   = $request->ip();  
        
        Log::channel('actions')->warning('[Onu Port Admin Statu Off] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  ZTE::zte_Onu_PortAdminStatus_OFF($ip,$read,$write,$ifindex,$portIndex,$user);
    }

    static public function zte_Onu_PortAdminStatusON(Request $request)
    {

        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'portIndex' => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
      
        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Admin Statu On] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        return  ZTE::zte_Onu_PortAdminStatus_ON($ip,$read,$write,$ifindex,$portIndex,$user);
    }    
     
    static public function zte_client_OnuMacs(Request $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

        
        return  ZTE::Client_Side_OnuMacs($ip,$read,$write,$user);
    }
     
    static public function zte_clientside_pon_select(Request $request)
    {
        $validator = validator()->make($request->only('ip' ,'read'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));

        return  ZTE::ClientSidePonSelect($ip,$read);
    }

    static public function zte_clientside_pon_data(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        
        return  ZTE::ClientSidePonData($ip,$pon,$read,$write);
    }

    static public function zte_clientside_pon_PonAllOnline(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        
        return  ZTE::ClientSidePonAllOnline($ip,$pon,$read,$write);
    }

    static public function zte_clientside_pon_PonAllOffline(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));   

        return  ZTE::ClientSidePonAllOffline($ip,$pon,$read,$write);
    }

    static public function zte_clientside_pon_PonAllWireDown(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        
        return  ZTE::ClientSidePonAllWireDown($ip,$pon,$read,$write);
    }

    static public function zte_clientside_pon_PonAllPowerOff(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read','write'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));

        return  ZTE::ClientSidePonAllPowerOff($ip,$pon,$read,$write);
    }
     
    //////////////////////////////////////////////////////////////////// VSOLUTION
    static public function vsolution_client_Onuinfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

      
        return  VSOLUTION::Client_Side_OnuInfo($ip,$read,$write,$user);
    }

    static public function vsolution_client_onuPorts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

      
        return  VSOLUTION::Client_Side_OnuPorts($ip,$read,$write,$user);
    }

    static public function vsolution_client_OnuMacs(Request $request)
    {
        $validator = validator()->make($request->only('ip', 'read', 'write','user'), [
            'ip'    => 'required|ipv4',
            'read'  => 'required|string',
            'write' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $read   = Crypt::decrypt($request->input('read'));
        $write  = Crypt::decrypt($request->input('write'));
        $user   = $request->input('user');

        
        return  VSOLUTION::Client_Side_OnuMacs($ip,$read,$write,$user);
    }
     
    static public function vsolution_client_OnuRestart(Request $request)
    {

        $validator = validator()->make($request->only('ip', 'read', 'write','ifindex','user','comment'), [
            'ip'       => 'required|ipv4',
            'read'     => 'required|string',
            'write'    => 'required|string',
            'ifindex'  => 'required|numeric',
            'user'     => 'required|string',
            'comment'  => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

            $ip         = $request->input('ip');
            $read       = Crypt::decrypt($request->input('read'));
            $write      = Crypt::decrypt($request->input('write'));
            $ifindex    = $request->input('ifindex');
            $user       = $request->input('user');
            $comment    = $request->input('comment');

            $username = $request->user()->name;
            $userIp   = $request->ip();
            Log::channel('actions')->warning('[Onu Restart] '.$user .'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);
         
    
        return  VSOLUTION::OnuRestart($ip,$read,$write,$ifindex,$user);
    }
     
    static public function vsolution_Onu_PortAdminStatusOFF(Request $request)
    {
      
        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');


        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Admin Statu Off] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  VSOLUTION::OnuAdminPortOff($ip,$read,$write,$ifindex,$portIndex,$user);
    }

    static public function vsolution_Onu_PortAdminStatusON(Request $request)
    {

        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
      
        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Admin Statu On] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        return  VSOLUTION::OnuAdminPortON($ip,$read,$write,$ifindex,$portIndex,$user);
    }

    static public function vsolution_Onu_PortVlanChange(Request $request)
    {
        $validator = validator()->make($request->only('user', 'ip' ,'read', 'write','ifindex','portindex','mode','vlan','comment'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'ifindex'   => 'required|string',
            'portindex' => 'required|string',
            'mode'      => 'required|numeric',
            'vlan'      => 'required|numeric',
            'comment'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portindex');
        $user       = $request->input('user');
        $mode       = $request->input('mode');
        $vlan       = $request->input('vlan');
        $comment    = $request->input('comment');

        $Translated = '';
        if($mode == 0 || $mode == 4 || $mode == 254) $Translated = 'transparent';
        else if($mode == 1 ) $Translated = 'tag';
        else if($mode == 2 ) $Translated = 'translation';
        else if($mode == 3 ) $Translated = 'stacking';
        
        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Vlan Change] '.$user .'\n[Port] '.$portIndex.'\n[Vlan Mode] '.$Translated.'\n[Vlan] '.$vlan.'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);
 

        return  VSOLUTION::OnuPortVlanChange($ip,$read,$write,$ifindex,$portIndex,$user,$vlan,$mode);
    }

    static public function vsolution_clientside_pon_select(Request $request)
    {
        $validator = validator()->make($request->only('ip' ,'read'), [
            'ip'        => 'required|ipv4',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));

        return  VSOLUTION::ClientSidePonSelect($ip,$read);
    }

    static public function vsolution_clientside_pon_data(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
     
        return  VSOLUTION::ClientSidePonData($ip,$pon,$read);
    } 

    static public function vsolution_clientside_pon_PonAllOnline(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        
        return  VSOLUTION::ClientSidePonAllOnline($ip,$pon,$read);
    }

    static public function vsolution_clientside_pon_PonAllOffline(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
 

        return  VSOLUTION::ClientSidePonAllOffline($ip,$pon,$read);
    }

    static public function vsolution_clientside_pon_PonAllWireDown(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));
        
        return  VSOLUTION::ClientSidePonAllWireDown($ip,$pon,$read);
    }

    static public function vsolution_clientside_pon_PonAllPowerOff(Request $request)
    {
        $validator = validator()->make($request->only('ip' , 'pon' , 'read'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'read'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $read       = Crypt::decrypt($request->input('read'));

        return  VSOLUTION::ClientSidePonAllPowerOff($ip,$pon,$read);
    }
    //////////////////////////////////////////////////////////////////// HSGQ
    static public function hsgq_client_Onuinfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token','user'), [
            'ip'    => 'required|ipv4',
            'token' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $token  = Crypt::decrypt($request->input('token'));
        $user   = $request->input('user');

      
        return  HSGQ::Client_Side_OnuInfo($ip,$token,$user);
    }

    static public function hsgq_client_OnuPorts(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip', 'token', 'user'), [
            'ip'    => 'required|ipv4',
            'token' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $token  = Crypt::decrypt($request->input('token'));
        $user   = $request->input('user');
     
        return  HSGQ::Client_Side_OnuPorts($ip,$token,$user);
    }

    static public function hsgq_client_OnuMacs (Request $request)
    {
        $validator = validator()->make($request->only('ip', 'token','user'), [
            'ip'    => 'required|ipv4',
            'token' => 'required|string',
            'user'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip     = $request->input('ip');
        $token  = Crypt::decrypt($request->input('token'));
        $user   = $request->input('user');

        
        return  HSGQ::Client_Side_OnuMacs($ip,$token,$user);
    }
     
    static public function hsgq_client_OnuRestart(Request $request)
    {

        $validator = validator()->make($request->only('ip', 'token','ifindex','user','comment'), [
            'ip'       => 'required|ipv4',
            'token'    => 'required|string',
            'ifindex'  => 'required|numeric',
            'user'     => 'required|string',
            'comment'  => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

            $ip         = $request->input('ip');
            $token      =  Crypt::decrypt($request->input('token'));
            $ifindex    = $request->input('ifindex');
            $user       = $request->input('user');
            $comment    = $request->input('comment');

            $username = $request->user()->name;
            $userIp   = $request->ip();
            Log::channel('actions')->warning('[Onu Restart] '.$user .'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);
         
    
        return  HSGQ::OnuRestart($ip,$token,$ifindex,$user);
    }
          
    static public function hsgq_Onu_PortAdminStatusOFF(Request $request)
    {
      
        $validator = validator()->make($request->only('user', 'ip' ,'token','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'token'     => 'required|string',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $token      =  Crypt::decrypt($request->input('token'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');


        $username = $request->user()->name;
        $userIp   = $request->ip();  
        
        Log::channel('actions')->warning('[Onu Port Admin Statu Off] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  HSGQ::Onu_PortAdminStatus_OFF($ip,$token,$ifindex,$portIndex,$user);
    }

    static public function hsgq_Onu_PortAdminStatusON(Request $request)
    {

        $validator = validator()->make($request->only('user', 'ip' ,'token','ifindex','portIndex'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'token'     => 'required|string',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
      
        $ip         = $request->input('ip');
        $token      =  Crypt::decrypt($request->input('token'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');
        $user       = $request->input('user');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Admin Statu On] '.$user .'\n[Port] '.$portIndex.'\n[User] '.$username.'\n[Address] '.$userIp);

        return  HSGQ::Onu_PortAdminStatus_ON($ip,$token,$ifindex,$portIndex,$user);
    }    

    static public function hsgq_Onu_PortVlanChange(Request $request)
    {
        $validator = validator()->make($request->only('user', 'ip' ,'token','ifindex','portindex','mode','vlan','comment'), [
            'user'      => 'required|numeric',
            'ip'        => 'required|ipv4',
            'token'     => 'required|string',
            'ifindex'   => 'required|string',
            'portindex' => 'required|string',
            'mode'      => 'required|numeric',
            'vlan'      => 'required|numeric',
            'comment'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $token      =  Crypt::decrypt($request->input('token'));
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portindex');
        $user       = $request->input('user');
        $mode       = $request->input('mode');
        $vlan       = $request->input('vlan');
        $comment    = $request->input('comment');

        $Translated = '';
        if($mode == 0 ) $Translated = 'transparent';
        else if($mode == 1 ) $Translated = 'tag';

        
        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Onu Port Vlan Change] '.$user .'\n[Port] '.$portIndex.'\n[Vlan Mode] '.$Translated.'\n[Vlan] '.$vlan.'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);
 

        return  HSGQ::OnuPortVlanChange($ip,$token,$ifindex,$portIndex,$user,$vlan,$mode);
    }

    static public function hsgq_clientside_pon_select(Request $request)
    {
        $validator = validator()->make($request->only('ip' ,'read'), [
            'ip'       => 'required|ipv4',
            'read'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $read       = Crypt::decrypt($request->input('read'));

        return  HSGQ::ClientSidePonSelect($ip,$read);
    }

    static public function hsgq_clientside_pon_data(Request $request)
    { 
        $validator = validator()->make($request->only('ip' , 'pon' , 'token'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'token'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $token      =  Crypt::decrypt($request->input('token'));
     
        return  HSGQ::ClientSidePonData($ip,$pon,$token);
    } 

    static public function hsgq_clientside_pon_PonAllOnline(Request $request)
    { 
        $validator = validator()->make($request->only('ip' , 'pon' , 'token'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'token'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $token      =  Crypt::decrypt($request->input('token'));
     
        return  HSGQ::ClientSidePonAllOnline($ip,$pon,$token);
    } 

    static public function hsgq_clientside_pon_PonAllOffline(Request $request)
    { 
        $validator = validator()->make($request->only('ip' , 'pon' , 'token'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'token'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $token      =  Crypt::decrypt($request->input('token'));
     
        return  HSGQ::ClientSidePonAllOffline($ip,$pon,$token);
    } 

    static public function hsgq_clientside_pon_PonAllWireDown(Request $request)
    { 
        $validator = validator()->make($request->only('ip' , 'pon' , 'token'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'token'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $token      =  Crypt::decrypt($request->input('token'));
     
        return  HSGQ::ClientSidePonAllWireDown($ip,$pon,$token);
    } 

    static public function hsgq_clientside_pon_PonAllPowerOff(Request $request)
    { 
        $validator = validator()->make($request->only('ip' , 'pon' , 'token'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',
            'token'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $pon        = $request->input('pon');
        $token      =  Crypt::decrypt($request->input('token'));
     
        return  HSGQ::ClientSidePonAllPowerOff($ip,$pon,$token);
    } 
    //////////////////////////////////////////////////////////////////// SECTOR
    static public function sectorSearch(Request $request)  
    {
        $creds = DB::table('parameters')->where('type','antenna')->first();
        $username = trim($creds->username);
        $password = trim($creds->password);
 
        
        $validator = validator()->make($request->only('ip'  , 'userIp' , 'user'), [
            'ip'       => 'required|ipv4',
            'userIp'   => 'required|ipv4',
            'user'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $user       = $request->input('user');
        $ip         = $request->input('ip');
        $userIp     = $request->input('userIp');
      
        return SECTOR::Client_Side_Info($ip,$userIp,$user,$username,$password);
    }

    static public function UISPSearch(Request $request)  
    {
        $creds = DB::table('parameters')->where('type','uisp')->first();
        $password = trim($creds->password);
        $url    = trim($creds->url);
        
        $validator = validator()->make($request->only('ip'  , 'mac' , 'user'), [
            'ip'       => 'required|ipv4',
            'mac'      => 'required|mac_address',
            'user'     => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $user       = $request->input('user');
        $ip         = $request->input('ip');
        $mac        = $request->input('mac');
      
        return UISP::Client_Side_Info($url,$ip,$mac,$user,$password);
    }

    static public function AntennaKick(Request $request)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $validator = validator()->make($request->only('ip','user', 'mac','comment'), [
            'ip'      => 'required|ipv4',
            'user'    => 'required|string',
            'mac'     => 'required|mac_address',
            'comment' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $client     = $request->input('user');
        $mac        = $request->input('mac');
        $comment    = $request->input('comment');

        $username = $request->user()->name;
        $userIp   = $request->ip();

        $commandArray = [
            "iwpriv ath0 kickmac ".$mac,   
        ];

        $creds = DB::table('parameters')->where('type','antenna_ssh')->first();  

        $res = sshModel::SSH_SECTOR($ip,22,$creds->username,$creds->password,$commandArray,true);   //dd($res); 

        if (is_array($res))
        {   
            if(!empty($res))
            {
                if(strpos($res, 'SSH Login failed') !== false)
                { 
                    sshModel::SSH_SECTOR($ip,22,$creds->username,$creds->password.' ',$commandArray,true);  
                }
            }
            else return true;
        }
        else
        {  
            sshModel::SSH_SECTOR($ip,22,$creds->username,$creds->password.' ',$commandArray,true);
        }
 
        
        $username = $request->user()->name;
        $userIp   = $request->ip();

        Log::channel('actions')->warning('[Antenna Kick] '.$client .'\n[Antenna Address] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);

        return true;
    }

    static public function AntennaReboot(Request $request)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $validator = validator()->make($request->only('ip','user','comment'), [
            'ip'      => 'required|ipv4',
            'user'    => 'required|string',
            'comment' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $client     = $request->input('user');
        $comment    = $request->input('comment');

        $username = $request->user()->name;
        $userIp   = $request->ip();

        $commandArray = [
            "reboot",   
        ];
 
        $creds = DB::table('parameters')->where('type','antenna')->first();

        try{

            $res = sshModel::SSH_SECTOR($ip,22,$creds->username,$creds->password,$commandArray,true);
            if (is_array($res))
            {   
                if(!empty($res))
                {
                    if(strpos($res, 'SSH Login failed') !== false)
                    { 
                        sshModel::SSH_SECTOR($ip,22,$creds->username,$creds->password.' ',$commandArray,true);  
                    }
                }
                else return true;
            }
            else
            {  
                sshModel::SSH_SECTOR($ip,22,$creds->username,$creds->password.' ',$commandArray,true);
            }

        }catch (\Exception $e){}
         

        Log::channel('actions')->warning('[Antenna Reboot] '.$client .'\n[Antenna Address] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp.'\n[comment] '.$comment);

        return true;
    }
     
    //////////////////////////////////////////////////////////////////// ZYXEL

     static public function ZyxelSearch(Request $request)  
     {
        $validator = validator()->make($request->only('ip', 'username','password','read','write','user'), [
            'ip'        => 'required|ipv4',
            'username'  => 'required|string',
            'password'  => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'user'      => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $username   = $request->input('username');
        $password   = $request->input('password');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $user       = $request->input('user');

      
        return  ZYXEL::Client_Side_Info($ip,$username,$password,$read,$write,$user);
     }

     static public function Zyxel_Onu_PortAdminStatusOFF(Request $request)
     {
       
         $validator = validator()->make($request->only('user', 'ip' ,'port', 'write'), [
             'user'      => 'required|string',
             'ip'        => 'required|ipv4',
             'port'      => 'required|string',
             'write'     => 'required|string',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
 
         
         $ip         = $request->input('ip');
         $port       = $request->input('port');
         $write      = Crypt::decrypt($request->input('write'));
         $user       = $request->input('user');
 
 
         $username = $request->user()->name;
         $userIp   = $request->ip();
         Log::channel('actions')->warning('[Zyxel Port Admin Statu Off] '.$user .'\n[Port] '.$port.'\n[User] '.$username.'\n[Address] '.$userIp);
 
         
         return  ZYXEL::AdminPortOff($ip,$write,$port);
     }
 
     static public function Zyxel_Onu_PortAdminStatusON(Request $request)
     {
 
         $validator = validator()->make($request->only('user', 'ip' ,'port', 'write',), [
            'user'      => 'required|string',
            'ip'        => 'required|ipv4',
            'port'      => 'required|string',
            'write'     => 'required|string',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
       
         $ip         = $request->input('ip');
         $port       = $request->input('port');
         $write      = Crypt::decrypt($request->input('write'));
         $user       = $request->input('user');
 
         $username = $request->user()->name;
         $userIp   = $request->ip();
         Log::channel('actions')->warning('[Zyxel Port Admin Statu On] '.$user .'\n[Port] '.$port.'\n[User] '.$username.'\n[Address] '.$userIp);
 
         return  ZYXEL::AdminPortON($ip,$write,$port);
     }
      
     static public function Zyxel_clientside_SwitchData(Request $request)
     {
         $validator = validator()->make($request->only('ip' , 'username' , 'password', 'read'), [
             'ip'        => 'required|ipv4',
             'username'  => 'required|string',
             'password'  => 'required|string',
             'read'      => 'required|string',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
 
         $ip         = $request->input('ip');
         $username   = $request->input('username');
         $password   = $request->input('password');
         $read       = Crypt::decrypt($request->input('read'));
      
         return  ZYXEL::ClientSideSwitchData($ip,$read,$username,$password);
     } 

     //////////////////////////////////////////////////////////////////// CISCO

     static public function ciscoSearch(Request $request)  
     {
        $validator = validator()->make($request->only('ip', 'username','password','read','write','user'), [
            'ip'        => 'required|ipv4',
            'username'  => 'required|string',
            'password'  => 'required|string',
            'read'      => 'required|string',
            'write'     => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $username   = $request->input('username');
        $password   = $request->input('password');
        $read       = Crypt::decrypt($request->input('read'));
        $write      = Crypt::decrypt($request->input('write'));
        $user       = $request->input('user');

  
        return  CISCO::Client_Side_Info($ip,$username,$password,$read,$write,$user);
     }
   
     static public function cisco_Onu_PortAdminStatusOFF(Request $request)
     {
       
         $validator = validator()->make($request->only('user', 'ip' ,'port', 'portname','write'), [
             'user'      => 'required|string',
             'ip'        => 'required|ipv4',
             'port'      => 'required|string',
             'portname'  => 'required|string',
             'write'     => 'required|string',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
 
         
         $ip         = $request->input('ip');
         $port       = $request->input('port');
         $portname   = $request->input('portname');
         $write      = Crypt::decrypt($request->input('write'));
         $user       = $request->input('user');
 
 
         $username = $request->user()->name;
         $userIp   = $request->ip();
         Log::channel('actions')->warning('[Cisco Port Admin Status Off] '.$user .'\n[Port] '.$portname.'\n[User] '.$username.'\n[Address] '.$userIp);
 
         
         return  CISCO::AdminPortOff($ip,$write,$port);
     }
 
     static public function cisco_Onu_PortAdminStatusON(Request $request)
     {
 
         $validator = validator()->make($request->only('user', 'ip' ,'port', 'portname', 'write',), [
            'user'      => 'required|string',
            'ip'        => 'required|ipv4',
            'portname'  => 'required|string',
            'port'      => 'required|string',
            'write'     => 'required|string',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
       
         $ip         = $request->input('ip');
         $port       = $request->input('port');
         $portname   = $request->input('portname');
         $write      = Crypt::decrypt($request->input('write'));
         $user       = $request->input('user');
 
         $username = $request->user()->name;
         $userIp   = $request->ip();
         Log::channel('actions')->warning('[Cisco Port Admin Statu On] '.$user .'\n[Port] '.$portname.'\n[User] '.$username.'\n[Address] '.$userIp);
 
         return  CISCO::AdminPortON($ip,$write,$port);
     }

     static public function cisco_clientside_SwitchData(Request $request)
     {
         $validator = validator()->make($request->only('ip' , 'username' , 'password', 'read'), [
             'ip'        => 'required|ipv4',
             'username'  => 'required|string',
             'password'  => 'required|string',
             'read'      => 'required|string',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
 
         $ip         = $request->input('ip');
         $username   = $request->input('username');
         $password   = $request->input('password');
         $read       = Crypt::decrypt($request->input('read'));
      
         return  CISCO::ClientSideSwitchData($ip,$read,$username,$password);
     } 
      
    //////////////////////////////////////////////////////////////////// MIKROTIK ROUTER

     static public function MikrotikRouter(Request $request)
     {
        $validator = validator()->make($request->only('ip', 'username','password','user'), [
            'ip'        => 'required|ipv4',
            'username'  => 'required|string',
            'password'  => 'required|string',
            'user'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $username   = $request->input('username');
        $password   = $request->input('password');
        $user       = $request->input('user');

         
        return  mikrotikRouter::Client_Side_Info($ip,$username,$password,$user);
     }

     static public function MikrotikRouterPortOff(Request $request)  
     {
        $validator = validator()->make($request->only('ip', 'userName','Pass','user','InterfaceID'), [
            'ip'            => 'required|ipv4',
            'userName'      => 'required|string',
            'Pass'          => 'required|string',
            'user'          => 'required|string',
            'InterfaceID'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip          = $request->input('ip');
        $username    = $request->input('userName');
        $password    = $request->input('Pass');
        $user        = $request->input('user');
        $InterfaceID = $request->input('InterfaceID');

        $Nocusername = $request->user()->name;
        $userIp   = $request->ip();

        Log::channel('actions')->warning('[Mikrotik Interface Port Admin Status Off] '.$ip .'\n[Client] '.$user.'\n[User] '.$Nocusername.'\n[Address] '.$userIp);
         
        return  mikrotikRouter::RouterPortOff($ip,$username,$password,$user,$InterfaceID);
     }

     static public function MikrotikRouterPortOn(Request $request)  
     {
        $validator = validator()->make($request->only('ip', 'userName','Pass','user','InterfaceID'), [
            'ip'            => 'required|ipv4',
            'userName'      => 'required|string',
            'Pass'          => 'required|string',
            'user'          => 'required|string',
            'InterfaceID'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip          = $request->input('ip');
        $username    = $request->input('userName');
        $password    = $request->input('Pass');
        $user        = $request->input('user');
        $InterfaceID = $request->input('InterfaceID');

        $Nocusername = $request->user()->name;
        $userIp   = $request->ip();

        Log::channel('actions')->warning('[Mikrotik Interface Port Admin Status On] '.$ip .'\n[Client] '.$user.'\n[User] '.$Nocusername.'\n[Address] '.$userIp);
         
        return  mikrotikRouter::RouterPortOn($ip,$username,$password,$user,$InterfaceID);
     }

      
      
}
