<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Requests\Auth\LoginRequest;


use App\Models\User;

class PrivilegesModel extends Model
{
    use HasFactory;

    static public function PrivCheck($Name)
    {
        $User = auth()->user()->username;  
        $Perm = DB::table('ldap_users')
        ->where('username', $User) 
        ->value($Name); 

        if($Perm == 1)return true;
        else if($User == 'administrator')return true;
        else abort(401,"User can't perform this action");
    }

    static public function SafePrivCheck($Name)
    {
        $User = auth()->user()->username;  
        $Perm = DB::table('ldap_users')
        ->where('username', $User) 
        ->value($Name); 

        if($Perm == 1)return true;
        else if($User == 'administrator')return true;
        else return false;
    }

    static public function PrivCheckSingle($Name)
    {
        $User = auth()->user()->username;  
        $Perm = DB::table('ldap_users')
        ->where('username', $User) 
        ->value($Name); 

        if($Perm == 1)return true;
        else if($User == 'administrator')return true;
        else return false;
    }
 

    static public function SharedPrivs()
    {
       $User = auth()->user()->username;    
       
       if($User == 'administrator') 
       {
            $PrivData = [
                'access'    => 1,
                'Devices'   => 1,
                'Onu'       => 1,
                'Pon'       => 1,
                'Uplink'    => 1,
                'Vlan'      => 1,
                'Install'   => 1,
                'Board'     => 1,
                'admin'     => 1,
                'Priv_Log'  => 1,
            ];
        
           return  $PrivData;
       }
       else
       {
            $PrivData = DB::table('ldap_users')->where('username', $User)->first();
            
            $PrivData = [
                            'access'    => $PrivData->access,
                            'Devices'   => $PrivData->Devices,
                            'Onu'       => $PrivData->Priv_Onu,
                            'Pon'       => $PrivData->Priv_Pon,
                            'Uplink'    => $PrivData->Priv_Uplink,
                            'Vlan'      => $PrivData->Priv_Vlan,
                            'Install'   => $PrivData->Priv_Install,
                            'Board'     => $PrivData->Priv_Board,
                            'Priv_Log'  => $PrivData->Priv_Log,
                            'admin'     => 0,
                        ];
            
            return $PrivData; 
       }          
    }
}
