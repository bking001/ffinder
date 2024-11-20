<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\PrivilegesModel;
use App\Models\Priv;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;


class Privillages extends Controller
{
   public function ViewTable()
   {  
      PrivilegesModel::PrivCheck('OnlyAdmin'); 
      return view('privilege', ['data' => Priv::LDAP_VIEW(12)]);
   }

   public function Disable_Enabled_Priv(Request $request) 
   { 
      PrivilegesModel::PrivCheck('OnlyAdmin');

      $username = $request->input('username');
      $column   = $request->input('column');
      $dn       = $request->input('dn');

      $state = DB::table('ldap_users')
               ->where('username', $username)   
               ->first();


      if($state !== null && $state->$column == 1)
      {
         DB::table('ldap_users')->where('username', $username)->update([$column => 0]);
      }
      else if($state !== null && $state->$column == 0)
      {
         DB::table('ldap_users')->where('username', $username)->update([$column => 1]);
      }
      else 
      {
         DB::table('ldap_users')->insert([
            'username'     => $username,
            'dn'           => $dn,
            'access'       => 1,
            'Devices'      => 0,
            'Priv_Onu'     => 0,
            'Priv_Pon'     => 0,
            'Priv_Uplink'  => 0,
            'Priv_Vlan'    => 0,
            'Priv_Install' => 0,
            'Priv_Board'   => 0,
            'OnlyAdmin'    => 0,
            'Priv_Log'     => 0,
        ]);
      } 
      
   }

   public function PrivSearch(Request $request)
   {   
      PrivilegesModel::PrivCheck('OnlyAdmin');
      try {     
              $param   = $request->input('default_search_priv');           
              $results = Priv::LDAP_VIEW(200);

               $filteredResults = [];
               foreach ($results as $value) 
               {
                  $paramLower = strtolower($param);
                  $usernameLower = strtolower($value['username']);
                  $dnLower = strtolower($value['dn']);

                  if (strpos($usernameLower , $paramLower,) !== false || strpos($dnLower , $paramLower) !== false)
                  {
                     $filteredResults[] = $value;
                  }
               }

               $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
                  collect($filteredResults)->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), 200),
                  count($filteredResults),
                  200,
                  \Illuminate\Pagination\Paginator::resolveCurrentPage(),
                  ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
              );

          
              return view('privilege', ['data' => $paginatedData]);  
          } catch (\Throwable $e) 
          {
             abort(500,"Error");
          }  
   }
}
