<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Install\antenna;

class UISP extends Model
{
    use HasFactory;


    static public function Client_Side_Info($uispUrl,$ip,$mac,$user,$token)    
    {   
        $html = []; 
        $Find = false;

        $allSector  = UISP::sectors($uispUrl,$token);
        $SectorData = json_decode($allSector, true);    
        foreach ($SectorData as  $item) 
        { 
            $fromip = explode('/',$item['ipAddress']);   
    
            $html ['status']     = $item['overview']['status'] ?? null;
 
            if ($ip == $fromip[0])
            {    
                $Data   = json_decode(UISP::stations($uispUrl,$item['identification']['id'],$token), true);  
                    
                $html ['SectorIp']              = $fromip[0] ?? null;
                $html ['SectorName']            = $item['identification']['name'] ?? null;
                $html ['SectorMac']             = $item['identification']['mac'] ?? null;
                $html ['SectorFrequency']       = $item['overview']['frequency'] ?? null;
                $html ['SectorChannelWidth']    = $item['overview']['channelWidth'] ?? null;
                $html ['SectorSSid']            = $item['attributes']['ssid'] ?? null;
                $html ['modelName']             = $item['identification']['modelName'] ?? null;
                $html ['firmwareVersion']       = $item['identification']['firmwareVersion'] ?? null;
                 

                if($Data)
                {
                    foreach ($Data as $key => $value)
                    {
                        if (strpos($value['name'], $user) !== false)$Find = true;
                        if($value['connected'])
                        {
                            $time = UISP::Uptime($value['uptime']);
                        }
                        else
                        {
                            $time = UISP::timestamp($value['timestamp']);
                        }
                          

                        $item = [];    
                        $item['status']         = $value['connected'];
                        $item['name']           = $value['name'];
                        $item['model']          = $value['model'];
                        $item['rxSignal']       = $value['rxSignal'];
                        $item['txSignal']       = $value['txSignal'];
                        $item['mac']            = $value['mac'];
                        $item['distance']       = $value['distance'];
                        $item['ipAddress']      = $value['ipAddress'];
                        $item['uptime']         = $time;
            
                        $html['users_'.$key]    = $item;
                    }
                }
                break;
            }
        }
 
        if(!$Find)
        {   
            $Searched           = UISP::search_by_mac($uispUrl,$mac,$token);    
            $SectorSearchedData = json_decode($Searched, true);   
            foreach ($SectorSearchedData as $key => $value)    
            { 
                if (isset($value['data']['device']['deviceId'])) 
                {
                    $Nextid = $value['data']['device']['deviceId'];
                    break;
                }
            }
        }
      
        if(isset($Nextid))
        {
            $Data   = json_decode(UISP::stations($uispUrl,$Nextid,$token), true);  
    
            try{
                    $SectorAddress          = antenna::SectorById($Nextid);   

                    $html ['SectorIp']              = $SectorAddress['SecIP'] ?? null;
                    $html ['SectorName']            = $SectorAddress['Sectorname'] ?? null;
                    $html ['SectorMac']             = $SectorAddress['SectorMac'] ?? null;
                    $html ['SectorFrequency']       = $SectorAddress['frequency'] ?? null;
                    $html ['SectorChannelWidth']    = $SectorAddress['Channel'] ?? null;
                    $html ['SectorSSid']            = $SectorAddress['ssid'] ?? null;

            }catch (\Exception $e){$html ['SectorIp']  = null;}
      
 
            if($Data)
            {
                foreach ($Data as $key => $value)
                {

                    if($value['connected'])
                    {
                        $time = UISP::Uptime($value['uptime']);
                    }
                    else
                    {
                        $time = UISP::timestamp($value['timestamp']);
                    }

                    $item = [];    
                    $item['status']         = $value['connected'];
                    $item['name']           = $value['name'];
                    $item['model']          = $value['model'];
                    $item['distance']       = $value['distance'];
                    $item['rxSignal']       = $value['rxSignal'];
                    $item['txSignal']       = $value['txSignal'];
                    $item['mac']            = $value['mac'];
                    $item['ipAddress']      = $value['ipAddress'];
                    $item['uptime']         = $time;
        
                    $html['users_'.$key]    =   $item;
                }
                $Find = true;
            }
        }
    
        if(!$Find)return response()->json(['error' => 'აბონენტი არ მოიძებნა UISP - ში ']);

        return $html;
    }

    static public function search_by_mac($uispUrl,$mac,$token)
    {
        $url = $uispUrl.'/nms/api/v2.1/nms/search?query='.$mac.'&page=1&count=10';
        $headers = [
                        'Accept: application/json',
                        'x-auth-token:'.$token ,
                ];
    
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
        ]);
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if ($err) {
            return  "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    static public function sectors($uispUrl,$token)
    {
  
        $url = $uispUrl.'/nms/api/v2.1/devices';
        $headers = [
            'Accept: application/json',
            'x-auth-token:'.$token ,
        ];

        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
        ]);

        $response = curl_exec($curl);
        $err = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    static public function stations($uispUrl,$id,$token)
    { 
        $url = $uispUrl.'/nms/api/v2.1/devices/aircubes/'.$id.'/stations';
        $headers = [
            'Accept: application/json',
            'x-auth-token:'.$token ,
        ];
    
        $curl = curl_init();
    
        curl_setopt_array($curl, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_HTTPHEADER => $headers,
        ]);
    
        $response = curl_exec($curl);
        $err = curl_error($curl);
    
        curl_close($curl);
    
        if ($err) {
            return "cURL Error #:" . $err;
        } else {
            return $response;
        }
    }

    static public function timestamp ($timestamp)
    {
      $currentTimestamp = time();
      $timestampDateTime = new \DateTime($timestamp);
      $currentDateTime = new \DateTime("@$currentTimestamp");
    
      $timeDiff = $currentDateTime->diff($timestampDateTime);
      $daysDiff = $timeDiff->days;
    
      $LastSeen  = "";
      $LastSeen .=  $daysDiff > 0 ? $daysDiff . " day ago" : "";
     
      return $LastSeen;
    }
    
    static public function Uptime($uptime)
    {     
          $days = floor($uptime / (60 * 60 * 24));
          $hours = floor(($uptime % (60 * 60 * 24)) / (60 * 60));
          $minutes = floor(($uptime % (60 * 60)) / 60);
          $seconds = $uptime % 60;
    
          $time = "";
         
          if ($days > 0) 
          {
            $time .= $days . " d, ";
          }
          $time .= ($hours . " h, " . $minutes . " min, " . $seconds . " sec");
          return $time;
    
    }
}
