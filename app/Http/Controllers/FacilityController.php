<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use App\Http\Controllers\ApiBaseController;
use Dingo\Api\Exception\ValidationHttpException;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request as Requests;
use Config;
use Storage;
use Mail;
use App\Data\Constants\UserType;
use App\Data\Entities\Facility;
use Facades\{
    App\Data\Services\VendorService,
    App\Data\Services\UserService,
    App\Data\Services\AuthService,
    App\Data\Services\FacilityService,
};
use App\Data\Entities\VendorStaff;
use App\Data\Entities\Vendor;
use App\Mail\PasswordMail;
use Log;
use Carbon\Carbon;
use DateTime;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class FacilityController extends ApiBaseController
{

    public function __construct()
    {
    }

    public function getFeaturedFacility()
    {
        $lat = false;
        $long = false;
        $input = Request::input();
        if (isset($input['lat']) && isset($input['long'])) {
            $lat = $input['lat'];
            $long = $input['long'];
        }
        $data = FacilityService::getFacilityList(true, false, false, $lat, $long, true);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function getRecentVisits()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $data = FacilityService::getRecentVisits($user->id);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function getFacilityNearYou()
    {
        $input = Request::input();
        $ipAddress = Request::ip();
        $rules = [
            'lat' => 'required',
            'long' => 'required',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $data = FacilityService::getFacilityWithDistance($input);
        if ($data || $data == []) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function getFacilityBySearch()
    {
        $input = Request::input();
        $ipAddress = Request::ip();
        $rules = [
            'lat' => 'required',
            'long' => 'required',
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $data = FacilityService::getFacilityWithDistance($input);
        if ($data || $data == []) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function getFacilityDetails($id)
    {
        $data = FacilityService::getFacilityDetails($id, true);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function getSubfacilities($id)
    {
        $input = Request::input();
        $result = FacilityService::getSubfacilitiesForCustomer($id, $input);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function saveView($facilityId)
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $data = FacilityService::saveView($facilityId, $user->id);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }
    public function saveRating($facilityId)
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        // $input = Request::input();
        $input = Request::json()->all();
        $validatorRules = [
            'booking_id' => 'required'
        ];
        $validator = Validator::make($input, $validatorRules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $input['user_id'] = $user->id;
        $data = FacilityService::saveRating($facilityId, $input);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }
    public function  getFacilitySlots($id)
    {
        $userTimezone = Request::header('User-Timezone', false);
        $input = Request::input();
        $date = false;
        if (isset($input['date']) && !empty($input['date'])) {
            $date = $input['date'];
        }
        $data = FacilityService::getFacilitySlots($id, $date,$userTimezone, false, false, true);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function getDurationsByslot($id)
    {
        $userTimezone = Request::header('User-Timezone', false);
        $input = Request::input();
        $validatorRules = [
            'selected_slot' => 'required',
            'booking_date' => 'required',
        ];
        $validator = Validator::make($input, $validatorRules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $data = FacilityService::getDurationsByslot($id, $input, $userTimezone);

        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false]);
    }

    public function getFacilityClosedDates($id)
    {
        $data = FacilityService::getFacilityClosedDates($id);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function checkSlotAvailbleForBooking()
    {
        $userTimezone = Request::header('User-Timezone', false);
        $input = Request::json()->all();
        $validatorRules = [
            'booking_date' => 'required',
            'slot' => 'required',
            'duration' => 'required| numeric',
            'facility_id' => 'required | numeric'
        ];
        $validator = Validator::make($input, $validatorRules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $data = FacilityService::checkSlotAvailbleForBooking($input, $userTimezone);
        if ($data) {
            return $this->response->array($data);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function getFacilityRatings($facilityId)
    {
        // $user = app('Dingo\Api\Auth\Auth')->user();
        $params = Request::all();
        $result = FacilityService::getFacilityRatings($facilityId, $params);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
