<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;


use App\Models\phpAPImodel;
 


class MikrotikModel extends Model
{
    use HasFactory;

    static public function mikrotik($server,$ab_nom)  
    {
        
      $credentials = DB::table('parameters')->where('type','mikrotik')->first();
      $username    = $credentials->username;
      $password    = $credentials->password;

  
      if(strlen($username) && strlen($password))
      {
          $client="";$hostname="";$ip="";$vlan="";$mac="";$expire="";$last="";$status="";$Arp="";$Download="";$Upload="";$Packet="";$DownloadEx="";$UploadEx="";

          $API = new phpAPImodel();
          $API->debug = false;		
          
          if ($API->connect($server, $username, $password)) 
          {  
                $CommandInfo  = '/ip/dhcp-server/lease/print';
                $API->write($CommandInfo,false);
                $API->write('?comment='.$ab_nom); 
                $READ_ONU  	 = $API->read(false);
                $OnuInfo     = $API->parseResponse($READ_ONU);
                
                foreach ($OnuInfo as  $value) 
                {
                  if ($value['comment'] === $ab_nom) 
                  {
                    $client 		= $value['comment'];
                    $hostname 	    = $value['host-name'] ?? '';
                    $ip 			= $value['address'] ?? '';
                    $vlan 		    = $value['active-server'] ?? '';
                    $mac 		    = $value['mac-address'] ?? '';
                    $expire 		= $value['expires-after'] ?? '';
                    $last 		    = $value['last-seen'] ?? '';
                    $status 		= $value['status'] ?? '';
                    break;
                  }
                }

                  $Queue	  = '/queue/simple/print';
                  $API->write($Queue,false);
                  $API->write('?name='.$ab_nom);
                  $READ_Queue  = $API->read(false);
                  $OnuQueue    = $API->parseResponse($READ_Queue);
                  foreach ($OnuQueue as  $value) 
                  {
                    if ($value['name'] === $ab_nom) 
                    {
                      $Packet   = intval((((int)$value['max-limit']/1000)/1000)).' MB';
                      $Rate     = explode('/', $value['rate']);
              
                      $DownloadEx = $Rate['0'];
                      $UploadEx   = $Rate['1'];
              
                      $Download = MikrotikModel::formatSpeed($DownloadEx);
                      $Upload = MikrotikModel::formatSpeed($UploadEx);
                      break;
                    }
                  }

                  $Arp  	  = '/ip/arp/print';
                  $API->write($Arp,false);
                  $API->write('?comment='.$ab_nom);
                  $READ_ARP    = $API->read(false);
                  $ArpInfo     = $API->parseResponse($READ_ARP);
                  $Arp_Vlan = '';
                  $Arp_VlanEX = '';
                  $vlan_name = '';
            
                  foreach ($ArpInfo as  $value) 
                  {												 					
                    if ($value['comment'] === $ab_nom) 
                    {  
                      $Arp = $value['disabled'];
                      $Arp_Vlan = trim($value['interface']);
                      $vlan_name = trim($value['interface']);
                      break;
                    }
                  }

                  if(!empty($Arp_Vlan))
                  { 
                      $API->write('/interface/monitor-traffic',false);
                      $API->write('=interface='.$Arp_Vlan,false);
                      $API->write('=once=');
                      $interfaceData = $API->read();
                
                      foreach ($interfaceData as $value) 
                      {
                        $Arp_Vlan   = MikrotikModel::formatSpeed($value['tx-bits-per-second']);
                        $Arp_VlanEX = ($value['tx-bits-per-second']);
                      }
                  }
                  else 
                  {
                    $Arp_Vlan = '0';
                    $Arp_VlanEX = '0';
                  }
                  $data = [
                    'client' => $client,
                    'status' => $status,
                    'ip' => $ip,
                    'vlan' => $vlan,
                    'mac' => $mac,
                    'hostname' => $hostname,
                    'expire' => $expire,
                    'last' => $last,
                    'Arp' => $Arp,
                    'Packet' => $Packet,
                    'Download' => $Download,
                    'DownloadEx' => intval((int)$DownloadEx / 1000000),
                    'Upload' => $Upload,
                    'UploadEx' => intval((int)$UploadEx / 1000000),
                    'Arp_Vlan' => $Arp_Vlan,
                    'Arp_VlanEX' => intval((int)$Arp_VlanEX / 1000000),
                    'vlan_name' => $vlan_name
                ];
                
                $API->disconnect();
                return json_encode($data);  
          }
          else 
          {
            // $API->disconnect();
            return 'none';
          }   
      }
      else
      {
         return "creds";
      }
    }


    
	static public function formatSpeed($speed)
	{
		if ($speed >= 1000000) 
		{
			$speed = round($speed / 1000000);
			return $speed . " Mbps";
		} 
		else 
		{
			$speed = number_format($speed / 1000, 1);
			return $speed . " kbps";
		}
	}
}
 