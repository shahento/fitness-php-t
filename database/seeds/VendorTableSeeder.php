<?php use Illuminate\Database\Seeder;
use App\Data\Entities\Vendor;
use App\Data\Entities\State;
use App\Data\Entities\Category;
use Illuminate\Support\Facades\File;
use Facades\App\Data\Services\VendorService;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon;

class VendorTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {
        Vendor::insert([
            [
                'name' => 'Kashmir & Lotus',
                'display_name' => 'Best Massage Therapy center in Rock Hill',
                'category_id' => $this->getCategoryIdFromName("Massage Therapy"),
                'icon_url' => $this->getVendorImage('vendor_logo_kashmir_and_lotus.jpg'),
                'latitude' => '34.9447563',
                'longitude' => '-80.9615794',
                'street1' => '1528 Meeting Street',
                'street2' => '',
                'state' => $this->getStateIdFromName("South Carolina"),
                'status' => 1,
                'city' => 'Rock Hill',
                'email' => 'jenhunt007@gmail.com',
                'postcode' => '29730',
                'contact_number' => '704-727-6746',
                'description' => "",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'name' => 'Jovie C. Salon & Spa',
                'display_name' => 'Helping you look good',
                'category_id' => $this->getCategoryIdFromName("Hair/Beauty"),
                'icon_url' => $this->getVendorImage('vendor_logo_jovi_c_saloon_and_spa.png'),
                'latitude' => '35.263553',
                'longitude' => '-81.1853817',
                'street1' => '169 W. Main Avenue',
                'street2' => '',
                'state' => $this->getStateIdFromName("North Carolina"),
                'status' => 1,
                'city' => 'Gastonia',
                'email' => 'johnnie@gmail.com',
                'postcode' => '28052',
                'contact_number' => '7048678311',
                'description' => "",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ], [
                'name' => 'Blue Print the Studio',
                'display_name' => 'Bringing your inner beauty out',
                'category_id' => $this->getCategoryIdFromName("Hair/Beauty"),
                'icon_url' => $this->getVendorImage('vendor_logo_blueprint_the_studio.jpg'),
                'latitude' => '35.032753',
                'longitude' => '-80.8163069',
                'street1' => '9208 Ardrey Kell Road',
                'street2' => 'Suite 300-8',
                'state' => $this->getStateIdFromName("Alabama"),
                'status' => 1,
                'city' => 'Charlotte',
                'email' => 'blueprintblakeney@gmail.com',
                'postcode' => '28277',
                'contact_number' => '9802925811',
                'description' => "",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'name' => 'Waggin Tails Pet Inn & Salon',
                'display_name' => 'A pet store with everything you need',
                'category_id' => $this->getCategoryIdFromName("Pet Services"),
                'icon_url' => $this->getVendorImage('vendor_logo_waggin_tails.jpg'),
                'latitude' => '35.0098616',
                'longitude' => '-81.0752718',
                'street1' => '4939 Mount Gallant Road',
                'street2' => '',
                'state' => $this->getStateIdFromName("South Carolina"),
                'status' => 1,
                'city' => 'Rock Hill',
                'email' => 'waggintailpetinn@outlook.com',
                'postcode' => '29732',
                'contact_number' => '(803) 329-5354',
                'description' => "",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ],[
                'name' => 'Blush Nail Bar & Spa',
                'display_name' => 'Your nails are a reflection of yourself',
                'category_id' => $this->getCategoryIdFromName("Nail Salon"),
                'icon_url' => $this->getVendorImage('vendor_logo_blush_nail_bar_and_spa.png'),
                'latitude' => '34.9431456',
                'longitude' => '-81.0066081',
                'street1' => '2674 Celanese Rd',
                'street2' => 'Suite 104',
                'state' => $this->getStateIdFromName("South Carolina"),
                'status' => 1,
                'city' => 'Rock Hill',
                'email' => 'johnnie@gmail.com',
                'postcode' => '29732',
                'contact_number' => '(803) 327-7400',
                'description' => "",
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
    }

    public function getVendorImage($imageName) {
        if (!File::exists(base_path('storage/app/images/vendor_icons/'))) {
            File::makeDirectory(base_path('storage/app/images/vendor_icons'));
        }
        $extension = "." . File::extension(base_path('storage/app/images/vendor_icons/') . $imageName);
        $imageId = Uuid::uuid1()->toString() . $extension;
        File::copy(base_path('database/seeds/images/vendors/') . $imageName, base_path('storage/app/images/vendor_icons/') . $imageId);

        return $imageId;
    }
    
    public function getCategoryIdFromName($categoryName) {
        return Category::where('name', '=', $categoryName)->first()->id;
    }
    
    public function getStateIdFromName($stateName) {
        return State::where('name', '=',  $stateName)->first()->id;
    }

}
