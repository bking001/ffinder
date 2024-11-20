<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

use App\Models\prtgClass;

class prtg_model extends Model
{
    use HasFactory;

    static public function PRTG($id)
    {
        $credentials = DB::table('parameters')->where('type','prtg')->first();
        $username    = $credentials->username;
        $password    = $credentials->password;
        $url         = $credentials->url;

        if(strlen($username) && strlen($password) && strlen($url))
        {
            $stack    = new prtgClass($url, $username, $password);
            $passhash = $stack->getpasshash();
            $Data 	  = $stack-> TableData($id);     
            if(!empty($Data))
            {
                return $stack-> xxd($Data,$url);
            }
            else
            {
                return (['error' => 'Device is not exist in PRTG']);
            }
        }
        else
        {
            return (['error' => 'Invalid PRTG credentials in local database']);
        }
    }

    static public function GRAPH($ip,$Select)
    {
        $credentials = DB::table('parameters')->where('type','prtg')->first();
        $username    = $credentials->username;
        $password    = $credentials->password;
        $url         = $credentials->url;
 
        if(strlen($username) && strlen($password) && strlen($url))
        {
            try {
                    $stack = new prtgClass($url, $username, $password);
                    $passhash = $stack->getpasshash();		 
                     
				    $Data 	= $stack-> TableData($ip);  
				if(!empty($Data))
				{
					if($Select == 0 || $Select == 1 || $Select == 2 || $Select == 3)
					{ 	 
						$ChartData =  $stack->chart($Data+1, '', '', $Select, 'svg', 15, 150, 900);
					}
					else
					{	 
						$ChartData =  $stack->chart($Data+1, '', '', 1, 'svg', 15, 150, 900);
					}
					 
					$ChartData 	= str_replace("fill='#000000'", "fill='#9ca3af'", $ChartData);	
					return $ChartData;
				} 
				else
				{	 
                    return (['error' => 'Device is not exist in PRTG']);

					// $Data = $stack-> TableDataFix($ip); 
					// if(!empty($Data))
					// { 
					// 	if($Select == 0 || $Select == 1 || $Select == 2 || $Select == 3)
					// 	{
					// 		$ChartData =  $stack->chart($Data+1, '', '', $Select, 'svg', 15, 150, 900);
					// 	}
					// 	else
					// 	{
					// 		$ChartData =  $stack->chart($Data+1, '', '', 1, 'svg', 15, 150, 900);
					// 	}
					// 	$ChartData 	= str_replace("fill='#000000'", "fill='#9ca3af'", $ChartData);	
					// 	return $ChartData;		 
					// }	   
					// else
					// {	 	 
					// 	return (['error' => 'Device is not exist in PRTG']);
					// }
				}			 
	 	  		 
			} catch (Exception $e) {return (['error' => $e->getMessage()]);}	
       
        }
        else
        {
            return (['error' => 'Invalid PRTG credentials in local database']);
        }
    }

}
