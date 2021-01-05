<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Auth;
use Illuminate\Support\Facades\Config;


use App\Providers\RouteServiceProvider;
use Carbon\Carbon;
use Illuminate\Http\Request as HttpRequest;


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
        $this->middleware('guest:admin')->except('logout');
    }

    public function show()
    {
        return view('auth.login', ['url' => 'admin']);
    }

    protected function guardLogin(Request $request, $guard)
    {


        return Auth::guard($guard)->attempt(
            [
                'email' => $request->email,
                'password' => $request->password
            ],
            $request->get('remember')
        );
    }


    public function authenticate(HttpRequest $request)
    {
        // error_log($request);
        // error_log($request->get('remember'));
        // return Auth::guard('admin')->attempt(['email' => $request('email'), 'password' => $request('password'), $request->get('remember')]);

        // if(Auth::guard('admin')->attempt(['email' => $request('email'), 'password' => $request('password'), $request->get('remember')])){
        //     error_log('Auth success');
        //     return redirect('/admin/home');
        // }
        // return redirect()->back()->withInput($request->only('email', 'password', 'remember'));

        if ($this->guardLogin($request, 'admin')) {
            return redirect()->intended('/admin/home');
        }

        return back()->withInput($request->only('email', 'remember'));
    }

    protected function login(HttpRequest $request){
        if ($this->guardLogin($request, 'web')) {
            $date = Carbon::now();

            $user = User::where('email', request('email'))->first();
            error_log($user);

            error_log($date);
            $fiveminLess = $date->subMinute(30);
            error_log($fiveminLess);

            error_log($user->last_signed_in);

            if($fiveminLess->greaterThan($user->last_signed_in)){
                error_log('You are 5 mins late');
                $update_user = User::where('email', request('email'))->update([
                    'last_signed_in' => Carbon::now()
                ]);

                return redirect()->route('mail', [
                    'status' => 'inactive',
                    'username' => $user->name
                ]);
                // return redirect()->route('home')->with([
                //     'status' => 'inactive'
                // ]);
            }
                $update_user = User::where('email', request('email'))->update([
                    'last_signed_in' => Carbon::now()
                ]);
                error_log($update_user);
                error_log('You are not late');
                error_log('Logged in as user');
                return redirect()->route('mail', [
                    'status' => 'active',
                    'username' => $user->name
                ]);
                // return redirect()->route('home')->with([
                //     'status' => 'active'
                // ]);


            // $update_user = $user->update([
            //     'last_signed_in' => $date
            // ]);

            // error_log($user);

        }
        return redirect('/login');
    }
}
