<?php

namespace App\Console;

use App\Console\Commands\Synchronization;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use SebastianBergmann\Environment\Console;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
        'App\Console\Commands\Synchronization',
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // $schedule->command('inspire')
        //          ->hourly();

        //每天凌晨零点运行任务
        $schedule->command('webService:synchronization')->hourly()
            ->before(function () {
                // 任务即将开始...
                Log::info('开始执行同步数据'.date('Y-m-d H:i:s'));
            })
            ->after(function () {
                // 任务已经完成...
                Log::info('执行同步数据完成,请查看storage/logs/webservice.log日志'.date('Y-m-d H:i:s'));

            });

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
