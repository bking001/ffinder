<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use App\Models\PrivilegesModel;


class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    static public function MONITOR()
    {
        PrivilegesModel::PrivCheck('OnlyAdmin');
        $html = [];
 
        // CPU Information
        $cpuInfo = shell_exec("lscpu");
        preg_match('/^CPU\(s\):\s+(\d+)/m', $cpuInfo, $cpuCores);
        preg_match('/^Model name:\s+(.+)$/m', $cpuInfo, $cpuModel);
        $html['cpuCores'] = $cpuCores[1] ?? 'N/A';
        $html['cpuModel'] = $cpuModel[1] ?? 'N/A';
        
        // CPU Usage
        $cpuUsage = sys_getloadavg()[0];
        $html['cpuUsage'] = (int)round($cpuUsage * 100);


       // Memory Information
        $memoryInfo = shell_exec('free -m');
        $memoryLines = explode("\n", $memoryInfo);
        $memoryData = preg_split('/\s+/', trim($memoryLines[1]));
        $memoryTotal = $memoryData[1]; // Total memory in MB
        $memoryUsed = $memoryData[2];   // Used memory in MB

        // Calculate actual used memory (excluding buffer/cache)
        $actualUsed = $memoryUsed; // Directly use the 'used' from free command

        $html['memoryUsage'] = number_format($actualUsed, 2) . ' MB / ' . number_format($memoryTotal, 2) . ' MB';
        $html['FixedmemoryUsage'] = number_format($actualUsed, 0);
        $html['FixedmemoryTotal'] = (float)number_format($memoryTotal, 2);
        $html['FixedmemoryAvailable'] = (float)number_format($memoryTotal, 2) - (float)number_format($actualUsed, 2);

        // Disk Information
        $diskFree = disk_free_space("/");
        $diskTotal = disk_total_space("/");
        $html['diskFree']  = number_format($diskFree / 1024 / 1024 / 1024, 2) . ' GB';
        $html['diskTotal'] = number_format($diskTotal / 1024 / 1024 / 1024, 2) . ' GB';

        $html['diskUsed'] = (int)number_format($diskTotal / 1024 / 1024 / 1024, 2)  - (int)number_format($diskFree / 1024 / 1024 / 1024, 2);
        $html['FixeddiskTotal'] = (int)round(number_format($diskTotal / 1024 / 1024 / 1024, 2));

 
        $html['Established'] = str_replace("\n",'',`netstat -ntu | grep -E ':80 |443 ' | grep ESTABLISHED | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`); 
		$html['totalconnections'] = str_replace("\n",'',`netstat -ntu | grep -E ':80 |443 ' | grep -v LISTEN | awk '{print $5}' | cut -d: -f1 | sort | uniq -c | sort -rn | grep -v 127.0.0.1 | wc -l`); 



        $html['hostname']               = $_SERVER['SERVER_NAME'];
        $html['serverip']               = $_SERVER['SERVER_ADDR'];
        $html['phpverison']             = phpversion();
        $html['uptime']                 = str_replace("\n",'',shell_exec("uptime"));  
        $html['node']                   = str_replace("\n",'',shell_exec("node -v"));   
        $html['sql']                    = str_replace("\n",'',shell_exec("mysql --version"));     
        $html['memory_limit']           = ini_get('memory_limit');
        $html['post_max_size']          = ini_get('post_max_size');
        $html['max_file_uploads']       = ini_get('max_file_uploads');
        $html['upload_max_filesize']    = ini_get('upload_max_filesize');
        $html['max_execution_time']     = ini_get('max_execution_time');

 
        return  view('serverMonitor', ['Serverstats' => $html]);
    }

    static public function UPDATE_MONITOR()
    {
        PrivilegesModel::PrivCheck('OnlyAdmin');
        $html = [];
 

        // CPU Usage
        $cpuUsage = sys_getloadavg()[0];
        $html['cpuUsage'] = (int)round($cpuUsage * 100);


       // Memory Information
        $memoryInfo = shell_exec('free -m');
        $memoryLines = explode("\n", $memoryInfo);
        $memoryData = preg_split('/\s+/', trim($memoryLines[1]));
        $memoryTotal = $memoryData[1]; // Total memory in MB
        $memoryUsed = $memoryData[2];   // Used memory in MB

        // Calculate actual used memory (excluding buffer/cache)
        $actualUsed = $memoryUsed; // Directly use the 'used' from free command

        $html['memoryUsage'] = number_format($actualUsed, 2) . ' MB / ' . number_format($memoryTotal, 2) . ' MB';
        $html['FixedmemoryUsage'] = number_format($actualUsed, 2);
        $html['FixedmemoryTotal'] = number_format($memoryTotal, 2);

        // Disk Information
        $diskFree = disk_free_space("/");
        $diskTotal = disk_total_space("/");
        $html['diskFree']  = number_format($diskFree / 1024 / 1024 / 1024, 2) . ' GB';
        $html['diskTotal'] = number_format($diskTotal / 1024 / 1024 / 1024, 2) . ' GB';

        $html['diskUsed'] = (int)number_format($diskTotal / 1024 / 1024 / 1024, 2)  - (int)number_format($diskFree / 1024 / 1024 / 1024, 2);
        $html['FixeddiskTotal'] = (int)round(number_format($diskTotal / 1024 / 1024 / 1024, 2));



        return  response()->json($html);
    }

    static public function pingAddress(Request $request) 
    {
        PrivilegesModel::PrivCheck('OnlyAdmin');
        
        $validator = validator()->make($request->only('ip'), [
            'ip'        => 'required|ipv4',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        
        $ip = $request->input('ip');

        $output = [];
        $result = exec("ping -c 4 $ip", $output, $return_var);

        $formatted_output = implode("\n", $output);
 
        return response()->json(['ping_output' => $formatted_output]);
    }
     
}
