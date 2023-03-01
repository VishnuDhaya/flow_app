<?php

namespace App\Http\Controllers\InvApp;

use App\Http\Controllers\Controller;
use App\Models\InvestorUser;
use App\Notifications\CreatePasswordNotify;
use App\Notifications\ResetPasswordNotify;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;


class InvForgotPasswordController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    protected function broker()
    {
        return Password::broker('inv_users');
    }

    public function sendResetLinkEmail(Request $request)
    {

        $data = $request->all();
        $token = Str::random(64);
        $user = InvestorUser::where('email',$data['email'])->first();
        DB::table('password_resets')->updateOrInsert(['email' => $data['email']],
            ['token' => Hash::make($token),
             'created_at' => Carbon::now()]
        );
        $url = env('INVESTOR_APP_URL')."password/reset/$token";
        Notification::route('mail', [$data['email'] => $user->name])->notify(new ResetPasswordNotify($token, $user->name, $url));
    }


}
