<?php

use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Controllers\UserController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});
Route::get('/verify-email', 'UserController@getVerifyEmail');
Route::get('/reset-password', 'UserController@getPasswordChangeView');
Route::post('/reset-password', 'UserController@passwordUpdate');

Route::get('images/{path}/{subpath}/{filename?}', function ($path, $subpath, $filename = null) {
    if($filename == null) {
        $path = 'images/'.$path . '/' . $subpath;
    } else {
        $path = 'images/'.$path . '/' . $subpath . '/' . $filename;
    }
    if (Storage::has($path)) {
        return Image::make(storage_path('app/' . $path))->response();
    }
});

Route::post('images/{path}/{filename?}', function ($path, $filename) {
    $path = 'images/' . $path . '/' . $filename;
    if (Storage::has($path)) {
        return Image::make(storage_path('app/' . $path))->response();
    }
});

Route::get('/terms-and-condition', function () {
    return view('terms-and-condition');
});

Route::get('/privacy-policy', function () {
    return view('privacy-policy');
});
