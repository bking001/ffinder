<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Auth\LoginRequest;




class Priv extends Model
{
    use HasFactory;

    static public function LDAP_VIEW($paginCount)
    {
        $data = DB::table('parameters')
            ->where('type', 'ldap')
            ->get();
        $ldapData = $data->first();

        $FullList = LoginRequest::LDAP_TABLE($ldapData->username, $ldapData->password, $ldapData->url);
        $LocalLdapUsers = json_decode(DB::table('ldap_users')->orderBy('access', 'desc')->get(), true);
 
        $LastData = [];
        foreach ($FullList as $value)
        {
            if (strpos($value['user'], '.') !== false)
            {
                foreach ($LocalLdapUsers as $local)
                {
                    $Find = false;
                    if (trim(strtoupper($value['user'])) == trim(strtoupper($local['username'])))
                    {
                        $Find = true;
                        $LastData[] = [
                            'username' => $local['username'],
                            'dn' => $local['dn'],
                            'access' => $local['access'],
                            'Devices' => $local['Devices'],
                            'Priv_Onu' => $local['Priv_Onu'],
                            'Priv_Pon' => $local['Priv_Pon'],
                            'Priv_Uplink' => $local['Priv_Uplink'],
                            'Priv_Vlan' => $local['Priv_Vlan'],
                            'Priv_Install' => $local['Priv_Install'],
                            'Priv_Board' => $local['Priv_Board'],
                            'Priv_Log' => $local['Priv_Log']
                        ];
                        break;
                    }
                }
                if (!$Find) {
                    $LastData[] = [
                        'username' => $value['user'],
                        'dn' => $value['cn'],
                        'access' => '',
                        'Devices' => '',
                        'Priv_Onu' => '',
                        'Priv_Pon' => '',
                        'Priv_Uplink' => '',
                        'Priv_Vlan' => '',
                        'Priv_Install' => '',
                        'Priv_Board' => '',
                        'Priv_Log' => ''
                    ];
                }
            }
        }

        $LastData = collect($LastData)->sortByDesc('access')->values()->all();
       
        $paginatedData = new \Illuminate\Pagination\LengthAwarePaginator(
            collect($LastData)->forPage(\Illuminate\Pagination\Paginator::resolveCurrentPage(), $paginCount),
            count($LastData),
            $paginCount,
            \Illuminate\Pagination\Paginator::resolveCurrentPage(),
            ['path' => \Illuminate\Pagination\Paginator::resolveCurrentPath()]
        );

        return $paginatedData;
    }
}
