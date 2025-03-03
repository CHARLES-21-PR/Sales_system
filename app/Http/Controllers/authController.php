<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Log;
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

            $user = User::where('google_id', $googleUser->id)->orWhere('email', $googleUser->email)->first();

            if ($user) {
                // Actualizar el google_id si es necesario
                if (!$user->google_id) {
                    $user->google_id = $googleUser->id;
                    $user->save();
                }
                Auth::login($user);
                return redirect()->route('dashboard');
            } else {
                $userData = User::create([
                    'name' => $googleUser->name,
                    'password' => bcrypt('123456dummy'), // Usar bcrypt en lugar de encrypt
                    'email' => $googleUser->email,
                    'google_id' => $googleUser->id,
                ]);

                if ($userData) {
                    Auth::login($userData);
                    return redirect()->route('dashboard');
                }
            }
        } catch (Exception $e) {
            // Registrar el error en los logs de Laravel
            Log::error('Error en la autenticación de Google: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Hubo un problema al iniciar sesión con Google.');
        }
    }
}
