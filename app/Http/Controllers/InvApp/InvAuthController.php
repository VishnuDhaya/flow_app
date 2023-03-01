<?php

namespace App\Http\Controllers\InvApp;

use App\Models\InvestorUser;
use App\Notifications\CreatePasswordNotify;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class InvAuthController extends Controller
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
    protected $redirectTo = '/inv/home';


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        
        $this->middleware('guest')->except('logout');
        $this->middleware('guest:investors')->except('logout');
    }

    public function loginInvestor(Request $request)
    {
        try {
            $this->validateLogin($request);
            if (Auth::guard('investors')->attempt($this->credentials($request), $request->filled('remember'))) {
                $request->session()->regenerate();
                $this->set_session_variables();
                $this->update_last_info(Auth('investors')->user()->id);
                return $this->authenticated($request, Auth::guard('investors')->user()) ?: redirect()->intended($this->redirectPath());
            }
            $this->sendFailedLoginResponse($request);
        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            return redirect()->back()->withErrors($errors);
        }
    }

    public function logout(Request $request)
    {
        Auth::guard('investors')->logout();

        $request->session()->invalidate();

        return $this->loggedOut($request) ?: redirect('/inv/login');
    }


    public function show(){
        return view("investorsite.login");
    }

    public function redirectToProvider($driver)
    {
        return Socialite::driver($driver)->redirect();
    }


    public function loginWithProvider($driver){
        try {
        $user = Socialite::driver($driver)->user();
        }
        catch (\Exception $e) {
            return redirect()->route('login');
        }
        $existingUser = InvestorUser::where('email', $user->getEmail())->first();

        if ($existingUser) {
            auth('investors')->login($existingUser, true);
            session()->regenerate();
            $this->set_session_variables();
        }

        return redirect($this->redirectPath());
    }

    public function createInvestorUser($email, $password = null, $person_id =null, $name = null)
    {
        $password = (isset($password)) ? $password : Str::random(10);
        $user = InvestorUser::create([
            'email' => $email,
            'password' => Hash::make($password),
            'person_id' => $person_id,
            'name' => $name
        ]);

        $this->sendSetPasswordEmail(['email' => $email, 'name' => $name]);
    }

    public function sendSetPasswordEmail($data)
    {
        $token = Str::random(64);

        DB::table('password_resets')->updateOrInsert(['email' => $data['email']],
            ['token' => Hash::make($token),
             'created_at' => Carbon::now()]
        );
        $url = env('INVESTOR_APP_URL')."password/reset/$token";
        Notification::route('mail', [$data['email'] => $data['name']])->notify(new CreatePasswordNotify($token, $data['name'], $url));
    }

    private function set_session_variables()
    {
        $inv_person_id = auth('investors')->user()->person_id;
        session()->put('inv_person_id', $inv_person_id);
        session()->put('country_code','*');
        $currency = DB::select("select distinct currency_code from investment_txns where person_id = $inv_person_id");
        if(sizeof($currency) != 1){
            thrw("You have misconfigured currency codes. Please report this to administrator");
        }else{
            session()->put('currency_code', $currency[0]->currency_code);
        }

        $month = DB::connection('report')->table('bonds_monthly')->max('month');
        session()->put('month', $month);
        $report_date = Carbon::createFromFormat('Ym',$month)->endOfMonth();
        session()->put('report_date', $report_date);
    }

    public function update_last_info($id)
    {
        DB::update("update investor_users set last_login = now(), login_count = login_count+1  where id = {$id}");
    }




}
