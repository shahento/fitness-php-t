<?php

namespace App\Http\Controllers\Admin;
use App\Http\Controllers\ApiBaseController;
use Dingo\Api\Exception\ValidationHttpException;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Auth;
use Facades\{
    App\Data\Services\AuthService
};
use App\Data\Constants\UserType;
use Tymon;
use Log;

class AuthController extends ApiBaseController
{
    /* check the login credentials and send a token */

    public function login()
    {
        $credentials = Request::json()->all();
        $validator = Validator::make($credentials, [
            'username' => 'required',
            'password' => 'required'
        ]);
        if ($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        $user = AuthService::checkUsernameForEmail($credentials['username'], UserType::ADMIN);
        if (empty($user)) {
            $user = AuthService::checkUsernameForEmail($credentials['username'], UserType::ADMIN, false);
            if (empty($user)) {
                return $this->response->errorUnauthorized('Please enter a valid username');
            } else {
                return $this->response->errorUnauthorized( 'This account is blocked. Please contact support');
            }
        }

        $credentials['email'] = $credentials['username'];
        $credentials['user_type'] = UserType::ADMIN;
        $credentials['status'] = 1;

        unset($credentials['username']);
        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                // return $this->response->errorUnauthorized('Invalid username or password');
                return response(['success' => false, 'message' => 'Invalid username or password']);
            }
        } catch (JWTException $e) {
            // return $this->response->errorUnauthorized('Login failed');
            return response(['success' => false, 'message' => 'Login failed'], 401);
        }

        return $this->response->array(['success' => true, 'user_id' => $user['id'], 'token' => $token]);
    }

    // refresh token before expiration. Expiration time set in config/jwt.php
    public function refreshToken()
    {
        Log::info("refresh called");
        $token = JWTAuth::getToken();
        try {
            if( JWTAuth::parseToken()->authenticate()) {
                $userOld = JWTAuth::setToken($token)->toUser(); 
                if ($userOld && $userOld->status == 0) {
                    Log::info('This account is blocked. Please contact support');
                    return response(['message' =>'This account is blocked. Please contact support', 'status' => 401], 401);
                }
            }
        } catch(\Tymon\JWTAuth\Exceptions\JWTException $e){

        }
        try {
            $newToken = JWTAuth::refresh($token);
            $user = JWTAuth::setToken($newToken)->toUser();
            if ($user->status == 0) {
                return response(['message' => 'This account is blocked. Please contact support', 'status' => 401], 401);
            }
            return response(['token' => $newToken]);
        } catch (TokenInvalidException $e) {
            return response(['error' => 'The token is invalid'], 401);
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {
            return response(['error' => 'Token absent'], 401);
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response(['error' => 'Token expired'], 401);
        } catch (\Exception $e) {
            return response(['message' => $e->getMessage(), 'status' => 401], 401);
        }
    }

    public function logout()
    {
        $token = JWTAuth::getToken();
        try {
            JWTAuth::invalidate($token);
        } catch (TokenInvalidException $e) {
            return response(['success' => false, 'message' => 'The token is invalid'], 401);
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {
            return response(['success' => true, 'message' => 'Token expired']);
        } catch (\Exception $e) {
            return response(['success' => false, 'message' => $e->getMessage()]);
        }
        return response(['success' => true]);
    }
}
