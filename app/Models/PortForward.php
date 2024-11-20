<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\phpAPImodel;
use Illuminate\Support\Collection;

class PortForward extends Model
{
    use HasFactory;


    static public function search($id)
    {
        $credentials = DB::table('parameters')->where('type','forward')->first();
        $username    = $credentials->username;
        $password    = $credentials->password;
        $server      = $credentials->url;
     
        $html = [];
        $IsPortRuleExist = false;
        $CommandInfo  = '/ip/firewall/nat/print';
        $API = new phpAPImodel();
        $API->debug = false;		

        if ($API->connect($server, $username, $password)) 
        {	
            $API->write($CommandInfo,true);
            $READ_ONU  = $API->read(false);
            $PortInfo  = $API->parseResponse($READ_ONU);
 
       
            foreach ($PortInfo as $XKeyX => $item)
            { 
                if(isset($item['comment']))
                {
                    if (strpos($item['comment'], $id) !== false)
                    {                  
                        $IsPortRuleExist = true;
                        $expanded = PortForward::expandRanges($item['dst-port']);
                    
                
                        if(strpos($item['chain'] , 'dstnat') !== false)   // scrnat გამქრალია დროებით 
                        {  
                            if (strpos($item['dst-port'] , '-') !== false)
                            {     
                                foreach ($expanded as $number) 
                                {
                                    
                                    $rule = '';
                                    if ( isset($item['disabled']) && trim($item['disabled']) == 'true')
                                    {
                                        $rule = 'disabled';  
                                    }
                                    else
                                    {
                                        $rule = 'enabled';   
                                    }

                                    $Scanner = '';
                                    if (PortForward::isPortOpen($item['dst-address'], $item['dst-port'],trim($item['protocol']))) 
                                    {
                                        $Scanner = 'open';   
                                    }
                                    else
                                    {
                                        $Scanner = 'closed';   
                                    }

                                    $Zitem = [];
                                    $Zitem ['id']           = $item['.id'];
                                    $Zitem ['client']       = $item['comment'];
                                    $Zitem ['to_addresses'] = $item['to-addresses'];
                                    $Zitem ['to_ports']     = isset($item['to-ports']) ? $item['to-ports'] :  $number;
                                    $Zitem ['protocol']     = $item['protocol'];
                                    $Zitem ['dst_address']  = $item['dst-address'];
                                    $Zitem ['dst_port']     = $number;
                                    $Zitem ['Scanner']      = $Scanner;
                                    $Zitem ['rule']         = $rule;
    
                                    $html ['PortNum_'.$XKeyX] = $Zitem;
                                        
                                }
                            }
                            else if (strpos($item['dst-port'] , ',') !== false)
                            { 
                                foreach ($expanded as $number) 
                                {
              
                                    $rule = '';
                                    if ( isset($item['disabled']) && trim($item['disabled']) == 'true')
                                    {
                                        $rule = 'disabled';  
                                    }
                                    else
                                    {
                                        $rule = 'enabled';   
                                    }

                                    $Scanner = '';
                                    if (PortForward::isPortOpen($item['dst-address'], $number ,trim($item['protocol']))) 
                                    {
                                        $Scanner = 'open';   
                                    }
                                    else
                                    {
                                        $Scanner = 'closed';   
                                    }
                                        
                                    $Zitem = [];
                                    $Zitem ['id']           = $item['.id'];
                                    $Zitem ['client']       = $item['comment'];
                                    $Zitem ['to_addresses'] = $item['to-addresses'];
                                    $Zitem ['to_ports']     = isset($item['to-ports']) ? $item['to-ports'] : $number;
                                    $Zitem ['protocol']     = $item['protocol'];
                                    $Zitem ['dst_address']  = $item['dst-address'];
                                    $Zitem ['dst_port']     = $number;
                                    $Zitem ['Scanner']      = $Scanner;
                                    $Zitem ['rule']         = $rule;
    
                                    $html ['PortNum_'.$number] = $Zitem;
                                }
                            }   
                            else
                            { 
                                $rule = '';
                                if ( isset($item['disabled']) && trim($item['disabled']) == 'true')
                                {
                                    $rule = 'disabled';  
                                }
                                else
                                {
                                    $rule = 'enabled';   
                                }

                                $Scanner = '';
                                if (PortForward::isPortOpen($item['dst-address'], $item['dst-port'],trim($item['protocol']))) 
                                {
                                    $Scanner = 'open';   
                                }
                                else
                                {
                                    $Scanner = 'closed';   
                                }

                     

                                $Zitem = [];
                                $Zitem ['id']           = $item['.id'];
                                $Zitem ['client']       = $item['comment'];
                                $Zitem ['to_addresses'] = $item['to-addresses'];                              
                                $Zitem ['to_ports']     = isset($item['to-ports']) ? $item['to-ports'] :  $item['dst-port'];
                                $Zitem ['protocol']     = $item['protocol'];
                                $Zitem ['dst_address']  = $item['dst-address'];
                                $Zitem ['dst_port']     = $item['dst-port'];
                                $Zitem ['Scanner']      = $Scanner;
                                $Zitem ['rule']         = $rule;

                                $html ['PortNum_'.$XKeyX] = $Zitem;
                            }     
                                
                        }
                    }
                }
            }
        }
        $html ['IsPortRuleExist'] = $IsPortRuleExist;
        return $html;
    }

    static public function CustomPortSearch($port)
    {
        $credentials = DB::table('parameters')->where('type','forward')->first();
        $username    = $credentials->username;
        $password    = $credentials->password;
        $server      = $credentials->url;
     
        $allowedAddresses = [
            '45.9.47.26',
            '45.9.47.27',
            '45.9.47.28',
            '45.9.47.29',
            '45.9.47.30'
        ];


        $html = [];$TempHtml = [];
        $CommandInfo  = '/ip/firewall/nat/print';
        $API = new phpAPImodel();
        $API->debug = false;		

        if ($API->connect($server, $username, $password))   
        {
            $API->write($CommandInfo,true);
            $READ_ONU  = $API->read(false);
            $PortInfo  = $API->parseResponse($READ_ONU);

            if (strpos($port, ',') !== false)
            {
                $ExstraPorts = explode(',',$port);
                foreach ($ExstraPorts as $keyX => $Portvalue)
                {
                    $TempHtml = [];
                    if (is_numeric($Portvalue))
                    {
                        foreach ($PortInfo as $keyX => $item) 
                        {  
                            if(isset($item['dst-port']))   
                            {
                                if (strpos($item['dst-port'], ',') !== false) //ვამოწმებს ერთზე მეტი პორტი თუ წერია
                                {
                                    $MultyPorts = explode(',',$item['dst-port']);
            
                                    foreach ($MultyPorts as $key => $valueZ) 
                                    { 
                                        if (strpos($valueZ, '-') !== false)
                                        {
                                            list($startPort, $endPort) = explode('-', $valueZ);
                                            if ((int)$Portvalue >= (int)$startPort && (int)$Portvalue <= (int)$endPort)
                                            {
                                                $Zitem = [];
                                                $Zitem ['dst_address']  = $item['dst-address'];
                                                $Zitem ['ForPort']      = $Portvalue;
                                                $TempHtml [] = $Zitem;
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            if ($valueZ == (int)$Portvalue)
                                            {
                                                $Zitem = [];
                                                $Zitem ['dst_address']  = $item['dst-address'];
                                                $Zitem ['ForPort']      = $Portvalue;
                                                $TempHtml [] = $Zitem;
                                            }
                                        }
                                    }
                                }
                                else if (strpos($item['dst-port'], '-') !== false) //ვამოწმებს რენჯით თუ წერია
                                {
                                    list($startPort, $endPort) = explode('-', $item['dst-port']);
                                    if ((int)$Portvalue >= (int)$startPort && (int)$Portvalue <= (int)$endPort)
                                    {
                                        $Zitem = [];
                                        $Zitem ['dst_address']  = $item['dst-address'];
                                        $Zitem ['ForPort']      = $Portvalue;
                                        $TempHtml [] = $Zitem;
                                    }
                                }
                                else  //  თუ ერთი პორტია მხოლოდ
                                {
                                    if ($item['dst-port'] == (int)$Portvalue)
                                    {
                                        $Zitem = [];
                                        $Zitem ['dst_address']  = $item['dst-address'];
                                        $Zitem ['ForPort']      = $Portvalue;
                                        $TempHtml [] = $Zitem;
                                    }
                                }
                            }
                        }

                        $allowedList = [
                            '45.9.47.26',
                            '45.9.47.27',
                            '45.9.47.28',
                            '45.9.47.29',
                            '45.9.47.30'
                        ];
              
                        foreach ($TempHtml as $key => $value) 
                        { 
                            $UsedIpKey = array_search($value['dst_address'], $allowedList);
                            if ($UsedIpKey !== false)
                            {
                                array_splice($allowedList, $UsedIpKey, 1);
                            }
                        }
    
                        $allowedList ['ForPort'] = $Portvalue;
                        $html ['allowedAddresses_'.$Portvalue] = $allowedList;

                    }
                }

            }
            else
            {
                foreach ($PortInfo as $keyX => $item) 
                {
                    if(isset($item['dst-port']))  
                    {
                        if (strpos($item['dst-port'], ',') !== false)
                        {
                            $MultyPorts = explode(',',$item['dst-port']);
            
                            foreach ($MultyPorts as $key => $valueZ) 
                            {
                                if (strpos($item['dst-port'], '-') !== false)
                                {
                                    list($startPort, $endPort) = explode('-', $valueZ);
                                    if ((int)$port >= (int)$startPort && (int)$port <= (int)$endPort)
                                    {
                                        $Zitem = [];
                                        $Zitem ['dst_address']  = $item['dst-address'];
                                        $Zitem ['ForPort']      = $port;
                                        $TempHtml [] = $Zitem;
                                    }
                                }
                                else
                                {
                                    if ($valueZ == (int)$port)
                                    {
                                        $Zitem = [];
                                        $Zitem ['dst_address']  = $item['dst-address'];
                                        $Zitem ['ForPort']      = $port;
                                        $TempHtml [] = $Zitem;
                                    }
                                }
                            }
                        }
                        else if (strpos($item['dst-port'], '-') !== false)
                        {
                            list($startPort, $endPort) = explode('-', $item['dst-port']);
                            if ((int)$port >= (int)$startPort && (int)$port <= (int)$endPort)
                            {
                                $Zitem = [];
                                $Zitem ['dst_address']  = $item['dst-address'];
                                $Zitem ['ForPort']      = $port;
                                $TempHtml [] = $Zitem;
                            }
                        }
                        else
                        {
                            if ($item['dst-port'] == (int)$port)
                            {
                                $Zitem = [];
                                $Zitem ['dst_address']  = $item['dst-address'];
                                $Zitem ['ForPort']      = $port;
                                $TempHtml [] = $Zitem;
                            }
                        }
 
                    }
                }
 
                foreach ($TempHtml as $key => $value) 
                {
                    $UsedIpKey = array_search($value['dst_address'], $allowedAddresses);
                    if ($UsedIpKey !== false)
                    {
                        array_splice($allowedAddresses, $UsedIpKey, 1);
                    }
                }
                    $allowedAddresses ['ForPort'] = $port;
                    $html ['allowedAddresses_'.$port] = $allowedAddresses;
            }

        }


        // $ipCollections = collect($html)->map(function ($addresses) {
        //     // Exclude the "ForPort" key and return only the IP addresses
        //     return collect($addresses)->except('ForPort')->values();
        // });
        
        // // Find the intersection of all IP address collections
        // $commonIPs = $ipCollections->reduce(function ($carry, $item) {
        //     return $carry ? $carry->intersect($item) : $item;
        // });
        
        // if ($commonIPs->isNotEmpty()) 
        // {
        //     $commonIP = $commonIPs->first();
        //     $html['commonIP'] = $commonIP;
        // } 

        $ipCollections = collect($html)->map(function ($addresses) {
            // Exclude the "ForPort" key and return only the IP addresses
            return collect($addresses)->except('ForPort')->values();
        });
        
        // Flatten the collection of IP addresses and count occurrences
        $ipCount = $ipCollections->flatten()->countBy();
        
        // Find the IP address with the highest count
        $mostCommonIP = $ipCount->sortDesc()->keys()->first();
        
        if ($mostCommonIP) {
            $html['commonIP'] = $mostCommonIP;
        }
        

        return $html;
    }

    static public function OpenPort($publicport,$privatport,$user,$privateip,$publicip,$protocol)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        $credentials = DB::table('parameters')->where('type','forward')->first();
        $username    = $credentials->username;
        $password    = $credentials->password;
        $server      = $credentials->url;


        $html = []; 
        $API = new phpAPImodel();
        $API->debug = false;		

        if ($API->connect($server, $username, $password))   
        {
            if($protocol == 'both')
            {
                $API->write('/ip/firewall/nat/add',false);
                $API->write('=action=dst-nat',false);
                $API->write('=chain=dstnat',false);
                $API->write('=comment='.$user,false);
                $API->write('=dst-address='.$publicip,false);
                $API->write('=dst-port='.$publicport,false);
                $API->write('=protocol=tcp',false);
                $API->write('=to-addresses='.$privateip,false);
                $API->write('=to-ports='.$privatport,true);


                $API->write('/ip/firewall/nat/add',false);
                $API->write('=action=dst-nat',false);
                $API->write('=chain=dstnat',false);
                $API->write('=comment='.$user,false);
                $API->write('=dst-address='.$publicip,false);
                $API->write('=dst-port='.$publicport,false);
                $API->write('=protocol=udp',false);
                $API->write('=to-addresses='.$privateip,false);
                $API->write('=to-ports='.$privatport,true);
            }
            else
            {
                $API->write('/ip/firewall/nat/add',false);
                $API->write('=action=dst-nat',false);
                $API->write('=chain=dstnat',false);
                $API->write('=comment='.$user,false);
                $API->write('=dst-address='.$publicip,false);
                $API->write('=dst-port='.$publicport,false);
                $API->write('=protocol=tcp',false);
                $API->write('=to-addresses='.$privateip,false);
                $API->write('=to-ports='.$privatport,true);
            }


            $READ_ONU  = $API->read(false);
            $result  = $API->parseResponse($READ_ONU);

        }

        return $result;
    }
    
    static public function PortForwardChange($client,$privatPort,$publicPort,$privatIP,$publicIP,$protocol,$id)
    { 
        PrivilegesModel::PrivCheck('Priv_Onu');

        $credentials = DB::table('parameters')->where('type','forward')->first();
        $username    = $credentials->username;
        $password    = $credentials->password;
        $server      = $credentials->url;
     
        $API = new phpAPImodel();
        $API->debug = false;		


        if ($API->connect($server, $username, $password))   
        {	
            $API->write('/ip/firewall/nat/set', false);
            $API->write('=.id=' . $id, false);
            $API->write('=comment='.$client,false);
            $API->write('=dst-address='.$publicIP,false);
            $API->write('=dst-port='.$publicPort,false);
            $API->write('=protocol='.$protocol,false);
            $API->write('=to-addresses='.$privatIP,false);
            $API->write('=to-ports='.$privatPort,true);

            $READ_ONU  = $API->read(false);
            $PortInfo  = $API->parseResponse($READ_ONU);
        }

        return true;
    }
     
    static public function ChangePrivatAddress($id,$ip)
    { 
        PrivilegesModel::PrivCheck('Priv_Onu');

        $credentials = DB::table('parameters')->where('type','forward')->first();
        $username    = $credentials->username;
        $password    = $credentials->password;
        $server      = $credentials->url;
     
        $API = new phpAPImodel();
        $API->debug = false;		

        if ($API->connect($server, $username, $password))   
        {	
            $API->write('/ip/firewall/nat/set', false);
            $API->write('=.id=' . $id, false);
            $API->write('=to-addresses='.$ip, true); 
            $READ_ONU  = $API->read(false);
            $PortInfo  = $API->parseResponse($READ_ONU);
        }

        return true;
    }

    static public function DeletePort($id)
    { 
        PrivilegesModel::PrivCheck('Priv_Onu');

        $credentials = DB::table('parameters')->where('type','forward')->first();
        $username    = $credentials->username;
        $password    = $credentials->password;
        $server      = $credentials->url;
     
        $API = new phpAPImodel();
        $API->debug = false;		

        if ($API->connect($server, $username, $password))   
        {	
            $API->write('/ip/firewall/nat/remove',false);
            $API->write('=.id='.$id,true);
            $READ_ONU  = $API->read(false);
            $PortInfo  = $API->parseResponse($READ_ONU);
        }

        return true;
    }

    static public function isPortOpen($host, $port, $protocol) 
    {   
        if ($protocol == 'tcp') 
        {   
            $connection = @fsockopen($host, $port, $errno, $errstr, 1);
            if ($connection !== false)
            {
                fclose($connection);
                return true;
            }
        }  
        return false;
    }
    
    static public function expandRanges($input) 
    {
        $ranges = explode(',', $input);
        $expandedNumbers = [];
    
        foreach ($ranges as $range) {
            if (strpos($range, '-') !== false) {
                list($start, $end) = explode('-', $range);
                $start = (int) trim($start);
                $end = (int) trim($end);
    
                for ($i = $start; $i <= $end; $i++) {
                    $expandedNumbers[] = $i;
                }
            } else {
                $numbers = explode(',', $range);
                foreach ($numbers as $number) {
                    $expandedNumbers[] = (int) trim($number);
                }
            }
        }
    
        return $expandedNumbers;
    }

    static public function generateRandomHexString($length = 16)
    {
        return bin2hex(random_bytes($length / 2));
    }

}
