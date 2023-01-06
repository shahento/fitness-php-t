<?php

use Illuminate\Database\Seeder;
use App\Data\Entities\VendorStaff;
//
class VendorStaffTableSeeder extends Seeder
{
//    /**
//     * Run the database seeds.
//     *
//     * @return void
//     */
   public function run()
   {
       VendorStaff::insert([
           [
               'vendor_id' => 1,
               'user_id' => 2
           ]       
       ]);
   }
}
