<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Controllers;

use Dingo\Api\Exception\ValidationHttpException;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use JWTAuth;
use Mail;
use Config;
use Illuminate\Support\Facades\Response;
use View;
use App\Data\Constants\UserType;
use Facades\{
    App\Data\Services\MessageService
};
use App\Mail\PasswordMail;
use Auth;
use Illuminate\Support\Facades\Input;


class MessageController extends ApiBaseController {

    public function __construct() {
        
    }

    public function getFacilityMessages($facilityId) {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $result = MessageService::getFacilityMessages($facilityId, $user->id);
        if ($result !== false) {
            return $this->response->array(['success' => true, 'data' => $result]);
        } else {
            return $this->response->array(['success' => false]);
        }
    }
    public function getUserMessages() {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $result = MessageService::getUserMessages($user->id);
        if ($result !== false) {
            return $this->response->array(['success' => true, 'data' => $result]);
        } else {
            return $this->response->array(['success' => false]);
        }
    }
    public function saveMessage($facilityId) {
        $input = Request::json()->all();
        $user = app('Dingo\Api\Auth\Auth')->user();
        $rules = [
            'message' => 'required'
        ];
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $result = MessageService::saveFacilityMessage($input, $facilityId, $user);
        if ($result !== false) {
            return $this->response->array(['success' => true]);
        } else {
            return $this->response->array(['success' => false]);
        }
    }
}
