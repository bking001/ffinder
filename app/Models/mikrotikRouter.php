<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\phpAPImodel;
use App\Models\MikrotikModel;
use App\Models\BDCOM;
 

class mikrotikRouter extends Model
{
    use HasFactory;

  
    static public function Client_Side_Info($server,$username,$password,$user)
    {
        $html = [];

        $API = new phpAPImodel();
        $API->debug = false;		
    
        if ($API->connect($server, $username, $password)) 
        { 
            $API->write('/interface/ethernet/print',true);
            $READ_ONU = $API->read(false);
            $Info     = $API->parseResponse($READ_ONU);

            $id = '';$interfaceName = null;$slave = false;
         
            foreach ($Info as $key => $value) 
            {
              if(isset($value['comment']))
              {
                if (strpos($value['comment'], $user) !== false)
                {
                    $id = $value['.id'] ?? null;
                    $interfaceName = $value['name'] ?? null;
                    $slave = $value['slave'] ?? false;

                    $html['InterfaceID'] = $value['.id'] ?? null;
                    $html['InterfaceName'] = $value['name'] ?? null;
                    $html['mtu'] = $value['mtu'] ?? null;
                    $html['l2mtu'] = $value['l2mtu'] ?? null;
                    $html['InterfaceMac'] = $value['mac-address'] ?? null;
                    $html['auto_negotiation'] = $value['auto-negotiation'] ?? null;
                    $html['speed'] = $value['speed'] ?? null;
                    $html['rx_fcs_error'] = $value['rx-fcs-error'] ?? null;
                    $html['rx_align_error'] = $value['rx-align-error'] ?? null;
                    $html['rx_length_error'] = $value['rx-length-error'] ?? null;
                    $html['rx_drop'] = $value['rx-drop'] ?? null;
                    $html['tx_pause'] = $value['tx-pause'] ?? null;
                    $html['running'] = $value['running'] ?? null;
                    $html['disabled'] = $value['disabled'] ?? null;
                    $html['slave'] = $value['slave'] ?? null;
                    $html['comment'] = $value['comment'] ?? null;
                }
                
              }
            }

            
              //მონიტორი
              $API->write('/interface/ethernet/monitor',false);
              $API->write('=.id='.$id,false);
              $API->write('=once=');

              $READ_MONITORING = $API->read(false);
              $MONITORING      = $API->parseResponse($READ_MONITORING);      
            
              $html['sfp_textarea'] = '';
              foreach ($MONITORING as $Zkey => $Zvalue) 
              {
 
                    if (is_array($Zvalue)) {
                        // Loop through each key-value pair in the monitoring object
                        foreach ($Zvalue as $key => $value) {
                            // Append each key-value pair to the output string
                            $html['sfp_textarea'] .= ucfirst(str_replace('-', ' ', $key)) . ": $value\n"; // Format the key and add the value
                        }
                        $html['sfp_textarea'] .= "\n"; // Add a newline to separate different monitoring entries
                    }
            
             
                    // $html['rate'] = $Zvalue['rate'] ?? null;
                    // $html['sfp_rx_loss'] = $Zvalue['sfp-rx-loss'] ?? null;
                    // $html['sfp_tx_fault'] = $Zvalue['sfp-tx-fault'] ?? null;
                    // $html['sfp_type'] = $Zvalue['sfp-type'] ?? null;
                    // $html['sfp_connector_type'] = $Zvalue['sfp-connector-type'] ?? null;
                    // $html['sfp_link_length_9um'] = $Zvalue['sfp-link-length-9um'] ?? null;
                    // $html['sfp_link_length_sm'] = $Zvalue['sfp-link-length-sm'] ?? null;
                    // $html['sfp_vendor_name'] = $Zvalue['sfp-vendor-name'] ?? null;
                    // $html['sfp_vendor_part_number'] = $Zvalue['sfp-vendor-part-number'] ?? null;
                    // $html['sfp_vendor_revision'] = $Zvalue['sfp-vendor-revision'] ?? null;
                    // $html['sfp_vendor_serial'] = $Zvalue['sfp-vendor-serial'] ?? null;
                    // $html['sfp_wavelength'] = $Zvalue['sfp-wavelength'] ?? null;
                    // $html['sfp_temperature'] = $Zvalue['sfp-temperature'] ?? null;
                    // $html['sfp_supply_voltage'] = $Zvalue['sfp-supply-voltage'] ?? null;
                    // $html['sfp_tx_bias_current'] = $Zvalue['sfp-tx-bias-current'] ?? null;
                    // $html['sfp_tx_power'] = $Zvalue['sfp-tx-power'] ?? null;
                    // $html['sfp_rx_power'] = $Zvalue['sfp-rx-power'] ?? null;
                    // $html['eeprom_checksum'] = $Zvalue['eeprom-checksum'] ?? null;
              }

 
                if($interfaceName !== null)
                {  
                    //  სიჩქარე ინტერფეისი 
                    $API->write('/interface/monitor-traffic',false);
                    $API->write('=interface='.$interfaceName,false);
                    $API->write('=once=');

                    $READ_SPEED = $API->read(false);
                    $Speed      = $API->parseResponse($READ_SPEED);    
                    
                    foreach ($Speed as $Xkey => $Xvalue) 
                    {
                        if($Xvalue['tx-bits-per-second'] && $Xvalue['rx-bits-per-second'])
                        {
                            $html['DownloadSpeed']   = MikrotikModel::formatSpeed($Xvalue['tx-bits-per-second']);
                            $html['UploadSpeed']     = MikrotikModel::formatSpeed($Xvalue['rx-bits-per-second']);
    
                            $html['DownloadFixedSpeed'] = intval((int)$Xvalue['tx-bits-per-second'] / 1000000);
                            $html['UploadFixedSpeed']   = intval((int)$Xvalue['rx-bits-per-second'] / 1000000); 
                        }
                        else
                        {
                            $html['DownloadSpeed']   = 0;
                            $html['UploadSpeed']     = 0;
    
                            $html['DownloadFixedSpeed'] = 0;
                            $html['UploadFixedSpeed']   = 0;
                        }
 
                    }

       
                    if($slave)
                    {
                        // მაკ ადრესების მოტანა 
                        $API->write('/interface/bridge/host/print',true);

                        $READ_MACS = $API->read(false);
                        $Macs      = $API->parseResponse($READ_MACS); 
                        
                        foreach ($Macs as $Ykey => $Yvalue) 
                        {
                            if (strpos($Yvalue['interface'], $interfaceName) !== false) 
                            {
                                $fixedVlan = $Yvalue['bridge'] ?? 0;
                                if($fixedVlan !== 0)
                                {
                                    $fixedVlan = self::extractNumbers($Yvalue['bridge']);
                                }

                                $item = [];
                                $item['mac']        = $Yvalue['mac-address'] ?? '-';
                                $item['dynamic']    = $Yvalue['dynamic'] ?? '-';
                                $item['age']        = $Yvalue['age'] ?? '-';
                                $item['interface']  = $Yvalue['interface'] ?? '-';
                                $item['bridge']     = $Yvalue['bridge'] ?? '-';
                                $item['fixedVlan']  = $fixedVlan;

                                if(isset($Yvalue['mac-address']))
                                {
                                    $item['vendoor'] = BDCOM::MacFind_SNMP(($Yvalue['mac-address']));
                                }
                                else 
                                {
                                    $item['vendoor'] = '-';
                                }
                                

                                $html['Macs_'.$Ykey] =  $item;
                            }
                        } 
 
                        // /interface/vlan> print where interface=sfp-sfpplus12 

                         // მაკ ადრესების მოტანა ვილანებით 
                         $API->write('/interface/vlan/print',true);
                 
                         $READ_VLANS = $API->read(false);
                         $Vlan       = $API->parseResponse($READ_VLANS); 

                         foreach ($Vlan as $Vkey => $Vvalue) 
                         {
                            if (strpos($Vvalue['interface'], $interfaceName) !== false) 
                            {
                                $Name = $Vvalue['name'];

                                foreach ($Macs as $Ykey => $Yvalue) 
                                {
                                    if ($Name == $Yvalue['interface']) 
                                    {

                                        $fixedVlan = $Yvalue['bridge'] ?? 0;
                                        if($fixedVlan !== 0)
                                        {
                                            $fixedVlan = self::extractNumbers($Yvalue['bridge']);
                                        }

                                        $item = [];
                                        $item['mac']        = $Yvalue['mac-address'] ?? '-';
                                        $item['dynamic']    = $Yvalue['dynamic'] ?? '-';
                                        $item['age']        = $Yvalue['age'] ?? '-';
                                        $item['interface']  = $Yvalue['interface'] ?? '-';
                                        $item['bridge']     = $Yvalue['bridge'] ?? '-';
                                        $item['fixedVlan']  = $fixedVlan;
        
                                        if(isset($Yvalue['mac-address']))
                                        {
                                            $item['vendoor'] = BDCOM::MacFind_SNMP(($Yvalue['mac-address']));
                                        }
                                        else 
                                        {
                                            $item['vendoor'] = '-';
                                        }
                                             
                                        $html['Macs_'.(int)$Ykey + 100] =  $item;
                                    }
                                } 
                            }
                         }
 
                    }
    
                } 

            $API->disconnect();
        }
        else
        {
            return response()->json(['error' => 'ვერ მოხერხდა '.$server.' მიკროტიკთან დაკავშირება api - ით']);
        }
        

     
        return $html;
    }

    static public function RouterPortOn($server,$username,$password,$user,$InterfaceID)
    { 
        $API = new phpAPImodel();
        $API->debug = false;		
    
        if ($API->connect($server, $username, $password)) 
        { 
            $API->write('/interface/ethernet/enable',false);
            $API->write('=.id='.$InterfaceID,true);
    
    
            $READ_ONU = $API->read(false);
            $Info     = $API->parseResponse($READ_ONU);
    
            return $Info;
        }
    }
     
    static public function RouterPortOff($server,$username,$password,$user,$InterfaceID)
    {
 
        $API = new phpAPImodel();
        $API->debug = false;		
    
        if ($API->connect($server, $username, $password)) 
        { 
            $API->write('/interface/ethernet/disable',false);
            $API->write('=.id='.$InterfaceID,true);
    
    
            $READ_ONU = $API->read(false);
            $Info     = $API->parseResponse($READ_ONU);
    
            return $Info;
        }
 
    }

    static public function extractNumbers($string) 
    {
        // Use preg_match_all to find all digits in the string
        preg_match_all('/\d+/', $string, $matches);
        
        // Concatenate all found digits
        $numbers = implode('', $matches[0]);
        
        return $numbers;
    }
}
