<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Console\Kernel;
use Carbon\Carbon;
use App\Models\PrivilegesModel;
use Illuminate\Support\Facades\DB; 
 

class CRON extends Controller
{
    protected $kernel;

    public function __construct(Kernel $kernel)
    {
        $this->kernel = $kernel;
    }

    public function CronTable()
    {
        PrivilegesModel::PrivCheck('OnlyAdmin'); 

        $events = collect($this->kernel->getScheduleEvents());
      
        $html = [];
        $data = DB::table('cronJobs')->paginate(12);

        foreach ($data as $job) 
        {  
            $found = false;

            foreach ($events as $event) 
            { 
                $nextRun = $event->nextRunDate();
                $commandParts = explode(' ', $event->command);
                $cleanCommand = end($commandParts);

                if (trim($cleanCommand) == trim($job->command)) 
                {    
                    $html [] = [
                        'command'               => $cleanCommand,
                        'expression'            => $event->expression,
                        'readable'              => $this->cronExpressionToReadable($event->expression),
                        'next_run'              => $nextRun->format('Y-m-d H:i:s'),
                        'time_until_next_run'   => $this->timeUntilNextRun($nextRun),
                        'timezone'              => $event->timezone,
                        'status'                => $job->status,
                    ];

                    $found = true;
                    break;
                }
          
            }  

            if(!$found)
            {
                if($job->every == 1)
                {
                    $html [] = [
                        'command'               => $job->command,
                        'expression'            => '-',
                        'readable'              => 'DAILYAT('.$job->option.')',
                        'next_run'              => '-',
                        'time_until_next_run'   => '-',
                        'timezone'              =>'ASIA/TBILISI',
                        'status'                => $job->status,
                    ];  
                }
                else if($job->every == 2)
                {
                    $html [] = [
                        'command'               => $job->command,
                        'expression'            => '-',
                        'readable'              => 'HOURLY('.$job->option.')',
                        'next_run'              => '-',
                        'time_until_next_run'   => '-',
                        'timezone'              =>'ASIA/TBILISI',
                        'status'                => $job->status,
                    ];  
                }
                else if($job->every == 3)
                {
                    $html [] = [
                        'command'               => $job->command,
                        'expression'            => '-',
                        'readable'              => 'everyThirtyMinutes('.$job->option.')',
                        'next_run'              => '-',
                        'time_until_next_run'   => '-',
                        'timezone'              =>'ASIA/TBILISI',
                        'status'                => $job->status,
                    ];  
                }
                else if($job->every == 4)
                {
                    $html [] = [
                        'command'               => $job->command,
                        'expression'            => '-',
                        'readable'              => 'HOURLY('.$job->option.')',
                        'next_run'              => '-',
                        'time_until_next_run'   => '-',
                        'timezone'              =>'ASIA/TBILISI',
                        'status'                => $job->status,
                    ];  
                }
          
            }
        }

        return view('schedule', ['eventDetails' => $html, 'data' => $data]);
    }

    private function timeUntilNextRun($nextRun)
    {
        $now = Carbon::now();
        $diff = $now->diffForHumans($nextRun, [
            'parts' => 2, // Limit to 3 parts, e.g., "11 hours, 30 minutes"
            'short' => false, // Short format, e.g., "11h 30m"
        ]);

        return $diff;
    }
    
    private function cronExpressionToReadable($expression)
    {
        // Splitting the cron expression
        $parts = explode(' ', $expression);

        // Assuming the format: [minute] [hour] [day] [month] [weekday]
        list($minute, $hour, $day, $month, $weekday) = $parts;

        if ($minute === '0' && $hour === '0' && $day === '*' && $month === '*' && $weekday === '*') {
            return 'everyMinute';
        } elseif ($minute === '0' && $hour === '*' && $day === '*' && $month === '*' && $weekday === '*') {
            return 'hourly';
        } elseif ($minute === '0' && $hour === '0' && $day === '*' && $month === '*' && $weekday === '*') {
            return 'daily';
        } elseif ($minute === '0' && $hour !== '*' && $day === '*' && $month === '*' && $weekday === '*') {
            return "dailyAt({$hour}:00)";
        } elseif ($minute !== '*' && $hour !== '*' && $day === '*' && $month === '*' && $weekday === '*') {
            return "dailyAt({$hour}:{$minute})";
        } elseif ($minute === '0' && $hour === '0' && $day === '1' && $month === '*' && $weekday === '*') {
            return 'monthly';
        } elseif ($minute === '0' && $hour === '0' && $day === '*' && $month === '*' && $weekday === '0') {
            return 'weekly';
        } else {
            return $expression;
        }
    }
 
    static public function start(REQUEST $request)
    {
        $validator = validator()->make($request->only('command'), [
            'command' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $command = $request->input('command');

        DB::table('cronJobs')->where('command', $command)->update(['status' => 1]);

        return true;
    }

    static public function stop(REQUEST $request)
    {
        $validator = validator()->make($request->only('command'), [
            'command' => 'required|string',
        ]);

        if ($validator->fails())
        {
            $errors = $validator->errors()->all();
            return response()->json(['error' => $errors]);
        }

        $command = $request->input('command');

        DB::table('cronJobs')->where('command', $command)->update(['status' => 0]);

        return true;
    }
}
