<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Models\PrivilegesModel;




class OptionPageController extends Controller
{
    public function getDataAndView()
    {
        PrivilegesModel::PrivCheck('OnlyAdmin');
        $data = DB::table('parameters')->get(); 
        return view('parameters', ['data' => $data]);
    }

   
    public function parametersUpdate(Request $request)
    {  
        PrivilegesModel::PrivCheck('OnlyAdmin');
        try {
                foreach ($request->all() as $item) 
                {           
                        DB::table('parameters')
                        ->where('type', $item['type'])  
                        ->update([
                            'url' => $item['url'],
                            'username' => $item['username'],
                            'password' => $item['password']
                        ]);    
                }
 
            return true;
        } catch (\Throwable $e) {
            return $e->getMessage();
        }
    }
   

}
