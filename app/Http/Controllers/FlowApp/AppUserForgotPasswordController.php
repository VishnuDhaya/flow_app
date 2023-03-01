<?php

namespace App\Http\Controllers\FlowApp;

use App\Http\Controllers\Controller;
use App\Models\FlowApp\AppUser;
use App\Notifications\ResetPasswordNotify;
use App\Repositories\SQL\PersonRepositorySQL;
use Carbon\Carbon;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Log;

class AppUserForgotPasswordController extends Controller
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

    public function sendResetLinkEmail(Request $request)
    {

        $data = $request->all();
        $token = Str::random(64);
        $user = AppUser::where('email',$data['email'])->first();
        $user->name = DB::table('persons')->find($user->person_id, ['first_name'])->first_name;
        DB::table('password_resets')->updateOrInsert(['email' => $data['email']],
            ['token' => Hash::make($token),
             'created_at' => Carbon::now()]
        );

        Notification::route('mail', [$data['email'] => $user->name])->notify(new ResetPasswordNotify($token, $user->name, route('password.reset',$token)));
    }
}
