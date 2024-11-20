<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;


class FILTER extends Model
{
    use HasFactory;

  
    static public function SEARCH($params)
    {
        $creds = DB::table('parameters')->where('type','airsoft')->first();

        $link  =  $creds->url.'/restapi/finder.php';   
        $Token = $creds->password;

 

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

    static public function Tarriff()
    {
        $creds = DB::table('parameters')->where('type','airsoft')->first();

        $link  =  $creds->url.'/restapi/finder.php';   
        $Token = $creds->password;

        $params = [
            'request' => 'tariff'
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

    static public function Regions()
    {
        $creds = DB::table('parameters')->where('type','airsoft')->first();

        $link  =  $creds->url.'/restapi/finder.php';   
        $Token = $creds->password;

        $params = [
            'request' => 'regions'
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

    static public function SubRegions($id)
    {
        $creds = DB::table('parameters')->where('type','airsoft')->first();

        $link  =  $creds->url.'/restapi/finder.php';   
        $Token = $creds->password;

        $params = [
            'request' => 'subregion',
            'region' => $id
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

     


}

/*
    user_name":"beso","user_lastname":"chkhutiashvili","personal_id":"60001128055","company_name":"com","old_contract_num":"5555",
    "user_id":"2829534","disabled":"1","legal":"1","is_fiber":"0","tv":"1","phone":"500050704","address":"misamarti","user_ip":"127.0.0.1",
    "antenna_ip":"192.168.0.1","sector_ip":"192.168.1.100","town":"10","subregion":"2431","tariff":"25","status":"1","provider":"111",
    "legal_status":"28","mac":"12:36:cf:ff:a1","tvmac":"25:af:ff:f4:c3",
    "activate_date":"2023-03-01 00:00:00.000000","activate_date_end":"2024-04-15 00:00:00.000000","power_provider":"1",
    "expired":"1","is_vip":"1","media_converter":"1","do_not_disable":"1","as_temporary":"1","discount":"1

    is_fiber => [false=>'all', 0=> "no", 1=> "fiber" ]
    tv => [false=>'all', 0=> "no", 1=> "tv" ]
    power_provider => [false=>'all', 0=> "no", 1=> "power_provider" ]
    is_vip => [false=>'all', 0=> "no", 1=> "is_vip" ]
    media_converter => [false=>'all', 0=> "no", 1=> "media_converter" ]
    do_not_disable => [false=>'all', 0=> "no", 1=> "do_not_disable" ]
    as_temporary => [false=>'all', 0=> "no", 1=> "as_temporary" ]
    Besarion chkhutiashvili17:13
    expired => true
    discount => true



    {"request":"debug","search":{"user_name":"beso","user_lastname":"chkhutiashvili","phone":"500050704"}}

    "activate_date":"2023-03-01 00:00:00.000000","activate_date_end":"2024-04-15 00:00:00.000000"

    {"request":"tariff"}
 
    {"request":"regions"}
 
    {"request":"subregion","region":2193}
 
        111 => Airlink
        112 => CityNet
        133 => Netcom
        153 => Netcom Plus
        154 => შპს მარია
        155 => წალკა
        156 => სკოლები

        პროვაიდერები
 
        იურიდიული ფორმა

        28 => კერძო პირი
        29 => შ.პ.სკოლები
        30 => ინდ. მეწარმე
        31 => არასამთავრობო
        32 => საბიუჯეტო

        მომხარებლის სტატუსი

        '0'=>'არ არის აქტივირებული',
        '1'=>'აქტიური',
        '2'=>'დროებით ჩართული',
        '3'=>'უფასო შეჩერება',
        '4'=>'ფასიანი შეჩერება',
        '5'=>'კრედიტი',
        '6'=>'გამორთულია დავალიანების გამო',
        '7'=>'გამორთულია კლიენტის მოთხოვნით',
        '8'=>'გამორთული',
        '-1'=>'გაუქმებული',

*/