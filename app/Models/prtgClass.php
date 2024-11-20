<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class prtgClass extends Model
{
    use HasFactory;

    private static $server;
	private static $username;
	private static $password;
	private static $passhash;

	function __construct($server, $username, $password)
	{
		self::$server  =  $server;
		self::$username = $username;
		self::$password = $password;
		self::$passhash = $this->getpasshash();
	}


    private function sendRequest($url)
	{
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
		$response = curl_exec($ch);
		curl_close($ch);

		return $response;
	}


	private function getData($path, $parameters, $json = true, $auth = true)
	{
		if ($auth)
		{
			$parameters['username'] = self::$username;
			$parameters['passhash'] = self::$passhash;
		}

		$baseUrl = self::$server;
		$queryString = http_build_query($parameters);
		$requestUrl = $baseUrl . '/' . $path . '?' . $queryString;

		$response = $this->sendRequest($requestUrl);

		if ($json) return json_decode($response, true);
		return $response;
	}

    public function getpasshash()
	{
		$response =  $this->getData('api/getpasshash.htm', ['username' => self::$username, 'password' => self::$password], false, false);

		if (!is_numeric($response)) return false;

		return $response;
	}

    public function TableData($host)
	{
		$response  =  array($this->getData('api/table.json?content=devices&id=20906&columns=host,name,objid,',['XX' => "XX"]));
		$response2 =  array($this->getData('api/table.json?content=devices&columns=host,name,objid&count=*&,',['XX' => "XX"]));
 
 		 for ($i=0; $i < count($response[0]['devices']) ; $i++)
 		 {
 		 	//$pos = strpos($response[0]['devices'][$i]['host'], $host);
			if (strcmp($response[0]['devices'][$i]['host'], $host) === 0)//if ($pos !== false)
			{
				$ID_FIXER = strpos($response[0]['devices'][$i]['name'], 'ID');
				if ($ID_FIXER !== false)
				{  
					return ($response[0]['devices'][$i]['objid']);
				}
			}
		 }

  		 for ($i=0; $i < count($response2[0]['devices']) ; $i++)
 		 {

			if($response2[0]['devices'][$i]['host'] == $host)//  $pos = strpos($response2[0]['devices'][$i]['host'], $host);
			{
				$ID_FIXER = strpos($response2[0]['devices'][$i]['name'], 'ID');

				if ($ID_FIXER !== false)
				{
				    return ($response2[0]['devices'][$i]['objid']);
				}

			}
		 }

	}

	public function NameSearch($name)
	{ 
		$response  =  array($this->getData('api/table.json?content=devices&id=20906&columns=host,name,objid,',['XX' => "XX"]));
		$response2 =  array($this->getData('api/table.json?content=devices&columns=host,name,objid&count=*&,',['XX' => "XX"]));

		$SearchResultArray = [];
 		 for ($i=0; $i < count($response[0]['devices']) ; $i++)
 		 { 
			if (strpos( $response[0]['devices'][$i]['name'],  $name  ) !== false)   //if (strcmp($response[0]['devices'][$i]['name'], $name) === 0)
			{ 
				$SearchResultArray[$i] = $response[0]['devices'][$i]['name'];
			}
		 }

		return $SearchResultArray;
	}

    public function xxd($id,$url)  
    {   
       $html = ($this->getData('/tablewithstyles.htm?tableid=devicesensortable&content=sensors&columns=position%3Dtextshort%2Csensor%2Cminigraph%2Cpriority&sortby=position&sortable=true&links=false&refreshable=false&_=1703960829909&id='.$id.'&count=500',['XX' => "XX"],false,true));

       $html 	= str_replace('src="/images/questionmark.png"', 'src="'.$url.'/images/questionmark.png"', $html);
       $html 	= str_replace('href="sensor.htm?id=', 'href="'.$url.'/sensor.htm?id=', $html);
       $html 	= str_replace('src="/images/transparent.gif"', 'src="'.$url.'/images/transparent.gif"', $html);
       $html 	= str_replace('<h2>', '<center><h4>', $html);
       $html 	= str_replace('</h2>', '</h4></center>', $html);
       $html 	= str_replace('href="#"', '', $html);
       $html 	= str_replace('<body id="mainbody" class="systemmenu tablewithstyles language_en">', '<body id="mainbody" class="systemmenu tablewithstyles language_en" style="min-width: 1px !important;background:transparent;color:#8b92a9;">', $html);
       //$html    = str_replace('</head>', '<link href="' . asset('vendor/bladewind/css/prtg.css') . '" rel="stylesheet" type="text/css"/></head>', $html);
       $html 	= str_replace('</head>', '<link href="'.$url.'/css/prtg.css?prtgversion=17.3.32.2478+&language=en" rel="stylesheet" type="text/css"/></head>', $html);
       $html 	= str_replace('src="/javascript/lib/jquery.js', 'src="'.$url.'/javascript/lib/jquery.js?prtgversion=17.3.32.2478+&language=en', $html);
       $html 	= str_replace('/css/print.css', '', $html);
       $html 	= str_replace('onclick="return _Prtg.objectTools.setObjectPriority.call', 'onclick="', $html);
       $html 	= str_replace('onclick="window.top.location.href=\'', 'nclick="window.top.location.href=\''.$url.'/', $html);
       $start      	= strpos($html,'<!DOCTYPE html>');
       $html  	    = substr($html, $start);
       $html 		= strstr($html, '<footer class="hidebuttons">', true);

       //$html 	   .= '<script src="'.$url.'/e/javascript/prtg.js?version=17.3.32.2478+&language=en"></script></body></html>';
       $html 	   .= '<script src="' . asset('vendor/bladewind/js/prtg.js').'?version=17.3.32.2478+&language=en"></script></body></html>';


       return $html;

    }

	public function chart($sensorId, $sdate, $edate, $graphid, $type = 'svg', $avg = 15, $height = 370, $width = 850)
	{
		//https://www.paessler.com/es/manuals/prtg/live_graphs 
		
		$response =  $this->getData('chart.' . $type, ['id' => $sensorId, 'sdate' => $sdate, 'edate' => $edate, 'avg' => $avg, 'graphid' => $graphid, 'height' => $height, 'width' => $width  ,'gridcolor' => '#ffffff' ,  'plotcolor2' => '#ffffff' , 'plotcolor1' => '#ffffff' ,'bgcolor' => '' , 'graphstyling' => "showLegend='1'+baseFontSize='7'", ], false);

		if ($response == 'Error creating chart.') throw new Exception('Error creating chart.');

		return $response;
	}

}
