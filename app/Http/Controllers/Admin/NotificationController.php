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
 
use Facades\{
    App\Data\Services\FcmService,
  
}; 

class NotificationController extends ApiBaseController
{

    public function __construct()
    {
    }

    public function createNotification()
    {
        $input = Request::json()->all();
        $user = app('Dingo\Api\Auth\Auth')->user();
 
        $validator = Validator::make(
            $input,
            [
                'title' => 'required',
                'description' => 'required',
                'recipient' => 'required',
            ]
        );
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $result = FcmService::saveAlert($input);
        if ($result) {
            return $this->response->array(['success' => true, 'data' => $result]);
        }
        return $this->response->array(['success' => false]);
    }
 
    public function getAllNotification()
    {
        $result = FcmService::getAllNotification();
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getNotificationDetails($id)
    { 
        $result = FcmService::alertDetails($id);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
