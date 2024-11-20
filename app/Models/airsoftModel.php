<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Models\PrivilegesModel;
 

class airsoftModel extends Model
{
    use HasFactory;

    static public function ab_search($id)
    {
        
        $creds = DB::table('parameters')->where('type','airsoft')->first();

        $link  =  $creds->url.'/restapi/finder.php';   
        $Token = $creds->password;

        $params = [
                        'request' => 'user',
                        'id' => $id,
                  ];

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'API-Key:'.$Token,
                'Content-Type: text/plain'
            ),
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {return 'Error: ' . curl_error($ch);}
        curl_close($ch);
 
        return $response;
    }

    public static function Comments($id)
    {
        $creds = DB::table('parameters')->where('type','airsoft')->first();

        $link  = $creds->url.'/restapi/finder.php';
        $Token = $creds->password;

        $params = [
                        'request' => 'comments',
                        'id' => $id,   
                  ];

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'API-Key:'.$Token,
                'Content-Type: text/plain'
            ),
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {return 'Error: ' . curl_error($ch);}
        curl_close($ch);

        return $response;
    } 

    public static function Tasks($id)
    {
        $creds = DB::table('parameters')->where('type','airsoft')->first();   

        $link  =  $creds->url.'/restapi/finder.php';
        $Token = $creds->password;

        $params = [
                        'request' => 'tasks',
                        'id' => $id,
                  ];

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'API-Key:'.$Token,
                'Content-Type: text/plain'
            ),
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {return 'Error: ' . curl_error($ch);}
        curl_close($ch);
 
        return $response;
    }

    public static function Comments_Add($Client,$id,$CommentType,$CommentData,$name)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $UserList = self::airsoft_users_list();
        $dataArray = json_decode($UserList, true);

        $UserName = '';$UserID = '';$UserPhone = '';
        foreach ($dataArray  as $key => $value) 
        { 
            if($value['name'] == $name)
            {
                $UserName  = $value['name'];
                $UserID    = $value['id'];
                $UserPhone = $value['mobile'];
            }
        }

        if( $UserName !== '' && $UserID !== '')
        {
            $creds = DB::table('parameters')->where('type','airsoft')->first();

            $link  = $creds->url.'/restapi/user/index.php';  
            $Token = $creds->username;  
  
            $ins_date = date('Y-m-d H:i:s');

            $params = [
                    'request' => 'comment', 
                    'id' => (int)$id,
                    'info' => ['staff'=>(int)$UserID,'type'=>(int)$CommentType,'comment'=>$CommentData,'ins_date'=>$ins_date]
            ];
    
            $ch = curl_init();
            curl_setopt_array($ch, array(
                CURLOPT_URL => $link,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => array(
                    'API-Key:'.$Token,
                    'Content-Type: text/plain'
                ),
            ));
    
            $response = curl_exec($ch);
            if (curl_errno($ch)) {return 'Error: ' . curl_error($ch);}
            curl_close($ch);
    
            return $response;
        }
        else
        {
            return (['error' => 'თანამშრობელი  ვერ მოიძებნა airsoft - ში ']);
        } 
    }

    static public function airsoft_users_list()
    {
        $creds = DB::table('parameters')->where('type','airsoft')->first();

        $link  = $creds->url.'/restapi/finder.php';
        $Token = $creds->password;

        $params = [
                'request' => 'admin_users', 
        ];

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'API-Key:'.$Token,
                'Content-Type: text/plain'
            ),
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {return 'Error: ' . curl_error($ch);}
        curl_close($ch);

        return $response;
    }

    static public function coordinates($user)
    {
         
        $creds = DB::table('parameters')->where('type','airsoft')->first();

        $link  =  $creds->url.'/restapi/finder.php';   
        $Token = $creds->password;

        $params = [
                    'request' => 'user',
                    'id' => $user,
                  ];

        $ch = curl_init();
        curl_setopt_array($ch, array(
            CURLOPT_URL => $link,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_POSTFIELDS => json_encode($params),
            CURLOPT_HTTPHEADER => array(
                'API-Key:'.$Token,
                'Content-Type: text/plain'
            ),
        ));

        $response = curl_exec($ch);
        if (curl_errno($ch)) {return 'Error: ' . curl_error($ch);}
        curl_close($ch);

        return $response;
    }

    static public function MacCalculateForAirsoft($line)
    {
            $macAddres   = airsoftModel::extractMacAddress($line);
            $Converted   = airsoftModel::format_mac_address($macAddres);
            $Converted   = strtoupper($Converted);

            $json_string = Storage::get('mac-vendors-export.json');
            $mac_vendors = json_decode($json_string, true);
            $mac_prefix  = substr($Converted, 0, 8);


            foreach ($mac_vendors as $vendor)
            {
                if ($vendor['macPrefix'] === strtoupper($mac_prefix))
                {
                    if ( strtoupper($mac_prefix) === '00:00:00')
                    {
                        return "Unknow Device";
                    }
                    $Mac = trim(str_replace("\"",'',$vendor['vendorName']));
                    return ($Mac);
                }
            }
    }

    static public function MacCalculate($line , $FullData)
    {
            $macAddres   = airsoftModel::extractMacAddress($line);
            $Converted   = airsoftModel::format_mac_address($macAddres);
            $Converted   = strtoupper($Converted);

            $json_string = Storage::get('mac-vendors-export.json');
            $mac_vendors = json_decode($json_string, true);
            $mac_prefix  = substr($Converted, 0, 8);


            foreach ($mac_vendors as $vendor)
            {
                if ($vendor['macPrefix'] === strtoupper($mac_prefix))
                {
                    if ( strtoupper($mac_prefix) === '00:00:00')
                    {
                        echo $FullData;die();
                    }
                    return $FullData.'  '.$vendor['vendorName']."\n";
                }
            }
    }

    static public function extractMacAddress($string)
    {
        preg_match('/([0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4})|(([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2}))|(([0-9A-Fa-f]{4}[-]){2}([0-9A-Fa-f]{4}))/', $string, $matches);
        if (isset($matches[0])) {
            return $matches[0];
        }
        return false;
    }

    static public function format_mac_address($mac_address)
    {
        $mac_address = preg_replace('/[^0-9A-Fa-f]/', '', $mac_address);
        $mac_address = str_pad($mac_address, 12, '0', STR_PAD_LEFT);
        $mac_address = strtoupper($mac_address);
        $mac_address = implode(':', str_split($mac_address, 2));
        return $mac_address;
    }

}



