<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ubiquiti extends Model
{
    use HasFactory;

    private $_ch;
	private $_baseurl;
	private $_timeout;
	private $_username;
	private $_password;
	private $_ip;


    public function __construct($ip, $user, $password, $https = true, $port = '443', $timeout = 5){
		$this->_ch			= curl_init();
		$this->_timeout		= $timeout;
		$this->_username	= $user;
		$this->_password	= $password;
		$this->_ip			= $ip;
		$this->_baseurl		= ($https) ? 'https://'.$ip.':'.$port.'/login.cgi?uri=' : 'http://'.$ip.':'.$port.'/login.cgi?uri=';
	}

	public  function Xquery($page, $timeout = false){
		if(!$timeout){
			$timeout = $this->_timeout;
		}

		$postdata	= [
			'username'	=> $this->_username,
			'password'	=> $this->_password,
			'redirect'	=> $this->_baseurl,
			'uri'		=> $page
		];

      
 
        curl_setopt($this->_ch, CURLOPT_URL, $this->_baseurl.$page);
        curl_setopt($this->_ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($this->_ch, CURLOPT_HTTPHEADER,array("Expect:  "));
        curl_setopt($this->_ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($this->_ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($this->_ch, CURLOPT_COOKIEFILE, '/tmp/cookie-'.$this->_ip);
        curl_setopt($this->_ch, CURLOPT_COOKIEJAR, '/tmp/cookie-'.$this->_ip);
        curl_setopt($this->_ch, CURLOPT_POST, true);
        curl_setopt($this->_ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($this->_ch, CURLOPT_CONNECTTIMEOUT, $timeout);
        curl_setopt($this->_ch, CURLOPT_FOLLOWLOCATION, true);
	    curl_setopt($this->_ch, CURLOPT_POSTFIELDS, $postdata);
        $result		= curl_exec($this->_ch);
		return ($result);
	}

	private function login(){
		$exec	= $this->Xquery('/');
		if(strlen($exec)>0){ 
			return true;
		} else {
			return false;
		}
	}
  
     public function INDEX($array = false){
        if($this->login()){
            $result = $this->Xquery('/index.cgi');
            if($array){
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }
 

    public function DHCP($array = false){
        if($this->login()){
            $result = $this->Xquery('/leases.cgi');
            if($array){
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }


                                                              
	public function stations($array = false){
		if($this->login()){
			$result	= $this->Xquery('/sta.cgi');
			if($array){ 
				$result = json_decode($result, true);
				return ($result);
			} else {
				return $result;
			}
		} else {
			return false;
		}
	}




    public function status($array = false){
        if($this->login()){
            $result = $this->Xquery('/status.cgi');
            if($array)
            {
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }

    public function status_new($array = false){
        if($this->login()){
            $result = $this->Xquery('/status-new.cgi');
            if($array)
            {
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }

    public function ifstats($array = false){
        if($this->login()){
            $result = $this->Xquery('/ifstats.cgi');
            if($array){
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }

    public function iflist($array = false){
        if($this->login()){
            $result = $this->Xquery('/iflist.cgi');
            if($array){
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }

    public function brmacs($array = false){
        if($this->login()){
            $result = $this->Xquery('/brmacs.cgi?brmacs=y');
            if($array){
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }
                       
    public function station_kick($mac, $interface, $array = false){
        if($this->login())
        {                          
            $result = $this->Xquery('/stakick.cgi?staid='.$mac.'&staif='.$interface);
            if($array)
            { 
                $result = json_decode($result, true);
                return $result;
            } 
            else 
            {
                return $result;
            }
        } 
        else 
        {
            return false;
        }
    }

    public function spectrum($timeout = 10, $array = false){
        if($this->login())
        {
            $result = $this->Xquery('/survey.json.cgi', $timeout);
            if($array){
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }

    public function signal($array = false){
        if($this->login()){
            $result = $this->Xquery('/signal.cgi');
            if($array){
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }

    public function air_view($array = false){
        if($this->login()){
            $result = $this->Xquery('/air-view.cgi');
            if($array){
                $result = json_decode($result, true);
                return ($result);
            } else {
                return $result;
            }
        } else {
            return false;
        }
    }

	public function __destruct(){
		curl_close($this->_ch);
	}


}
