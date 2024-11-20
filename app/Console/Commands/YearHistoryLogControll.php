<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class YearHistoryLogControll extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:year-history-log-controll';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {   
        DB::table('_'.Carbon::now()->format('F'))->where('last_update', '<=', Carbon::now()->subYear())->delete();
    }
}
