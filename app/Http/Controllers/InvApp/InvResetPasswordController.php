<?php

namespace App\Http\Controllers\InvApp;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;

class InvResetPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

    /**
     * Where to redirect users after resetting their password.
     *
     * @var string
     */
    protected $redirectTo = '/inv/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
        $this->middleware('guest:investors');
    }

    protected function guard(){
        return Auth::guard('investors');
    }

    protected function broker()
    {
        return Password::broker('inv_users');
    }


    private function get_user_by_token($token){
    	$records =  DB::table('password_resets')->get();
    	foreach ($records as $record) {
			if (Hash::check($token, $record->token) ) {
       			return $record->email;
            }
        }
    }

    public function showResetForm(Request $request, $token = null)
    {
        $email = $this->get_user_by_token($token);
        return view('auth.passwords.reset')->with(
            ['token' => $token, 'email' => $email, 'path' => route('password.inv_update')]
        );
    }

    public function resetPassword($user, $password)
    {
        $this->setUserPassword($user, $password);
        $user->setRememberToken(Str::random(60));
        $user->save();
        event(new PasswordReset($user));
    }


}
