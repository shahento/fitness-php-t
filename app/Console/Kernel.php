<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Config;
use App\Jobs\ProcessBusinessPayouts;
use App\Jobs\CancelPendingBookings;
use App\Jobs\SentPendingBookingWarning;
use App\Jobs\UpdatePayoutStatus;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Commands\CustomerBookingReview::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        
        $schedule->command('command:EndUserBookingReview')->cron('*/1 * * * *');
        $schedule->job(new CancelPendingBookings)->everyMinute();
        $schedule->job(new SentPendingBookingWarning)->everyMinute();
        if(env('ENABLE_PAYOUT',false)) {
            $schedule->job(new ProcessBusinessPayouts)->dailyAt(env('PAYOUT_AT','13:00'));
        }
        $schedule->job(new UpdatePayoutStatus)->dailyAt(env('PAYOUT_STATUS_UPDATE_AT','14:00'));
    }

    /**
     * Register the Closure based commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        require base_path('routes/console.php');
    }
}
