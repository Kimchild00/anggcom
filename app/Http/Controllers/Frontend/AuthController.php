<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\UserOtp;
use App\Models\User;
use App\Repositories\AuthRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Session;

class AuthController extends Controller
{
    protected $authRepository;

    public function __construct()
    {
        $this->authRepository = new AuthRepository();
    }

    public function register() {
        return view('frontend.auth.register');
    }

    public function registerPost(Request $request) {
        $this->validate($request, [
            'company_name' => 'required|max:255',
            'name' => 'required|max:255',
            'phone' => 'required',
            'email' => 'required|email',
            'password' => 'min:6',
        ]);
        $result = $this->authRepository->registerPost($request->all());
        alertNotify($result['status'], $result['message'], $request);
        if ($result['status']) {
            return redirect(url('login'));
        }
        return redirect(url('register'));
    }

    public function login() {
        return view('frontend.auth.login');
    }

    public function loginPost(Request $request) {
        $this->validate($request, [
            'company_name' => 'required',
            'email' => 'required|email',
            'password' => 'required',
        ]);
        $result = $this->authRepository->loginPost($request->all());
        if ($result['status']) {
            if ($result['message']->is_otp != 'Off') {
                $sendOtp = $this->authRepository->sendOtp($result['message']);
                if (!$sendOtp['status']) {
                    alertNotify(false, $result['message'], $request);
                    return redirect(url('login'));
                }

                if ($result['message']['is_otp'] == 'GoogleAuthenticator') {
                    return redirect(url('/login/otp'))->with('sessionOtp', $sendOtp['message']['codeGoogleAuth']);
                } else {
                    return redirect(url('/login/otp'))->with('sessionOtp', $sendOtp['message']['codeEmail']);
                }
            } else {
                alertNotify($result['status'], 'Success to logged in', $request);
                return redirect(url('/dashboard'));
            }
        }
        alertNotify($result['status'], $result['message'], $request);
        return redirect(url('login'));
    }

    public function otpVerification(Request $request){
        if(!$request->session()->has('sessionOtp')) {
            $session = Session::get('sessionMessage');
            if($session){
                return view('frontend.auth.otp');
            }
            return redirect(url('login'));       
        }
         // Time Expired Data
        $messageCodeGoogle = Session::get('sessionOtp');
        $timeEx = $this->authRepository->timeExpiredDate($messageCodeGoogle);
        $sessionMessage = 'Successfuly send your email';
        return view('frontend.auth.otp', ['messageCodeGoogle' => $messageCodeGoogle ,'sessionMessage' => $sessionMessage, 'timeEx' => $timeEx]);
    }

    public function postOtpVerificationLogin(Request $request){

        // Time Expired
        $timeNow = date('Y-m-d H:i:s');
        
        if($request->timeEx < $timeNow){
            $sessionMessage = 'Your Time is Expired';
            return redirect(url('login/otp'))->with(['sessionMessage' => $sessionMessage, 'timeEx' => $request->timeEx]);
        }
        //Check
        $otpCheck   = UserOtp::where('code_email', $request->is_otp)->orWhere('code_google_auth', $request->googleAuth)->with('users')->first();

        if(!$otpCheck){
            $sessionMessage = 'Your OTP is wrong';
            return redirect(url('login/otp'))->with(['sessionMessage' => $sessionMessage, 'timeEx' => $request->timeEx]);
        }

        if(isset($otpCheck->users)){
            $userCheck  = $otpCheck->users;
        }

        if(isset($userCheck) && $userCheck['is_otp'] == "GoogleAuthenticator"){
            $googleCheck  = (new \PragmaRX\Google2FAQRCode\Google2FA())->verifyKey($otpCheck['code_google_auth'], $request->is_otp);
            if(!$googleCheck){
                $sessionMessage = 'Your OTP Google Authenticator is wrong';
                return redirect(url('login/otp'))->with(['sessionMessage' => $sessionMessage, 'timeEx' => $request->timeEx, 'messageCodeGoogle' => $request->googleAuth]);
            }
        }else if(isset($userCheck) && $userCheck['is_otp'] == "Email"){
            $emailCheck   =  $otpCheck   = UserOtp::where('code_email', $request->is_otp)->first();
            if(!$emailCheck){
                $sessionMessage = 'Your OTP is wrong';
                return redirect(url('login/otp'))->with(['sessionMessage' => $sessionMessage, 'timeEx' => $request->timeEx]);
            }
        }

        Auth::login($userCheck);
        return redirect(url('/dashboard'));
    }

    public function logout(Request $request) {
        Auth::logout();
        alertNotify(true, "You have been successfully logged out", $request);
        return redirect(url('login'));
    }

    public function getCompanyName(Request $request) {
        $request = $request->all();
        $result = $this->authRepository->getCompanyNameByJournalName($request);
        return response()->json($result);

    }
}
