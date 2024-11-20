<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
 
use App\Models\ubiquiti;

class SECTOR extends Model
{
    use HasFactory;

 
    static public function Client_Side_Info($ip,$userIp,$user,$username,$password)
    {   

        $html = [];
 
        $ubiquiti 	=  new ubiquiti($userIp, $username, $password, false, '80', 5);
	    $String 	= $ubiquiti->status(true);
 

        $html ['hostname']  = '';
        $html ['uptime']    = '';
        $html ['devmodel']  = '';
        $html ['signal']    = '';
        $html ['TX']        = '';
        $html ['RX']        = '';
     
        if (isset($String['host']) && is_array($String['host'])) 
        {
            $html['hostname']  = $String['host']['hostname'] ?? '-';
            $html['uptime']    = SECTOR::RealTime($String['host']['uptime']);
            $html['devmodel']  = $String['host']['devmodel'] ?? '-';
            $html['shutDown']  = '0';
        }
        else 
        {
            $html ['shutDown'] = '1';
        }
        
        if (isset($String['wireless']) && is_array($String['wireless'])) 
        {
            $html['signal']     = $String['wireless']['signal'];
            $html['TX']         = $String['wireless']['txrate'];
            $html['RX']         = $String['wireless']['rxrate'];
            $html['essid']      = $String['wireless']['essid'];
            $html['channel']    = $String['wireless']['chanbw'];
            $html['frequency']  = $String['wireless']['frequency'];


            $html['shutDown']   = '0';
        }
        else 
        {
            $html ['shutDown'] = '1';
        }
 
        if (isset($String['interfaces']) && is_array($String['interfaces'])) 
        {
            try {
                    foreach ($String['interfaces'] as $key => $interface) 
                    {
                        if ($interface['ifname'] == 'eth0') 
                        {
                            $eth0_status = $interface['status'];
            
                            $item = [];    
                            $item['plugged'] 	 = $eth0_status['plugged'];
                            $item['speed'] 	     = $eth0_status['speed'];
                            $item['duplex'] 	 = $eth0_status['duplex'];                     
                            $html["dhcp_$key"] 	 = $item;  
                        }
                    }
        
                    $array = array();
                    $dhcp = $ubiquiti->DHCP($array);
                    
                    if($dhcp)
                    {
                        $dhcp = str_replace('&', '&amp;', $dhcp);
                    
                        $dom = new \DOMDocument();
                        $dom->loadHTML($dhcp);
                        $xpath = new \DOMXPath($dom);
                        $rows = $xpath->query("//table[@class='listhead sortable']/tr[position()>1]");
                        
                        foreach ($rows as $key => $row) 
                        {
                            $cols = $row->getElementsByTagName('td');
                            $values = array();
                            foreach ($cols as $col) 
                            { 
                                $values[] = $col->nodeValue;                 
                            }
                            $html["station_$key"] = $values;   
                            $html ['dhcp'] = 'true';
                        }
                    }
                    
 
            }
            catch (\Exception $e){
                return response()->json(['error' => $e->getMessage()]);
            }  
        }
        else
        {
            $html ['dhcp'] = 'false';
        }
 

        return $html;
    }

 

    static public function RealTime($uptimeX)
    {
       $days = floor($uptimeX / 86400);
       $hours = floor(($uptimeX % 86400) / 3600);
       $minutes = floor(($uptimeX % 3600) / 60);
       $seconds = $uptimeX % 60;
   
       $formatted_uptime = "$days days $hours:$minutes:$seconds";
       return $formatted_uptime;
    }
}
