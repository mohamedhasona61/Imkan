<?php
namespace Modules\Api\Controllers;

use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use Matrix\Exception;
use Modules\User\Events\SendMailUserRegistered;
use Modules\User\Resources\UserResource;
use Validator;

class AuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:sanctum', ['except' => ['login','register']]);
    }
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_phone' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Validation error',
                'errors' => $validator->errors(),
                'status' => 0
            ], 422);
        }
        if (strpos($request->email_or_phone, '@') === false) {
            $request->email_or_phone = '+2' . $request->email_or_phone;
        }
        
        $user = User::where('email', $request->email_or_phone)->Orwhere('phone', $request->email_or_phone)->first();
        if ($user != null) {
            $credentials = [
                'email' => $request->email_or_phone,
                'password' => $request->password,
            ];
            $user->token = $request->token;
            $user->save();
            return response()->json([
                'data' => $user,
                'message' => "Success",
                'status' => 1
            ]);
        
        } else {
            return response()->json([
                'message' => 'User Not Found or Invalid credentials',
                'status' => 0
            ], 401);
        }
    }
    public function register(Request $request)
    {
        if (!is_enable_registration()) {
            return $this->sendError(__("You are not allowed to register"));
        }
        $rules = [
            'first_name' => [
                'required',
                'string',
                'max:255'
            ],
            'last_name'  => [
                'required',
                'string',
                'max:255'
            ],
            'email'      => [
                'required',
                'string',
                'email',
                'max:255',
                'unique:users'
            ],
            'password'   => [
                'required',
                'string'
            ],
            'phone'   => [
                'required',
                'numeric',
                'unique:users'
            ],

            'term'       => ['required'],
        ];
        $messages = [
            'email.required'      => __('Email is required field'),
            'email.email'         => __('Email invalidate'),
            'password.required'   => __('Password is required field'),
            'first_name.required' => __('The first name is required field'),
            'last_name.required'  => __('The last name is required field'),
            'phone.required'  => __('The Phone is required field'),
            'term.required'       => __('The terms and conditions field is required'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        } else {
            $user = \App\User::create([
                'first_name' => $request->input('first_name'),
                'last_name'  => $request->input('last_name'),
                'email'      => $request->input('email'),
                'password'   => Hash::make($request->input('password')),
                'publish'    => $request->input('publish'),
                'phone'    => '+2'.$request->input('phone'),
                'token'    => $request->input('token'),
            ]);
            event(new Registered($user));
            //Auth::loginUsingId($user->id);
            try {
                event(new SendMailUserRegistered($user));
            } catch (Exception $exception) {
                Log::warning("SendMailUserRegistered: " . $exception->getMessage());
            }
            $user->assignRole(setting_item('user_role'));
            return response()->json([
                'success' => true,
                'message' => "Register successfully",
                'data' => $user,
            ]);
        }
    }
    public function me()
    {
        $user = auth()->user();
        if(!empty($user['avatar_id'])){
            $user['avatar_url'] = get_file_url($user['avatar_id'],'full');
            $user['avatar_thumb_url'] = get_file_url($user['avatar_id']);
        }

        return $this->sendSuccess([
            'data'=>$user
        ]);
    }
    public function updateUser(Request $request)
    {
        $user = Auth::user();
        $rules = [
            'first_name' => 'required|max:255',
            'last_name'  => 'required|max:255',
            'email'      => [
                'required',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id)
            ],
        ];
        $messages = [
            'first_name.required' => __('The first name is required field'),
            'last_name.required'  => __('The last name is required field'),
            'email.required'       => __('The email field is required'),
        ];
        $validator = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return $this->sendError($validator->errors());
        }
        $user->fill($request->input());
        $user->birthday = date("Y-m-d", strtotime($user->birthday));
        $user->save();
        return $this->sendSuccess(__('Update successfully'));
    }
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Successfully logged out']);
    }
    public function changePassword(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => 'required|min:6',
        ]);

        if ($validator->fails()) {
            return $this->sendError('',['errors'=>$validator->errors()]);
        }
        $user = auth()->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return $this->sendError(__("Current password is not correct"),['code'=>'invalid_current_password']);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        // Invalidate all Tokens
        $user->tokens()->delete();

        return $this->sendSuccess(['message'=>__("Password updated. Please re-login"),'code'=>"need_relogin"]);
    }
    
    
    public function delete_account(Request $request)
    {
        $user=User::find($request->input("userId"));
        if(!$user){return response()->json(["success" => false, 'status' => 500 ,"message" => "Failed To Delete Account"]);}
        $user->email=$user->email . "deleted_account";
        $user->phone= $user->phone . "deleted_account";
        $user->name=$user->name . "deleted_account";    
        $user->save();
        return response()->json(["success" => true, 'status' => 200 , "message" => "Account Deleted Successfully"]);
    }
}
