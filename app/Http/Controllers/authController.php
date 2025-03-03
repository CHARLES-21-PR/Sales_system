<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Models\User;

class authController extends Controller
{
    public function googleLogin()
    {
        return Socialite::driver('google')->redirect();
    }
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

        $user = User::where('google_id', $googleUser->id)->first();

        if($user){
            
            Auth::login($user);
            
            return redirect()->route('dashboard');
        }else{
            
            $userData = User::create([
                    'name' => $googleUser->name,
                    'password' => encrypt('123456dummy'),
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                
            ]);

            if($userData){
                Auth::login($userData);
                
                 return redirect()->route('dashboard');
            }

            
            
        }

        } catch (Exception $e) {
            dd($e);
        }
        
        
    }
}
