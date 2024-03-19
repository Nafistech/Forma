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
        // Retrieve the access token from the request headers
        $access_token = request()->header("access_token");

        // Find the user based on the access token
        $user = User::where("access_token", $access_token)->first();

        if (!$user) {
            // If the user does not exist, create a new user instance
            $user = new User();
            $user->name = $data->name;
            $user->email = $data->email;

            // Generate a JWT token for the new user
            $access_token = JWT::encode([
                'username' => $data->username,
                'email' => $data->email,
            ], 'your_secret_key', 'HS256');

            // Set the generated access token for the new user
            $user->access_token = $access_token;
        }

        // Update or set Google ID, access token, and refresh token
        $user->google_id = $data->id;
        $user->google_access_token = $data->token;
        $user->google_refresh_token = $data->refreshToken;

        // Save the user instance to the database
        $user->save();

        // Log in the user
        Auth::login($user);

        // Return a JSON response indicating success and providing the access token
        return response()->json([
            "success" => 'Welcome! User logged in or registered successfully.',
            "access_token" => $user->access_token
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
   // Construct the redirect URL with the access token
   $redirectUrl = url('http://localhost:5173/google/auth/redirect?access_token=' . $access_token);

   // Redirect the user to the specified URL
   return redirect()->to($redirectUrl);
}


}
