<?php

use Illuminate\Database\Seeder;
use App\Data\Entities\Facility;
use Facades\{
    App\Data\Services\GoogleMapService
};

class VendorTimezoneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $facilitys = Facility::get();
        foreach($facilitys as $facility) {
            $timezone = false;
            if($facility->latitude && $facility->longitude) {
                $timezone = GoogleMapService::getVendorTimeZone($facility->latitude, $facility->longitude);
                if($timezone) {
                    $facility->timezone = $timezone;
                    $facility->save();
                } 
            }
        }
    }
}
