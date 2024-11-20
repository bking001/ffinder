<?php

namespace App\Providers;

use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Opcodes\LogViewer\LogFile;
use Opcodes\LogViewer\LogFolder;
use App\Models\PrivilegesModel;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;



class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
 
        Gate::define('deleteLogFile', function (?User $user, LogFile $file) 
        {   
            if ($user->username == 'administrator') 
            {
                return true;
            }
            return false;
            // return true if the user is allowed to delete the specific log file.
        });

        Gate::define('deleteLogFolder', function (?User $user, LogFolder $folder) 
        {
            // return true if the user is allowed to delete the whole folder.
            return false;
        });
    }
}
