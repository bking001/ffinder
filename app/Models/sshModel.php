<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use phpseclib3\Net\SSH2;
use Illuminate\Support\Str;

class sshModel extends Model
{
    use HasFactory;

    static public function SSH($server,$port,$username,$password,$commandArray,$Readmode = true)
    {
      
        $ssh = new SSH2($server, $port);
        if (!$ssh->login($username, $password)) {
            return response()->json(['error' => 'SSH Login failed']);
        }
 
        if(!$ssh->isConnected())
        {
            return response()->json(['error' => 'SSH No Connection']);
        }

        $ssh->setTimeout(1);

        $result = [];
   
        $CommandCount = 1;
        foreach ($commandArray as  $Command) 
        {        
            $CommandCount++;
            $ssh->write($Command."\n");   
           
            if($Readmode)
            {
                $sms = $ssh->read('#');

                //$sms = preg_replace('/[^\x20-\x7E]/', '', $sms);
                $lines = explode("\n", $sms); 
                foreach ($lines as $line) 
                { 
                    $line = str_replace(["\r","\""],'',$line);
                    $line = ltrim($line);
                    if(!empty($line) && $line !== $Command && strpos($line,'#') == false && $CommandCount > 3)
                    $result[] = $line;     
                }
            }
 
        }

        return  $result;  
    }

    static public function SSH_CUSTOM($server,$port,$username,$password,$commandArray,$Readmode = true)
    {
      
        $ssh = new SSH2($server, $port);
        if (!$ssh->login($username, $password)) {
            return response()->json(['error' => 'SSH Login failed']);
        }
 
        if(!$ssh->isConnected())
        {
            return response()->json(['error' => 'SSH No Connection']);
        }

        $ssh->setTimeout(3);

        $result = [];
 
        $CommandCount = 1;
        foreach ($commandArray as  $Command) 
        {         
            $CommandCount++;
            $ssh->write($Command."\n");   
           
            if($Readmode)
            {
                $sms = $ssh->read('#');   
 
                $lines = explode("\n", $sms);       
                foreach ($lines as $line) 
                {   
                    $line = str_replace(["\r","\""],'',$line);
                    $line = ltrim($line);
                    if(!empty($line) && $line !== $Command && strpos($line,'#') == false && $CommandCount > 3)
                    {
                        if(strpos($line,'gpon-onu') !== false)
                        $result[] = $line; 
                    }                      
                }
            }
 
        }
 
        return  $result;  
    }

    static public function SSH_EPON_CUSTOM($server,$port,$username,$password,$commandArray,$Readmode = true)
    {
      
        $ssh = new SSH2($server, $port);
        if (!$ssh->login($username, $password)) {
            return response()->json(['error' => 'SSH Login failed']);
        }
 
        if(!$ssh->isConnected())
        {
            return response()->json(['error' => 'SSH No Connection']);
        }

        $ssh->setTimeout(3);

        $result = [];


        foreach ($commandArray as  $Command) 
        {        
            $Command = trim($Command); 
            $ssh->write($Command . "\n");

            if ($Readmode) 
            {
                $sms = $ssh->read('');
               
                $lines = explode("\n", $sms);
                foreach ($lines as $line) {
                    $line = str_replace(["\r", "\"", "\e", "\x07"], '', $line); // Remove control characters
                    $line = ltrim($line);
    
                    $result[] = $line;
                }
            }
 
        }

        return  $result;  
    }

    static public function SSH_SECTOR($server,$port,$username,$password,$commandArray,$Readmode = true)
    {
      
        $ssh = new SSH2($server, $port);
        if (!$ssh->login($username, $password)) {
            return response()->json(['error' => 'SSH Login failed']);
        }
 
        if(!$ssh->isConnected())
        {
            return response()->json(['error' => 'SSH No Connection']);
        }

        $ssh->setTimeout(1);

        $result = [];

        $CommandCount = 1;
        foreach ($commandArray as  $Command) 
        {        
            $CommandCount++;
            $ssh->write($Command."\n");   

            if($Readmode)
            {
                $sms = $ssh->read('OK!');

                $lines = explode("\n", $sms);   
                foreach ($lines as $line) 
                { 
                    $line = str_replace(["\r","\""],'',$line);
                    $line = ltrim($line);
                    if(!empty($line) && $line !== $Command && strpos($line,'#') == false && $CommandCount > 3)
                    $result[] = $line;     
                }
            }
 
        }

        return  $result;  
    }

    

    static public function CustomEncrypt($data)
    { 
        // Generate a random encryption key (32 bytes for aes-256)
        $encryptionKey = 'Ntquerysysteminformat!0n';

        // Generate a random initialization vector (16 bytes for aes-256-cbc)
        $initializationVector = 'Win32SecretVecto';


        $encryptedData = openssl_encrypt($data, 'aes-256-cbc', $encryptionKey, 0, $initializationVector);

        // Encode the encrypted data using Base64
        return base64_encode($encryptedData);        
    }

}
