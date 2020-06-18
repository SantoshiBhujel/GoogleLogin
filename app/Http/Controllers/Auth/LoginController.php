<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Mail\MailPassword;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Providers\RouteServiceProvider;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Foundation\Auth\AuthenticatesUsers;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
    
    public function redirectToGoogleProvider()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @return \Illuminate\Http\Response
     */
    public function handleGoogleProviderCallback()
    {
        try
        {
            //$user = Socialite::driver('google')->user();  replaced  by::
            $user = Socialite::driver('google')->stateless()->user();
        }
        catch (Exception $e) 
        {
            return redirect('login/google');
        }

        //dd('Auth');
        $authUser = $this->CreateUser($user);
        Auth::login($authUser, true);

        return view('layouts.app');
    }

    public function CreateUser($user)
    {
        $authUser = User::where('email', $user->email)->first();
        if ($authUser) 
        {
            return $authUser;
        }

        $password= mt_rand(10000000,99999999);

        //dd($password);
        
        $user =User::create([
            'name'     => $user->name,
            'email'    => $user->email,
            'password' => Hash::make($password),
        ]);

        Mail::queue(new MailPassword($user,$password));

        //dd($user);
        return $user;
    }

    public function showLoginForm()
    {
        return view('auth.login')->withTitle('Login');
    }

}
