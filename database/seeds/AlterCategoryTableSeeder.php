<?php

// namespace Database\Seeds;

use Illuminate\Database\Seeder;
use App\Data\Entities\Category;
// use CategoryTableSeeder as CategoryTableSeeder;
use Storage as Storage;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\File;

class AlterCategoryTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = ['Hair/Beauty' => 'haircut_update_1.png', 
            'Massage Therapy' => 'massage_update_1.png', 
            'Nail Salon' => 'salon_update_1.png', 
            'Pet Services' => 'pet-service_update_1.png', 
            'House Cleaning' => 'cleaning_update_1.png', 
            'Restaurants' => 'restaurent_update_1.png', 
            'Photography' => 'photo_update_1.png'];
        foreach($categories as $categoryName => $imageName) {
            $dbItem = Category::where('name', $categoryName)->first();
            if($dbItem) {
                $oldUrl = $dbItem->icon_url;
                // $icon_url = CategoryTableSeeder::getCategoryImage($imageName, 'icon');
                $icon_url = $this->getCategoryImage($imageName, 'icon');
                $dbItem->icon_url = $icon_url;
                $result = $dbItem->save();
                if($result && !empty($oldUrl)) {
                    $this->removeOldImage($oldUrl);
                }
            }
        }
    }

    public function removeOldImage($imageUrl) {
        $path = Storage::disk('local')->getDriver()->getAdapter()->getPathPrefix() . 'images/category_icons/' . $imageUrl;
        unlink($path);
        return true;
    }

    public function getCategoryImage($imageName, $type) {

        if (!(File::exists(base_path('storage/app/images')))) {
            File::makeDirectory(base_path('storage/app/images'));
        }
        $path = 'storage/app/images/category_icons/thumbnails/';
        $seed = 'database/seeds/images/categories/thumbnails/';
        if ($type == 'icon') {
            $path = 'storage/app/images/category_icons/';
            $seed = 'database/seeds/images/categories/';
        }
        if (!(File::exists(base_path($path)))) {
            File::makeDirectory(base_path($path));
        }

        $extension = "." . File::extension(base_path($path) . $imageName);
        $imageId = Uuid::uuid1()->toString() . $extension;
        File::copy(base_path($seed) . $imageName, base_path($path) . $imageId);

        return $imageId;
    }
}
