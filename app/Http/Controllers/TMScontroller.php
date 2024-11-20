<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\TMS;
use Illuminate\Support\Facades\Log;

 

class TMScontroller extends Controller
{
    static public function tvipcheck(Request $request)
    { 
        $id = $request->input('client');
    
        $request->validate([
            'client' => 'required|integer',
        ]);

        
        return response()->json(TMS::FindClient($id));
    }

    static public function accountSearch(Request $request)
    { 
        $id = $request->input('client');
    
        $request->validate([
            'client' => 'required|integer',
        ]);

        
        return response()->json(TMS::FindAccount($id));
    }

    static public function SearchByMac(Request $request)
    { 
        $validator = validator()->make($request->only('mac'), [
            'mac'  => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $mac = $request->input('mac');
        
        return response()->json(TMS::FindByMac($mac));
    }

    static public function accountCreate(Request $request)
    { 
        $validator = validator()->make($request->only('account','tarrif'), [
            'account'  => 'required|numeric',
            'tarrif'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $account = $request->input('account');
        $tarrif  = $request->input('tarrif');

        $username  = $request->user()->name;
        $userIp    = $request->ip();

        Log::channel('install')->info('[TMS] '.$account .'\n[Account Create]'.$account.'\n[ტარიფი] '.$tarrif.'\n[ნოკი] '.$username.'\n[ნოკის აიპი] '.$userIp);

        return response()->json(TMS::CreateAccount($account,$tarrif));
    }
     
    static public function accountDelete(Request $request)
    { 
        $validator = validator()->make($request->only('account','tarrif'), [
            'account'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $account = $request->input('account');

        $username  = $request->user()->name;
        $userIp    = $request->ip();

        Log::channel('install')->info('[TMS] '.$account .'\n[Account Delete]'.$account.'\n[ნოკი] '.$username.'\n[ნოკის აიპი] '.$userIp);

        return response()->json(TMS::DeleteAccount($account));
    }

    static public function devicetDelete(Request $request)
    { 
        $validator = validator()->make($request->only('account','device','mac'), [
            'account'  => 'required|numeric',
            'device'   => 'required|numeric',
            'mac'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $account = $request->input('account');
        $device  = $request->input('device');
        $mac     = $request->input('mac');

        $username  = $request->user()->name;
        $userIp    = $request->ip();

 
        Log::channel('install')->info('[TMS] '.$account .'\n[Device Delete] '.$account.'\n[TVBOX - ის მაკი] '.$mac.'\n[ნოკი] '.$username.'\n[ნოკის აიპი] '.$userIp);

        return response()->json(TMS::DeleteDevice($account,$device));
    }
     
    static public function devicetBind(Request $request) 
    { 
        $validator = validator()->make($request->only('account','id','mac','address','unique'), [
            'account'  => 'required|numeric',
            'id'       => 'required|numeric',
            'mac'      => 'required|string',
            'address'  => 'required|ipv4',
            'unique'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $account = $request->input('account');
        $id      = $request->input('id');
        $mac     = $request->input('mac');
        $address = $request->input('address');
        $unique  = $request->input('unique');

        $username  = $request->user()->name;
        $userIp    = $request->ip();


        Log::channel('install')->info('[TMS] '.$account .'\n[Device Bind] '.$account.'\n[TVBOX - ის მაკი] '.$mac.'\n[ნოკი] '.$username.'\n[ნოკის აიპი] '.$userIp);

        return response()->json(TMS::BindDevice($account,$id,$mac,$address,$unique));
    }
     
    static public function unactivatedDevices(Request $request)
    { 
        $validator = validator()->make($request->only('account','tarrif'), [
            'account'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $account = $request->input('account');
 
        return response()->json(TMS::AllunactivatedDevices($account));
    }
     
    static public function tviptarriflist()
    { 
        return response()->json(TMS::TarrifList());
    }

    static public function tviptarrifchange(Request $request)
    { 
 
        $validator = validator()->make($request->only('account', 'tarrif', 'ActiveTarrif'), [
            'account'   => 'required|numeric',
            'tarrif'    => 'required|numeric',
            'ActiveTarrif'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $account   = $request->input('account');
        $tarrif    = $request->input('tarrif');
        $tarrifID  = $request->input('ActiveTarrif');

        $username  = $request->user()->name;
        $userIp    = $request->ip();

        Log::channel('actions')->warning('[TMS Change Tarrif] '.$account .'\n[Tarrif id] '.$tarrif.'\n[User] '.$username.'\n[Address] '.$userIp);

        return response()->json(TMS::TarrifChange($account,$tarrif,$tarrifID));
    }

    static public function tvipupdate(Request $request)
    { 
 
        $validator = validator()->make($request->only('account', 'deviceID'), [
            'account'   => 'required|numeric',
            'deviceID'  => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $tvboxID   = $request->input('deviceID');

        return response()->json(TMS::TvBoxUpdate($tvboxID));
    }

    static public function tarriffromaccount(Request $request)
    { 
 
        $validator = validator()->make($request->only('account'), [
            'account'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $account   = $request->input('account');

        return response()->json(TMS::getTarrifListFromAccount($account));
    }
     
    static public function tarrifdelete(Request $request)
    { 
 
        $validator = validator()->make($request->only('tarrifID','account'), [
            'tarrifID'  => 'required|numeric',
            'account'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $tarrifID  = $request->input('tarrifID');
        $account   = $request->input('account');

        $username  = $request->user()->name;
        $userIp    = $request->ip();

        Log::channel('actions')->error('[TMS Delete Tarrif] '.$account .'\n[Tarrif id] '.$tarrifID.'\n[User] '.$username.'\n[Address] '.$userIp);

        return response()->json(TMS::DeleteTarrif($tarrifID));
    }
     
    static public function tarrifcreate(Request $request)
    { 
 
        $validator = validator()->make($request->only('account','tarrifID'), [
            'account'    => 'required|numeric',
            'tarrifID'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $account   = $request->input('account');
        $tarrifID  = $request->input('tarrifID');

        $username  = $request->user()->name;
        $userIp    = $request->ip();

        Log::channel('actions')->warning('[TMS Create Tarrif] '.$account .'\n[Tarrif id] '.$tarrifID.'\n[User] '.$username.'\n[Address] '.$userIp);

        return response()->json(TMS::CreateTarrif($account,$tarrifID));
    }

    static public function tviprestart(Request $request)
    { 
 
        $validator = validator()->make($request->only('account','deviceID'), [
            'account'    => 'required|numeric',
            'deviceID'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $account   = $request->input('account');
        $deviceID  = $request->input('deviceID');

        $username  = $request->user()->name;
        $userIp    = $request->ip();

        Log::channel('actions')->warning('[TMS Device Restart] '.$account .'\n[Device id] '.$deviceID.'\n[User] '.$username.'\n[Address] '.$userIp);

        return response()->json(TMS::restart($deviceID));
    }

    static public function channelList(Request $request)
    { 
        $validator = validator()->make($request->only('tarrif'), [
            'tarrif'    => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $tarrif   = $request->input('tarrif');
 

        return response()->json(TMS::Channel_List($tarrif));
    }
    static public function channelChange(Request $request)
    { 
        $validator = validator()->make($request->only('channel','deviceID'), [
            'channel'    => 'required|numeric',
            'deviceID'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $channel   = $request->input('channel');
        $deviceID  = $request->input('deviceID');

        return response()->json(TMS::Channel_Change($channel,$deviceID));

    }
     
}
