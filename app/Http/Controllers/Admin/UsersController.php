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
    App\Data\Services\UserService,
    App\Data\Services\AuthService,
};
use App\Data\Entities\VendorStaff;

class UsersController extends ApiBaseController
{

    public function __construct()
    {
    }

    public function getUsersList()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $params = Request::all();
        $result = UserService::getUsersList($params);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function removeUser($id)
    {
        $result = UserService::removeUser($id);
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getUserProfile($id)
    {
        $result = UserService::getUserProfile($id);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function editUserProfile($id = null)
    {
        $input = Request::json()->all();
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required | email | unique_email',
            'password' => 'required_if_register',
        ];
        $usertype = UserType::END_USER;
        Validator::extendImplicit('unique_email', function ($attribute, $value, $parameters, $validator) use ($usertype, $id) {
            return UserService::isValidEmail($value, $usertype, $id);
        });
        Validator::extendImplicit('required_if_register', function ($attribute, $value, $parameters, $validator) use ($id) {
            if ($id && $value) {
                return false;
            }
            if (!$id && !$value) {
                return false;
            } else {
                return true;
            }
        });

        $validator = Validator::make($input, $rules, [
            'unique_email' => 'Email already used for another customer',
            'required_if_register' => 'Password required for registration and not required for update',
        ]);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        if (isset($input['password']) && !empty($input['password'])) {
            $data['password'] = bcrypt($input['password']);
        }
        $data['first_name'] = $input['first_name'];
        $data['last_name'] = $input['last_name'];
        $data['email'] = $input['email'];
        $data['contact_number'] = $input['contact_number'];
        $data['status'] = $input['status'];
        if (!empty($data['first_name'])) {
            $data['first_name'] = ucfirst($data['first_name']);
        }
        if (!empty($data['last_name'])) {
            $data['last_name'] = ucfirst($data['last_name']);
        }
        if ($id) {
            $result = UserService::editUserProfile($id, $data);
        } else {
            $result = UserService::createUser($data, $usertype);
        }
        if ($result) {
            if (!$id) {
                AuthService::sendUserVerifyCode($data['email'], $result);
            }
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getAdminProfile()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        if (isset($user->id) && !empty($user->id)) {
            $userDetails = UserService::getProfileData($user->id);
        }
        return $this->response->array(['success' => true, 'data' => $userDetails]);
    }

    public function getStates()
    {
        $data = UserService::getStates();
        if (!empty($data)) {
            return $this->response->array(['success' => true, 'data' => $data]);
        } else {
            return $this->response->array(['success' => false, 'data' => 'Empty values']);
        }
    }

    public function getAdminList()
    {
        // $user = app('Dingo\Api\Auth\Auth')->user();
        $params = Request::all();
        $result = UserService::getAdminList($params);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function saveAdminUser($id = null)
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        if ($user['user_type'] == UserType::ADMIN) {
            $input = Request::json()->all();
            $rules = [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required | email | unique_email',
                'password' => 'required_if_register',
            ];
            $usertype = UserType::ADMIN;
            Validator::extendImplicit('unique_email', function ($attribute, $value, $parameters, $validator) use ($usertype, $id) {
                return UserService::isValidEmail($value, $usertype, $id);
            });
            Validator::extendImplicit('required_if_register', function ($attribute, $value, $parameters, $validator) use ($id) {
                if ($id && $value) {
                    return false;
                }
                if (!$id && !$value) {
                    return false;
                } else {
                    return true;
                }
            });

            $validator = Validator::make($input, $rules, [
                'unique_email' => 'Email already used for another Adminstrator',
                'required_if_register' => 'Password required for creating administrator and not required for update',
            ]);
            if ($validator->fails()) {
                return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
            }
            if (isset($input['password']) && !empty($input['password'])) {
                $data['password'] = bcrypt($input['password']);
            }
            $data['first_name'] = $input['first_name'];
            $data['last_name'] = $input['last_name'];
            $data['email'] = $input['email'];
            if (isset($input['contact_number'])) $data['contact_number'] = $input['contact_number'];
            if (isset($input['status'])) $data['status'] = $input['status'];

            $result = UserService::saveAdminUser($id, $data);
            if ($result) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        }
        return response()->json(['success' => false]);
    }

    public function deleteAdminUser($adminId)
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        if ($user['user_type'] == UserType::ADMIN) {
            $result = UserService::deleteAdminUser($adminId);
            if ($result) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        }
        return response()->json(['success' => false]);
    }

    public function changeAdminPassword($id)
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        if ($user['user_type'] == UserType::ADMIN) {
            $input = Request::json()->all();
            $rules = [
                'password' => 'required',
            ];
            $validator = Validator::make($input, $rules, []);
            if ($validator->fails()) {
                return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
            }
            $data['password'] = bcrypt($input['password']);
            $result = UserService::saveAdminUser($id, $data);
            if ($result) {
                return response()->json(['success' => true]);
            } else {
                return response()->json(['success' => false]);
            }
        }
    }
}
