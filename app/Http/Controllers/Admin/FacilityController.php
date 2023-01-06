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
    App\Data\Services\FacilityService
};

class FacilityController extends ApiBaseController
{

    public function __construct()
    {
    }

    public function getVendorFacilityList($vendorId)
    {
        // $user = app('Dingo\Api\Auth\Auth')->user();
        $params = Request::all();
        $result = FacilityService::getVendorFacilityList($vendorId, $params);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getFacilityDetails($id)
    {
        $result = FacilityService::getFacilityDetails($id);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function deleteVendorFacility($vendorId, $facilityId)
    {
        $data = FacilityService::deleteVendorFacility($vendorId, $facilityId);
        if ($data) {
            return $this->response->array(['success' => true]);
        } else {
            return $this->response->array(['success' => false]);
        }
    }

    public function saveVendorFacility($businessId, $facilityId = null)
    {

        $input = Request::json()->all();
        $validatorRules = [
            'categories' => 'required',
            'name' => 'required',
            'street1' => 'required',
            'street2' => '',
            'state_id' => 'required|numeric',
            'city' => 'required',
            'postcode' => 'required',
            'contact_number' => 'required',
            'latitude' => '',
            'longitude' => '',
            'rules' => 'required',
            // 'facility_id' => 'required | numeric',
            // 'images' => 'required',
            // 'amenties' => 'required',
            'opening_time' => 'required',
            'closing_time' => 'required',
            // 'daily_available' => 'required',
            'max_occupation' => 'required|numeric',
            'available_days' => '',
            'price' => 'required| numeric',
            'price_unit' => 'required',
            'minimum_duration' => 'required| numeric',
            'minimum_break' => 'required| numeric',
            'first_appointment_time' => 'required',
            'last_appointment_time' => 'required',
        ];
        $validator = Validator::make($input, $validatorRules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        if (!isset($input['latitude']) && !isset($input['longitude'])) {
            // find facility latitude & longitude
            $latLong = FacilityService::getLatlongByAddress($input);
            if ($latLong) {
                $input['latitude'] = $latLong['latitude'];
                $input['longitude'] = $latLong['longitude'];
            } else {
                return $this->response->array(['success' => false, 'message' => "Invalid Address!"]);
            }
        }
        $data = FacilityService::saveVendorFacility($input, $businessId, $facilityId);
        if ($data) {
            return $this->response->array(['success' => true, 'data' => $data]);
        }
        return $this->response->array(['success' => false, 'message' => "Failed facility operation"]);
    }

    public function changeFacilityFeaturedStatus($id)
    {
        $input = Request::json()->all();
        $result = FacilityService::changeFacilityFeaturedStatus($input, $id);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getAmenitiyList()
    {
        $result = FacilityService::getAmenitiyList();
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function saveFacilitySlideImages($id)
    {
        $input = Request::json()->all();
        $data['images'] = $input['images'];
        $data['facility_id'] = $id;
        $result = FacilityService::saveFacilityImages($data);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function deleteFacilityImages($facilityId)
    {
        $input = Request::json()->all();
        $result = FacilityService::deleteFacilityImages($facilityId, $input['image_ids']);
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function createSubFacility($id)
    {
        $input = Request::json()->all();
        $validatorRules = [
            'name' => 'required'
        ];
        $validator = Validator::make($input, $validatorRules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $result = FacilityService::createSubFacility($id, $input);
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function getSubfacilities($id, $sid = false)
    {
        $result = FacilityService::getSubfacilities($id, $sid);
        if ($result) {
            return response()->json(['success' => true, 'data' => $result]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function updateSubfacility($id)
    {
        $input = Request::json()->all();
        $validatorRules = [
            'name' => 'required',
            'id' => 'required'
        ];
        $validator = Validator::make($input, $validatorRules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $result = FacilityService::updateSubfacility($id, $input);
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }


    public function deListSubfacility($id)
    {
        $input = Request::json()->all();
        $validatorRules = [
            'status' => 'required',
            'id' => 'required',
        ];
        $validator = Validator::make($input, $validatorRules);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $result = FacilityService::deListSubfacility($id, $input);
        if ($result) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }
}
