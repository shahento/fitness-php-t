<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Log;

use App\Data\Entities\Vendor;
use App\Data\Entities\Booking;
use App\Notifications\VendorOrderAckReminderNotification;
use Facades\{
    App\Data\Services\BookingService
};
use App\Jobs\ReviewNotifyJob;
use Carbon\Carbon;


use \App\Data\Constants\OrderStatus;

class CustomerBookingReview extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:EndUserBookingReview';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ask for review';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $slotDiff = env('SLOT_DIFF', 15);
        Log::info("Booking warning mail init");
        $bookings = Booking::where('status', OrderStatus::COMPLETED)
            ->where('review_notifiation_sent', false)
            ->where('utc_end_time', '<',  Carbon::now())
            ->get();
        foreach ($bookings as $booking) {
            Log::info("Sending notification to booking" . $booking->id);
            $booking->review_notifiation_sent = 1;
            $booking->save();
            dispatch(new ReviewNotifyJob($booking));
        }
    }
}
