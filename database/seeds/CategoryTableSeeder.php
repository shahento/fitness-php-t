<?php

use Illuminate\Database\Seeder;
use App\Data\Entities\Category;
use Carbon\Carbon;
use Facades\App\Data\Services\VendorService;
use Ramsey\Uuid\Uuid;
use Illuminate\Support\Facades\File;

class CategoryTableSeeder extends Seeder {

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run() {

        Category::insert([
            [
                'name' => 'Hair/Beauty',
                'show_on_home' => true,
                'icon_url' => $this->getCategoryImage('haircut_update_1.png', 'icon'),
                'thumbnail_url' => $this->getCategoryImage('beauty-saloon.png', 'thumbnail'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ], [
                'name' => 'Massage Therapy',
                'show_on_home' => true,
                'icon_url' => $this->getCategoryImage('massage_update_1.png', 'icon'),
                'thumbnail_url' => $this->getCategoryImage('massage_therapy.png', 'thumbnail'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ], [
                'name' => 'Nail Salon',
                'show_on_home' => true,
                'icon_url' => $this->getCategoryImage('salon_update_1.png', 'icon'),
                'thumbnail_url' => $this->getCategoryImage('nail_salon.png', 'thumbnail'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ], [
                'name' => 'Pet Services',
                'show_on_home' => true,
                'icon_url' => $this->getCategoryImage('pet-service_update_1.png', 'icon'),
                'thumbnail_url' => $this->getCategoryImage('pet_services.png', 'thumbnail'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ], [
                'name' => 'House Cleaning',
                'show_on_home' => true,
                'icon_url' => $this->getCategoryImage('cleaning_update_1.png', 'icon'),
                'thumbnail_url' => $this->getCategoryImage('house_cleaning.png', 'thumbnail'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ], [
                'name' => 'Restaurants',
                'show_on_home' => true,
                'icon_url' => $this->getCategoryImage('restaurent_update_1.png', 'icon'),
                'thumbnail_url' => $this->getCategoryImage('restaurent.png', 'thumbnail'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ], [
                'name' => 'Photography',
                'show_on_home' => true,
                'icon_url' => $this->getCategoryImage('photo_update_1.png', 'icon'),
                'thumbnail_url' => $this->getCategoryImage('photography.png', 'thumbnail'),
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]
        ]);
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
