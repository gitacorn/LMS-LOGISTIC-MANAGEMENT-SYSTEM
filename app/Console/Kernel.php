<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Auto-process queue jobs every minute for email scheduling
        $schedule->command('queue:work --stop-when-empty')->everyMinute()->withoutOverlapping();
        
        // Check announcements at 12 AM daily for email scheduling
        $schedule->command('announcement:check-emails')->dailyAt('12:00');
        
        // Also run the existing cron jobs
        $schedule->call(function () {
            $controller = new \App\Http\Controllers\CronController();
            // $controller->index();
        })->everyMinute();
        
        $schedule->call(function () {
            $controller = new \App\Http\Controllers\CronController();
            $controller->logoutEntry();
        })->everyMinute();
        
        $schedule->call(function () {
            $controller = new \App\Http\Controllers\CronController();
            $controller->sendMailToPendingDeliveryOfBuyer();
        })->dailyAt('00:15');

        // Lock tomorrow's pallet forecasts per warehouse daily at 23:59
        $schedule->call(function () {
            $controller = new \App\Http\Controllers\CronController();
            $controller->lockWarehousePalletForecasts();
        })->dailyAt('23:59');
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
