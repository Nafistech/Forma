<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Firebase\JWT\JWT;
use Carbon\Carbon;

class ApiAuthController extends Controller
{
    public function register(Request $request)
    {
            // Validate the request data
           $validator= Validator::make($request->all(),[
                'name' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users',
                'password' => 'required|string|min:8|confirmed',
            ]);

            if($validator->fails())
            {
                return response()->json([
                    "errors"=>$validator->errors()
                ],301);
            }

            $access_token =JWT::encode([
                'username' => $request->username,
                'email' => $request->email,
            ], 'your_secret_key', 'HS256');

            // Create and save the user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'access_token' => $access_token
            ]);

            return response()->json(['message' => 'User registered successfully', 'access_token' => $access_token , "user" => $user], 201);
    }

    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "email" => 'required|email|max:255',
            "password" => 'required|min:8'
        ]);
        if ($validator->fails()) {
            return response()->json([
                "errors" => $validator->errors()
            ], 422);
        }

        $user = User::where("email", $request->email)->first();
        if ($user !== null) {
            $userPassword = $user->password;

            $access_token =JWT::encode([
                'username' => $request->username,
                'email' => $request->email,
            ], 'your_secret_key', 'HS256');

            $isVerfied = Hash::check($request->password, $userPassword);
            if ($isVerfied) {
                $user->update([
                    "access_token" => $access_token
                ]);

                return response()->json([
                    "success" => 'welcome User Logged in successfully',
                    "access_token" => $access_token,
                    "user" => $user
                ], 200);

            } else {
                return response()->json([
                    "msg" => "crediniatials not correct"
                ], 404);
            }


        } else {
            return response()->json([
                "error" => "This account does not exist"
            ], 404);
        }
    }

    public function updateUser(Request $request) {
        $access_token = $request->header("authorization-token");
        $user = User::where("access_token", $access_token)->first(); // Retrieve user before validation

        if ($user !== null) {
            $validator = Validator::make($request->all(), [
                "name" => 'nullable|string|max:255',
                "email" => 'nullable|email|unique:users,email,' . $user->id, // Now $user is available
            ]);

            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422); // Handle validation errors
            }

            $user->update([
                "name" => $request->name,
                "email" => $request->email
            ]);

            return response()->json(["user" => $user], 200);
        }
    }

    public function updateUserPassword(Request $request) {
        $access_token = $request->header("authorization-token");
        $user = User::where("access_token", $access_token)->first(); // Retrieve user before validation
        if ($user !== null) {
            $validator = Validator::make($request->all(), [
                "password" => 'required|string|min:8',
            ]);
            if ($validator->fails()) {
                return response()->json(["errors" => $validator->errors()], 422); // Handle validation errors
            }

            $user->update([
                'password' => Hash::make($request->password),
            ]);

            return response()->json(["user" => $user], 200);
        }
    }

    public function updateUserProfilePhoto(Request $request){
        $validator = Validator::make($request->all(), [
            "img" => 'file',
        ]);
        $access_token=$request->header("authorization-token");
        $user=User::where("access_token",$access_token)->first();
        if ($user !==null) {
            $file = $request->file('img');
            $img_path = 'images/logos';
            $currentTime = Carbon::now();
            $file_name = $user->id . '-' . $user->name . $currentTime;
            $path = $file->move($img_path,$file_name);
            $url = url(asset($path));
            $user->update(['img' => $url]);
            return response()->json([ "user" => $user ],200);
        }
    }

    public function logout(Request $request)
    {
        $access_token=$request->header("authorization-token");
        if ($access_token !==null) {
            $user=User::where("access_token",$access_token)->first();
            if ($user !==null) {
                $user->update([
                    "access_token"=>null
                ]);
                return response()->json([
                    'msg' => 'Logged Out successfully'
                ]);
            }else {
                return response()->json([
                    "msg"=>"access token not correct"
                ]);
            }
        }else {
            return response()->json([
                "msg"=>"access token not found"
            ]);
        }
    }

    public function userAuthorize(Request $request)
    {
        $access_token=$request->header("authorization-token");
        $user=User::where("access_token",$access_token)->first();
        if ($user !==null) {
            return response()->json([
                "user"=> $user
            ]);
        }else {
            return response()->json([
                "msg"=>"unauthorized"
            ] , 401);
        }
    }
}
