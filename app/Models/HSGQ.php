<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;


class HSGQ extends Model
{
    use HasFactory;

     
    static public function Uninstall_Side_OnuInfo($ip,$token,$user,$oltName)
    {
        $html = [];
        $html ['clone'] = '';
        $Real_Desc_Key = 0;
        $DescriptionClone = 0;
 
        HSGQ::API('https://'.$ip.'/onu_allow_list?t=1709042982000',$token);
        $ArrayFirst =  HSGQ::API('http://'.$ip.'/onutable',$token);     
        $dataArray = json_decode($ArrayFirst, true);
      
        foreach ($dataArray['data'] as $item) 
        {
            if(strpos($item['onu_name'], $user) !== false)
            { 
                $Port_id = $item['port_id'];
 
                $ArraySecond =   HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$Port_id,$token);
                $SecDataArray = json_decode($ArraySecond, true);  
                foreach ($SecDataArray['data'] as $item) 
                {
                    if (strpos($item['onu_name'], $user) !== false) 
                    {     
            
                        $DescriptionClone += 1;
                        $Real_Desc_Key += 1;
   
                        $ItemArray = [];
                        $ItemArray ['ifIndex']     = $item['port_id'].'.'. $item['onu_id'];
                        $ItemArray ['description'] = $item['onu_name'];  
                        $ItemArray ['ponPort']     = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                        $ItemArray ['Mac']         = $item['macaddr'];
                        $ItemArray ['StatusOnu']   = $item['status'];
                        $ItemArray ['Uptime']      = HSGQ::timeAgo($item['last_down_time']);
                        $ItemArray ['Downtime']    = HSGQ::timeAgo($item['register_time']);
                    
                        $html ['ontList'.$Real_Desc_Key] = $ItemArray;         
                    }
                }
                break;
            }    
        }

         

        if($DescriptionClone > 1)
        {
            $html ['clone'] = 'ეს დესქრიფშენი გაწერილია '.$DescriptionClone.' - ონუზე ';
        }

        if (empty($Real_Desc_Key))
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }     

        $html ['oltType']    = 'HSGQ';
        $html ['oltAddress'] = $ip;
        $html ['oltName']    = $oltName;

        return $html;
    }

    static public function Client_Side_OnuInfo($ip,$token,$user)
    {
        $html = [];
        $PonCoordinates = [];
        $html ['clone'] = '';
        $Real_Desc_Key = 0;
        $DescriptionClone = 0;
 
        HSGQ::API('https://'.$ip.'/onu_allow_list?t=1709042982000',$token);
        $ArrayFirst =  HSGQ::API('http://'.$ip.'/onutable',$token);     
        $dataArray = json_decode($ArrayFirst, true);
      
        foreach ($dataArray['data'] as $item) 
        {
            if (strpos($item['onu_name'], $user) !== false){$Port_id = $item['port_id'];break;}
        }

        if(!empty($Port_id))
        { 
            $ArraySecond =   HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$Port_id,$token);
            $SecDataArray = json_decode($ArraySecond, true);
            foreach ($SecDataArray['data'] as $item) 
            {
                if (strpos($item['onu_name'], $user) !== false) 
                {     
            
                        $DescriptionClone += 1;
                        $Real_Desc_Key += 1;
 
                        $html ['ifIndex']       = $item['port_id'].'.'. $item['onu_id'];
                        $html ['PonPort']       = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                        $html ['Description']   = $item['onu_name'];                    
                        $html ['Type']          = $item['dev_type'].' - '.$item['onu_type'];
                        $html ['Mac']           = $item['macaddr'];
                        $html ['Status']        = $item['status'];
                        $html ['Reason']        = $item['last_down_reason'];
                        if (is_numeric($item['receive_power']))$html ['dbm'] = round($item['receive_power'],2);
                        else   $html ['dbm']    = $item['receive_power'];
                  
                }
            }
        }    

        if($DescriptionClone > 1)
        {
            $html ['clone'] = 'ეს დესქრიფშენი გაწერილია '.$DescriptionClone.' - ონუზე ';
        }

        if (empty($Real_Desc_Key))
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }     

        return $html;
    }

    static public function Client_Side_OnuPorts($ip,$token,$user)
    {
        $html = [];
        $html['shutDown'] = 0;
        $Port_id = '';$OntID = '';$Description = '';$OnuStatus = '';

        Sleep(1);
        HSGQ::API('https://'.$ip.'/onu_allow_list',$token);
        Sleep(1);
        $ArrayFirst =  HSGQ::API('https://'.$ip.'/onutable',$token); 
        $dataArray  = json_decode($ArrayFirst, true); 

        foreach ($dataArray['data'] as $item) 
        {
            if (strpos($item['onu_name'], $user) !== false){$Port_id = $item['port_id'];$OntID = $item['onu_id'];$Description = $item['onu_name'];$OnuStatus = $item['status'];  break;}
        }
        if(!empty($Port_id))
        { 
            if( $OnuStatus == 'Online')
            {
                $ArraySecond =  HSGQ::API('https://'.$ip.'/onumgmt?form=port_cfg&port_id='.$Port_id.'&onu_id='.$OntID.'',$token);
                $ArrayThird  =  HSGQ::API('https://'.$ip.'/onumgmt?form=port_vlan&port_id='.$Port_id.'&onu_id='.$OntID.'',$token);
                        
        
                $FirstDataArray = json_decode($ArrayThird, true);     
                $SecDataArray   = json_decode($ArraySecond, true);  
                foreach ($SecDataArray['data'] as $key => $item) 
                { 
                    $op_vlan_mode = '';$def_vlan_id = ''; 
                    foreach ($FirstDataArray['data'] as $Zitem) 
                    {  
                        if($item['op_id'] == $Zitem['op_id'])
                        {   
                            $op_vlan_mode = $Zitem['op_vlan_mode'];
                            if($Zitem['def_vlan_id'] == 0)$def_vlan_id  = 1;
                            else $def_vlan_id  = $Zitem['def_vlan_id'];
                        }
                    }    
    
                            $Xtem = [];
                            $Xtem['ifIndex']        = $Port_id.'.'.$OntID;
                            $Xtem['portIndex']      = $item['op_id'];
                            $Xtem['vlan']           = $def_vlan_id;
                            $Xtem['portStatus']     = $item['status'];
                            $Xtem['VlanType']       = $op_vlan_mode;
                            $Xtem['portAdmin']      = $item['enable'];
                            $html["port_num_$key"]  = $Xtem;       
                }   
            }
            else
            {
                $html['shutDown'] = 1;
            }
 
        }
        else
        {
            return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
        }
    
        $html["Description"]  = $Description;    
        $html["PonPort"]      = 'EPON0/'.$Port_id.':'.$OntID;
 
        return $html;
    }

    static public function Client_Side_OnuMacs($ip,$token,$user)    
    {
        $html = [];
        $html['shutDown'] = 0;
        $Port_id = '';$OntID = '';$Description = '';$OnuStatus = '';
        Sleep(3);
        HSGQ::API('https://'.$ip.'/onu_allow_list',$token);
        Sleep(1);
        $ArrayFirst =  HSGQ::API('https://'.$ip.'/onutable',$token); 
        $dataArray = json_decode($ArrayFirst, true); 

        try {

                foreach ($dataArray['data'] as $item) 
                {
                    if (strpos($item['onu_name'], $user) !== false){$Port_id = $item['port_id'];$OntID = $item['onu_id'];$Description = $item['onu_name'];$OnuStatus = $item['status'];  break;}
                }
                if(!empty($Port_id))
                { 
                    if( $OnuStatus == 'Online')
                    {
                        HSGQ::API('https://'.$ip.'/pon_mac?form=table',$token); 
                        $ArraySecond  =   HSGQ::API('https://'.$ip.'/pon_mac_table',$token);
                        $SecDataArray = json_decode($ArraySecond, true);   
                        foreach ($SecDataArray['data'] as $key => $item) 
                        { 
                            if (strpos($item['onu_name'], $user) !== false)  
                            { 
                                $Xtem = [];
                                $Xtem['vlan']           = $item['vlan_id'];
                                $Xtem['mac']            = $item['macaddr'];
                                $Xtem['vendoor']        = HSGQ::MacFind_SNMP($item['macaddr']);
                                $html["macs_num_$key"]  = $Xtem;               
                            }
                        }   
                    }
                    else
                    {
                        $html['shutDown'] = 1;
                    }
         
                }
                else
                {
                    return response()->json(['error' => 'აბონენტი არ მოიძებნა ოელტეზე']);
                }
            
        } 
        catch (\Exception $e) 
        {}

  
 
        $html["Description"]  = $Description;    
        $html["PonPort"]      = 'EPON0/'.$Port_id.':'.$OntID;
 
        return $html;
    }

    static public function ClientSidePonSelect($ip,$read) 
    {
        $html = []; 
        $PonList = '';
        $snmp    = new \SNMP(\SNMP::VERSION_2c, $ip, $read);
 
        try {$PonList = $snmp->walk("1.3.6.1.2.1.31.1.1.1.1", TRUE);} 
        catch (\Exception $e) 
        {
            if (strpos($e->getMessage(), 'No response') !== false) 
            {
                return response()->json(['error' => $snmp->getError()]);
            }
        }

        foreach ($PonList as $key => $value) 
        {
            $filteredArray = array_filter($PonList, function ($value) 
            {
                if(strpos($value, 'PON') !== false)return  strpos($value, 'PON') !== false;
                else if(strpos($value, 'E0/') !== false)  return strpos($value, 'E0/') !== false;
            });

            foreach ($filteredArray as $key => $value) 
            {
                $ponValue  = substr($value, strpos($value, 'PON'));
                $ponValue2 = str_replace("STRING:", "", $ponValue);
                if (strpos($value, 'EPON0') == 0) 
                {   
                    $item['PonName']       = $ponValue2;
                    $item['PonIndex']      = $key;
                    $html["PonList_$key"]  = $item; 
                }
            }
        }
      
        return $html;
    }

    static public function ClientSidePonData($ip,$pon,$token)
    {
        $html = []; 
        $PonCoordinates = [];

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;   

        if(isset($pon) && isset($ip))
        { 
            $ArraySecond =  HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$pon,$token);   
    
            $SecDataArray = json_decode($ArraySecond, true); 

            if(isset($SecDataArray['data']))
            {
                foreach ($SecDataArray['data'] as $key => $item) 
                {
                    if($item['port_id'] == $pon)
                    { 
                        if($item['status'] == 'Online')
                        {
                            $TotalOnline++;
                        }
                        else
                        {
                            $TotalOffline++;
                            if($item['last_down_reason'] == 'Dying gasp')$TotalPowerOff++;
                            else if($item['last_down_reason'] == 'Laser out')$TotalWireDown++;
                        }
                      
                     
                        if($item['last_down_reason'] == '')$item['last_down_reason'] = '-';
                        $TotalOnu++;
    
                        $CoordOnuStatus = '';
                        if (strpos($item['status'], 'Online') !== false)
                        {
                            $CoordOnuStatus = 1;
                        }
                        else 
                        {
                            $CoordOnuStatus = 2;
                        }      
                        $PonCoordinates[] = $item['onu_name'].'|'.'EPON0/'.$item['port_id'].':'.$item['onu_id'].'|'.str_replace(',',' ',HSGQ::timeAgo($item['register_time'])).'|'.str_replace(',',' ',HSGQ::timeAgo($item['last_down_time'])).'|'.$CoordOnuStatus;
    

                        $Xitem['pon']         = $item['port_id'];
                        $Xitem['port']        = $item['onu_id'];
                        $Xitem['PonPort']     = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                        $Xitem['Client']      = $item['onu_name'];
                        $Xitem['Mac']         = $item['macaddr'];
                        $Xitem['Status']      = $item['status'];
                        $Xitem['Type']        = $item['dev_type'];
                        $Xitem['Vendor']      = $item['onu_type'];
                        $Xitem['Reason']      = $item['last_down_reason'];
                        $Xitem['dbm']         = $item['receive_power'];
                        $Xitem['downtime']    = HSGQ::timeAgo($item['last_down_time']);
                        $Xitem['uptime']      = HSGQ::timeAgo($item['register_time']);
                        $Xitem['Titledowntime']  = $item['last_down_time'];
                        $Xitem['Titleuptime']    = $item['register_time'];
    
                        $html["PonList_$key"]  = $Xitem; 
                    }
                }
            }
 
        }


        $html["TotalPowerOff"]  = $TotalPowerOff; 
        $html["TotalWireDown"]  = $TotalWireDown; 
        $html["TotalOnu"]       = $TotalOnu; 
        $html["TotalOnline"]    = $TotalOnline; 
        $html["TotalOffline"]   = $TotalOffline; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function ClientSidePonAllOnline($ip,$pon,$token)
    {
        $html = []; 
        $PonCoordinates = [];

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;   

        if(isset($pon) && isset($ip))
        { 
            $ArraySecond =  HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$pon,$token);
    
            $SecDataArray = json_decode($ArraySecond, true); 

            if(isset($SecDataArray['data']))
            {
                foreach ($SecDataArray['data'] as $key => $item) 
                {
                    if($item['port_id'] == $pon)
                    { 
                        if($item['status'] == 'Online')
                        {
                            $TotalOnline++;
                        }
                        else
                        {
                            $TotalOffline++;
                            if($item['last_down_reason'] == 'Dying gasp')$TotalPowerOff++;
                            else if($item['last_down_reason'] == 'Laser out')$TotalWireDown++;
                        }
                      

                        if($item['status'] == 'Online')
                        {
                            if($item['last_down_reason'] == '')$item['last_down_reason'] = '-';
                            $TotalOnu++;
        
                                    
                            $CoordOnuStatus = '';
                            if (strpos($item['status'], 'Online') !== false)
                            {
                                $CoordOnuStatus = 1;
                            }
                            else 
                            {
                                $CoordOnuStatus = 2;
                            }      
                            $PonCoordinates[] = $item['onu_name'].'|'.'EPON0/'.$item['port_id'].':'.$item['onu_id'].'|'.str_replace(',',' ',HSGQ::timeAgo($item['register_time'])).'|'.str_replace(',',' ',HSGQ::timeAgo($item['last_down_time'])).'|'.$CoordOnuStatus;
        
                            $Xitem['pon']         = $item['port_id'];
                            $Xitem['port']        = $item['onu_id'];
                            $Xitem['PonPort']     = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                            $Xitem['Client']      = $item['onu_name'];
                            $Xitem['Mac']         = $item['macaddr'];
                            $Xitem['Status']      = $item['status'];
                            $Xitem['Type']        = $item['dev_type'];
                            $Xitem['Vendor']      = $item['onu_type'];
                            $Xitem['Reason']      = $item['last_down_reason'];
                            $Xitem['dbm']         = $item['receive_power'];
                            $Xitem['downtime']    = HSGQ::timeAgo($item['last_down_time']);
                            $Xitem['uptime']      = HSGQ::timeAgo($item['register_time']);
                            $Xitem['Titledowntime']  = $item['last_down_time'];
                            $Xitem['Titleuptime']    = $item['register_time'];
                            $html["PonList_$key"]  = $Xitem; 
                        }
                     
                    }
                }
            }
 
        }


        $html["TotalPowerOff"]  = $TotalPowerOff; 
        $html["TotalWireDown"]  = $TotalWireDown; 
        $html["TotalOnu"]       = $TotalOnu; 
        $html["TotalOnline"]    = $TotalOnline; 
        $html["TotalOffline"]   = $TotalOffline; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function ClientSidePonAllOffline($ip,$pon,$token)
    {
        $html = []; 
        $PonCoordinates = [];

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;   

        if(isset($pon) && isset($ip))
        { 
            $ArraySecond =  HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$pon,$token);
    
            $SecDataArray = json_decode($ArraySecond, true); 

            if(isset($SecDataArray['data']))
            {
                foreach ($SecDataArray['data'] as $key => $item) 
                {
                    if($item['port_id'] == $pon)
                    { 
                        if($item['status'] == 'Online')
                        {
                            $TotalOnline++;
                        }
                        else
                        {
                            $TotalOffline++;
                            if($item['last_down_reason'] == 'Dying gasp')$TotalPowerOff++;
                            else if($item['last_down_reason'] == 'Laser out')$TotalWireDown++;
                        }
                      

                        if($item['status'] == 'Online')
                        {
                            //
                        }
                        else
                        {
                            if($item['last_down_reason'] == '')$item['last_down_reason'] = '-';
                            $TotalOnu++;
        
                                
                            $CoordOnuStatus = '';
                            if (strpos($item['status'], 'Online') !== false)
                            {
                                $CoordOnuStatus = 1;
                            }
                            else 
                            {
                                $CoordOnuStatus = 2;
                            }      
                            $PonCoordinates[] = $item['onu_name'].'|'.'EPON0/'.$item['port_id'].':'.$item['onu_id'].'|'.str_replace(',',' ',HSGQ::timeAgo($item['register_time'])).'|'.str_replace(',',' ',HSGQ::timeAgo($item['last_down_time'])).'|'.$CoordOnuStatus;
        
                            $Xitem['pon']         = $item['port_id'];
                            $Xitem['port']        = $item['onu_id'];
                            $Xitem['PonPort']     = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                            $Xitem['Client']      = $item['onu_name'];
                            $Xitem['Mac']         = $item['macaddr'];
                            $Xitem['Status']      = $item['status'];
                            $Xitem['Type']        = $item['dev_type'];
                            $Xitem['Vendor']      = $item['onu_type'];
                            $Xitem['Reason']      = $item['last_down_reason'];
                            $Xitem['dbm']         = $item['receive_power'];
                            $Xitem['downtime']    = HSGQ::timeAgo($item['last_down_time']);
                            $Xitem['uptime']      = HSGQ::timeAgo($item['register_time']);
                            $Xitem['Titledowntime']  = $item['last_down_time'];
                            $Xitem['Titleuptime']    = $item['register_time'];
                            $html["PonList_$key"]  = $Xitem; 
                        }
                     
                    }
                }
            }
 
        }


        $html["TotalPowerOff"]  = $TotalPowerOff; 
        $html["TotalWireDown"]  = $TotalWireDown; 
        $html["TotalOnu"]       = $TotalOnu; 
        $html["TotalOnline"]    = $TotalOnline; 
        $html["TotalOffline"]   = $TotalOffline; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function ClientSidePonAllWireDown($ip,$pon,$token)
    {
        $html = []; 
        $PonCoordinates = [];

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;   

        if(isset($pon) && isset($ip))
        { 
            $ArraySecond =  HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$pon,$token);
    
            $SecDataArray = json_decode($ArraySecond, true); 

            if(isset($SecDataArray['data']))
            {
                foreach ($SecDataArray['data'] as $key => $item) 
                {
                    if($item['port_id'] == $pon)
                    { 
                        if($item['status'] == 'Online')
                        {
                            $TotalOnline++;
                        }
                        else
                        {
                            $TotalOffline++;
                            if($item['last_down_reason'] == 'Dying gasp')$TotalPowerOff++;
                            else if($item['last_down_reason'] == 'Laser out')$TotalWireDown++;
                        }
                      

                        if($item['status'] == 'Online')
                        {
                            //
                        }
                        else
                        {
                            if($item['last_down_reason'] == 'Laser out')
                            {
                                if($item['last_down_reason'] == '')$item['last_down_reason'] = '-';
                                $TotalOnu++;
            
                                            
                                $CoordOnuStatus = '';
                                if (strpos($item['status'], 'Online') !== false)
                                {
                                    $CoordOnuStatus = 1;
                                }
                                else 
                                {
                                    $CoordOnuStatus = 2;
                                }      
                                $PonCoordinates[] = $item['onu_name'].'|'.'EPON0/'.$item['port_id'].':'.$item['onu_id'].'|'.str_replace(',',' ',HSGQ::timeAgo($item['register_time'])).'|'.str_replace(',',' ',HSGQ::timeAgo($item['last_down_time'])).'|'.$CoordOnuStatus;
        
                                $Xitem['pon']         = $item['port_id'];
                                $Xitem['port']        = $item['onu_id'];
                                $Xitem['PonPort']     = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                                $Xitem['Client']      = $item['onu_name'];
                                $Xitem['Mac']         = $item['macaddr'];
                                $Xitem['Status']      = $item['status'];
                                $Xitem['Type']        = $item['dev_type'];
                                $Xitem['Vendor']      = $item['onu_type'];
                                $Xitem['Reason']      = $item['last_down_reason'];
                                $Xitem['dbm']         = $item['receive_power'];
                                $Xitem['downtime']    = HSGQ::timeAgo($item['last_down_time']);
                                $Xitem['uptime']      = HSGQ::timeAgo($item['register_time']);
                                $Xitem['Titledowntime']  = $item['last_down_time'];
                                $Xitem['Titleuptime']    = $item['register_time'];
                                $html["PonList_$key"]  = $Xitem; 
                            }
                        }
                     
                    }
                }
            }
 
        }


        $html["TotalPowerOff"]  = $TotalPowerOff; 
        $html["TotalWireDown"]  = $TotalWireDown; 
        $html["TotalOnu"]       = $TotalOnu; 
        $html["TotalOnline"]    = $TotalOnline; 
        $html["TotalOffline"]   = $TotalOffline; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function ClientSidePonAllPowerOff($ip,$pon,$token)
    {
        $html = []; 
        $PonCoordinates = [];

        $TotalPowerOff = 0;$TotalWireDown = 0;$TotalOnu = 0;$TotalOnline = 0;$TotalOffline = 0;   

        if(isset($pon) && isset($ip))
        { 
            $ArraySecond =  HSGQ::API('https://'.$ip.'/onu_allow_list?port_id='.$pon,$token);
    
            $SecDataArray = json_decode($ArraySecond, true); 

            if(isset($SecDataArray['data']))
            {
                foreach ($SecDataArray['data'] as $key => $item) 
                {
                    if($item['port_id'] == $pon)
                    { 
                        if($item['status'] == 'Online')
                        {
                            $TotalOnline++;
                        }
                        else
                        {
                            $TotalOffline++;
                            if($item['last_down_reason'] == 'Dying gasp')$TotalPowerOff++;
                            else if($item['last_down_reason'] == 'Laser out')$TotalWireDown++;
                        }
                      

                        if($item['status'] == 'Online')
                        {
                            //
                        }
                        else
                        {
                            if($item['last_down_reason'] == 'Dying gasp')
                            {
                                if($item['last_down_reason'] == '')$item['last_down_reason'] = '-';
                                $TotalOnu++;
            
                                    
                                $CoordOnuStatus = '';
                                if (strpos($item['status'], 'Online') !== false)
                                {
                                    $CoordOnuStatus = 1;
                                }
                                else 
                                {
                                    $CoordOnuStatus = 2;
                                }      
                                $PonCoordinates[] = $item['onu_name'].'|'.'EPON0/'.$item['port_id'].':'.$item['onu_id'].'|'.str_replace(',',' ',HSGQ::timeAgo($item['register_time'])).'|'.str_replace(',',' ',HSGQ::timeAgo($item['last_down_time'])).'|'.$CoordOnuStatus;
        
                                $Xitem['pon']         = $item['port_id'];
                                $Xitem['port']        = $item['onu_id'];
                                $Xitem['PonPort']     = 'EPON0/'.$item['port_id'].':'.$item['onu_id'];
                                $Xitem['Client']      = $item['onu_name'];
                                $Xitem['Mac']         = $item['macaddr'];
                                $Xitem['Status']      = $item['status'];
                                $Xitem['Type']        = $item['dev_type'];
                                $Xitem['Vendor']      = $item['onu_type'];
                                $Xitem['Reason']      = $item['last_down_reason'];
                                $Xitem['dbm']         = $item['receive_power'];
                                $Xitem['downtime']    = HSGQ::timeAgo($item['last_down_time']);
                                $Xitem['uptime']      = HSGQ::timeAgo($item['register_time']);
                                $Xitem['Titledowntime']  = $item['last_down_time'];
                                $Xitem['Titleuptime']    = $item['register_time'];
                                $html["PonList_$key"]  = $Xitem; 
                            }
                        }
                     
                    }
                }
            }
 
        }


        $html["TotalPowerOff"]  = $TotalPowerOff; 
        $html["TotalWireDown"]  = $TotalWireDown; 
        $html["TotalOnu"]       = $TotalOnu; 
        $html["TotalOnline"]    = $TotalOnline; 
        $html["TotalOffline"]   = $TotalOffline; 
        $html['PONcoordinates'] = $PonCoordinates;
        return $html;
    }

    static public function Onu_PortAdminStatus_OFF($ip,$token,$ifindex,$portIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');       
        $index = explode('.',$ifindex);
      
        try {
    
            $ArraySecond =  HSGQ::API('https://'.$ip.'/onumgmt?form=port_cfg&port_id='.$index[0].'&onu_id='.$index[1].'',$token);
    
            $dataArray = json_decode($ArraySecond, true); 
            foreach ($dataArray['data'] as $item) 
            {
                if($portIndex == $item['op_id'])
                {
                    $Pon        = $index[0];
                    $Port       = $index[1];
                    $op_id      = $item['op_id'];
                    $auto_neg   = $item['auto_neg'];
                    $flow_ctrl  = $item['flow_ctrl'];
                    $loopdetect = $item['loopdetect'];
                    $rlds_opt   = $item['rlds_opt'];
                    $rl_cir     = $item['rl_cir'];
                    $rl_pir     = $item['rl_pir'];
                    $rlus_opt   = $item['rlus_opt'];
                    $bandwidth  = $item['bandwidth'];
                }
    
            }
                                                                                             
            if(isset($op_id) && isset($Pon) && isset($Port))
            {

                $data = array(
                    'method' => 'set',
                    'param' => array(
                        "port_id"   =>$Pon,
                        "onu_id"    =>$Port,
                        "op_id"     =>$op_id,
                        "flags"     =>8,
                        "auto_neg"  =>$auto_neg,
                        "flow_ctrl" =>$flow_ctrl,
                        "loopdetect"=>$loopdetect,
                        "enable"    =>0,
                        "rlds_opt"  =>$rlds_opt,
                        "rl_cir"    =>$rl_cir,
                        "rl_pir"    =>$rl_pir,
                        "rlus_opt"  =>$rlus_opt,
                        "bandwidth" =>$bandwidth,
                    )
                );
                
                $jsonData = json_encode($data);  
         
      
                $headers = array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonData),
                    "X-Token: {$token}"
                );
                
                $curl = curl_init('https://'.$ip.'/onumgmt?form=port_cfg');
                
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                
                $response = curl_exec($curl);
                
                if (curl_errno($curl)) {
                    return response()->json(['error' => curl_error($curl)]);
                }
                
                curl_close($curl);
         
                if(strstr($response,'success') !== false)
                {
                    return 1;
                }
                else
                {
                    return response()->json(['error' => $response]);
                }
            }
 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }

        return response()->json(['error' => 'error unknow']);
    }

    static public function Onu_PortAdminStatus_ON($ip,$token,$ifindex,$portIndex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');       
        $index = explode('.',$ifindex);
     
        try {
    
            $ArraySecond =  HSGQ::API('https://'.$ip.'/onumgmt?form=port_cfg&port_id='.$index[0].'&onu_id='.$index[1].'',$token);
    
            $dataArray = json_decode($ArraySecond, true); 
            foreach ($dataArray['data'] as $item) 
            {
                if($portIndex == $item['op_id'])
                {
                    $Pon        = $index[0];
                    $Port       = $index[1];
                    $op_id      = $item['op_id'];
                    $auto_neg   = $item['auto_neg'];
                    $flow_ctrl  = $item['flow_ctrl'];
                    $loopdetect = $item['loopdetect'];
                    $rlds_opt   = $item['rlds_opt'];
                    $rl_cir     = $item['rl_cir'];
                    $rl_pir     = $item['rl_pir'];
                    $rlus_opt   = $item['rlus_opt'];
                    $bandwidth  = $item['bandwidth'];
                }
    
            }
                                                                                             
            if(isset($op_id) && isset($Pon) && isset($Port))
            {

                $data = array(
                    'method' => 'set',
                    'param' => array(
                        "port_id"   =>$Pon,
                        "onu_id"    =>$Port,
                        "op_id"     =>$op_id,
                        "flags"     =>8,
                        "auto_neg"  =>$auto_neg,
                        "flow_ctrl" =>$flow_ctrl,
                        "loopdetect"=>$loopdetect,
                        "enable"    =>1,
                        "rlds_opt"  =>$rlds_opt,
                        "rl_cir"    =>$rl_cir,
                        "rl_pir"    =>$rl_pir,
                        "rlus_opt"  =>$rlus_opt,
                        "bandwidth" =>$bandwidth,
                    )
                );
                
                $jsonData = json_encode($data);  
         
      
                $headers = array(
                    'Content-Type: application/json',
                    'Content-Length: ' . strlen($jsonData),
                    "X-Token: {$token}"
                );
                
                $curl = curl_init('https://'.$ip.'/onumgmt?form=port_cfg');
                
                curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
                curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
                
                $response = curl_exec($curl);
                
                if (curl_errno($curl)) {
                    return response()->json(['error' => curl_error($curl)]);
                }
                
                curl_close($curl);
         
                if(strstr($response,'success') !== false)
                {
                    return 1;
                }
                else
                {
                    return response()->json(['error' => $response]);
                }
            }
 
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }

        return response()->json(['error' => 'error unknow']);
    }

    static public function OnuPortVlanChange($ip,$token,$ifindex,$portIndex,$user,$vlan,$vlanMode)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');

        if($vlanMode == 0)$vlan = 1;
        $Segment = explode('.',$ifindex);
   
        try {   
   
            if($vlanMode == 0) $vlan = 0;

            $data = array(
                'method' => 'set',
                'param'  => array(
                    'port_id'      => $Segment[0],
                    'onu_id'       =>  $Segment[1],
                    'op_id'        => $portIndex,
                    'op_vlan_mode' => $vlanMode,
                    'def_vlan_id'  => $vlan,
                    'def_vlan_pri' => 0
                )
            );
            
            $jsonData = json_encode($data);
            
            $headers = array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData),
                "X-Token: {$token}"
            );
            
            $curl = curl_init('https://'.$ip.'/onumgmt?form=port_vlan');
            
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($curl);
            
            if (curl_errno($curl)) {
                return response()->json(['error' => curl_error($curl)]);
            }
            
            curl_close($curl);
        
            if(strstr($response,'success') !== false)
            {
                return 1;
            }

        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $e->getMessage()]);
        }

        return response()->json(['error' => 'error unknow']);
    }

    static public function OnuRestart($ip,$token,$ifindex,$user)
    {
        PrivilegesModel::PrivCheck('Priv_Onu');
        $Port_id = '';$OntID = '';

        try {
                self::API('https://'.$ip.'/onu_allow_list?t=1709042982000',$token);
                $ArrayFirst = HSGQ::API('https://'.$ip.'/onutable',$token);
                $dataArray  = json_decode($ArrayFirst, true);
            
                foreach ($dataArray['data'] as $item) 
                {
                    if (strpos($item['onu_name'], $user) !== false){$Port_id = $item['port_id'];$OntID = $item['onu_id']; break;}
                }
        } 
        catch (\Exception $e) 
        {
            return response()->json(['error' => $ArrayFirst]);
        }
            
        if(!empty($Port_id))
        { 

            $data = array(
                'method' => 'reboot',
                'param' => array(
                    'port_id' => $Port_id,
                    'onu_id' => [$OntID]
                )
            );
            
            $jsonData = json_encode($data);
            
            $headers = array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($jsonData),
                "X-Token: {$token}"
            );
            
            $curl = curl_init('https://'.$ip.'/onu_allow_list?form=batch');
            
            curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
            curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            
            $response = curl_exec($curl);
            
            if (curl_errno($curl)) 
            {
                return response()->json(['error' => curl_error($curl)]);
            }
            
            curl_close($curl);
    
            if(strstr($response,'success') !== false)
            {
                return true;
            }
            else
            {
                return response()->json(['error' => curl_error($curl)]);
            }
        
        }
        return response()->json(['error' => 'unknow error']);
    }

    static public function MacFind_SNMP($line)
    {

        $macAddres   = HSGQ::extractMacAddress($line);
        $Converted   = HSGQ::format_mac_address($macAddres);
        $Converted   = strtoupper($Converted);

        $json_string = Storage::get('mac-vendors-export.json');
        $mac_vendors = json_decode($json_string, true);
        $mac_prefix  = substr($Converted, 0, 8);


        foreach ($mac_vendors as $vendor)
        {
            if ($vendor['macPrefix'] === strtoupper($mac_prefix))
            {
                if ( strtoupper($mac_prefix) === '00:00:00')
                {
                    return "Unknow Device";
                }
                $Mac = trim(str_replace("\"",'',$vendor['vendorName']));
                return ($Mac);
            }
        }
    }
  
    static public function extractMacAddress($string)
    {
        preg_match('/([0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4}\.[0-9A-Fa-f]{4})|(([0-9A-Fa-f]{2}[:-]){5}([0-9A-Fa-f]{2}))|(([0-9A-Fa-f]{4}[-]){2}([0-9A-Fa-f]{4}))/', $string, $matches);
        if (isset($matches[0])) {
            return $matches[0];
        }
        return false;
    }

    static public function format_mac_address($mac_address) 
    {
        $mac_address = preg_replace('/[^0-9A-Fa-f]/', '', $mac_address);
        $mac_address = str_pad($mac_address, 12, '0', STR_PAD_LEFT);
        $mac_address = strtoupper($mac_address);
        $mac_address = implode(':', str_split($mac_address, 2));
        return $mac_address;
    }    

    static public function API($Url,$accessKey)
    {
        $ch = curl_init($Url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
        $headers = ["X-Token: {$accessKey}",];

        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
 
        
        $response = curl_exec($ch);

        if (curl_errno($ch)) 
        {
            return curl_error($ch);
        }
        curl_close($ch);
        
        return  $response;
    }

    static public function timeAgo($originalDateTimeString) 
    {
        if(strstr($originalDateTimeString, 'Not Down Before') !== false )
        {
            return 'Not Down Before';
        }
        else  if(strstr($originalDateTimeString, 'Not Register Before') !== false )
        {
            return 'Not Register Before';
        }
         

        // Check if the input string is a valid date/time format
        if (strtotime($originalDateTimeString) === false) {
            return 'Invalid date/time format';
        }
    
        // Create a DateTime object from the original string
        $originalDateTime = new \DateTime($originalDateTimeString);
    
        // Get the current DateTime
        $currentDateTime = new \DateTime();
    
        // Calculate the difference between the original date and the current date
        $uptimeInterval = $currentDateTime->diff($originalDateTime);
    
        // Extract the individual components (years, months, days, hours, minutes)
        $uptimeYears = $uptimeInterval->format('%y');
        $uptimeMonths = $uptimeInterval->format('%m');
        $uptimeDays = $uptimeInterval->format('%d');
        $uptimeHours = $uptimeInterval->format('%h');
        $uptimeMinutes = $uptimeInterval->format('%i');
    
        // Build and return the uptime string
        $uptimeString = "";
        $uptimeString .= ($uptimeYears > 0) ? "{$uptimeYears} y, " : "";
        $uptimeString .= ($uptimeMonths > 0) ? "{$uptimeMonths} m, " : "";
        $uptimeString .= "{$uptimeDays} d, {$uptimeHours} h, {$uptimeMinutes} min";
    
        if($uptimeYears > 10) {
            $uptimeString = 'Never';
        }
    
        return $uptimeString;
    }
    
}
