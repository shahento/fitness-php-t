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
use Illuminate\Http\Request as Requests;
use App\Mail\PasswordResetSuccessMail;
use App\Data\Constants\UserType;
use App\Data\Entities\VerifyUser;
use JWTAuth;
use Illuminate\Support\Facades\Input;
use Mail;
use Config;
use DateTime;
use Facades\{
    App\Data\Services\UserService
};
use Facades\{
    App\Data\Services\AuthService
};
use Facades\App\Data\Services\GoogleMapService;
use Illuminate\Support\Facades\Hash;
use App\Data\Entities\VendorStaff;
use App\Data\Entities\User;
use Log;
use App\Data\Entities\PasswordReset;

class UserController extends ApiBaseController
{

    public function __construct()
    {
    }

    public function registerUser()
    {
        $email = $phone = null;
        $input = Request::json()->all();
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required | email | unique_email',
            'password' => 'required'
        ];

        if (!empty($input['password'])) {
            $rules['password'] = ['regex:/^(?=.*[`~!@#$%^&*)(-_+=}"{|;:<>,.\/?\'[\])(?=.*[a-z])[a-zA-Z0-9`~!@#$%^&*)(-_+=}"{|;:<>,.\/?\']{8,}$/i'];
        }
        $usertype = UserType::END_USER;
        Validator::extendImplicit('unique_email', function ($attribute, $value, $parameters, $validator) use ($usertype) {
            return UserService::isValidEmail($value, $usertype);
        });

        $validator = Validator::make($input, $rules, [
            'unique_email' => 'Email already used for another customer',
        ]);
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }
        $input['password'] = bcrypt($input['password']);
        $userId = UserService::createUser($input);
        if ($userId) {
            AuthService::sendUserVerifyCode($input['email'], $userId);
        }
        if ($userId) {
            return $this->response->array(['success' => true, 'id' => $userId]);
        }
        return $this->response->array(['success' => false]);
    }

    public function getSavedCards()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $customerId = $user->id;
        $data = UserService::getSavedCards($customerId);
        if (!$data) {
            return $this->response->array(['success' => false]);
        }
        return $this->response->array(['success' => true, 'data' => $data]);
    }

    public function getCustomerDefaultCard()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $customerId = $user->id;
        $data = UserService::getCustomerDefaultCard($customerId);
        if (!$data) {
            return $this->response->array(['success' => false]);
        }
        return $this->response->array(['success' => true, 'data' => $data]);
    }

    public function setCustomerDefaultCard($id)
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $customerId = $user->id;
        $data = UserService::setCustomerDefaultCard($id, $customerId);
        if (!$data) {
            return $this->response->array(['success' => false]);
        }
        return $this->response->array(['success' => true]);
    }

    public function saveCard()
    {
        $input = Request::json()->all();
        $validator = Validator::make($input, [
            'payment_method_id' => 'required',
            'is_default' => 'required',
        ]);
        if ($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }
        $user = app('Dingo\Api\Auth\Auth')->user();
        $customerId = $user->id;
        return UserService::saveCard($customerId, $input['payment_method_id'], $input['is_default']);
    }

    public function deleteSavedCard($id)
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $customerId = $user->id;
        $data = UserService::deleteSavedCard($customerId, $id);
        if (!$data) {
            return $this->response->array(['success' => false]);
        }
        return $this->response->array(['success' => true]);
    }

    public function getCurrentUser()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        return $user;
    }

    public function getUsers()
    {
        $users = UserService::getUsers();
        return $this->response->array(['data' => $users]);
    }

    public function getUserDetails($id)
    {
        $data = UserService::getUserDetails((int) $id);
        return $this->response->array(['data' => $data]);
    }  

    public function getVerifyEmail($error = null)
    {
        Log::debug("Verify email#####");
        $code = $data['token'] = $_GET['token'];
        $data['email'] = $_GET['email'];
        $data['error'] = '';
        Log::debug("Checking user verification code valid or not.");
        $result = UserService::checkUserVerifyCode($data['email'], null, $data['token']);
        if ($result['success']) {
            $userData = User::where('id', $result['user_id'])->first();
            if ($userData) {
                $data['id'] = $result['user_id'];
                $data['email'] = $userData['email'];
                $data['tokenValid'] = true;
            } else {
                $data['tokenValid'] = false;
                $error = 'The link you are trying to access has expired';
            }
        } else {
            $data['tokenValid'] = false;
            // $error = 'The link you are trying to access has expired';
            $error = $result['message'];
        }
        if (!empty($error)) {
            $data['error'] = $error;
        }
        return view('emails.success')->with('data', $data);
    }
    public function userVerify()
    {
        $input = Request::json()->all();
        $userId = null;
        $validator = Validator::make($input, [
            'code' => 'required',
            'username' => 'required',
            'user_type' => 'required'
        ]);
        if ($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $result = false;
        $user = AuthService::checkUsernameForEmail($input['username'], $input['user_type']);

        if (!empty($user)) {
            $result = UserService::checkUserVerifyCode($input['username'], null, $input['code']);
        } else {
            $user = AuthService::checkUsernameForContact($input['username'], $input['user_type']);
            $result = UserService::checkUserVerifyCode(null, $input['username'], $input['code']);
            if ($result['success']) {
                if (empty($user)) {
                    $user = User::find($result['user_id']);
                }
                $user->contact_number = $input['username'];
                $user->save();
            }
        }

        return $this->response->array($result);
    }

    public function verifyEmail()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $result = AuthService::verifyEmail($user->email, $user->id, true);

        if ($result) {
            return $this->response->array(['success' => true, 'message' => $result]);
        } else {
            return $this->response->array(['success' => false]);
        }
    } 

    public function updateUser($id)
    {
        $input = Request::json()->all();
        $input['userId'] = $id;
        $usertype = UserType::END_USER;
        Validator::extendImplicit('unique_email', function ($attribute, $value, $parameters, $validator) use ($usertype, $id) {
            return UserService::isValidEmail($value, $usertype, $id);
        });

        Validator::extendImplicit('unique_contact_number', function ($attribute, $value, $parameters, $validator) use ($usertype, $id) {
            return UserService::isValidPhoneNumber($value, $usertype, $id);
        });

        $validator = Validator::make(
            $input,
            [
                'first_name' => 'required',
                'last_name' => 'required',
                'email' => 'required|unique_email',
                'contact_number' => 'required|unique_contact_number'
            ],
            [
                'unique_email' => 'Email already used for another customer',
                'unique_contact_number' => 'Phone number already used for another customer'
            ]
        );

        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }

        $result = UserService::updateUser($input);

        return $this->response->array($result);
    } 

    public function forgotUserPassword()
    {
        $input = Request::json()->all();

        $validator = Validator::make(
            $input,
            [
                'username' => 'required'
            ]
        );

        if ($validator->fails()) {
            throw new ValidationHttpException($validator->errors()->all());
        }

        $phone = $email = $name = null;
        $user = AuthService::checkUsernameForEmail($input['username'], UserType::END_USER);
        if (!empty($user)) {
            $email = $user->email;
            $name = $user->first_name . ' ' . $user->last_name;

        }

        if ($user == null) {
            return $this->response->array(['success' => false, 'message' => 'Invalid username']);
        }

        if (!empty($email)) {
            AuthService::sendPasswordResetCode($email, $phone, $name, $user->user_type);
            return $this->response->array(['success' => true, 'verification_method' => 'email']);
        }
        return $this->response->array(['success' => false, 'message' => 'Something went wrong, try again later!']);
    }

    public function getPasswordChangeView()
    {
        $data = Request::all();
        $data['error'] = false;
        $data['isValid'] = AuthService::isVaildPasswordChangeToken($data['token']);
        return view('pages.password_reset')->with('data', $data);
    }

    public function passwordUpdate()
    {
        $input = Request::all();

        $rules =  [
            'token' => 'required',
            'user_type' => 'required',
            'password' => 'required',
            'confirm_password' => 'sometimes | same:password'
        ];

        if (!empty($input['password'])) {
            $rules['password'] = ['regex:^(?=.*[A-Za-z])(?=.*\d)(?=.*[@$!%*#?&])[A-Za-z\d@$!%*#?&]{8,}$^'];
        }

        $validator = Validator::make($input, $rules, array(
            'password.regex' => 'Password must contain at least 8 characters, including alphabets, numbers and special characters.'
        ));
        if ($validator->fails()) {
            $data['token'] = $input['token'];
            $data['error'] = $validator->errors()->first();
            $data['isValid'] = true;
            $data['user_type'] = $input['user_type'];
            return view('pages.password_reset')->with('data', $data);
        }
        if (!PasswordReset::where('email_token', $input['token'])->first()) {
            $data['token'] = $input['token'];
            $data['error'] = 'Invalid token.';
            $data['isValid'] = false;
            $data['user_type'] = $input['user_type'];
            return view('pages.password_reset')->with('data', $data);
        }
        $result = $this->UserPasswordUpdate($input['token'], bcrypt($input['password']), $input['user_type']);

        return view('pages.password_reset_success')->with('data', $result);
    }

    public function UserPasswordUpdate($token, $password, $userType)
    {
        $result = UserService::verifyToken($token);
        if ($result['success']) {
            $user = UserService::getUserDetailsbyCondition($result['username'], $userType);
            if (!empty($user)) {
                $user->password = $password;
                $success = $user->save();
                if ($success) {
                    UserService::removeToken($result['username']);
                    return ['success' => true];
                }
            }
        }
        return ['success' => false];
    }
    

    public function getUserProfile()
    {
        $user = app('Dingo\Api\Auth\Auth')->user();
        $loyaltyPoints = 0;
        if (isset($user->id) && !empty($user->id)) {
            $userDetails = UserService::getProfileData($user->id);
        }
        return $this->response->array(['success' => true, 'data' => $userDetails]);
    }

    public function uploadProfilePic(Requests $request)
    {
        if ($request->hasFile('file')) {
            $allowedTypes = array(IMAGETYPE_PNG, IMAGETYPE_JPEG, IMAGETYPE_GIF);
            $detectedType = exif_imagetype($request->file('file'));
            $error = in_array($detectedType, $allowedTypes);
            if ($error) {
                $id = app('Dingo\Api\Auth\Auth')->user()->id;
                $upload_file = UserService::uploadProfilePic($id, $request);
                return $this->response->array(['success' => true]);
            } else {
                return $this->response->array(['success' => false, 'message' => 'Uploaded File is not an Image']);
            }
        } else {
            return $this->response->array(['success' => false]);
        }
    }

    public function deleteUserProfilePic($id = null)
    {
        $current_user = app('Dingo\Api\Auth\Auth')->user();
        if (!empty($current_user) && $current_user->user_type != UserType::ADMIN) {
            $id = $current_user->id;
        }
        $result = UserService::deleteUserProfilePic($id);
        if ($result) {
            return $this->response->array(['success' => true]);
        }
        return $this->response->array(['success' => false]);
    }

    public function updateUserProfile()
    {
        $input = Request::json()->all();
        $current_user = app('Dingo\Api\Auth\Auth')->user();
        $usertype = $current_user->user_type;
        $userId = $current_user->id;

        if (!isset($input['timezone'])) {
            Validator::extendImplicit('unique_email', function ($attribute, $value, $parameters, $validator) use ($usertype, $userId) {
                return UserService::isValidEmail($value, $usertype, $userId);
            });

            Validator::extendImplicit('unique_contact_number', function ($attribute, $value, $parameters, $validator) use ($usertype, $userId) {
                return UserService::isValidPhoneNumber($value, $usertype, $userId);
            });

            $validator = Validator::make(
                $input,
                [
                    'first_name' => 'required',
                    'last_name' => 'required',
                    'email' => 'unique_email',
                    'contact_number' => 'unique_contact_number'
                ],
                [
                    'unique_email' => 'Email already used for another user',
                    'unique_contact_number' => 'Phone number already used for another user'
                ]
            );

            if ($validator->fails()) {
                return $this->response->array(['success' => false, 'data' => $validator->errors()->first()]);
            }
        }
        return $this->response->array($result);
    }

    public function updateUserPassword()
    {
        $input = Request::json()->all();
        $user = app('Dingo\Api\Auth\Auth')->user();
        if (empty($user)) {
            return $this->response->array(['success' => false, 'message' => 'Invalid Authentication']);
        }
        $userId = $user->id;

        $validator = Validator::make(
            $input,
            array('password' => 'required', 'new_password' => 'required')
        );
        if ($validator->fails()) {
            return $this->response->array(['success' => false, 'message' => $validator->errors()->first()]);
        }

        if (Hash::check($input['password'], $user->password)) {
            $newPassword = bcrypt($input['new_password']);
            UserService::updatePassword($userId, $newPassword);
            return $this->response->array(['success' => true]);
        } else {
            return $this->response->array(['success' => false, 'message' => 'The old password you have entered is incorrect']);
        }
        return $this->response->array(['success' => false]);
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

    public function getCountries()
    {
        $data = UserService::getCountries();
        if (!empty($data)) {
            return $this->response->array(['success' => true, 'data' => $data]);
        } else {
            return $this->response->array(['success' => false, 'data' => 'Empty values']);
        }
    }
 

    public function deleteUser($id)
    {
        $result = UserService::deleteUser($id);
        if ($result) {
            return $this->response->array(['success' => true, 'data' => $result]);
        }
        return $this->response->array(['success' => false]);
    }
}
