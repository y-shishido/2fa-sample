<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PragmaRX\Google2FA\Google2FA;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Http\Response
     * @throws \PragmaRX\Google2FA\Exceptions\InsecureCallException
     */
    public function index()
    {
        $user = Auth::user();

        $g2fa = new Google2FA();

        $g2fa->setAllowInsecureCallToGoogleApis(true);
        $qrUrl = $g2fa->getQRCodeGoogleUrl(
            config('app.name'),
            $user->email,
            $user->g2fa_key
        );
        return view('home', ['qr' => $qrUrl]);
    }
}
