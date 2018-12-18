<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function authenticate(Request $request)
    {

        $email = $request->input('email');
        $password = $request->input('password');
        $secretKey = $request->input('secret_key');

        $credentials = ['email' => $email, 'password' => $password];

        if (Auth::once($credentials)) {
            $user = Auth::user();
            if ($user->g2fa_key) {
                $g2fa = new Google2FA();
                if (!$g2fa->verifyKey($user->g2fa_key, $secretKey)) {
                    return redirect()->route('login');
                }
                Auth::attempt($credentials);
            }
            return redirect()->intended($this->redirectTo);
        }

        return redirect()->route('login');
    }
}
