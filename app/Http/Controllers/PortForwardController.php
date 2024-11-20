<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

use App\Models\PortForward;

class PortForwardController extends Controller
{
    public function search(Request $request)
    {
        $validator = validator()->make($request->only('id'), [
            'id' => 'required|integer',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $id = $request->input('id');

        return response()->json(PortForward::search($id));
    }

    public function custom_port_search(Request $request)
    {
        $validator = validator()->make($request->only('port'), [
            'port' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $port = $request->input('port');

        return response()->json(PortForward::CustomPortSearch($port));
    }

    public function port_add(Request $request)
    {
        $validator = validator()->make($request->only('privatPort','publicPort','privatIP','publicIP','protocol','client','comment'), [
            'privatPort' => 'required|numeric',
            'publicPort' => 'required|numeric',
            'privatIP'   => 'required|ipv4',
            'publicIP'   => 'required|ipv4',
            'protocol'   => 'required|string',
            'client'     => 'required|string',
            'comment'    => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $privatPort = $request->input('privatPort');
        $publicPort = $request->input('publicPort');
        $privatIP   = $request->input('privatIP');
        $publicIP   = $request->input('publicIP');
        $protocol   = $request->input('protocol');
        $client     = $request->input('client');
        $comment    = $request->input('comment');
 
        $username = $request->user()->name;
        $userIp   = $request->ip();
 
         
       Log::channel('actions')->warning('[PORT OPEN] '.$client 
       .'\n[ნოკი] '.$username
       .'\n[ნოკის აიპი] '.$userIp
       .'\n[აბონენტი] '.$client
       .'\n[გარე აიპი] '.$publicIP
       .'\n[შიდა აიპი] '.$privatIP
       .'\n[გარე პორტი] '.$publicPort
       .'\n[შიდა პორტი] '.$privatPort
       .'\n[პროტოკოლი] '.$protocol
       .'\n[კომენტარი] '.$comment
       );
 

        return response()->json(PortForward::OpenPort($publicPort,$privatPort,$client,$privatIP,$publicIP,$protocol));
    }
     
    public function port_delete(Request $request)
    {
        $validator = validator()->make($request->only('id','publicPort','publicIP','protocol','client','comment'), [
            'publicIP'   => 'required|ipv4',
            'publicPort' => 'required|numeric',
            'client'     => 'required|string',
            'protocol'   => 'required|string',
            'id'         => 'required|string',
            'comment'    => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

 
        $publicPort = $request->input('publicPort');
        $publicIP   = $request->input('publicIP');
        $protocol   = $request->input('protocol');
        $client     = $request->input('client');
        $id         = $request->input('id');
        $comment    = $request->input('comment');
 
        $username = $request->user()->name;
        $userIp   = $request->ip();
 
         
       Log::channel('actions')->warning('[PORT DELETE] '.$client 
       .'\n[ნოკი] '.$username
       .'\n[ნოკის აიპი] '.$userIp
       .'\n[აბონენტი] '.$client
       .'\n[გარე აიპი] '.$publicIP
       .'\n[გარე პორტი] '.$publicPort
       .'\n[პროტოკოლი] '.$protocol
       .'\n[კომენტარი] '.$comment
       );
 
        return response()->json(PortForward::DeletePort($id));
    }
 
    public static function port_forward_edit(Request $request)
    {

        $validator = validator()->make($request->only('client','privatPort','publicPort','privatIP','publicIP','protocol','id','comment'), [
            'client'         => 'required|string',
            'privatPort'     => 'required|numeric',
            'publicPort'     => 'required|numeric',
            'privatIP'       => 'required|ipv4',
            'publicIP'       => 'required|ipv4',
            'protocol'       => 'required|string',
            'id'             => 'required|string',
            'comment'        => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }


        $client       = $request->input('client');
        $privatPort   = $request->input('privatPort');
        $publicPort   = $request->input('publicPort');
        $privatIP     = $request->input('privatIP');
        $publicIP     = $request->input('publicIP');
        $protocol     = $request->input('protocol');
        $id           = $request->input('id');
        $comment      = $request->input('comment');

            $username = $request->user()->name;
            $userIp   = $request->ip();
    
            
           Log::channel('actions')->warning('[PORT FORWARD CHANGE] '.$client 
           .'\n[ნოკი] '.$username
           .'\n[ნოკის აიპი] '.$userIp
           .'\n[აბონენტი] '.$client
           .'\n[შიდა აიპი] '.$privatIP
           .'\n[გარე აიპი] '.$publicIP
           .'\n[გარე პორტი] '.$publicPort
           .'\n[შიდა პორტი] '.$privatPort
           .'\n[პროტოკოლი] '.$protocol
           .'\n[კომენტარი] '.$comment
           );
 
        return response()->json(PortForward::PortForwardChange($client,$privatPort,$publicPort,$privatIP,$publicIP,$protocol,$id));
    }

    public static function privat_address_change(Request $request)
    {

        $validator = validator()->make($request->only('ip','user','port'), [
            'ip'         => 'required|ipv4',
            'user'       => 'required|numeric',
            'port'       => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }


        $ip     = $request->input('ip');
        $user   = $request->input('user');
        $port   = $request->input('port');
 
        return response()->json(PortForward::search($user));



    //     $username = $request->user()->name;
    //     $userIp   = $request->ip();
 
         
    //    Log::channel('actions')->warning('[PORT FORWARD CHANGE PRIVAT ADDRESS] '.$user 
    //    .'\n[ნოკი] '.$username
    //    .'\n[ნოკის აიპი] '.$userIp
    //    .'\n[აბონენტი] '.$user
    //    .'\n[შიდა აიპი] '.$ip
    //    .'\n[გარე პორტი] '.$port
    //    );
 
        //return response()->json(PortForward::ChangePrivatAddress($id,$ip));
    }

}
