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
use Facades \{
    App\Data\Services\CategoryService,
};
use Illuminate\Http\Request as req;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Log;
use Ramsey\Uuid\Uuid;

class CategoryController extends ApiBaseController
{

    public function __construct()
    {

    }

    public function getCategoryList()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $params = Request::all();
        $result = CategoryService::getCategoryList($params);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function removeCategory($id)
    {
        $result = CategoryService::removeCategory($id);
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getCategory($id)
    {
        $result = CategoryService::getCategory($id);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function editCategory($id, req $request)
    {
        $data['name'] = $request['name'];
        $data['show_on_home'] = $request['show_on_home'];

        $result = CategoryService::editCategory($id, $data);
        if($request->hasFile('file_up')) {
            $result = CategoryService::uploadCategoryLogo($id, $request);
        }
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function createCategory(req $request)
    {
        $input = Request::json()->all();
        $rules = [
            'name' => 'required',
        ];

        $input['name'] = $request['name'];
        $input['show_on_home'] = $request['show_on_home'];
        $categoryId = CategoryService::createCategory($input);
        if($request->hasFile('file_up')) {
            $categoryId = CategoryService::uploadCategoryLogo($categoryId, $request);
        }
        if ($categoryId) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

}
