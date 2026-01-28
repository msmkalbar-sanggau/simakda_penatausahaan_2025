<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Support\Facades\Redirect;

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

    use AuthenticatesUsers, ThrottlesLogins;

    // protected $redirectTo = RouteServiceProvider::HOME;

    public function index()
    {
        Cookie::queue(Cookie::forget('laravel2_session'));
        Cookie::queue(Cookie::forget('laravel3_session'));
        Cookie::queue(Cookie::forget('simakdaskpd2021_session'));
        Cookie::queue(Cookie::forget('laravel100_session'));  //ini sesion penatausahaan 2024

        $data = [
            'daerah'            => DB::table('config_app')->select('nm_pemda', 'nm_badan', 'logo_pemda_hp')->first()
        ];

        return view('auth.login')->with($data);
    }

    public function authenticate(Request $request)
    {
        Cookie::queue(Cookie::forget('laravel2_session'));
        Cookie::queue(Cookie::forget('laravel3_session'));
        Cookie::queue(Cookie::forget('simakdaskpd2021_session'));
        Cookie::queue(Cookie::forget('laravel100_session')); //ini sesion penatausahaan 2024

        $credentials = $request->validate([
            'username' => ['required'],
            'password' => ['required'],
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = DB::table('pengguna')
                ->where(['username' => $request->username])
                ->first();

            if ($user->status == 1) {
                Auth::logoutOtherDevices($request->password);
                return redirect()->route('home');
            } else {
                Auth::logout();
                request()->session()->invalidate();
                request()->session()->regenerateToken();
                // return back()->withErrors(['msg' => 'Akun Anda Tidak Aktif, Hubungi Perben!']);
                return redirect()->route('login')
                    ->with('message', 'Akun Anda Tidak Aktif, Hubungi Perben!');
            }
        } else {
             return redirect()->route('login')
                ->with('message', 'Username atau Password Anda Salah!');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        request()->session()->invalidate();

        request()->session()->regenerateToken();

        return redirect()->route('login');
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }
}
