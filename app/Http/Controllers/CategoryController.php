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
use Illuminate\Support\Facades\Input;
use Illuminate\Http\Request as Requests;
use Config;
use Storage;
use Mail;
use Facades\{
    App\Data\Services\CategoryService,
    App\Data\Services\FacilityService,
};

class CategoryController extends ApiBaseController
{

    public function __construct()
    {
    }

    public function getCategoryList()
    {
        $result = CategoryService::getCategoryList();
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getFacilitysByCategory($id)
    {
        $lat = false;
        $long = false;
        $input = Request::input();
        if (isset($input['lat']) && isset($input['long'])) {
            $lat = $input['lat'];
            $long = $input['long'];
        }
        $result = FacilityService::getFacilityList(false, $id, false, $lat, $long, true);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
