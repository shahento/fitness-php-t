<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers\Admin;

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
use App\Data\Constants\VendorOrderAmountPaymentStatus;
use Facades\{
    App\Data\Services\VendorService,
    App\Data\Services\UserService,
    App\Data\Services\AuthService
};
use App\Data\Entities\VendorStaff;
use App\Mail\PasswordMail;
use Log;
use Carbon\Carbon;
use DateTime;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class VendorController extends ApiBaseController
{

    public function __construct()
    {
    }

    public function saveVendors($bussinesId = null)
    {
        $input = Request::json()->all();
        $user = app('Dingo\Api\Auth\Auth')->user();
        Validator::extendImplicit('unique_email', function ($attribute, $value, $parameters, $validator) use ($bussinesId) {
            return VendorService::isUniqEmailForBussiness($value,  $bussinesId);
        });
        Validator::extendImplicit('required_if_register', function ($attribute, $value, $parameters, $validator) use ($bussinesId) {
            if ($bussinesId && $value) {
                return false;
            }
            if (!$bussinesId && !$value) {
                return false;
            } else {
                return true;
            }
        });
        $validator = Validator::make(
            $input,
            [
                'name' => 'required',
                'email' => 'required|unique_email',
                'password' => 'required_if_register',
                'street1' => 'required',
                'state' => 'required',
                'city' => 'required',
                'postcode' => 'required',
                // 'contact_number' => 'required'
            ],
            [
                'unique_email' => 'Email already used for another bussiness',
                'required_if_register' => 'Password required for registration and not required for update',
            ]
        );
        if (!empty($input['password'])) {
            $rules['password'] = ['regex:/^(?=.*[`~!@#$%^&*)(-_+=}"{|;:<>,.\/?\'[\])(?=.*[a-z])[a-zA-Z0-9`~!@#$%^&*)(-_+=}"{|;:<>,.\/?\']{8,}$/i'];
        }
        if (!empty($user) && $user->user_type != UserType::VENDOR_STAFF) {
            //return unauthenticated
        }
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        if (!empty($input['password'])) {
            $input['password'] = bcrypt($input['password']);
        }
        $result = VendorService::saveVendors($input, $bussinesId);
        if ($result) {
            return $this->response->array(['success' => true, 'data' => $result]);
        }
        return $this->response->array(['success' => false]);
    }

    public function deleteVendor($id)
    {
        $data = VendorService::deleteVendor($id);
        if ($data) {
            return $this->response->array(['success' => true]);
        } else {
            return $this->response->array(['success' => false]);
        }
    }

    public function getVendorList()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $params = Request::all();
        $result = VendorService::getVendorList($params);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getVendorDetails($vendorId)
    {
        // $user = app('Dingo\Api\Auth\Auth')->user();
        $result = VendorService::getVendorDetails($vendorId);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getPayouts($id)
    {

        $result = VendorService::getVendorPayouts($id);
        if ($result) {
            return $this->response->array(['success' => true, 'data' => $result]);
        } else {
            return $this->response->array(['success' => false]);
        }
    }

    public function retryVendorPayout()
    {
        $input = Request::json()->all();
        $validator = Validator::make(
            $input,
            [
                'vendor_id' => 'required',
                'payout_id' => 'required',
            ]
        );
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $result = VendorService::retryVendorPayout($input);
        if ($result) {
            return $this->response->array(['success' => true, 'data' => $result]);
        } else {
            return $this->response->array(['success' => false]);
        }
    }
}
