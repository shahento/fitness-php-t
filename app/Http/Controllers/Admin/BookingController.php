<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\ApiBaseController;

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

    public function getBookingList()
    {
        $params = Request::all();
        $result = BookingService::getBookingList($params);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getBookingDetails($id)
    {
        $result = BookingService::getBookingDetails($id, false, true, true, true, true);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function deleteBooking($id)
    {
        $data = BookingService::deleteBooking($id);
        if ($data) {
            return $this->response->array(['success' => true]);
        } else {
            return $this->response->array(['success' => false]);
        }
    }
}
