<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\Install\crm;
use App\Models\PrivilegesModel;

class TMS extends Model
{
    use HasFactory;

    static public function FindClient($ab_num)
    {

        $html = [];
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 

        $url      = $urlTVIP."/api/provider/accounts/?limit=1&login=".$ab_num."&start=0";
        $response = TMS::getChannels($url, $authorization);
    
        $data      = json_decode($response, true);  
        if (count($data['data']) == 0)return false; 
        $idValue   = $data['data'][0]['id'];
    
        try {
                if($idValue > 0)
                {
                    $urlX      = $urlTVIP."/api/provider/devices/?account=".$idValue."&limit=50&start=0";
                    $responseX = TMS::getChannels($urlX, $authorization);
                    $dataX     = json_decode($responseX, true); 
                
                    
                    $urlTarrifEx   = $urlTVIP."/api/provider/account_subscriptions?account=".$idValue."&limit=1&start=0";
                    $TarrifEx      = TMS::getChannels($urlTarrifEx, $authorization);
                    $data_TarrifEx = json_decode($TarrifEx, true); 
                    $TarrifID      = $data_TarrifEx['data'][0]['tarif'];
                
                    $AccountTarrifID  = $data_TarrifEx['data'][0]['id'];
        
                        $urlTarrifList    = $urlTVIP."/api/provider/tarifs?limit=100&start=0";
                        $TarrifListEx     = TMS::getChannels($urlTarrifList, $authorization);
                        $data_TarrifList  = json_decode($TarrifListEx, true); 
                            
                        $Tarrif = 'Unknow';
                        
                        $channels = $data_TarrifList['data'];
                        foreach ($channels as $channel) 
                        {
                            if($TarrifID == $channel['id'])
                            {
                                $Tarrif = $channel['tarif_name'];
                            }
                        }
                
        
                
                        if ($dataX && isset($dataX['data'])) 
                        {
                            $channels = $dataX['data'];
                 
                            foreach ($channels as $key => $channel) 
                            {
                                $lastOnlineDate = date("Y-m-d H:i:s", strtotime($channel['last_online']));
            
                                $Type_TV_Box = 'Unknow';
                                if(trim($channel['device_type']) == 1)$Type_TV_Box = 's530';
                                else if(trim($channel['device_type']) == 2)$Type_TV_Box = 's605';
                                else if (trim($channel['device_type'])== 35)$Type_TV_Box = 's710a';
                                else if (trim($channel['device_type'])== 5)$Type_TV_Box = 'tx2';
        
                                $item = [];
                                $item['WS']              = $lastOnlineDate;                           
                                $item['Type_TV_Box']     = $Type_TV_Box;    
                                $item['mac']             = $channel['mac'];    
                                $item['client']          = $data['data'][0]['login'];                              
                                $item['Tarrif']          = $Tarrif;    
                                $item['Tarrif_ID']       = $TarrifID;    
                                $item['AccountTarrifID'] = $AccountTarrifID;
                                $item['last_fw_ver']     = $channel['last_fw_ver'];      
                                $item['deviceID']        = $channel['id'];  
                                $item['unique_id']       = $channel['unique_id'];  
                                $item['operation_system']= $channel['operation_system'];  
                        
                                $html["tv_box_$key"] = $item;    
            
                            }
                        }        
                }
            } 
            catch (\Exception $e) 
            {}
    
        return $html;
    }

    static public function AllunactivatedDevices($account)
    {
        $html = [];
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url           = $urlTVIP.'/api/provider/devices?start=0&limit=100&account=1497';
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];

        $accName  = self::Get_Account_Name(1497);
        $TypeList = self::DevicesTypes();  
        $TVBOX    = json_decode($TypeList, true);  

        try {
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

                $response = curl_exec($curl);
                curl_close($curl);

                $Data = json_decode($response, true); 

                foreach ($Data['data'] as $key => $value) 
                {
                    $lastOnlineDate = date("Y-m-d H:i:s", strtotime($value['last_online']));
                    $currentDate = date("Y-m-d H:i:s");
                    $threshold = 120; // 2 minutes in seconds
                    
                    $lastOnlineTimestamp = strtotime($lastOnlineDate);
                    $currentTimestamp    = strtotime($currentDate);
                    
                    $WS = '';
                    if ($currentTimestamp - $lastOnlineTimestamp > $threshold) 
                    {
                        
                        $WS = '<td class="td-class" style="text-align:center;background:#111b30;letter-spacing: .1em;color:#ef7676;text-transform: uppercase;font-size: 11px;">' .$lastOnlineDate. '</td>';
                    } 
                    else 
                    {
                       
                        $WS = '<td class="td-class" style="text-align:center;background:#111b30;letter-spacing: .1em;color:#bfffa8;text-transform: uppercase;font-size: 11px;">' .$lastOnlineDate. '</td>';
                    }

                    $DevType = 'Unknow';
                    foreach ($TVBOX['data'] as $Zkey => $Zvalues) 
                    { 
                        if($value['device_type'] == $Zvalues['id'])
                        $DevType = $Zvalues['device_type'];
                    }

                   $item = [];
                   $item['id']                  = $value['id'];
                   $item['mac']                 = $value['mac'];
                   $item['unique_id']           = $value['unique_id'];
                   $item['last_fw_ver']         = $value['last_fw_ver'];
                   $item['device_type']         = $DevType;
                   $item['last_online']         = $WS;
                   $item['account']             = $accName;
                   $item['operation_system']    = $value['operation_system'];     
                   $item['ipaddr']              = $value['ipaddr'];  
                   $html['tvbox_'.$key]         = $item;
                }

        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }

          
        return $html;
    }

    static public function FindByMac($mac)
    { 
        $html     = [];
        $creds    = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url           = $urlTVIP.'/api/provider/devices?start=0&limit=25&quick_search='.$mac;
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];
    
       
        try{    
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                $response = curl_exec($curl);
                curl_close($curl);

                $Data  = json_decode($response, true);
        } 
        catch (\Exception $e){$Data = '';}

 
        try{ 
                if($Data != '')
                {
                    foreach ($Data['data'] as $key => $value) 
                    { 
                        if($value['account'] !== null)$accName  = self::Get_Account_Name($value['account']);
                        else $accName  = '-';
                        $TypeList = self::DevicesTypes();  
                        $TVBOX    = json_decode($TypeList, true);  


                        $DevType = 'Unknow';
                        foreach ($TVBOX['data'] as $Zkey => $Zvalues) 
                        { 
                            if($value['device_type'] == $Zvalues['id'])
                            $DevType = $Zvalues['device_type'];
                        }
            

                        $lastOnlineDate = date("Y-m-d H:i:s", strtotime($value['last_online']));
                        $currentDate = date("Y-m-d H:i:s");
                        $threshold = 120; // 2 minutes in seconds
                        
                        $lastOnlineTimestamp = strtotime($lastOnlineDate);
                        $currentTimestamp    = strtotime($currentDate);

                        $WS = '';
                        if ($currentTimestamp - $lastOnlineTimestamp > $threshold) 
                        {
                            
                            $WS = '<td class="td-class" style="text-align:center;background:#111b30;letter-spacing: .1em;color:#ef7676;text-transform: uppercase;font-size: 11px;">' .$lastOnlineDate. '</td>';
                        } 
                        else 
                        {
                        
                            $WS = '<td class="td-class" style="text-align:center;background:#111b30;letter-spacing: .1em;color:#bfffa8;text-transform: uppercase;font-size: 11px;">' .$lastOnlineDate. '</td>';
                        }

                        $devices = [];

                        $devices ['id']              = $value['id'];
                        $devices ['account']         = $accName;
                        $devices ['ipaddr']          = $value['ipaddr'];
                        $devices ['mac']             = $value['mac'];
                        $devices ['unique_id']       = $value['unique_id'];
                        $devices ['firmware']        = $value['last_fw_ver'];
                        $devices ['type']            = $DevType;
                        $devices ['last_online']     = $WS;
                        $devices['operation_system'] = $value['operation_system'];    

                        $html ['deviceList_'.$key] = $devices;
                    }       
                }
        } 
        catch (\Exception $e){}

        return $html;
    }

    static public function DeleteDevice($account,$device)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/devices/'. urlencode($device);

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];
 
      
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) 
        {
            return true;
        } 
        else 
        {
            return response()->json(['error' => "Request failed with HTTP code: $httpCode"]);
        }

 
    }

    static public function BindDevice($account,$id,$mac,$address,$unique)
    {  
        PrivilegesModel::PrivCheck('Priv_Install');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/devices/'.$id;

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];

        $data = json_encode([       
            "ipaddr" =>  $address,
            "mac" =>  $mac,
            "unique_id" =>  $unique,
            "remote_custom_field" =>  "",
            "comment" =>  "",
            "last_fw_ver" =>  "",
            "use_nat" =>  true,
            "operation_system" =>  "",
            "udpxy_addr" =>  "",
            "device_type" =>  null,
            "provider" =>  1,
            "account" => self::Get_Account_ID($account)     
        ]);
 
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
            
        $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

        if ($response === false) 
        {
            $error = curl_error($curl);
            curl_close($curl);
            echo "Error: " . $error;
            return response()->json(['error' =>$error]);
        } 

        if ($response === false || $httpCode !== 200) 
        {
            $error = curl_error($curl);
            curl_close($curl);
            if ($response === false) 
            {
                return response()->json(['error' => "Error: cURL request failed: " . $error]);
            } 
            else 
            {
                return response()->json(['error' => "Error: HTTP response code " . $httpCode . " - " . $error]);
            }
        } 
        else 
        {
            curl_close($curl); 
            crm::AIRSOFT_ADD_TVMAC($account,$mac);
            return true;
        }

    }

    static public function DevicesTypes()
    {
        $html = [];
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/device_types?start=0&limit=-1';

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];


        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);
 
        if(!empty($response)) return $response;
    
        else return false;

    }

    static public function Get_Account_Name($Account)
    { 
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url           = $urlTVIP.'/api/provider/accounts/'.urlencode(trim($Account));
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];
 

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);
        
        $Data  = json_decode($response, true);
        return $Data['login'];
    }

    static public function FindAccount($id)
    {
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/accounts/?limit=1&login='.trim($id).'&start=0';

        
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];

        try {
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
                $response = curl_exec($curl);
                curl_close($curl);
        
                $Data    = json_decode($response, true);  
                return $Data;
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    static public function DeleteAccount($Account)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/devices?start=0&limit=100&account='.self::Get_Account_ID($Account);

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);

        if($response == '{"data":[],"total":0,"start":0,"limit":100}')
        {
            //
        }
        else
        {
            $data = json_decode($response, true); 
  
            foreach ($data['data'] as $item) 
            {    
                self::ForceDeleteDevice(trim($item['id']));
            }
        }
       
        self::AccountRemove($Account);

        return true;
 
    }
     
    static public function ForceDeleteDevice($Device)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/devices/'. urlencode($Device);
 
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
 
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        curl_exec($ch);  
        curl_close($ch);
   
        if ($httpCode === 200) 
        {
            return true;
        } 
        else 
        {
            return response()->json(['error' => "Request failed with HTTP code: $httpCode"]);
        }
    }

    static public function AccountRemove($Account)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/accounts/'.self::Get_Account_ID($Account);

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];
 
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode === 200) 
        {
           return true;
        } 
        else 
        {
            return response()->json(['error' => "Request failed with HTTP code: $httpCode"]);
        }     
    }

    static public function CreateAccount($Account,$Tarrif)
    {
        PrivilegesModel::PrivCheck('Priv_Install');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $Pass = self::generateRandomHexString(4);
        $data = json_encode([
            "login" => $Account,
            "remote_custom_field" => "",
            "fullname" => $Account,
            "pin_md5" =>  md5($Pass),
            "contract_info" => "",
            "main_address" => "",
            "account_desc" => $Pass,
            "provider" => 1,
            "region_tag" => null,
            "enabled" => true,
            "last_online" => 0,
            "created" => 0,
            "updated" => 0
        ]);
        
        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/accounts';

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];

        try {
                        
                $curl = curl_init($url);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                
                $response = curl_exec($curl);
                
                $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

                if ($response === false || $httpCode !== 200) 
                {
                    $error = curl_error($curl);
                    curl_close($curl);
                    if ($response === false) 
                    {
                        return response()->json(['error' => $$error]);
                    }
                    else 
                    {
                        return response()->json(['error' => "Error: HTTP response code " . $httpCode . " - " . $error]);
                    }
                } 
                else 
                {
                    $AccountID  =   trim((int)self::Get_Account_ID($Account));
                    if(!empty($AccountID))
                    {
                        $Status = self::tarrif_bound_account($AccountID,$Tarrif);
                    }

                    curl_close($curl); 
                    return $Status;
                }
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    static public function tarrif_bound_account($account_id,$tarrif_id)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

 
        $data = json_encode([
            'start' => '2019-11-10T06:57:56+0000',
            'stop' => null,
            'tarif' => $tarrif_id,
            'time_shift_depth' => 0,
            'id' => null,
            'account' => $account_id
        ]);
 
        
        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/account_subscriptions';

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];
        
        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        
        $response = curl_exec($curl);
               
        if ($response === false) 
        {
            $error = curl_error($curl);
            curl_close($curl);
            return response()->json(['error' => $error]);
        } 
        else 
        {
            curl_close($curl);
            return true;
        }
    }

    static public function TvBoxUpdate($deviceID)
    {
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/commands/send/devices?broadcast=false';

            $data = json_encode(
                [
                    'commands'=> [['command' => 'update']],'ids' => [$deviceID]
                ] 
            );
         
            $headers = [
                'Content-Type: application/json',
                'Authorization: ' . $authorization
            ];     
    
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_POST, true); 
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);

            if ($response === false) {
                $error = curl_error($curl);
                curl_close($curl);
                return "Error: " . $error;
            } 
            else 
            {
                curl_close($curl);
                return true;
            }

    }

    static public function getChannels($url, $authorization)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'accept: */*',
            'authorization: ' . $authorization,
        ));

        $response = curl_exec($ch);

        if (curl_errno($ch)) {
            echo 'Error: ' . curl_error($ch);
        }

        curl_close($ch);

        return $response;
    }

    static public function restart($deviceID)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/commands/send/devices?broadcast=false';

            $data = json_encode(
                [
                    'commands'=> [['command' => 'restart']],'ids' => [$deviceID]
                ] 
            );
         
            $headers = [
                'Content-Type: application/json',
                'Authorization: ' . $authorization
            ];     
    
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_POST, true); 
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);

            if ($response === false) {
                $error = curl_error($curl);
                curl_close($curl);
                return "Error: " . $error;
            } 
            else 
            {
                curl_close($curl);
                return true;
            }
    }

    static public function CreateTarrif($account,$tarrifID)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/account_subscriptions/';

            $headers = [
                'Content-Type: application/json',
                'Authorization: ' . $authorization
            ];
 
            $data = json_encode([
                'start' => '2019-11-10T06:57:56+0000',
                'stop'  => null,
                'tarif' => $tarrifID,
                'time_shift_depth'  => 0,
                'id' => null,
                'account' => (int)TMS::Get_Account_ID($account),
            ]);
            
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_POST, true); 
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data); 
            $response = curl_exec($curl);


            if ($response === false) {
                $error = curl_error($curl);
                curl_close($curl);
                return "Error: " . $error;
            } 
            else 
            {
                curl_close($curl);
                return true;
            }

    }
     
    static public function DeleteTarrif($tarrifID)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/account_subscriptions/'.(int)$tarrifID;

            $headers = [
                'Content-Type: application/json',
                'Authorization: ' . $authorization
            ];

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "DELETE");
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);

            if ($response === false) {
                $error = curl_error($curl);
                curl_close($curl);
                return "Error: " . $error;
            } 
            else 
            {
                curl_close($curl);
                return true;
            }

    }

    static public function TarrifChange($account,$tarrif,$tarrifID)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/account_subscriptions/'.(int)$tarrifID;

        $AccountID  = TMS::Get_Account_ID($account);

        if($AccountID)
        {
            $data = json_encode([
                'start' => '2019-11-10T06:57:56+0000',
                'stop' => null,
                'tarif' => (int)$tarrif,
                'time_shift_depth' => 0,
                'id' => (int)$tarrifID,
                'account' => (int)$AccountID
            ]);
            
 
           
            $headers = [
                'Content-Type: application/json',
                'Authorization: ' . $authorization
            ];
 
            

            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "PUT");
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);

            if ($response === false) {
                $error = curl_error($curl);
                curl_close($curl);
                return "Error: " . $error;
            } 
            else 
            {
                curl_close($curl);
                return true;
            }

        }
        else
        {
            return 0;
        }

    }

    static public  function getTarrifListFromAccount($account)
    {
        $html = [];
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/account_subscriptions?start=0&limit=25&quick_search='.$account;
                 
        $headers = [
                'Content-Type: application/json',
                'Authorization: ' . $authorization
            ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($ch);
        curl_close($ch);

        if(!empty($response))
        {
            $TarrifList = json_decode(TMS::All_Tarrifs(), true);
            $Data       = json_decode($response, true);  
 
            foreach ($Data['data'] as $key => $tariff) 
            {           
                $item = [];
                $item['Tarrifid'] = $tariff['id'];
             
                foreach ($TarrifList['data'] as  $value) 
                {
                    if($value['id'] == $tariff['tarif'])
                    {
                        $item['tarif_name'] = $value['tarif_name'];
                        $item['tarif_id']   = $value['id'];
                    }              
                }

                $html["tarrifNum_$key"] = $item;  
            }  


            foreach ($TarrifList['data'] as $Xkey => $Xvalue) 
            {
                $item = [];
                $item['id'] = $Xvalue['id'];
                $item['tarif_name'] = $Xvalue['tarif_name'];
                $html["tarrifList_$Xkey"] = $item;  
            }

            return $html;
        }
        else
        {
            if (curl_errno($ch)) {
                return 'Error: ' . curl_error($ch);
            }       
        }

    }
 
    static public function All_Tarrifs()
    {
        $html = [];
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/tarifs?start=0&limit=255';

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];


        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);
 
        if(!empty($response)) return $response;
        else return false;
    }
 
    static public function Get_Account_ID($Account)
    {
    
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/accounts/?limit=1&login='.trim($Account).'&start=0';

        
        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];


        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);

        $Data    = json_decode($response, true);  
        foreach ($Data['data'] as $tariff) 
        {
            return $tariff['id'];
        }

        return false;
    }

    static public  function Channel_List($tarrif)
    {
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/channels?start=0&limit=500'; // &tarif='.$tarrif

        $headers = [
            'Content-Type: application/json',
            'Authorization: ' . $authorization
        ];

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        $response = curl_exec($curl);
        curl_close($curl);
 
        if(!empty($response)) return $response;
    
        else return false;
    } 

    static public  function Channel_Change($channel,$deviceID)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        // refresh_channel_list
        $creds = DB::table('parameters')->where('type','tvip')->first();
        $username = $creds->username;
        $password = $creds->password;
        $urlTVIP  = $creds->url;

        $authorization = "Basic ".base64_encode($username.':'.$password); 
        $url = $urlTVIP.'/api/provider/commands/send/devices?broadcast=false';

            $data = json_encode(
                [
                    'commands'=> [['command' => 'switch_to_channel', 'channel' => (int)$channel ]],'ids' => [$deviceID]
                ] 
            );
         
            $headers = [
                'Content-Type: application/json',
                'Authorization: ' . $authorization
            ];     
    
            $curl = curl_init($url);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_POST, true); 
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

            $response = curl_exec($curl);

            if ($response === false) {
                $error = curl_error($curl);
                curl_close($curl);
                return "Error: " . $error;
            } 
            else 
            {
                curl_close($curl);
                return true;
            }
    }

    static public function generateRandomHexString($length = 4)
    {
        $min = pow(10, $length - 1);
        $max = pow(10, $length) - 1;
        return (string) random_int($min, $max);
    }
}
