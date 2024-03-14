<?php

namespace App\Http\Controllers;

use App\Models\User;
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
            $user->save();
        }
        Auth::login($user);
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

    // Register or log in the user
    $this->_registerOrLoginUser($googleUser);

    // Redirect the user to the dashboard
    return redirect()->back();

}


}
