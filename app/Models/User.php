<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Auth\LoginRequest;

 

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];


    static public function LDAP_LOGIN_DATA()
    {
        $Ldap = DB::table('parameters')
            ->where('type', 'ldap')
            ->first();

        return $Ldap;
    }

 
    function getLdapUsersData($param1,$param2)
    {
        return DB::table('ldap_users')
            ->where($param1, $param2)
            ->get(); 
    }
 

    static public function LDAP_LOGIN_USER_ACCESS($Username)
    {
        $Ldap_User = DB::table('ldap_users')
                    ->where('username', $Username)
                    ->first();

      
        if($Ldap_User)
        {
            if($Ldap_User -> access === 1)
            {
                return true;
            }
            else 
            {
                return false;
            }
        }

        return false;
    }

 
}
