<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\PrivilegesModel;
use App\Http\Controllers\chatController;
 
use Opcodes\LogViewer\Facades\LogViewer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;



class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    { 

        Blade::component('bladewind.datepicker', 'datepicker');
       
        view()->composer('layouts.navigation', function ($view) 
        {
            $user = Auth::user();
                  
            if ($user !== null && $user->username === 'administrator') {
                \Debugbar::enable();
            }
            else \Debugbar::enable();

            if ($user) 
            {        
                $count = DB::table('ontColoneCountNum')
                ->first();
                $view->with('count', $count->count); 
                
                $NAcount = DB::table('ontNACountNum')
                ->first();
                $view->with('NAcount', $NAcount->count); 

                $privData = PrivilegesModel::SharedPrivs();
                $view->with('PrivData', $privData);   
                $view->with('switch', chatController::ChatCountData($user->name));    

                $FixedTask = DB::table('TaskMonitoring')
                ->where('taskStatus',2)
                ->first();
                $view->with('FixedTask', $FixedTask);   
                
                // $UserFixedTaskNotification = DB::table('TaskMonitoring')
                // ->where('taskStatus',2)
                // ->where('staff',$user->name)
                // ->first();
                // $view->with('UserFixedTaskNotification', $UserFixedTaskNotification);   

            }
        });


        view()->composer('dashboard', function ($view) 
        {
            $user = Auth::user();

            if ($user) 
            {
                $UserFixedTaskNotification = DB::table('TaskMonitoring')
                    ->where('taskStatus', 2)
                    ->where('staff', $user->name)
                    ->first();
                $view->with('UserFixedTaskNotification', $UserFixedTaskNotification);
            }
        });

        LogViewer::auth(function ($request) 
        {
            $user = Auth::user();
             
            if ($user !== null)
            {
                if($user->username == 'administrator')
                {                    
                    return true;
                } 
                else if ($user !== null)
                {               
                    if(PrivilegesModel::PrivCheckSingle('Priv_Log'))
                    {
                        return true;
                    }
                    return false;
                }  
                else return false;
            }
            else
            {
                return false;
            }

     


            // return $request->user()
            //     && in_array($request->user()->email, [
            //         'john@example.com',
            //     ]);
        });
    }
}


 