<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;

use Facades\{
    App\Data\Services\BookingService
};


class BookingController extends ApiBaseController
{

    public function __construct()
    {
    }

    public function createBooking()
    {
        $userTimezone = Request::header('User-Timezone', false);
        $user = app('Dingo\Api\Auth\Auth')->user();
        $input = Request::json()->all();
        $validatorRules = [
            'booking_date' => 'required',
            'slot' => 'required',
            'duration' => 'required| numeric',
            'facility_id' => 'required | numeric',
            'credit_card_id' => 'required | numeric',
            'transaction_id' => 'required'
        ];
        $validator = Validator::make($input, $validatorRules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $input['user_id'] = $user->id;
        $data = BookingService::createBooking($input, $userTimezone);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function getBookings()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $userTimezone = Request::header('User-Timezone', false);
        $params = Request::all();
        if (isset($params['start_date_utc']) && isset($params['end_date_utc'])) {
            $data = BookingService::getBookings($user->id, $userTimezone,  (isset($params['page']) && !empty($params['page'])), $params['start_date_utc'], $params['end_date_utc']);
        } else {
            $data = BookingService::getBookings($user->id, $userTimezone,  (isset($params['page']) && !empty($params['page'])));
        }
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function cancelBooking($id)
    {
        $userTimezone = Request::header('User-Timezone', false);

        $user = app('Dingo\Api\Auth\Auth')->user();
        $data = BookingService::cancelBooking($id, $user->id, false, $userTimezone);
        if ($data) {
            return $this->response->array(['success' => true, 'message' => "Your booking has been cancelled successfully."]);
        }
        return $this->response->array(['success' => false, 'message' => "Failed to cancel booking."]);
    }

    public function getBookingDetails($id)
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $userTimezone = Request::header('User-Timezone', false);
        $data = BookingService::getBookingDetails($id, $user->id, false, true, false, false, $userTimezone);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }

    public function validateBooking()
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
        $data = BookingService::validateBooking($input, $userTimezone);
        if ($data) {
            return $this->response->array($data);
        }
        return $this->response->array(['success' => false, 'message' => "No data found"]);
    }
}
