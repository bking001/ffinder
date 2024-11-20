<?php

namespace App\Http\Controllers;


use App\Models\OLTSIDE_HUAWEI;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB; 

use App\Models\globalsearch;
use App\Models\ZYXEL;
use App\Models\CISCO;
use App\Models\OLTSIDE_ZYXEL;
use App\Models\OLTSIDE_CISCO;
use App\Models\OLTSIDE_VSOLUTION;
use App\Models\OLTSIDE_HSGQ;
use App\Models\OLTSIDE_BDCOM;
use App\Models\OLTSIDE_ZTE;

use App\Models\Install\crm;
use App\Models\Install\antenna;
use App\Models\Install\ethernet;

use App\Models\Install\_bdcom;
use App\Models\Install\_vsolution;
use App\Models\Install\_hsgq;
use App\Models\Install\_zte;
use App\Models\Install\_huawei;
 
use App\Models\VSOLUTION;
use App\Models\HSGQ;
use App\Models\ZTE;
use App\Models\HUAWEI;
use App\Models\BDCOM;
use App\Models\sshModel;

 
 

class INSTALLController extends Controller
{
    static public function oltList(REQUEST $request)
    {
        $validator = validator()->make($request->only('macSn'), [
            'macSn' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $username = $request->user()->name;
        $userIp   = $request->ip();
        $macSN    = $request->input('macSn');
        Log::channel('actions')->info('[Global Search] '.$macSN .'\n[User] '.$username.'\n[Address] '.$userIp);

        $data = DB::table('devices')->get();
 
        $arrayList = [];
        foreach ($data as $item) 
        {
            if($item->Type == 'BDCOM' || $item->Type == 'HUAWEI' || $item->Type == 'ZTE' || $item->Type == 'VSOLUTION' || $item->Type == 'HSGQ')
            {
                $arrayList [] = $item->Address;
            }
        }

        return response()->json($arrayList);
    }

    static public function oltList_autofind(REQUEST $request)
    {

        $data = DB::table('devices')->get();
 
        $arrayList = [];
        foreach ($data as $item) 
        {
            if($item->Type == 'HUAWEI' || $item->Type == 'ZTE')
            {
                $arrayList [] = $item->Address;
            }
        }

        return response()->json($arrayList);
    }

    static public function GlobalSearch(REQUEST $request)
    {
        $validator = validator()->make($request->only('macSN','ip'), [
            'macSN' => 'required|string',
            'ip'    => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $macSN = $request->input('macSN');
        $ip    = $request->input('ip');

        $DB = DB::table('devices')
        ->where('Address', $ip)
        ->get();

        $username = $request->user()->name;
        $userIp   = $request->ip();
 
        foreach ($DB as $device) 
        {
            if($device->Type == 'BDCOM')return globalsearch::BDCOM($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            else if($device->Type == 'ZTE')return globalsearch::ZTE($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            else if($device->Type == 'HUAWEI')return globalsearch::HUAWEI($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            else if($device->Type == 'VSOLUTION')return globalsearch::VSOLUTION($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            else if($device->Type == 'HSGQ')
            {
                $Token = DB::table('parameters')->where('type','hsgq')->first();
                return globalsearch::HSGQ($device->Address,$Token->password,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            }
        }
        return response()->json(['error' => 'OLT with this '.$ip.' IP address cannot find in database']);
    }

    static public function GpsAddCRM(REQUEST $request)
    {
        $validator = validator()->make($request->only('client','coordinates','comment'), [
            'client'        => 'required|string',
            'coordinates'   => 'required|string',
            'comment'       => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $client  = $request->input('client');
        $gps     = $request->input('coordinates');
        $comment = $request->input('comment');

        $api = DB::table('parameters')->where('type','airsoft')->first();  
        $token       =  $api->username;
        $urlAirsoft  =  $api->url; 

        $username = $request->user()->name;
        $userIp   = $request->ip();
   
        Log::channel('actions')->notice('[Coordinates Uptade] '.$client 
        .'\n[ნოკი] '.$username
        .'\n[ნოკის აიპი] '.$userIp
        .'\n[იუზერი] '.$client
        .'\n[კოორდინატები] '.$gps
        .'\n[კომენტარი] '.$comment
        );
 
        

        if($gps !== 'N/A')return crm::AIRSOFT_GPS($gps,$client,$urlAirsoft,$token);
    }

    ////////////////////////////////////////////////////////////////////////////////    U N I N S T A L
     
    static public function uninstall_autofind(REQUEST $request)
    {
        $validator = validator()->make($request->only('ab_nom','ip'), [
            'ab_nom' => 'required|string',
            'ip'     => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ab_nom = $request->input('ab_nom');
        $ip     = $request->input('ip');


        $DB = DB::table('devices')
        ->where('Address', $ip)
        ->get();

        
       foreach ($DB as $device)  
       {
            if($device->Type == 'BDCOM')return BDCOM::Uninstall_Side_OnuInfo($ip,$device->snmpRcomunity,$device->snmpWcomunity,$ab_nom,$device->device_name);  
            else if($device->Type == 'ZTE')return ZTE::Uninstall_Side_OnuInfo($ip,$device->snmpRcomunity,$device->snmpWcomunity,$ab_nom,$device->device_name);
            else if($device->Type == 'HUAWEI')return HUAWEI::Uninstall_Side_OnuInfo($ip,$device->snmpRcomunity,$device->snmpWcomunity,$ab_nom,$device->device_name);
            else if($device->Type == 'VSOLUTION')return VSOLUTION::Uninstall_Side_OnuInfo($ip,$device->snmpRcomunity,$device->snmpWcomunity,$ab_nom,$device->device_name);
            else if($device->Type == 'HSGQ')
            {
                $Token = DB::table('parameters')->where('type','hsgq')->first();
                return HSGQ::Uninstall_Side_OnuInfo($ip,$Token->password,$ab_nom,$device->device_name);
            }
        }

    }

    static public function uninstall_finish(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip','ifindex' ,'ab_nom'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'ab_nom'    => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $ifindex  = $request->input('ifindex');
        $user     = $request->input('ab_nom');

        $username = $request->user()->name;
        $userIp   = $request->ip();
        
        Log::channel('actions')->error('[Onu Uninstall] '.$user .'\n[OLT] '.$ip.'\n[User] '.$username.'\n[Address] '.$userIp);

        $DB = DB::table('devices')->where('Address', $ip)->first();
 
        if($DB->Type == 'BDCOM')
        {
            return  OLTSIDE_BDCOM::OLT_SIDE_ONU_UNINSTALL($ip,$DB->snmpRcomunity,$DB->snmpWcomunity,$ifindex);
        }
        else if($DB->Type == 'ZTE')
        {
            return  OLTSIDE_ZTE::OLT_SIDE_ONU_UNINSTALL($ip,$DB->snmpRcomunity,$DB->snmpWcomunity,$ifindex);
        }
        else if($DB->Type == 'HUAWEI')
        {
            return  OLTSIDE_HUAWEI::OLT_SIDE_ONU_UNINSTALL($ip,$DB->snmpRcomunity,$DB->snmpWcomunity,$ifindex);
        }
        else if($DB->Type == 'HSGQ')
        {
           $Token = DB::table('parameters')->where('type','hsgq')->first();
           return OLTSIDE_HSGQ::OLT_SIDE_ONU_UNINSTALL($ip,$Token->password,$ifindex);
        }
        else if($DB->Type == 'VSOLUTION')
        {
            return OLTSIDE_VSOLUTION::OLT_SIDE_ONU_UNINSTALL($ip,$DB->snmpRcomunity,$DB->snmpWcomunity,$ifindex);
        }
        else
        {
            return response()->json(['error' => 'OLT with this '.$ip.' IP address cannot find in database']);
        }
    
    }

    ////////////////////////////////////////////////////////////////////////////////    A I R S O F T

    static public function InstallMACSNSearch(REQUEST $request)
    {
        $validator = validator()->make($request->only('macSN','ip'), [
            'macSN' => 'required|string',
            'ip'    => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $macSN = $request->input('macSN');
        $ip    = $request->input('ip');

        $DB = DB::table('devices')
        ->where('Address', $ip)
        ->get();

        $username = $request->user()->name;
        $userIp   = $request->ip();
 
        foreach ($DB as $device)  
        {
            if($device->Type == 'BDCOM')return _bdcom::BDCOM_SEARCH($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            else if($device->Type == 'ZTE')return _zte::ZTE_SEARCH($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            else if($device->Type == 'HUAWEI')return _huawei::HUAWEI_SEARCH($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            else if($device->Type == 'VSOLUTION')return _vsolution::VSOLUTION_SEARCH($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            else if($device->Type == 'HSGQ')
            {
                $Token = DB::table('parameters')->where('type','hsgq')->first();
                return globalsearch::HSGQ($device->Address,$Token->password,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp,$macSN);
            }
        }
        return response()->json(['error' => 'OLT with this '.$ip.' IP address cannot find in database']);
    }

    static public function InstallGponAutofind(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip'), [
            'ip'    => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip    = $request->input('ip');

        $DB = DB::table('devices')
        ->where('Address', $ip)
        ->get();

        $username = $request->user()->name;
        $userIp   = $request->ip();
 
        foreach ($DB as $device)  
        {
            if($device->Type == 'HUAWEI')return _huawei::HUAWEI_AUTOFIND($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp);
            else if($device->Type == 'ZTE')return _zte::ZTE_AUTOFIND($device->Address,$device->snmpRcomunity,$device->snmpWcomunity,$device->Username,$device->Pass,$username,$userIp);
        }

        return response()->json(['error' => 'OLT with this '.$ip.' IP address cannot find in database']);
    }
     

    static public function AirsoftInstallFirst(REQUEST $request)
    {   
        $validator = validator()->make($request->only('ab_nom','mac','ip','vlan'), [
            'mac'       => 'required|mac_address',
            'ip'        => 'required|ipv4',
            'ab_nom'    => 'required|string',
            'vlan'      => 'required|numeric'
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $mac        = $request->input('mac');
        $ip         = $request->input('ip');
        $ab_nom     = $request->input('ab_nom');
        $vlan       = $request->input('vlan');

        $token = DB::table('parameters')->where('type','airsoft')->first();
        $link  =  $token->url.'/restapi/finder.php';   
 

        return  crm::FirstInstallCheck($ip,$mac,$ab_nom,$vlan, $token->password,$link);
    }

    static public function AirsoftAntennaInstallFirst(REQUEST $request)
    {   
        $validator = validator()->make($request->only('ab_nom','mac','ip'), [
            'mac'       => 'required|mac_address',
            'ip'        => 'required|ipv4',
            'ab_nom'    => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $mac        = $request->input('mac');
        $ip         = $request->input('ip');
        $ab_nom     = $request->input('ab_nom');
        $vlan       = $request->input('vlan');

        $token = DB::table('parameters')->where('type','airsoft')->first();
        $link  =  $token->url.'/restapi/finder.php';   
 
        return  crm::FirstInstallAntennaCheck($ip,$mac,$ab_nom, $token->password,$link);
    }
     
     
    static public function AirsoftInstallponID(REQUEST $request)
    {   
        $validator = validator()->make($request->only('ponID','Mikrotik','net_sector_id'), [
            'ponID'         => 'required|string',
            'Mikrotik'      => 'required|ipv4',
            'net_sector_id' => 'required|numeric'
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ponID          = $request->input('ponID');
        $Mikrotik       = $request->input('Mikrotik');
        $net_sector_id  = $request->input('net_sector_id');

        $token = DB::table('parameters')->where('type','airsoft')->first();
        $link  =  $token->url.'/restapi/finder.php';   
 
        return  crm::AIRSOFT_PON_CHANGE($Mikrotik,$net_sector_id,$ponID, $token->password,$link);
    }

    static public function AirsoftInstallFinish(REQUEST $request)
    {   
        $validator = validator()->make($request->only('ab_nom','olt','mikrotik','mac','net_virtual_port_id','net_sector_id','clientIP','gps','net_tariff','user_id','vlanName','comment'), [
            'ab_nom'                => 'required|numeric',
            'olt'                   => 'required|ipv4',
            'mikrotik'              => 'required|ipv4',
            'mac'                   => 'required|mac_address',
            'net_virtual_port_id'   => 'required|string',
            'net_sector_id'         => 'required|numeric',
            'clientIP'              => 'required|ipv4',
            'gps'                   => 'required|string',
            'net_tariff'            => 'required|numeric',
            'user_id'               => 'required|numeric',
            'vlanName'              => 'required|string',
            'comment'               => 'sometimes|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ab_nom                 = $request->input('ab_nom');
        $olt                    = $request->input('olt');
        $mikrotik               = $request->input('mikrotik');
        $mac                    = $request->input('mac');
        $net_virtual_port_id    = $request->input('net_virtual_port_id');
        $net_sector_id          = $request->input('net_sector_id');
        $clientIP               = $request->input('clientIP');
        $gps                    = $request->input('gps');
        $net_tariff             = $request->input('net_tariff');
        $user_id                = $request->input('user_id');
        $vlanName               = $request->input('vlanName');


        $token = DB::table('parameters')->where('type','airsoft2')->first();
        $url       =  $token->url; 
        $username  =  $token->username;   
        $password  =  $token->password;   

        $token = DB::table('parameters')->where('type','airsoft')->first();
        $token  =  $token->username;

        $noc = $request->user()->name;
        $nocIp   = $request->ip();


        $comment               = $request->input('comment');
  
        return  crm::AIRSOFT_FINISH_INSTALL($ab_nom,$olt,$mikrotik,$mac,$net_virtual_port_id,$net_sector_id,$clientIP,$gps,$net_tariff,$url,$username,$password,$user_id,$token,$noc,$nocIp,$vlanName,$comment);
    }

    static public function GPS()
    {   
        $token = DB::table('parameters')->where('type','gps')->first();
        $url       =  $token->url;
        $username  =  $token->username;   
        $password  =  $token->password;   

        return  crm::NEW_GPS($url,$username,$password);
    }
    
    ////////////////////////////////////////////////////////////////////////////////    A N T E N N A

    static public function andzebi()
    {
        return antenna::MastList();
    }
     
    static public function choosenMast(REQUEST $request)
    {
        $validator = validator()->make($request->only('mastID'), [
            'mastID'  => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $mastID = $request->input('mastID');
 
        return  antenna::sectorChoosenMast($mastID);
    }
     
    static public function SearchByCustomer(REQUEST $request)
    {
        $validator = validator()->make($request->only('customer'), [
            'customer'  => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $customer = $request->input('customer');
 
        return  antenna::sectorCustomerSearch($customer);
    }

    static public function SearchBysectorMac(REQUEST $request)
    {
        $validator = validator()->make($request->only('antenis_mac'), [
            'antenis_mac'  => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $antenis_mac = $request->input('antenis_mac');
 
        return  antenna::sectorMacSearch($antenis_mac);
    }

    static public function SearchBysectorName(REQUEST $request)
    {
        $validator = validator()->make($request->only('sectoris_saxeli'), [
            'sectoris_saxeli'  => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $sectoris_saxeli = $request->input('sectoris_saxeli');
 
        return  antenna::sectorNameSearch($sectoris_saxeli);
    }
     
    static public function SearchBysectorIP(REQUEST $request)
    {
        $validator = validator()->make($request->only('antenis_ip'), [
            'antenis_ip'  => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $antenis_ip = $request->input('antenis_ip');
 
        return  antenna::sectorIPSearch($antenis_ip);
    }

    static public function SectorInfo(REQUEST $request)
    {
        $validator = validator()->make($request->only('SectorID'), [
            'SectorID'  => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $SectorID = $request->input('SectorID');
 
        return  antenna::SectorById($SectorID);
    }
      
    ////////////////////////////////////////////////////////////////////////////////    E T H E R N E T
    
    static public function EthernetAndzebi()
    {
        $html = [];
        $credentials = DB::table('andzebi')->get();
 
        foreach ($credentials as $key => $item)
        {
            $tmp = [];
            $tmp['id']   = $item->id; 
            $tmp['name'] = $item->saxeli; 
            $html['SectorList'.$key] = $tmp;
        }

        return response()->json($html);
    }

    static public function EthernetSwitches(REQUEST $request)
    {
        $validator = validator()->make($request->only('switchID'), [
            'switchID'  => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $switchID = $request->input('switchID');  

        $html = [];
        $credentials = DB::table('devices')->where('type','ZYXEL')->orWhere('type', 'CISCO_CATALYST')->get();
 
        foreach ($credentials as $key => $item)
        {
            if (strpos( $item->mast,$switchID) !== false) 
            {
                $tmp = [];
                $tmp['id']         = $item->id ; 
                $tmp['Address']     = $item->Address; 
                $tmp['device_name'] = $item->device_name; 
                $html['SwitchList'.$key] = $tmp;
            }
        }

        return response()->json($html);
    }

    static public function EthernetSwitchData(Request $request)
    {
        $validator = validator()->make($request->only('address'), [
            'address' => 'required|ipv4',

        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $address  = $request->input('address');

        $credentials = DB::table('devices')->where('Address',$address)->first();


        if($credentials->Type == 'ZYXEL')
        {
            return  ZYXEL::ClientSideSwitchData($credentials->Address,$credentials->snmpRcomunity,$credentials->Username,$credentials->Pass);
        }
        else if($credentials->Type == 'CISCO_CATALYST')
        {
            return  CISCO::ClientSideSwitchData($credentials->Address,$credentials->snmpRcomunity,$credentials->Username,$credentials->Pass);
        }
        else
        {
            return response()->json(['error' => 'ვერ მოხერხდა სვიჩის ტიპის დადგენა']);
        }
       
    } 

    static public function ZyxelEthernetPortAdminOff(Request $request)
    {
        $validator = validator()->make($request->only('user', 'ip' ,'port'), [
            'user'      => 'required|string',
            'ip'        => 'required|ipv4',
            'port'      => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $port       = $request->input('port');
        $user       = $request->input('user');


        $credentials = DB::table('devices')->where('Address',$ip)->first();
 

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Zyxel Port Admin Statu Off] '.$user .'\n[Port] '.$port.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  ZYXEL::AdminPortOff($ip,$credentials->snmpWcomunity,$port);
    }

    static public function ZyxelEthernetPortAdminON(Request $request)
    {
        $validator = validator()->make($request->only('user', 'ip' ,'port'), [
            'user'      => 'required|string',
            'ip'        => 'required|ipv4',
            'port'      => 'required|string',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
       
         $ip         = $request->input('ip');
         $port       = $request->input('port');
         $user       = $request->input('user');

         $credentials = DB::table('devices')->where('Address',$ip)->first();
 
         $username = $request->user()->name;
         $userIp   = $request->ip();
         Log::channel('actions')->warning('[Zyxel Port Admin Statu On] '.$user .'\n[Port] '.$port.'\n[User] '.$username.'\n[Address] '.$userIp);
 
         return  ZYXEL::AdminPortON($ip,$credentials->snmpWcomunity,$port);
    }
     
    static public function ZyxelInstall(Request $request)  
    {
       $validator = validator()->make($request->only('ip', 'port','user','gps'), [
           'ip'        => 'required|ipv4',
           'port'      => 'required|string',
           'user'      => 'required|numeric',
           'gps'       => 'required|string',
       ]);

       if ($validator->fails())
       {
           $errors = $validator->errors()->all();
           return response()->json(['error' => $errors]);
       }
      
       $ip         = $request->input('ip');
       $port       = $request->input('port');
       $user       = $request->input('user');
       $gps        = $request->input('gps');

       $credentials = DB::table('devices')->where('Address',$ip)->first();

       $username = $request->user()->name;
       $userIp   = $request->ip();

       Log::channel('install')->notice('[ZYXEL] '.$user 
       .'\n[ნოკი] '.$username
       .'\n[ნოკის აიპი] '.$userIp
       .'\n[სვიჩი] '.$ip
       .'\n[იუზერი] '.$user
       .'\n[პორტი] '.$port
       .'\n[კოორდინატები] '.$gps
       );
 
       $api = DB::table('parameters')->where('type','airsoft')->first();  
       $token       =  $api->username;
       $urlAirsoft  =  $api->url; 
  
 
       if(OLTSIDE_ZYXEL::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$credentials->snmpWcomunity,$port,$user))
       {
         if($gps !== 'N/A')crm::AIRSOFT_GPS($gps,$user,$urlAirsoft,$token);
         if(crm::Airsoft_Only_OLT_Save($user,$token,$ip,$urlAirsoft))
         {
            crm::Airsoft_Only_EthernetPort_Save($user,$port,$token,$ip,$urlAirsoft); 
            //crm::FIBER($user,$urlAirsoft,$token,0);
         }
         else
         {
            return response()->json(['error' => 'ვერ მოხერხდა AIRSOFT - ში  სვიჩის აიპის დამახსოვრება']);
         }
                                    
         return response()->json(['username' => $credentials->Username,'password' => $credentials->Pass,'read' => Crypt::encrypt($credentials->snmpRcomunity),'write' => Crypt::encrypt($credentials->snmpWcomunity)]);
       }
       else
       {    
         return response()->json(['error' => 'ვერ მოხერხდა პორტზე აბონენტის ნომრის მინიჭება']);
       }
       
    }

    static public function SwitchesListInstall(REQUEST $request)
    {
        $data = DB::table('devices')->get();
 
        $arrayList = [];
        foreach ($data as $item) 
        {
            if($item->Type == 'ZYXEL' || $item->Type == 'CISCO_CATALYST')
            {
                $arrayList [] = $item->Address;
            }
        }
        return response()->json($arrayList);
    }

    static public function FindRooMInstall(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip','ab_nom'), [
            'ab_nom' => 'required|string',
            'ip'     => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        $ip         = $request->input('ip');
        $ab_nom     = $request->input('ab_nom');

        $DB = DB::table('devices')
        ->where('Address', $ip)
        ->get();

        foreach ($DB as $device) 
        {            
            if($device->Type == 'ZYXEL')return  ZYXEL::Client_Side_Info($ip,$device->Username,$device->Pass,$device->snmpRcomunity,$device->snmpWcomunity,$ab_nom);
            else if($device->Type == 'CISCO_CATALYST')return  CISCO::Client_Side_Info($ip,$device->Username,$device->Pass,$device->snmpRcomunity,$device->snmpWcomunity,$ab_nom);
        }

        return response()->json(['error' => 'Switch with this '.$ip.' IP address cannot find in database']);
    }
     
    static public function CiscoEthernetPortAdminOff(Request $request)
    {
        $validator = validator()->make($request->only('user', 'ip' ,'ifindex' , 'portName'), [
            'user'      => 'required|string',
            'portName'  => 'required|string',
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portName   = $request->input('portName');
        $user       = $request->input('user');


        $credentials = DB::table('devices')->where('Address',$ip)->first();
 

        $username = $request->user()->name;
        $userIp   = $request->ip();
        Log::channel('actions')->warning('[Cisco Port Admin Statu Off] '.$user .'\n[Port] '.$portName.'\n[User] '.$username.'\n[Address] '.$userIp);

        
        return  CISCO::AdminPortOff($ip,$credentials->snmpWcomunity,$ifindex);
    }

    static public function CiscoEthernetPortAdminON(Request $request)
    {
        $validator = validator()->make($request->only('user', 'ip' ,'ifindex' , 'portName'), [
            'user'      => 'required|string',
            'portName'  => 'required|string',
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|numeric',
         ]);
 
         if ($validator->fails())
         {
             $errors = $validator->errors()->all();
             return response()->json(['error' => $errors]);
         }
       
         $ip         = $request->input('ip');
         $ifindex    = $request->input('ifindex');
         $portName   = $request->input('portName');
         $user       = $request->input('user');

         $credentials = DB::table('devices')->where('Address',$ip)->first();
 
         $username = $request->user()->name;
         $userIp   = $request->ip();
         Log::channel('actions')->warning('[Cisco Port Admin Statu On] '.$user .'\n[Port] '.$portName.'\n[User] '.$username.'\n[Address] '.$userIp);
 
         return  CISCO::AdminPortON($ip,$credentials->snmpWcomunity,$ifindex);
    }
     
    static public function CiscoInstall(Request $request)  
    {
       $validator = validator()->make($request->only('ip', 'ifIndex', 'port', 'FullName', 'user','gps'), [
           'ip'        => 'required|ipv4',
           'port'      => 'required|string',
           'FullName'  => 'required|string',      
           'user'      => 'required|string',
           'gps'       => 'required|string',
           'ifIndex'   => 'required|numeric',
       ]);

       if ($validator->fails())
       {
           $errors = $validator->errors()->all();
           return response()->json(['error' => $errors]);
       }
      
       $ip         = $request->input('ip');
       $ifIndex    = $request->input('ifIndex');
       $port       = $request->input('port');
       $FullName   = $request->input('FullName');
       $user       = $request->input('user');
       $gps        = $request->input('gps');

        
       

       $credentials = DB::table('devices')->where('Address',$ip)->first();

       $username = $request->user()->name;
       $userIp   = $request->ip();

 
       Log::channel('install')->notice('[CISCO] '.$user 
       .'\n[ნოკი] '.$username
       .'\n[ნოკის აიპი] '.$userIp
       .'\n[სვიჩი] '.$ip
       .'\n[იუზერი] '.$user
       .'\n[პორტი] '.$FullName
       .'\n[კოორდინატები] '.$gps
       );
 
       $api = DB::table('parameters')->where('type','airsoft')->first();  
       $token       =  $api->username;
       $urlAirsoft  =  $api->url; 
  
 
       if(OLTSIDE_CISCO::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$credentials->snmpWcomunity,$ifIndex,$user))   
       {
         if($gps !== 'N/A')crm::AIRSOFT_GPS($gps,$user,$urlAirsoft,$token);
         if(crm::Airsoft_Only_OLT_Save($user,$token,$ip,$urlAirsoft))
         {
            crm::Airsoft_Only_EthernetPort_Save($user,$port,$token,$ip,$urlAirsoft); 
         }
         else
         {
            return response()->json(['error' => 'ვერ მოხერხდა AIRSOFT - ში  სვიჩის აიპის დამახსოვრება']);
         }
                                    
         return response()->json(['username' => $credentials->Username,'password' => $credentials->Pass,'read' => Crypt::encrypt($credentials->snmpRcomunity),'write' => Crypt::encrypt($credentials->snmpWcomunity)]);
       }
       else
       {    
         return response()->json(['error' => 'ვერ მოხერხდა პორტზე აბონენტის ნომრის მინიჭება']);
       }
       
    }

    ////////////////////////////////////////////////////////////////////////////////    H S G Q   
     
    static public function HsgqInstall(Request $request)  
    {
       $validator = validator()->make($request->only('ip', 'ifIndex', 'port','user','gps','ontMac','ontDbm'), [
           'ip'        => 'required|ipv4',
           'port'      => 'required|string',   
           'user'      => 'required|numeric',
           'gps'       => 'required|string',
           'ontMac'    => 'required|string',
           'ontDbm'    => 'required|string',
       ]);

       if ($validator->fails())
       {
           $errors = $validator->errors()->all();
           return response()->json(['error' => $errors]);
       }
      
       $ip         = $request->input('ip');
       $port       = $request->input('port');
       $user       = $request->input('user');
       $gps        = $request->input('gps');
       $ontMac     = $request->input('ontMac');
       $ontDbm     = $request->input('ontDbm');


       $credentials = DB::table('devices')->where('Address',$ip)->first();
       $username = $request->user()->name;
       $userIp   = $request->ip();

       $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  
 
       Log::channel('install')->notice('[HSGQ] '.$user 
       .'\n[ნოკი] '.$username
       .'\n[ნოკის აიპი] '.$userIp
       .'\n[ოელტე] '.$ip
       .'\n[იუზერი] '.$user
       .'\n[პონ / პორტი] '.$port
       .'\n[ონუს მაკი] '.$ontMac
       .'\n[ონუს დეციბალი] '.$ontDbm
       .'\n[კოორდინატები] '.$gps
       );
 
       $api = DB::table('parameters')->where('type','airsoft')->first();  
       $token       =  $api->username;
       $urlAirsoft  =  $api->url; 
                                          
       preg_match('/(\d+):(\d+)/', $port, $matches); 
 
       if(OLTSIDE_HSGQ::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$HSGQtoken->password,$matches[1].'.'. $matches[2],$user))   
       {
         if($gps !== 'N/A')crm::AIRSOFT_GPS($gps,$user,$urlAirsoft,$token);
         if(crm::Airsoft_Only_OLT_Save($user,$token,$ip,$urlAirsoft))
         {
            crm::FIBER($user,$urlAirsoft,$token);
         }
         else
         {
            return response()->json(['error' => 'ვერ მოხერხდა AIRSOFT - ში  ოელტეს აიპის დამახსოვრება']);
         }
                                    
         return response()->json(['username' => $credentials->Username,'password' => $credentials->Pass,'read' => Crypt::encrypt($credentials->snmpRcomunity),'write' => Crypt::encrypt($credentials->snmpWcomunity)]);
       }
       else
       {    
         return response()->json(['error' => 'ვერ მოხერხდა პორტზე აბონენტის ნომრის მინიჭება']);
       }   
    }

    static public function HsgqInfoByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  

        if (strpos($ifIndex,  'EPON0/') !== false)
        {
            preg_match('/(\d+):(\d+)/', $ifIndex, $matches); 
            return _hsgq::ONT_INFO_BY_IFINDEX($ip,$matches[1].'.'. $matches[2],$HSGQtoken->password);
        }
        else
        {
            return _hsgq::ONT_INFO_BY_IFINDEX($ip,$ifIndex,$HSGQtoken->password);
        }

    }

    static public function HsgqPortByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  

        if (strpos($ifIndex,  'EPON0/') !== false)
        {
            preg_match('/(\d+):(\d+)/', $ifIndex, $matches); 
            return _hsgq::ONT_PORT_BY_IFINDEX($ip,$matches[1].'.'. $matches[2],$HSGQtoken->password);
        }
        else
        {
            return _hsgq::ONT_PORT_BY_IFINDEX($ip,$ifIndex,$HSGQtoken->password);
        }

    }
    
    static public function HsgqMacByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  

        if (strpos($ifIndex,  'EPON0/') !== false)
        {
            preg_match('/(\d+):(\d+)/', $ifIndex, $matches); 
            return _hsgq::ONT_MACS_BY_IFINDEX($ip,$matches[1].'.'. $matches[2],$HSGQtoken->password);
        }
        else
        {
            return _hsgq::ONT_MACS_BY_IFINDEX($ip,$ifIndex,$HSGQtoken->password);
        }

    }

    static public function HsgqPortVlanChange(Request $request)
    {
        $validator = validator()->make($request->only('ip','ifindex','portindex','mode','vlan'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'portindex' => 'required|string',
            'mode'      => 'required|numeric',
            'vlan'      => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

      
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portindex');
        $mode       = $request->input('mode');
        $vlan       = $request->input('vlan');

        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  
 
        if (strpos($ifindex,  'EPON0/') !== false)
        {
            preg_match('/(\d+):(\d+)/', $ifindex, $matches); 
            return  HSGQ::OnuPortVlanChange($ip,$HSGQtoken->password,$matches[1].'.'. $matches[2],$portIndex,'',$vlan,$mode);
        }
        else
        {
            return  HSGQ::OnuPortVlanChange($ip,$HSGQtoken->password,$ifindex,$portIndex,'',$vlan,$mode);
        }
    }
     
    static public function HsgqOnuPortAdminStatusOFF(Request $request)
    {
      
        $validator = validator()->make($request->only('ip','ifindex','portIndex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');

        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  
 
        if (strpos($ifindex,  'EPON0/') !== false)
        {
            preg_match('/(\d+):(\d+)/', $ifindex, $matches); 
            return  HSGQ::OnuAdminPortOff($ip,$HSGQtoken->password,$matches[1].'.'. $matches[2],$ifindex,$portIndex,'');
        }
        else
        {
            return  HSGQ::Onu_PortAdminStatus_OFF($ip,$HSGQtoken->password,$ifindex,$portIndex,'');
        }
    }

    static public function HsgqOnuPortAdminStatusON(Request $request)
    {
      
        $validator = validator()->make($request->only('ip','ifindex','portIndex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
 
        
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');

        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  
 
        if (strpos($ifindex,  'EPON0/') !== false)
        {
            preg_match('/(\d+):(\d+)/', $ifindex, $matches); 
            return  HSGQ::Onu_PortAdminStatus_ON($ip,$HSGQtoken->password,$matches[1].'.'. $matches[2],$ifindex,$portIndex,'');
        }
        else
        {
            return  HSGQ::Onu_PortAdminStatus_ON($ip,$HSGQtoken->password,$ifindex,$portIndex,'');
        }
    }

    static public function HsgqOnuRestartByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifindex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifindex');

        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  

        if (strpos($ifIndex,  'EPON0/') !== false)
        {
            preg_match('/(\d+):(\d+)/', $ifIndex, $matches); 
            return _hsgq::OnuRestart($ip,$matches[1].'.'. $matches[2],$HSGQtoken->password);
        }
        else
        {
            return  _hsgq::OnuRestart($ip,$HSGQtoken->password,$ifIndex);
        }
    }

    static public function HsgqDetails(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip','ifindex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $ifindex  = $request->input('ifindex');

        $HSGQtoken = DB::table('parameters')->where('type','hsgq')->first();  

        return  OLTSIDE_HSGQ::OLT_SIDE_ONT_DETAILS($ip,$HSGQtoken->password,$ifindex);
    }
     

    ////////////////////////////////////////////////////////////////////////////////    V S O L U T I O N
    
    static public function VsolutionInstall(Request $request)  
    {
       $validator = validator()->make($request->only('ip', 'ifIndex', 'port','user','gps','ontMac','ontDbm'), [
           'ip'        => 'required|ipv4',
           'port'      => 'required|string',   
           'user'      => 'required|numeric',
           'gps'       => 'required|string',
           'ifIndex'   => 'required|string',
           'ontMac'    => 'required|string',
           'ontDbm'    => 'required|string',
       ]);

       if ($validator->fails())
       {
           $errors = $validator->errors()->all();
           return response()->json(['error' => $errors]);
       }
      
       $ip         = $request->input('ip');
       $ifIndex    = $request->input('ifIndex');
       $port       = $request->input('port');
       $user       = $request->input('user');
       $gps        = $request->input('gps');
       $ontMac     = $request->input('ontMac');
       $ontDbm     = $request->input('ontDbm');


       $credentials = DB::table('devices')->where('Address',$ip)->first();
       $username = $request->user()->name;
       $userIp   = $request->ip();

 
       Log::channel('install')->notice('[VSOLUTION] '.$user 
       .'\n[ნოკი] '.$username
       .'\n[ნოკის აიპი] '.$userIp
       .'\n[ოელტე] '.$ip
       .'\n[იუზერი] '.$user
       .'\n[პონ / პორტი] '.$port
       .'\n[ონუს მაკი] '.$ontMac
       .'\n[ონუს დეციბალი] '.$ontDbm
       .'\n[კოორდინატები] '.$gps
       );
 
       $api = DB::table('parameters')->where('type','airsoft')->first();  
       $token       =  $api->username;
       $urlAirsoft  =  $api->url; 
                                                              
 
       if(OLTSIDE_VSOLUTION::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$ifIndex,$user))   
       {
         if($gps !== 'N/A')crm::AIRSOFT_GPS($gps,$user,$urlAirsoft,$token);
         if(crm::Airsoft_Only_OLT_Save($user,$token,$ip,$urlAirsoft))
         {
            crm::FIBER($user,$urlAirsoft,$token);
         }
         else
         {
            return response()->json(['error' => 'ვერ მოხერხდა AIRSOFT - ში  ოელტეს აიპის დამახსოვრება']);
         }
                                    
         return response()->json(['username' => $credentials->Username,'password' => $credentials->Pass,'read' => Crypt::encrypt($credentials->snmpRcomunity),'write' => Crypt::encrypt($credentials->snmpWcomunity)]);
       }
       else
       {    
         return response()->json(['error' => 'ვერ მოხერხდა პორტზე აბონენტის ნომრის მინიჭება']);
       }   
    }
     
    static public function VsolutionInfoByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _vsolution::ONT_INFO_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity);
    }

    static public function VsolutionPortByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _vsolution::ONT_PORT_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity,$credentials->snmpWcomunity);
    }

    static public function VsolutionMacByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _vsolution::ONT_MAC_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity,$credentials->snmpWcomunity);
    }

    static public function VsolutionOnuRestartByIfindex(Request $request)
    {

        $validator = validator()->make($request->only('ip','ifindex'), [
            'ip'       => 'required|ipv4',
            'ifindex'  => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

            $ip         = $request->input('ip');
            $ifIndex    = $request->input('ifindex');
     
            $credentials = DB::table('devices')->where('Address',$ip)->first();

            return _vsolution::OnuRestart($ip,$credentials->snmpWcomunity,$ifIndex);
    }
     
    static public function VsolutionOnuPortAdminStatusOFF(Request $request)
    {
      
        $validator = validator()->make($request->only('ip','ifindex','portIndex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');

        $username = $request->user()->name;
        $userIp   = $request->ip();

        $credentials = DB::table('devices')->where('Address',$ip)->first();


        return  VSOLUTION::OnuAdminPortOff($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$ifindex,$portIndex,'');
    }

    static public function VsolutionOnuPortAdminStatusON(Request $request)
    {

        $validator = validator()->make($request->only('ip','ifindex','portIndex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
      
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');

        $username = $request->user()->name;
        $userIp   = $request->ip();
       
        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return  VSOLUTION::OnuAdminPortON($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$ifindex,$portIndex,'');
    }

    static public function VsolutionPortVlanChange(Request $request)
    {
        $validator = validator()->make($request->only('ip','ifindex','portindex','mode','vlan'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'portindex' => 'required|string',
            'mode'      => 'required|numeric',
            'vlan'      => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portindex');
        $mode       = $request->input('mode');
        $vlan       = $request->input('vlan');

        
        $username = $request->user()->name;
        $userIp   = $request->ip();

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return  VSOLUTION::OnuPortVlanChange($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$ifindex,$portIndex,'',$vlan,$mode);
    }

    static public function VsolutionDetails(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip','ifindex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $ifindex  = $request->input('ifindex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return  OLTSIDE_VSOLUTION::OLT_SIDE_ONT_DETAILS($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$ifindex);
    }
     
     
    ////////////////////////////////////////////////////////////////////////////////    B D C O M

    static public function BdcomInstall(Request $request)  
    {
       $validator = validator()->make($request->only('ip', 'ifIndex', 'port','user','gps','ontMac','ontDbm'), [
           'ip'        => 'required|ipv4',
           'port'      => 'required|string',   
           'user'      => 'required|numeric',
           'gps'       => 'required|string',
           'ifIndex'   => 'required|string',
           'ontMac'    => 'required|string',
           'ontDbm'    => 'required|string',
       ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
      
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');
        $port       = $request->input('port');
        $user       = $request->input('user');
        $gps        = $request->input('gps');
        $ontMac     = $request->input('ontMac');
        $ontDbm     = $request->input('ontDbm');


        $credentials = DB::table('devices')->where('Address',$ip)->first();
        $username = $request->user()->name;
        $userIp   = $request->ip();

    
        Log::channel('install')->notice('[BDCOM] '.$user 
        .'\n[ნოკი] '.$username
        .'\n[ნოკის აიპი] '.$userIp
        .'\n[ოელტე] '.$ip
        .'\n[იუზერი] '.$user
        .'\n[პონ / პორტი] '.$port
        .'\n[ონუს მაკი] '.$ontMac
        .'\n[ონუს დეციბალი] '.$ontDbm
        .'\n[კოორდინატები] '.$gps
        );
    
        $api = DB::table('parameters')->where('type','airsoft')->first();  
        $token       =  $api->username;
        $urlAirsoft  =  $api->url; 
                                                              
 
        if(OLTSIDE_BDCOM::OLT_SIDE_ONU_DESCRIPTION_EDIT($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$ifIndex,$user))   
        {

            $commandArray = [
                "ena",   
                "AIRLINK2014",
                "write all"
            ];
            sshModel::SSH($ip,22,$credentials->Username,$credentials->Pass,$commandArray);

            if($gps !== 'N/A')crm::AIRSOFT_GPS($gps,$user,$urlAirsoft,$token);
            if(crm::Airsoft_Only_OLT_Save($user,$token,$ip,$urlAirsoft))
            {
                crm::FIBER($user,$urlAirsoft,$token);
            }
            else
            {
                return response()->json(['error' => 'ვერ მოხერხდა AIRSOFT - ში  ოელტეს აიპის დამახსოვრება']);
            }
                                        
            return response()->json(['username' => $credentials->Username,'password' => $credentials->Pass,'read' => Crypt::encrypt($credentials->snmpRcomunity),'write' => Crypt::encrypt($credentials->snmpWcomunity)]);
        }
        else
        {    
            return response()->json(['error' => 'ვერ მოხერხდა პორტზე აბონენტის ნომრის მინიჭება']);
        }   
    }

    static public function BdcomInfoByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _bdcom::ONT_INFO_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity);
    }

    static public function BdcomPortByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _bdcom::ONT_PORT_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity,$credentials->snmpWcomunity);
    }

    static public function BdcomMacByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _bdcom::ONT_MAC_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity,$credentials->snmpWcomunity);
    }

    static public function BdcomOnuRestartByIfindex(Request $request)
    {

        $validator = validator()->make($request->only('ip','ifindex'), [
            'ip'       => 'required|ipv4',
            'ifindex'  => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

            $ip         = $request->input('ip');
            $ifIndex    = $request->input('ifindex');
     
            $credentials = DB::table('devices')->where('Address',$ip)->first();

            return _bdcom::OnuRestart($ip,$ifIndex,$credentials->snmpWcomunity);
    }

    static public function BdcomOnuPortAdminStatusOFF(Request $request)
    {
      
        $validator = validator()->make($request->only('ip','ifindex','portIndex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');

        $username = $request->user()->name;
        $userIp   = $request->ip();

        $credentials = DB::table('devices')->where('Address',$ip)->first();


        return  BDCOM::OnuAdminPortOff($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$ifindex,$portIndex,'');
    }

    static public function BdcomOnuPortAdminStatusON(Request $request)
    {

        $validator = validator()->make($request->only('ip','ifindex','portIndex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'portIndex' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
      
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portIndex');

        $username = $request->user()->name;
        $userIp   = $request->ip();
       
        $credentials = DB::table('devices')->where('Address',$ip)->first();
      
        return  BDCOM::OnuAdminPortON($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$ifindex,$portIndex,'');
    }

    static public function BdcomPortVlanChange(Request $request)
    {
        $validator = validator()->make($request->only('ip','ifindex','portindex','mode','vlan'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|string',
            'portindex' => 'required|string',
            'mode'      => 'required|numeric',
            'vlan'      => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $portIndex  = $request->input('portindex');
        $mode       = $request->input('mode');
        $vlan       = $request->input('vlan');

        
        $username = $request->user()->name;
        $userIp   = $request->ip();

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return  BDCOM::OnuPortVlanChange($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$ifindex,$portIndex,'',$vlan,$mode);
    }

    static public function BdcomDetails(REQUEST $request)
    {
        $validator = validator()->make($request->only('ip','ifindex'), [
            'ip'        => 'required|ipv4',
            'ifindex'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $ip       = $request->input('ip');
        $ifindex  = $request->input('ifindex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return  OLTSIDE_BDCOM::OLT_SIDE_ONT_DETAILS($ip,$credentials->snmpRcomunity,$ifindex);
    }
     

    ////////////////////////////////////////////////////////////////////////////////    Z T E

    static public function ZteInstall(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifindex', 'pon','user','FullSn'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',   
            'user'      => 'required|numeric',
            'FullSn'    => 'required|string',
            'ifindex'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
    
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifindex');
        $pon        = $request->input('pon');
        $FullSn     = $request->input('FullSn');
        $user       = $request->input('user');
 

        $credentials = DB::table('devices')->where('Address',$ip)->first();
        $username = $request->user()->name;
        $userIp   = $request->ip();

   
        return  _zte::ZTE_FAKE_INSTALL($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$FullSn,$ifIndex,$pon,$username,$userIp,$credentials->Username,$credentials->Pass);
    }

    static public function ZteFinishInstall(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifindex', 'pon','user','macSN','gps','LINE','SERVICE','TYPE','MODE','PortCount','Port1Vlan','Port2Vlan','Port3Vlan','Port4Vlan','Trunk'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',   
            'user'      => 'required|numeric',
            'macSN'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'gps'       => 'required|string',
            'LINE'      => 'required|string',
            'SERVICE'   => 'required|string',
            'TYPE'      => 'required|string',
            'MODE'      => 'required|string',
            'PortCount' => 'required|numeric',
            'Port1Vlan' => 'required|numeric',
            'Port2Vlan' => 'required|numeric',
            'Port3Vlan' => 'required|numeric',
            'Port4Vlan' => 'required|numeric',
            'Trunk'     => 'required|array|min:0',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
    
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $pon        = $request->input('pon');
        $macSN      = $request->input('macSN');
        $user       = $request->input('user');
        $gps        = $request->input('gps');
        $LINE       = $request->input('LINE');
        $SERVICE    = $request->input('SERVICE');
        $TYPE       = $request->input('TYPE');
        $MODE       = $request->input('MODE');
        $PortCount  = $request->input('PortCount');
        $Port1Vlan  = $request->input('Port1Vlan');
        $Port2Vlan  = $request->input('Port2Vlan');
        $Port3Vlan  = $request->input('Port3Vlan');
        $Port4Vlan  = $request->input('Port4Vlan');
        $Trunk      = $request->input('Trunk');


        $credentials = DB::table('devices')->where('Address',$ip)->first();
        $username = $request->user()->name;
        $userIp   = $request->ip();

        if($MODE == 'BRIDGSTER') $rejimi = 'ROUTER + BRIDGE';
        else  $rejimi = $MODE;

        Log::channel('install')->notice('[ZTE] '.$user 
        .'\n[ნოკი] '.$username
        .'\n[ნოკის აიპი] '.$userIp
        .'\n[ოელტე] '.$ip
        .'\n[იუზერი] '.$user
        .'\n[პონი] '.$pon
        .'\n[ონუს სერიული] '.$macSN
        .'\n[ლაინ პროფილი] '.$LINE
        .'\n[სერვის პროფილი] '.$SERVICE  
        .'\n[ონუს ტიპი] '.$TYPE
        .'\n[ონუს რეჟიმი] '.$rejimi
        .'\n[კოორდინატები] '.$gps
        );

        $api = DB::table('parameters')->where('type','airsoft')->first();  
        $token       =  $api->username;
        $urlAirsoft  =  $api->url; 

        if($gps !== 'N/A')crm::AIRSOFT_GPS($gps,$user,$urlAirsoft,$token);
        if(crm::Airsoft_Only_OLT_Save($user,$token,$ip,$urlAirsoft))
        {
            crm::FIBER($user,$urlAirsoft,$token);
        }
        else
        {
            return response()->json(['error' => 'ვერ მოხერხდა AIRSOFT - ში  ოელტეს აიპის დამახსოვრება']);
        }
 
        return  _zte::ZTE_REAL_INSTALL($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$credentials->Username,$credentials->Pass,$ifindex,$user,$macSN,$LINE,$SERVICE,$TYPE,$MODE,$PortCount,$Port1Vlan,$Port2Vlan,$Port3Vlan,$Port4Vlan,$Trunk);
    }

    static public function ZteBridge4PortModeVlans(Request $request)
    {
        $validator = validator()->make($request->only('ip', 'profile'), [
            'ip'        => 'required|ipv4',
            'profile'   => 'required|string',   
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
    
        $ip         = $request->input('ip');
        $profile    = $request->input('profile');


        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return  _zte::Bridge4PortVlans($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$credentials->Username,$credentials->Pass,$profile);
    }
    
    static public function ZteVlanList(Request $request)
    {
        $validator = validator()->make($request->only('ip'), [
            'ip'        => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
    
        $ip         = $request->input('ip');



        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return  _zte::VlanList($ip,$credentials->snmpRcomunity);
    }

    static public function ZteInfoByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _zte::ONT_INFO_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity);
    }

    static public function ZtePortByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _zte::ONT_PORT_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity,$credentials->snmpWcomunity);
    }

    static public function ZteMacByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _zte::ONT_MAC_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity,$credentials->snmpWcomunity);
    }


    ////////////////////////////////////////////////////////////////////////////////    H U A W E I
    static public function HuaweiInstall(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifindex', 'pon','user','FullSn'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',   
            'user'      => 'required|numeric',
            'FullSn'    => 'required|string',
            'ifindex'   => 'required|numeric',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
    
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifindex');
        $pon        = $request->input('pon');
        $FullSn     = $request->input('FullSn');
        $user       = $request->input('user');
 

        $credentials = DB::table('devices')->where('Address',$ip)->first();
        $username = $request->user()->name;
        $userIp   = $request->ip();


        if (strpos($pon, 'EPON') !== false)
        {
            return  _huawei::HUAWEI_EPON_FAKE_INSTALL($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$FullSn,$ifIndex,$pon,$username,$userIp,$credentials->Username,$credentials->Pass);
        }
        else
        {
            return  _huawei::HUAWEI_FAKE_INSTALL($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$FullSn,$ifIndex,$pon,$username,$userIp,$credentials->Username,$credentials->Pass);
        }
 
    }

    static public function HuaweiFinishInstall(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifindex', 'pon','user','macSN','gps','LINE','SERVICE','MODE','PortCount','Iphost'), [
            'ip'        => 'required|ipv4',
            'pon'       => 'required|string',   
            'user'      => 'required|numeric',
            'macSN'     => 'required|string',
            'ifindex'   => 'required|numeric',
            'gps'       => 'required|string',
            'LINE'      => 'required|string',
            'SERVICE'   => 'required|string',
            'MODE'      => 'required|string',
            'PortCount' => 'required|numeric',
            'Iphost'    => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
    
        $ip         = $request->input('ip');
        $ifindex    = $request->input('ifindex');
        $pon        = $request->input('pon');
        $macSN      = $request->input('macSN');
        $user       = $request->input('user');
        $gps        = $request->input('gps');
        $LINE       = $request->input('LINE');
        $SERVICE    = $request->input('SERVICE');
        $MODE       = $request->input('MODE');
        $PortCount  = $request->input('PortCount');
        $Iphost     = $request->input('Iphost');


        $credentials = DB::table('devices')->where('Address',$ip)->first();
        $username = $request->user()->name;
        $userIp   = $request->ip();

        if($MODE == 'BRIDGSTER') $rejimi = 'ROUTER + BRIDGE';
        else  $rejimi = $MODE;

 

        $api = DB::table('parameters')->where('type','airsoft')->first();  
        $token       =  $api->username;
        $urlAirsoft  =  $api->url; 

        if($gps !== 'N/A')crm::AIRSOFT_GPS($gps,$user,$urlAirsoft,$token);
        if(crm::Airsoft_Only_OLT_Save($user,$token,$ip,$urlAirsoft))
        {
            crm::FIBER($user,$urlAirsoft,$token);
        }
        else
        {
            return response()->json(['error' => 'ვერ მოხერხდა AIRSOFT - ში  ოელტეს აიპის დამახსოვრება']);
        }
 

        if (strpos($pon, 'EPON') !== false)
        {
            Log::channel('install')->notice('[HUAWEI] '.$user 
            .'\n[ნოკი] '.$username
            .'\n[ნოკის აიპი] '.$userIp
            .'\n[ოელტე] '.$ip
            .'\n[იუზერი] '.$user
            .'\n[პონი] '.$pon                                               
            .'\n[აიპიჰოსტი] '.$Iphost                                               
            .'\n[ონუს სერიული] '.$macSN
            .'\n[ლაინ პროფილი] '.$LINE
            .'\n[სერვის პროფილი] '.$SERVICE  
            .'\n[ონუს რეჟიმი] '.$rejimi
            .'\n[კოორდინატები] '.$gps
            );

            return  _huawei::HUAWEI_EPON_REAL_INSTALL($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$credentials->Username,$credentials->Pass,$ifindex,$user,$pon,$macSN,$LINE,$SERVICE,$MODE,$PortCount,$Iphost);  
        }
        else
        {
            Log::channel('install')->notice('[HUAWEI] '.$user 
            .'\n[ნოკი] '.$username
            .'\n[ნოკის აიპი] '.$userIp
            .'\n[ოელტე] '.$ip
            .'\n[იუზერი] '.$user
            .'\n[პონი] '.$pon
            .'\n[ონუს სერიული] '.$macSN
            .'\n[ლაინ პროფილი] '.$LINE
            .'\n[სერვის პროფილი] '.$SERVICE  
            .'\n[ონუს რეჟიმი] '.$rejimi
            .'\n[კოორდინატები] '.$gps
            );

            return  _huawei::HUAWEI_REAL_INSTALL($ip,$credentials->snmpRcomunity,$credentials->snmpWcomunity,$credentials->Username,$credentials->Pass,$ifindex,$user,$pon,$macSN,$LINE,$SERVICE,$MODE,$PortCount);
        }

    }

    static public function HuaweiInfoByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _huawei::ONT_INFO_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity);
    }

    static public function HuaweiPortByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _huawei::ONT_PORT_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity,$credentials->snmpWcomunity);
    }

    static public function HuaweiMacByIfindex(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'ifIndex'), [
            'ip'        => 'required|ipv4',
            'ifIndex'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $ifIndex    = $request->input('ifIndex');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _huawei::ONT_MAC_BY_IFINDEX($ip,$ifIndex,$credentials->snmpRcomunity,$credentials->snmpWcomunity);
    }

    ////////////////////////////////////////////////////////////////////////////////    H U A W E I   E P O N

    static public function HuaweiePON_VlansFromServiceProfile(Request $request)  
    {
        $validator = validator()->make($request->only('ip', 'profile'), [
            'ip'        => 'required|ipv4',
            'profile'   => 'required|string',
        ]);
 
        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }
       
        $ip         = $request->input('ip');
        $profile    = $request->input('profile');

        $credentials = DB::table('devices')->where('Address',$ip)->first();

        return _huawei::HUAWEI_EPON_SERVICE_PROFILE_READ_FOR_VLANS($ip,$credentials->snmpRcomunity,$profile);
    }
     

}
 