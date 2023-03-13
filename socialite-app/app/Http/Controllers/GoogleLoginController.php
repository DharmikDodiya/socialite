<?php

namespace App\Http\Controllers;
use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleLoginController extends Controller
{

    public function redirectToGoogle(){
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback(){

        try{
        $user = Socialite::driver('google')->user();

        $finduser = User::where('google_id', $user->id)->first();

        if($finduser){
            Auth::login($finduser);

            return redirect()->intended('dashboard');
        }
        else{
            $newUser = User::updateOrCreate(['email' => $user->email],[
                'name' => $user->name,
                'email' => $user->email,
                'google_id'=> $user->id,
                'password' => encrypt($user->password)
            ]);

            Auth::login($newUser);

            return redirect()->intended('dashboard');
        }

    } catch (Exception $e) {
        dd($e->getMessage());
    }
}
}
