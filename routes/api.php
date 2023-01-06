<?php

use Illuminate\Http\Request;
use App\Data\Constants\UserType;

/*
  |--------------------------------------------------------------------------
  | API Routes
  |--------------------------------------------------------------------------
  |
  | Here is where you can register API routes for your application. These
  | routes are loaded by the RouteServiceProvider within a group which
  | is assigned the "api" middleware group. Enjoy building your API!
  |
 */

$api = app('Dingo\Api\Routing\Router');

$api->version('v1', function ($api) {

    $api->group(['namespace' => 'App\Http\Controllers'], function ($api) {
        $api->post('/login', 'AuthController@login');
        $api->post('/logout', 'AuthController@logout');
        $api->post('/refresh-token', 'AuthController@refreshToken');
        $api->post('/register-device', 'FcmController@registerDevice');
        $api->post('/forgot-password', 'UserController@forgotUserPassword');
        $api->post('/reset-password', 'UserController@passwordUpdate');

        $api->post('/user/register', 'UserController@registerUser');
        $api->get('/home', 'VendorController@getHomepageData');
        $api->get('/places', 'GoogleMapController@getPlacesFromGoogleMap');
        $api->get('/lat-long', 'GoogleMapController@getLatandLongFromAddress');
        $api->get('/states', 'UserController@getStates');

        $api->get('/facility/featured', 'FacilityController@getFeaturedFacility');
        $api->get('/facility/near-by', 'FacilityController@getFacilityNearYou');
        $api->get('/facility/search', 'FacilityController@getFacilityBySearch');
        $api->post('/facility/validate-slots', 'FacilityController@checkSlotAvailbleForBooking');

        $api->get('/facility/{id}', 'FacilityController@getFacilityDetails');
        $api->get('/facility/{id}/slots', 'FacilityController@getFacilitySlots');
        $api->get('/facility/{id}/durations', 'FacilityController@getDurationsByslot');
        $api->get('/facility/{id}/closed', 'FacilityController@getFacilityClosedDates');
        $api->get('/facility/{id}/ratings', 'FacilityController@getFacilityRatings');

        $api->post('/booking/validate', 'BookingController@validateBooking');

        $api->get('/category', 'CategoryController@getCategoryList');
        $api->get('/category/{id}/facility', 'CategoryController@getFacilitysByCategory');
        $api->get('/facility/{id}/sub-facility', 'FacilityController@getSubfacilities');
    });

    $api->group(['namespace' => 'App\Http\Controllers\Business', 'prefix' => 'business'], function ($api) {
        $api->post('/login', 'AuthController@login');
        $api->post('/logout', 'AuthController@logout');
        $api->post('/register', 'VendorController@saveVendors');
        $api->post('/refresh-token', 'AuthController@refreshToken');
        $api->get('/places', 'GoogleMapController@getPlacesFromGoogleMap');
        $api->get('/payment-frequency', 'FacilityController@getPaymentFrequency');
        $api->post('/forgot-password', 'AuthController@forgotUserPassword');
    });
    $api->group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin'], function ($api) {
        $api->post('/login', 'AuthController@login');
        $api->post('/logout', 'AuthController@logout');
        $api->post('/refresh-token', 'AuthController@refreshToken');
        $api->get('/states', 'UsersController@getStates');
    });

    $api->group(['middleware' => 'api.auth'], function ($api) {
        $api->group(['namespace' => 'App\Http\Controllers', 'middleware' => 'validate.token:' . UserType::END_USER], function ($api) { //need to add a middleware to filter other users
            $api->get('/user', 'App\Http\Controllers\UserController@getCurrentUser');

            $api->get('/me/cards', 'UserController@getSavedCards');
            $api->get('/me/cards/default', 'UserController@getCustomerDefaultCard');
            $api->post('/me/cards', 'UserController@saveCard');
            $api->post('/me/cards/{id}/default', 'UserController@setCustomerDefaultCard');
            $api->delete('/me/cards/{id}', 'UserController@deleteSavedCard');
            $api->get('/me', 'UserController@getUserProfile');
            $api->get('/me/messages', 'MessageController@getUserMessages');
            $api->get('/me/messages/{id}', 'MessageController@getFacilityMessages');
            $api->post('/me/messages/{id}', 'MessageController@saveMessage');
            $api->post('/me', 'UserController@updateUserProfile');
            $api->post('/me/password-update', 'UserController@updateUserPassword');
            $api->post('/me/upload-profile-pic', 'UserController@uploadProfilePic');
            $api->post('/me/remove-icon/{id?}', 'UserController@deleteUserProfilePic');
            $api->post('/booking', 'BookingController@createBooking');
            $api->get('/booking', 'BookingController@getBookings');
            $api->get('/booking/{id}', 'BookingController@getBookingDetails');
            $api->post('/rating/{id}', 'FacilityController@saveRating');
            $api->post('/view/{id}', 'FacilityController@saveView');
            $api->get('/my-visits/', 'FacilityController@getRecentVisits');
            $api->post('/verify-user/', 'UserController@verifyEmail');
            // $api->get('/facility/{id}/ratings', 'App\Http\Controllers\FacilityController@getFacilityRatings');

            $api->get('/notification', 'NotificationController@getUserNotifications');
            $api->get('/notification/{id}', 'NotificationController@getNotificationDetails'); 
            
            $api->post('/payment-intent', 'PaymentController@createPaymentIntent');

            $api->post('/booking/{id}/cancel', 'BookingController@cancelBooking');
        });


        $api->group(['namespace' => 'App\Http\Controllers\Business', 'prefix' => 'business', 'middleware' => 'validate.token:' . UserType::VENDOR_STAFF], function ($api) { //need to add a middleware to filter other users
            $api->put('/profile', 'VendorController@saveVendors');
            $api->get('/profile', 'VendorController@getVendor');
            $api->post('/password-update', 'VendorController@updateUserPassword');
            $api->post('/bank-acc-with-tin', 'VendorController@updateVendorAccAndTin');
            $api->get('/bank-acc-with-tin', 'VendorController@getVendorAccAndTin');
            $api->get('/payout', 'VendorController@getVendorPayouts');

            $api->post('/facility', 'FacilityController@createFacility');
            $api->get('/amenities/{id}', 'FacilityController@getAmenities');
            $api->get('/messages', 'MessageController@getVendorMessages');
            $api->get('/messages/{id}/{cid}', 'MessageController@getFacilityMessages');
            $api->post('/messages/{id}/{cid}', 'MessageController@saveMessage');
            $api->get('/categories', 'CategoryController@getCategoryList');
            $api->get('/facilities', 'FacilityController@getVendorFacilityList');
            $api->get('/facility/{id}', 'FacilityController@getFacilityDetails');
            $api->get('/facility/{id}/closed', 'FacilityController@getFacilityClosedDatesAndSlotsWithBookings');
            $api->post('/facility/{id}/closed', 'FacilityController@markFacilityAsClosed');
            $api->post('/facility/{id}/image', 'FacilityController@deleteFacilityImages');            // used to delete images
            $api->get('/facility/{id}/ratings', 'FacilityController@getFacilityRatings');
            $api->delete('/facility/{facilityId}/reopen/{id}', 'FacilityController@unmarkFacilityClosed');
            $api->delete('/facility/{facilityId}/reopen/{id}/slot', 'FacilityController@unmarkSlotFacilityClosed');


            $api->get('/facility/{id}/sub-facility', 'FacilityController@getSubfacilities');
            $api->post('/facility/{id}/sub-facility', 'FacilityController@createSubFacility');
            $api->put('/facility/{id}/sub-facility', 'FacilityController@updateSubfacility');
            $api->put('/facility/{id}/sub-facility/de-list', 'FacilityController@deListSubfacility');

            $api->get('/bookings', 'BookingController@getmyBookings');
            $api->get('/upcoming-bookings', 'BookingController@getNewBookingsList');
            $api->get('/completed-bookings', 'BookingController@getOldBookingsList');
            $api->get('/booking/{id}', 'BookingController@getBookingDetails');
            $api->post('/booking-status/{id}', 'BookingController@updateBoookingStatus');

            $api->post('/verify-user/', 'VendorController@verifyEmail');
 
            $api->get('/notification', 'NotificationController@getUserNotifications');
            $api->get('/notification/{id}', 'NotificationController@getNotificationDetails'); 
            $api->post('/booking/{id}/cancel', 'BookingController@cancelBooking');
        });
        $api->group(['namespace' => 'App\Http\Controllers\Admin', 'prefix' => 'admin', 'middleware' => 'validate.token:' . UserType::ADMIN], function ($api) {
            // $api->post('/refresh-token', 'App\Http\Controllers\AuthController@refreshToken');
            $api->get('/me', 'UsersController@getAdminProfile');
            $api->get('/users', 'UsersController@getUsersList');
            $api->delete('/user/{id}', 'UsersController@removeUser');
            $api->get('/user/{id}', 'UsersController@getUserProfile');
            $api->post('/user/{id?}', 'UsersController@editUserProfile');

            $api->get('/categories', 'CategoryController@getCategoryList');
            $api->delete('/category/{id}', 'CategoryController@removeCategory');
            $api->get('/category/{id}', 'CategoryController@getCategory');
            $api->post('/category/{id}', 'CategoryController@editCategory');
            $api->post('/category', 'CategoryController@createCategory');
            $api->post('/category/{id}/icon', 'CategoryController@updateCategoryIcon');

            $api->get('/vendors', 'VendorController@getVendorList');
            $api->post('/vendors/{id?}', 'VendorController@saveVendors');
            $api->delete('/vendor/{id}', 'VendorController@deleteVendor');
            $api->get('/vendor/{id}', 'VendorController@getVendorDetails');

            $api->get('/vendor/{id}/facilities', 'FacilityController@getVendorFacilityList');
            $api->get('/vendor/{id}/payouts', 'VendorController@getPayouts');
            $api->post('/vendor/retry-payout', 'VendorController@retryVendorPayout');


            $api->post('/vendor/{id}/facility/{fid?}', 'FacilityController@saveVendorFacility');
            $api->delete('/vendor/{id}/facility/{fid}', 'FacilityController@deleteVendorFacility');
            $api->get('facility/{fid}', 'FacilityController@getFacilityDetails');
            $api->post('facility/{fid}/featured', 'FacilityController@changeFacilityFeaturedStatus');
            $api->get('/amenities', 'FacilityController@getAmenitiyList');
            $api->post('facility/{fid}/images', 'FacilityController@saveFacilitySlideImages');
            $api->post('facility/{id}/image', 'FacilityController@deleteFacilityImages');               // used to delete images

            $api->get('/booking', 'BookingController@getBookingList');
            $api->get('/booking/{id}', 'BookingController@getBookingDetails');

            $api->get('/admin-users', 'UsersController@getAdminList');
            $api->post('/admin-user/{id?}', 'UsersController@saveAdminUser');
            $api->post('/admin-user/{id}/change-password', 'UsersController@changeAdminPassword');
            $api->delete('/admin-user/{id?}', 'UsersController@deleteAdminUser');

            $api->get('/facility/{id}/sub-facility/{sid?}', 'FacilityController@getSubfacilities');
            $api->post('/facility/{id}/sub-facility', 'FacilityController@createSubFacility');
            $api->put('/facility/{id}/sub-facility', 'FacilityController@updateSubfacility');
            $api->put('/facility/{id}/sub-facility/de-list', 'FacilityController@deListSubfacility');
       
            $api->post('/notification', 'NotificationController@createNotification');
            $api->get('/notification', 'NotificationController@getAllNotification');
            $api->get('/notification/{id}', 'NotificationController@getNotificationDetails'); 
        });
    });
});
