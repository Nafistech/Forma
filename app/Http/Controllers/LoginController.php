<?php

namespace App\Http\Controllers;

use App\Models\User;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class LoginController extends Controller
{
    // public function redirectGoogle()
    // {
    // return Socialite::driver('google')->redirect();
    // }

    protected function _registerOrLoginUser($data)
    {
        $user = User::where("email", '=', $data->email)->first();
        if (!$user) {
            $user = new User();
            $user->name = $data->name;
            $user->email = $data->email;
            $user->google_id= $data->id;
            $user->google_access_token=$data->token;
            $user->google_refresh_token=$data->refreshToken;

            // Generate a JWT token
            $access_token =JWT::encode([
                'username' => $data->username,
                'email' => $data->email,
            ], 'your_secret_key', 'HS256');

            $user->access_token = $access_token;
            $user->save();
        }
        Auth::login($user);

        return response()->json([
            "success" => 'welcome User Logged in successfully',
            "access_token" => $access_token
        ], 200);


    }

    public function redirectGoogle()
{
    return Socialite::driver('google')
        ->scopes([
            'https://www.googleapis.com/auth/drive',
        ])
        ->with(['access_type' => 'offline', 'prompt' => 'consent'])
        ->redirect();
}


//     public function redirectGoogleCallback()
// {
//     $user = Socialite::driver('google')->user();
// }

public function redirectGoogleCallback()
{
    $user = auth()->user(); // Assuming you're using authentication

    // Retrieve user information from Google Drive
    $googleUser = Socialite::driver('google')->stateless()->user();

    // Update the user's information in your database
    if ($user) {
        $user->update([
            'google_drive_id' => $googleUser->id,
            'google_drive_access_token' => $googleUser->token,
            'google_drive_refresh_token' => $googleUser->refreshToken,
        ]);
    }

   // Register or log in the user and retrieve the access token
   $response = $this->_registerOrLoginUser($googleUser);
   $access_token = $response->original['access_token'];

   // Redirect the user to the specified URL with the access token
   return redirect()->to('http://localhost:5173/google/auth/redirect?access_token=' . $access_token);
}


}
