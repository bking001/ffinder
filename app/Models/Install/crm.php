<?php

namespace App\Models\Install;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\phpAPImodel;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Log;
use App\Models\airsoftModel;
 
use App\Models\PrivilegesModel;

class crm extends Model
{
    use HasFactory;

    public static function FirstInstallCheck($ip,$mac,$ab_nom,$vlan,$token,$crmUrl)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $html = [];
        $user_id = '';
        $net_sector_id = '';
        $pool_name = '';
        $Mikrotik = '';
        $Virtual_Port_id = '';
        $net_ip = '';
        $net_tariff = '';
        $oltIP = $ip;

        $First_Json = self::Airsoft_User_Search($token,$ab_nom,$crmUrl);
        $Dcoded     = json_decode($First_Json,true);
 
        if($Dcoded['status'] !== 0)
        {
            $user_id    = $Dcoded['id'];
            $net_tariff = $Dcoded['tariff'];  
  
            $Second_Json = self::Airsoft_Net_Sector_Id($token,$ip,$crmUrl);
            $SecDcoded   = json_decode($Second_Json,true);
 
            if($SecDcoded['status'] !== 0)
            {
                $net_sector_id = $SecDcoded['id'];
            }
            else
            {
                return response()->json(['error' => 'net_sector_id არის null']);
            }
            
        }
        else
        {
            return response()->json(['error' => 'user_id ან tariff არის null']);
        }
        
        if(!empty($net_sector_id))
        {
            $Final_Json     = self::Airsoft_Net_Virtual_Port_Id($vlan,$net_sector_id,$token,$crmUrl);
            $SFinalDcoded   = json_decode($Final_Json,true);  
            
            if($SFinalDcoded['status'] !== 0)
            {
                $Mikrotik = '';
                $pool_name = '';
                foreach ($SFinalDcoded['info'] as $key => $item)
                {
                    $id = $item['id'];
                    $port_name = $item['port_name'];
                    $Mikrotik  = trim($item['ip']);
                    $pool_name = trim( $item['pool_name']);

                    $itemZ = [];
                    $itemZ ['pool_name'] = trim($item['pool_name']);
                    $itemZ ['port_name'] = $port_name;
                    $itemZ ['id'] = $id;
                    $html  ['VirtualVlanList_'.$key] = $itemZ;
                }

                $credentials = DB::table('parameters')->where('type','mikrotik')->first();
                $username    = $credentials->username;
                $password    = $credentials->password;
    
                $API = new phpAPImodel();
                $API->debug = false;
            
                if ($API->connect($Mikrotik,$username, $password)) 
                {		 
                    $API->write('/ip/pool/print'); 
                    $READ_ONU  	= $API->read(false);
                    $result     = $API->parseResponse($READ_ONU);
                    $API->disconnect();                                                
                    
                    $pools = array();
                    foreach ($result as $item)
                    {
                        $tmp = array();
                        $tmp['name']   = trim($item['name']);
                        $tmp['ranges'] = trim($item['ranges']);
            
                        if (!$tmp['name'] || !$tmp['ranges']) { continue; }
                        $pools[$tmp['name']] = $tmp['ranges'];
                    }
    
    
    
                    $ranges = trim($pools[$pool_name]);   
                    if (!$ranges) { return array(); }
    
                    $ips   = self::getIPListFromRanges($ranges);  
                    $mkIPs = self::getMKIPList($Mikrotik,$username, $password);   

                    $freeIPs = array();
                    foreach ($ips as $key => $ip)
                    {
                        if (array_key_exists($ip,$mkIPs)) {  continue; }  
                        $freeIPs[$key] = $ip;
                    }
                    $html['freeIPs'] = $freeIPs;
                }
                else
                {
                    return response()->json(['error' => $Mikrotik.' Mikrotik - ის იუზერი ან პაროლი არასწორია , ან კავშირის პრობელმაა']);
                }
            }
            else
            {
                return response()->json(['error' => 'virtual_port_id არის null']);
            }

        }
        else
        {
            return response()->json(['error' => 'net_sector_id არის null']);
        }

        $html['Mikrotik']       = $Mikrotik;
        $html['user_id']        = $user_id;
        $html['net_sector_id']  = $net_sector_id;
        $html['net_mac']        = $mac;
        $html['net_tariff']     = $net_tariff;
        $html['olt']            = $oltIP;

        return $html;
    }

    public static function FirstInstallAntennaCheck($ip,$mac,$ab_nom,$token,$crmUrl)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        $html = [];
        $user_id = '';
        $net_sector_id = '';
        $pool_name = '';
        $Mikrotik = '';
        $Virtual_Port_id = '';
        $net_ip = '';
        $net_tariff = '';
        $oltIP = $ip;

        $First_Json = self::Airsoft_User_Search($token,$ab_nom,$crmUrl);
        $Dcoded     = json_decode($First_Json,true);  
 
        if($Dcoded == null)
        {
            return response()->json(['error' => 'აბონენტი '.$ab_nom.' ვერ მოიძებნა AIRSOFT -ში']);
        }

        if($Dcoded['status'] !== 0)
        {
            $user_id    = $Dcoded['id'];
            $net_tariff = $Dcoded['tariff'];  
  
            $Second_Json = self::Airsoft_Net_Sector_Id($token,$ip,$crmUrl);
            $SecDcoded   = json_decode($Second_Json,true);
 
            if($SecDcoded['status'] !== 0)
            {
                $net_sector_id = $SecDcoded['id'];
            }
            else
            {
                return response()->json(['error' => 'net_sector_id არის null']);
            }
            
        }
        else
        {
            return response()->json(['error' => 'user_id ან tariff არის null']);
        }
        
        if(!empty($net_sector_id))
        {
            $Final_Json     = self::Airsoft_Antenna_Net_Virtual_Port_Id($net_sector_id,$token,$crmUrl);
            $SFinalDcoded   = json_decode($Final_Json,true);  
            
            if($SFinalDcoded['status'] !== 0)
            {
                $Mikrotik = '';
                $pool_name = '';
                foreach ($SFinalDcoded['info'] as $key => $item)
                {
                    $id = $item['id'];
                    $port_name = $item['port_name'];
                    $Mikrotik  = trim($item['ip']);
                    $pool_name = trim( $item['pool_name']);

                    $itemZ = [];
                    $itemZ ['pool_name'] = trim($item['pool_name']);
                    $itemZ ['port_name'] = $port_name;
                    $itemZ ['id'] = $id;
                    $html  ['VirtualVlanList_'.$key] = $itemZ;
                }

                $credentials = DB::table('parameters')->where('type','mikrotik')->first();
                $username    = $credentials->username;
                $password    = $credentials->password;
    
                $API = new phpAPImodel();
                $API->debug = false;
            
                if ($API->connect($Mikrotik,$username, $password)) 
                {		 
                    $API->write('/ip/pool/print'); 
                    $READ_ONU  	= $API->read(false);
                    $result     = $API->parseResponse($READ_ONU);
                    $API->disconnect();                                           
                    
                    $pools = array();
                    foreach ($result as $item)
                    {
                        $tmp = array();
                        $tmp['name']   = trim($item['name']);
                        $tmp['ranges'] = trim($item['ranges']);
            
                        if (!$tmp['name'] || !$tmp['ranges']) { continue; }
                        $pools[$tmp['name']] = $tmp['ranges'];
                    }
    
    
    
                    $ranges = trim($pools[$pool_name]);   
                    if (!$ranges) { return array(); }
    
                    $ips   = self::getIPListFromRanges($ranges);  
                    $mkIPs = self::getMKIPList($Mikrotik,$username, $password);   

                    $freeIPs = array();
                    foreach ($ips as $key => $ip)
                    {
                        if (array_key_exists($ip,$mkIPs)) {  continue; }  
                        $freeIPs[$key] = $ip;
                    }
                    $html['freeIPs'] = $freeIPs;
                    
                }
                else
                {
                    return response()->json(['error' => $Mikrotik.' Mikrotik - ის იუზერი ან პაროლი არასწორია , ან კავშირის პრობელმაა']);
                }
            }
            else
            {
                return response()->json(['error' => 'virtual_port_id არის null']);
            }

        }
        else
        {
            return response()->json(['error' => 'net_sector_id არის null']);
        }

        $html['Mikrotik']       = $Mikrotik;
        $html['user_id']        = $user_id;
        $html['net_sector_id']  = $net_sector_id;
        $html['net_mac']        = $mac;
        $html['net_tariff']     = $net_tariff;
        $html['olt']            = $oltIP;

        return $html;
    }

    static public function AIRSOFT_ADD_TVMAC($user,$tvmac)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $jsonString = airsoftModel::ab_search($user);
        $userArray  = json_decode($jsonString, true);  

         
        if (isset($userArray['tv']) && isset($userArray['user_id']) && isset($userArray['tvmac'])) 
        {      
            if($user == $userArray['user_id']) 
            {
                $creds = DB::table('parameters')->where('type','airsoft')->first();
                $token  =  $creds->username;
                $crmUrl =  $creds->url;

                $LastArray = [];
                $LastArray ['tv'] = 1;
                 


                if($userArray['tvmac'] == '')
                { 
                    $LastArray ['tvmac'] = $tvmac;
                    $LastArray ['tvmac_additional'] = $userArray['tvmac_additional'];
                    $LastArray ['tv_youtube'] = $userArray['tv_youtube'];           
                }
                else
                {
                    $LastArray ['tvmac'] = $userArray['tvmac'];
                    if($userArray['tvmac_additional'] == '')
                    {
                        $LastArray ['tvmac_additional'] = $tvmac;
                    }
                    else
                    {
                        $LastArray ['tvmac_additional'] = $userArray['tvmac_additional'] .','.$tvmac;
                    }       
                } 
                    $LastArray ['tv_youtube'] = 1;

                    try {
                            $LastArrayJson = json_encode($LastArray);  
                            $ch = curl_init();             
                            curl_setopt_array($ch, array(
                                CURLOPT_URL => $crmUrl.'/restapi/user/',    
                                CURLOPT_RETURNTRANSFER => true,
                                CURLOPT_ENCODING => '',
                                CURLOPT_MAXREDIRS => 10,
                                CURLOPT_TIMEOUT => 0,
                                CURLOPT_FOLLOWLOCATION => true,
                                CURLOPT_SSL_VERIFYPEER => false,
                                CURLOPT_SSL_VERIFYHOST => false,
                                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                                CURLOPT_CUSTOMREQUEST => 'GET',
                                CURLOPT_POSTFIELDS =>'{"id": '.(int)$user.' , "request":"tv","info":'.$LastArrayJson.'}',
                                CURLOPT_HTTPHEADER => array(
                                'API-Key: '.$token,
                                'Content-Type: text/plain'
                                ),
                            ));
            
                            $response = curl_exec($ch);   
                            if(curl_errno($ch))
                            {
                                return response()->json(['error' => curl_error($ch)]);
                            }
                            curl_close($ch);   
                
                        return true;
                    } 
                    catch (\Exception $e) 
                    {
                        return response()->json(['error' => $e->getMessage()]);
                    }
                     


                
            }
            else
            {
                return response()->json(['error' => 'აბონენტის ნომერი არ დაემთხვა AIRSOFT - ში TV MAC ის ჩაწერის ეტაპზე']);
            }
          
        }
        else
        {
            return response()->json(['error' => 'AIRSOFT - დან მოსულ  ინფორმაციაში არ მოიძებნა ტელევიზიის პარამეტრები']);
        }
       
            
    }

    public static function AIRSOFT_FINISH_INSTALL($ab_nom,$olt,$mikrotik,$mac,$net_virtual_port_id,$net_sector_id,$clientIP,$gps,$net_tariff,$url,$username,$password,$user_id,$token,$noc,$nocIp,$vlanName,$comment = ' ')
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        self::AIRSOFT_REMOVE_USER($user_id,$url,$username,$password);
        try{

            $virtualPort = explode('|',$net_virtual_port_id);

            $curl = curl_init();  
            curl_setopt($curl, CURLOPT_URL, $url."/index.php?c=abonents&act=NETUserAdd");
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_AUTOREFERER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, array(
            'auth_process_authorization'    => '1',
            'user_login'                    => $username, 
            'user_pass'                     => $password,
            'sb'                            => 'შეცვლა',
        
            'user_id' => $user_id,
            'net_sector_id' => $net_sector_id,
            'net_virtual_port_id' => trim($virtualPort[0]),
            'net_ip' => $clientIP,
            'net_mac' => strtoupper($mac),
            'net_tariff' => $net_tariff
            ));

            if (curl_error($curl)) 
            {
                return response()->json(['error' => curl_error($curl)]);
            }

            if(curl_exec($curl))
            {
                $data = json_decode(curl_exec($curl), true, 512, JSON_UNESCAPED_UNICODE);
                if (json_last_error() === JSON_ERROR_NONE) 
                {
                    $msg = $data['msg'];
                    return response()->json(['error' => $msg]);
                } 
                else 
                {
                    return response()->json(['error' => 'JSON parsing error '.json_last_error_msg()]);
                }
            }
            else 
            {
                if($gps !== 'N/A')self::AIRSOFT_GPS($gps,$ab_nom,$url,$token);
                Log::channel('install')->emergency('[AIRSOFT] '.$ab_nom 
                .'\n[ნოკი] '.$noc
                .'\n[ნოკის აიპი] '.$nocIp
                .'\n[ოელტე] '.$olt
                .'\n[პონ/პორტი] '.$vlanName
                .'\n[გასტატიკებული მაკი] '.$mac
                .'\n[ონუს აიპი მიასამართი] '.$clientIP
                .'\n[მიკროტიკის აიპი მიასამართი] '.$mikrotik
                .'\n[კოორდინატები] '.$gps
                .'\n[კომენტარი] '.$comment
                );
                return true;  
            }
   
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }

    }

    public static function AIRSOFT_REMOVE_USER($ab_id,$url,$username,$password)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        try {
                $urlAirsoft = $url.'/index.php?c=abonents&act=NETUserDisable&id='.$ab_id;       
                $curl = curl_init();  
                curl_setopt($curl, CURLOPT_URL, $urlAirsoft);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
                curl_setopt($curl, CURLOPT_AUTOREFERER, true);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_POST, true);
                curl_setopt($curl, CURLOPT_POSTFIELDS, array(
                'auth_process_authorization'    => '1',
                'user_login'                    => $username, 
                'user_pass'                     => $password,
                'sb'                            => 'შეცვლა'
                ));
                $Data = curl_exec($curl);
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    public static function AIRSOFT_PON_CHANGE($Mikrotik,$net_sector_id,$ponID,$token,$crmUrl)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        if( !empty($ponID) && !empty($Mikrotik) && !empty($net_sector_id))
        {
            $FixedVID = explode('|', $ponID);

            $credentials = DB::table('parameters')->where('type','mikrotik')->first();
            $username    = $credentials->username;
            $password    = $credentials->password;


            $API = new phpAPImodel();
            $API->debug = false;
        
            if ($API->connect($Mikrotik,$username, $password)) 
            {		 
                $API->write('/ip/pool/print'); 
                $READ_ONU  	= $API->read(false);
                $result     = $API->parseResponse($READ_ONU);
                $API->disconnect();                                               

                $pools = array();
                foreach ($result as $item)
                {
                    $tmp = array();
                    $tmp['name']   = trim($item['name']);
                    $tmp['ranges'] = trim($item['ranges']);
        
                    if (!$tmp['name'] || !$tmp['ranges']) { continue; }
                    $pools[$tmp['name']] = $tmp['ranges'];
                }
            
                $ranges = trim($pools[$FixedVID[1]]);  
                if (!$ranges) { return array(); }
        
                $ips   = self::getIPListFromRanges($ranges);  
                $mkIPs = self::getMKIPList($Mikrotik,$username,$password);   
        
                $freeIPs = array();
                foreach ($ips as $ip)
                {
                    if (array_key_exists($ip,$mkIPs)) {  continue; }  
                    $freeIPs[] = $ip;
                }

                return $freeIPs;
            }
        }
        else 
        {
            return response()->json(['error' => 'Misssing Arguments In Virtual_Pon_Change , net_sector_id , Mikrotik [AIRSOFT]']);
        }
    }

    static public function getMKIPList($server,$username, $password)
    {

        $API = new phpAPImodel();
        $API->debug = false;

        if ($API->connect($server, $username, $password)) 
        {		 
            $API->write('/ip/arp/print'); 
            $READ_ONU  = $API->read(false);
            $ips       = $API->parseResponse($READ_ONU);
            $API->disconnect();
        }

        $all = array();

        foreach ($ips as $item)
        {
            $tmp = array(
                'id'=>$item['.id'],
                'address'=>$item['address'],
                //'mac-address'=>$item['mac-address'],
                //'interface'=>$item['interface'],
                // 'published'=>$item['published'],
                // 'invalid'=>$item['invalid'],
                // 'DHCP'=>$item['DHCP'],
                //'dynamic'=>$item['dynamic'],
                // 'disabled'=>$item['disabled'],
                // 'comment'=>$item['comment'],
            );
            if (!trim($tmp['address'])) { continue; }

            $all[$tmp['address']] = $tmp;
        }
        return $all;
    }

    static public function getIPListFromRanges($ranges)
    {
        list($rangeStart,$rangeEnd) = explode("-",$ranges);
        $rangeStart = trim($rangeStart);
        $rangeEnd = trim($rangeEnd);

        list($sTr1,$sTr2,$sTr3,$sTr4) = explode(".",$rangeStart);
        list($eTr1,$eTr2,$eTr3,$eTr4) = explode(".",$rangeEnd);

        $ips = array();
        for ($i=$sTr4;$i<=$eTr4;$i++)
        {
            $ips[] = $sTr1.'.'.$sTr2.'.'.$sTr3.'.'.$i;
        }
        return $ips;
    }

    static public function Airsoft_Net_Virtual_Port_Id($vlan,$net_sector_id,$token,$crmUrl)
    {
        try {
            $ch = curl_init();             
            curl_setopt_array($ch, array(
                CURLOPT_URL => $crmUrl.'/restapi/finder.php',    
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS =>'{"request":"virtual","vlan":'.$vlan.',"sector_id":'.$net_sector_id.'}',     
                CURLOPT_HTTPHEADER => array(
                  'API-Key: '.$token,
                  'Content-Type: text/plain'
                ),
              ));

            $response = curl_exec($ch);   
            if(curl_errno($ch))
            {
                return response()->json(['error' => curl_error($ch)]);
            }
            curl_close($ch);  
            return   $response;
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }

    static public function Airsoft_Antenna_Net_Virtual_Port_Id($net_sector_id,$token,$crmUrl)
    {
        try {
            $ch = curl_init();             
            curl_setopt_array($ch, array(
                CURLOPT_URL => $crmUrl.'/restapi/finder.php',    
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER => false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS =>'{"request":"first_virtual","sector_id":'.$net_sector_id.'}',     
                CURLOPT_HTTPHEADER => array(
                  'API-Key: '.$token,
                  'Content-Type: text/plain'
                ),
              ));

            $response = curl_exec($ch);   
            if(curl_errno($ch))
            {
                return response()->json(['error' => curl_error($ch)]);
            }
            curl_close($ch);  
            return   $response;
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
        
    }
    
    static public function Airsoft_Only_OLT_Save($ab_nom,$token,$ip,$crmUrl)
    {
 
        try {
                $ch = curl_init();             
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $crmUrl.'/restapi/user/',    
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS =>'{"request":"sector","id":'.(int)$ab_nom.',"info":"'.$ip.'"}',
                    CURLOPT_HTTPHEADER => array(
                    'API-Key: '.$token,
                    'Content-Type: text/plain'
                    ),
                ));

                $response = curl_exec($ch);

                $response_data  = json_decode($response, true);
                if ($response_data === null) 
                {
                    return response()->json(['error' => "Error decoding JSON: " . json_last_error_msg()]);
                }
                else 
                {
                    if ($response_data['code'] === 0) 
                    {
                        return "true";
                    } 
                    else 
                    {
                        return response()->json(['error' => $response_data['status']]);  
                    }
                }
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    static public function Airsoft_Only_EthernetPort_Save($ab_nom,$port,$token,$ip,$crmUrl)
    {
 
        try {
                $ch = curl_init();             
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $crmUrl.'/restapi/user/',    
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS =>'{"request":"antenna_port","id":'.(int)$ab_nom.',"info":'.(int)$port.'}',
                    CURLOPT_HTTPHEADER => array(
                    'API-Key: '.$token,
                    'Content-Type: text/plain'
                    ),
                ));

                $response = curl_exec($ch);

                $response_data  = json_decode($response, true);
                if ($response_data === null) 
                {
                    return response()->json(['error' => "Error decoding JSON: " . json_last_error_msg()]);
                }
                else 
                {
                    if ($response_data['code'] === 0) 
                    {
                        return "true";
                    } 
                    else 
                    {
                        return response()->json(['error' => $response_data['status']]);  
                    }
                }
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    static public function Airsoft_Net_Sector_Id($token,$ip,$crmUrl)
    {
        try {
                $ch = curl_init();             
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $crmUrl.'/restapi/finder.php',    
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS =>'{"request":"sector","ip":"'.$ip.'"}',
                    CURLOPT_HTTPHEADER => array(
                    'API-Key: '.$token,
                    'Content-Type: text/plain'
                    ),
                ));

                $response = curl_exec($ch);
                if(curl_errno($ch))
                {
                    return response()->json(['error' => curl_error($ch)]);
                }
                curl_close($ch);  
                return   $response;
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    static public function Airsoft_User_Search($token,$user,$crmUrl) 
    {
        try {
                $ch = curl_init();             
                curl_setopt_array($ch, array(
                    CURLOPT_URL => $crmUrl.'/restapi/finder.php',    
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS =>'{"request":"user","id":'.$user.'}',
                    CURLOPT_HTTPHEADER => array(
                    'API-Key: '.$token,
                    'Content-Type: text/plain'
                    ),
                ));

                $response = curl_exec($ch);
                if(curl_errno($ch))
                {
                    return response()->json(['error' => curl_error($ch)]);
                }
                curl_close($ch);   

            return   $response;
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }
    }

    static public function generateRandomHexString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }

    static public function AIRSOFT_GPS($gps,$ab_nom,$url,$token)
    {
        PrivilegesModel::PrivCheck('Priv_Install');

        if( !empty($ab_nom) && !empty($token) && !empty($gps))
        {
            try{
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $url.'/restapi/user/',  
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS =>'{"request":"gps","id":'.(int)$ab_nom.',"info":"'.$gps.'"}',
                    CURLOPT_HTTPHEADER => array(
                        'API-Key: '.$token
                    ),
                    ));

                    $response = curl_exec($curl);  
                    curl_close($curl);

                    $response_data  = json_decode($response, true);
                    if ($response_data === null) 
                    {
                        echo " Error decoding JSON: " . json_last_error_msg();
                    }
                    else 
                    {
                        if ($response_data['code'] === 0) 
                        {
                            return true;
                        } 
                        else 
                        {
                            return response()->json(['error' => $response_data['status']]);
                        }
                    }
            }
            catch (\Exception $e) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
        else 
        {
            return response()->json(['error' => 'Misssing Arguments In GPS Save [AIRSOFT]']);
        }
    }

    static public function FIBER($ab_nom,$url,$token,$mode = "1")
    {
        PrivilegesModel::PrivCheck('Priv_Install');

        if( !empty($ab_nom) && !empty($token) && !empty($url))
        {
            try{
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $url.'/restapi/user/',  
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_POSTFIELDS =>'{"request":"fiber","id":'.(int)$ab_nom.',"info":'.$mode.'}',
                    CURLOPT_HTTPHEADER => array(
                        'API-Key: '.$token
                    ),
                    ));

                    $response = curl_exec($curl);  
                    curl_close($curl);

                    $response_data  = json_decode($response, true);
                    if ($response_data === null) 
                    {
                        echo " Error decoding JSON: " . json_last_error_msg();
                    }
                    else 
                    {
                        if ($response_data['code'] === 0) 
                        {
                            return true;
                        } 
                        else 
                        {
                            return response()->json(['error' => $response_data['status']]);
                        }
                    }
            }
            catch (\Exception $e) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
        }
        else 
        {
            return response()->json(['error' => 'Misssing Arguments During Fiber Save [AIRSOFT]']);
        }
    }

    static public function NEW_GPS($url,$username,$password)
    {
            $html = [];
            $positions = array(); $devices = array();
    
            $creds = base64_encode($username.':'.$password);  
    
            try {
                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $url.'/api/positions',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                            'Authorization: Basic '.$creds
                    ),
                    ));
    
                    $positions = json_decode(curl_exec($curl), true); 
                    
                    curl_close($curl);
    
            } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }

            try {
                    $ch = curl_init();

                    curl_setopt_array($ch, array(
                    CURLOPT_URL => 'http://45.9.44.210:8082/api/devices',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => '',
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 0,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => 'GET',
                    CURLOPT_HTTPHEADER => array(
                        'API-Key: cc11e1a54fb9eced741c0ea699bf610581e274881b4064a87da2cec1e8e691d6',
                            'Authorization: Basic ZmluZGVyQGFpcmxpbmsuZ2U6XjN1VlJhMDVLQDhq'
                    ),
                    ));

                    $devices = json_decode(curl_exec($ch), true); 

                    curl_close($ch);
            } 
            catch (\Exception $e) 
            {
                return response()->json(['error' => $e->getMessage()]);
            }
    
            foreach ($devices as $key => $value) 
            {
                $coordinates = '';

                foreach ($positions as $keyz => $valuez) 
                {
                    if($value['id'] == $valuez['deviceId'])
                    {
                        $coordinates = $valuez['latitude'].' '.$valuez['longitude'];
                        break;
                    }
                }
                
                $item = [];
                $item ['name']          = $value['name'];
                $item ['coordinates']   = $coordinates;
                $html ['item_'.$key]    = $item;
            }
            return $html;
    }
}
