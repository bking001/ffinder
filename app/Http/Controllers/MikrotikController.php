<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MikrotikModel;
use Illuminate\Support\Facades\Log;


class MikrotikController extends Controller
{
    public function search(Request $request)
    {
        
        $index    = $request->input('client');
        $mikrotik = $request->input('server');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        // Log::channel('mikrotik')->warning('[Mikrotik Search] '.$index .'\n[Mikrotik] '.$mikrotik.'\n[User] '.$username.'\n[Address] '.$userIp);  
 

        return MikrotikModel::mikrotik($mikrotik,$index);
    }
}
