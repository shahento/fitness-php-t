<?php

use Illuminate\Database\Seeder;
use Carbon\Carbon;
use App\Data\Entities\Booking;
use App\Data\Entities\BookingUnavailableSlots;
use App\Helper\TimezoneHelper;

class UpdateUTCforBookingsAndClosedSlots extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $slotDiff = env('SLOT_DIFF', 15);

        $allBookings = Booking::with(['facility' => function ($query) {
            $query->withTrashed();
        }])->get();
        $allClosedSlots = BookingUnavailableSlots::with(['facility' => function ($query) {
            $query->withTrashed();
        }])->get();
        foreach ($allBookings as $booking) {
            if ($booking['facility'] && $booking['facility']['timezone']) {
                $booking->timezone =  $booking['facility']['timezone'];
                $utcTimeStart = TimezoneHelper::getUtcDateTimeFromMixedData($booking['booking_date'], $booking->slot_start, $booking['facility']['timezone']);
                $utcTimeEnd  =$utcTimeStart->copy()->addHours($booking->hours); 
                $booking->slot_end = $utcTimeStart->copy()->addHours($booking->hours)->format('H:i:s');
                $booking->utc_start_time = $utcTimeStart->toDateTimeString();
                $booking->utc_end_time = $utcTimeEnd->toDateTimeString();
                $booking->timezone = $booking['facility']['timezone'];
                $booking->save();
            }
        }

        foreach ($allClosedSlots as $closedSlot) {
            if ($closedSlot['facility'] && $closedSlot['facility']['timezone']) {
                $utcTimeStart = TimezoneHelper::getUtcDateTimeFromMixedData($closedSlot['unavailable_date'], $closedSlot->start_time, $closedSlot['facility']['timezone']);
                $utcTimeEnd  = TimezoneHelper::getUtcDateTimeFromMixedData($closedSlot['unavailable_date'], $closedSlot->end_time, $closedSlot['facility']['timezone']);
                if($utcTimeEnd < $utcTimeStart) {
                    $utcTimeEnd->addDay();
                }
                $closedSlot->utc_start_time = $utcTimeStart->toDateTimeString();
                $closedSlot->utc_end_time = $utcTimeEnd->toDateTimeString();
                $closedSlot->timezone = $closedSlot['facility']['timezone'];
                $closedSlot->save();
            }
        }
    }
}
