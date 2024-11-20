<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\DB; 

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     */
    protected function schedule(Schedule $schedule): void
    {
        $data = DB::table('cronJobs')->get();

        foreach ($data as $job) 
        {
            // every ->   1 == daily  ,  2 - hourly , 3 - everyFifteenMinutes

            if($job->status == 1)
            {
                if($job->every == 1)
                {
                    $schedule->command($job->command)->withoutOverlapping()->dailyAt($job->option);
                }
                else  if($job->every == 2)
                {
                    $schedule->command($job->command)->withoutOverlapping()->hourly();
                }
                else  if($job->every == 3)
                {
                    $schedule->command($job->command)->withoutOverlapping()->everyThirtyMinutes();
                }
                else if($job->every == 4)
                {
                    $schedule->command($job->command)->everyTwoHours(35);
                }              
            }
        }

        //$schedule->command('app:clone-onu-check')->withoutOverlapping(); //ტესტი
    }

        /**
     * Get the scheduled events.
     */
    public function getScheduleEvents()
    {
        $schedule = app(Schedule::class);
        return $schedule->events();
    }
    
    /**
     * Register the commands for the application.
     */
    protected function commands(): void
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
