<?php

namespace App\Models\Install;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB; 


class antenna extends Model
{
    use HasFactory;


     
    static public function MastList()
    {
 
        $credentials = DB::table('parameters')->where('type','uisp')->first();
        $url    = $credentials->url;
        $token  = $credentials->password;

        $Andzebi = self::andzebi($url,$token);    
        $Data    = json_decode($Andzebi, true);  
 
        foreach ($Data as$key => $item) 
        {
            $tmp = [];
            $tmp ['id']     = $item['identification']['id'];
            $tmp ['name']   = $item['identification']['name'];
            $tmp ['note']   = $item['description']['note'];
            $tmp ['coord']  = $item['description']['address'];
            $html['SectorList'.$key]= $tmp;
        }

        return $html;
    }

    static public function sectorChoosenMast($MastID)
    {
        $html = [];

        $credentials = DB::table('parameters')->where('type','uisp')->first();
        $url    = $credentials->url;
        $token  = $credentials->password;

        $Data = json_decode(self::Mast_Devices($MastID,$url,$token), true);  
      
        $SecArray = [];
        foreach ($Data as $key => $item) 
        {
            $name = $item['identification']['name'];
            $id   = $item['identification']['id'];
            $SecArray [$key] = $id;
        }

        if($SecArray)
        {
            foreach ($SecArray as $key => $value) 
            {

                $SecData       = json_decode(self::stationsById($value,$token,$url), true);        
                $ChannelWidth  = json_decode(self::ChannelWidth($value,$token,$url), true); 
                $Statistic     = self::SectorById($value);
 
                if($SecData['identification']['category'] == 'wireless')
                {

                    $FixedIP    = $SecData['ipAddress'];
                    $position   = strpos($SecData['ipAddress'], '/');
                    if ($position !== false)$FixedIP = substr($SecData['ipAddress'], 0, $position);

                 
                        $item = [];
                        $item['id']                 = $value;
                        $item['status']             = $SecData['overview']['status'] ?? null;
                        $item['Name']               = $SecData['identification']['hostname'] ?? null;
                        $item['SectorMac']          = $SecData['identification']['mac'] ?? null;
                        $item['ipAddress']          = $FixedIP;
                        $item['ssid']               = $SecData['attributes']['ssid'] ?? null;
                        $item['frequency']          = $SecData['overview']['frequency'] ?? null;
                        $item['schannelWidthsid']   = $ChannelWidth['overview']['channelWidth'] ?? null;
                        $item['Statistic']          = $Statistic;

                        $html ['SecList_'.$key] = $item;
                }
            }
        }
      
        return $html;
    }

    static public function sectorMacSearch($antenis_mac)
    {
        $html = [];
        $antenis_mac = str_replace('-','',$antenis_mac);
        $antenis_mac = str_replace(':','',$antenis_mac);
        $antenis_mac = str_replace(' ','',$antenis_mac);
     
        $credentials = DB::table('parameters')->where('type','uisp')->first();
        $url    = $credentials->url;
        $token  = $credentials->password;

        $allSector = self::sectors($url,$token);
        $Data = json_decode($allSector, true);
                      
              
        foreach ($Data as $key => $item) 
        {
            $TempMac = $item['identification']['mac'];
            $TempMac = str_replace(':','',$TempMac);
    
            if (strpos(strtoupper($TempMac), strtoupper($antenis_mac)) !== false)
            {
                $tmp = [];
                $tmp ['id']     = $item['identification']['id'];
                $tmp ['name']   = $item['identification']['name'];
                $tmp ['mac']    = $item['identification']['mac'];
                $html['SectorList'.$key]= $tmp;
            }
        }
    
        return $html;
    }

    static public function sectorNameSearch($sectoris_saxeli)
    {
        $html = [];
   
        $credentials = DB::table('parameters')->where('type','uisp')->first();
        $url    = $credentials->url;
        $token  = $credentials->password;

        $allSector = self::sectors($url,$token);
        $Data = json_decode($allSector, true);
                     
              
        foreach ($Data as $key => $item) 
        {  
            $Name = $item['identification']['hostname'];

            if (strpos(strtoupper($Name), strtoupper($sectoris_saxeli)) !== false)
            {
                $tmp = [];
                $tmp ['id']     = $item['identification']['id'];
                $tmp ['name']   = $item['identification']['name'];
                $tmp ['mac']    = $item['identification']['mac'];
                $html['SectorList'.$key]= $tmp;
            }
        }
    
        return $html;
    }

    static public function sectorIPSearch($antenis_ip)
    {
        $html = [];
   
        $credentials = DB::table('parameters')->where('type','uisp')->first();
        $url    = $credentials->url;
        $token  = $credentials->password;

        $allSector = self::sectors($url,$token);
        $Data = json_decode($allSector, true);
                     
              
        foreach ($Data as $key => $item) 
        {  
            $address = $item['ipAddress'];
          
            if (strpos(strtoupper($address), strtoupper($antenis_ip)) !== false)
            {
                $tmp = [];
                $tmp ['id']     = $item['identification']['id'];
                $tmp ['name']   = $item['identification']['name'];
                $tmp ['mac']    = $item['identification']['mac'];
                $html['SectorList'.$key]= $tmp;
            }
        }
    
        return $html;
    }
     
    static public function sectorCustomerSearch($customer)
    {
        $html = [];

        $credentials = DB::table('parameters')->where('type','uisp')->first();
        $url    = $credentials->url;
        $token  = $credentials->password;
        
        $customer = self::ab_nom_search($customer,$url,$token);
        $Data     = json_decode($customer, true);
              
        if (count($Data) !== 0)
        { 
            foreach ($Data as $key => $item) 
            { 
                $tmp = [];
                $tmp ['id']          = $item['data']['device']['deviceId'] ?? null;
                $tmp ['name']        = $item['data']['device']['name'] ?? null;
                $html['SectorList'.$key]= $tmp  ?? null;           
            }
        }
      
        return $html;
    }

    static public function andzebi($url,$token)
    {

        $url = $url.'/nms/api/v2.1/sites';
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
            return response()->json(['error' => $err]);
        } else {
            return $response;
        }
    }

    static public function SectorById($SectorID)
    {
        $html = [];
        $credentials = DB::table('parameters')->where('type','uisp')->first();
        $url    = $credentials->url;
        $token  = $credentials->password;

        $Data   = json_decode(self::stations($SectorID,$token,$url), true);  

        $Online = 0;$Offline = 0;$Total = 0; 

        $SecData       = json_decode(self::stationsById($SectorID,$token,$url), true);    
        $ChannelWidth  = json_decode(self::ChannelWidth($SectorID,$token,$url), true); 

        $FixedIP    = $SecData['ipAddress'];
        $position   = strpos($SecData['ipAddress'], '/');
        if ($position !== false)$FixedIP = substr($SecData['ipAddress'], 0, $position);
    
    
        $html ['Sectorname'] = $SecData['identification']['hostname'] ?? null;  
        $html ['SectorMac']  = $SecData['identification']['mac'] ?? null; 
        $html ['SecIP']      = $FixedIP ?? null; 
        $html ['ssid']       = $SecData['attributes']['ssid'] ?? null; 
        $html ['frequency']  = $SecData['overview']['frequency'] ?? null;
        $html ['Channel']    = $ChannelWidth['overview']['channelWidth'] ?? null;
        $html ['status']     = $SecData['overview']['status'] ?? null;
        $html ['modelName']       = $SecData['identification']['modelName'] ?? null;
        $html ['firmwareVersion'] = $SecData['identification']['firmwareVersion'] ?? null;
        $html ['station']    = $Data ?? null;

        return $html;
    }
     
    static public function stationsById($SectorID,$token,$url)
    {                   
                           
        $url = $url.'/nms/api/v2.1/devices/'.$SectorID;
        $headers = [
            'Accept: application/json',
            'x-auth-token: '.$token ,
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
            return response()->json(['error' => $err]);
        } else {
            return $response;
        }
    }

    static public function ChannelWidth($SectorID,$token,$url)
    {
 
        $url = $url.'/nms/api/v2.1/devices/aircubes/'.$SectorID.'?withStations=false';
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
            return response()->json(['error' => $err]);
        } else {
            return $response;
        }
    }

    static public function stations($SectorID,$token,$url)
    {
        $url = $url.'/nms/api/v2.1/devices/aircubes/'.$SectorID.'/stations';
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
            return response()->json(['error' => $err]);
        } else {
            return $response;
        }
    }

    static public function ab_nom_search($customer,$url,$token)
    {
        $link = $url.'/nms/api/v2.1/nms/search?query='.$customer.'&page=1&count=10';
        $headers = [
                        'Accept: application/json',
                        'x-auth-token:'.$token ,
                ];

        $curl = curl_init();
 
        curl_setopt_array($curl, [
            CURLOPT_URL => $link,
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
            return response()->json(['error' => $err]);
        } else {
            return $response;
        }
    }

    static public function sectors($url,$Token)
    {
  
        $url = 'https://uisp.airlink.ge/nms/api/v2.1/devices';
        $headers = [
            'Accept: application/json',
            'x-auth-token:'.$Token ,
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
            return response()->json(['error' => $err]);
        } else {
            return $response;
        }
    }

    static public function Mast_Devices($MastID,$url,$token)
    {              
        $url     = $url.'/nms/api/v2.1/devices?siteId='.$MastID;
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
        $err      = curl_error($curl);

        curl_close($curl);

        if ($err) {
            return response()->json(['error' => $err]);
        } else {
            return $response;
        }
    }
}
