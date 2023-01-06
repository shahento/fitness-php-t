<?php

namespace App\Http\Controllers;

use Facades\{
    App\Data\Services\GoogleMapService,
    App\Data\Services\UserService
};

use Illuminate\Support\Facades\Request;

use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Validator;
use Dingo\Api\Exception\ValidationHttpException;

class GoogleMapController extends ApiBaseController
{
    public function getPlacesFromGoogleMap()
    {
        $input = Request::input();
        $result = GoogleMapService::getPlacesFromGoogleMap($input, env('GOOGLE_API_KEY'));
        return $this->response->array($result);
    }
}
