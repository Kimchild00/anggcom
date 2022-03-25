<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserOtp;
use App\Models\UserCompany;
use App\Models\UserMemberOrder;
use App\PhoneNumbers\PhoneNumberService;
use App\ThirdParty\EmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use PragmaRX\Google2FAQRCode\Google2FA;

class AuthRepository
{
    public function registerPost($data)
    {
        try {
            // Initialize DB Transaction
            DB::beginTransaction();

            // Default select first package
            $selectedPackage = memberOrderPackages()[0];

            // Get user company by title and get ID only
            $companyExist = UserCompany::where('title', $data['company_name'])
                ->select('id')
                ->first();
            // Cannot duplicate or exist in DB
            if ($companyExist) {
                return returnCustom("Err-code AR-RP: The company name has already exist");
            }

            $phoneNumberService = new PhoneNumberService();
            $isValidPhone = $phoneNumberService->validatePhone($data['phone']);
            if (!$isValidPhone['status']) {
                return $isValidPhone;
            }
            $data['phone'] = $isValidPhone['message'];

            // Create new user company
            $newCompany = new UserCompany();
            $newCompany->title = $data['company_name'];
            $newCompany->package_name = $selectedPackage['name'];
            $newCompany->expired_at = null;
            $newCompany->save();

            // Create new user member order
            $newUserMemberOrder = new UserMemberOrder();
            $newUserMemberOrder->user_company_id = $newCompany->id;
            $newUserMemberOrder->invoice_number = '';
            $newUserMemberOrder->email = $data['email'];
            $newUserMemberOrder->package_name = $selectedPackage['name'];
            $newUserMemberOrder->package_price = $selectedPackage['price'];
            $newUserMemberOrder->long_expired = $selectedPackage['long_expired'];
            $newUserMemberOrder->save();
            $newUserMemberOrder->invoice_number = 'MOA' . $newUserMemberOrder->id;
            $newUserMemberOrder->save();

            // Get user by email and user company id and get ID only
            $emailExist = User::where('email', $data['email'])
                ->where('user_company_id', $newCompany->id)
                ->select('id')
                ->count();
            // Cannot duplicate or exist in DB
            if ($emailExist > 0) {
                return returnCustom("Err-code AR-RP: The email has already exist");
            }

            // Create new user by user company
            $newEmail = new User();
            $data['password'] = Hash::make($data['password']);
            $newEmail->createOrUpdate($newCompany->id, 'master', $data['name'], $data['email'], $data['password'], $data['phone']);

            $data = [
                'recipient_mail' => $data['email'],
                'recipient_name' => $data['name'],
                'cc' => [],
                'bcc' => [],
                'subject' => "New account created successfully",
                'title' => 'New Account',
                'message' => 'New account created successfully, please logged in to ' . url('/') . ' to use our platform.'

            ];
            $emailService = new EmailService();
            $emailService->curlPost($data);

            DB::commit();
            return returnCustom('Your data has been successfully registered, please logged in.', true);
        } catch (\Exception $e) {
            return returnCustom('Err-code AR-RP: ' . $e->getMessage());
        }
    }

    public function loginPost($data)
    {
        try {
            // Get User Company by title and ID only
            $userCompany = UserCompany::find($data['company_name']);
            // Cannot empty or null
            if (!$userCompany) {
                return returnCustom('Err-code AR-LP: The company name is not exist, please make sure your company name has been registered.');
            }

            // Get user by email and user company id and get all columns
            $user = User::with([])
                ->where('email', $data['email'])
                ->where('user_company_id', $userCompany->id)
                ->first();

            // Cannot empty or null
            if (!$user) {
                return returnCustom("Err-code AR-LP: the email is not exist");
            }

            // The DB password is same with input password
            if (!Hash::check($data['password'], $user->password)) {
                return returnCustom("Err-code AR-LP: The password is wrong");
            }


            //if (env('APP_ENV') == 'production') {
            // Uncomment to run the otp feature
            // If is_otp Enable
            if($user->is_otp != 'Off'){
                return returnCustom($user, true);
            }    

            // Set cookies login
            Auth::login($user);
            return returnCustom($user, true);
        } catch (\Exception $e) {
            return returnCustom("Err code AR-LP: " . $e->getMessage());
        }
    }

    public function getCodeUniqueActive()
    {
        $code = sprintf("%06d", mt_rand(1, 999999));
        return $code;
    }

    public function createOrUpdateSendOtp($admin, $codeEmail = null, $codeGoogleAuth = null)
    {
        try {
            $data = UserOtp::where('users_id', $admin['id'])->first();

            // Time Expired
            $date = date("Y-m-d H:i:s", strtotime('+2 minutes'));

            if (!$data) {
                $data = new UserOtp();
            }

            // Send to Database
            $data->createOrUpdateOtp($admin['id'], $codeEmail, $codeGoogleAuth, $date);
            return returnCustom('Send data successfully', true);
        } catch (\Throwable $th) {
            return returnCustom($th->getMessage());
        }
    }

    public function timeExpiredDate($date)
    {
        $codeOtp = $date;
        $time = UserOtp::where('code_email', $codeOtp)->orWhere('code_google_auth', $codeOtp)->first();
        $timeEx = $time['time_expired'];
        return $timeEx;
    }

    public function sendOtp($admin)
    {
        try {
            $findUserOtp = UserOtp::where('users_id', $admin['id'])->first();
            if ($admin['is_otp'] == 'Email') {
                // Genrate Code
                $codeEmail = $this->getCodeUniqueActive();

                //  Params Email
                $data = [
                    'recipient_mail' => $admin['email'],
                    'recipient_name' => $admin['name'],
                    'cc' => [],
                    'bcc' => [],
                    'subject' => "OTP",
                    'title' => 'OTP',
                    'message' => 'This is your OTP' . ' ' . $codeEmail
                ];

            } else {
                // look for this user, previously activated Google Auth
                $google2fa = new Google2FA();
                if(isset($findUserOtp) && $findUserOtp['code_google_auth']){
                    $codeGoogleAuth = $findUserOtp['code_google_auth'];
                }else{
                    $codeGoogleAuth = $google2fa->generateSecretKey();
                }

                if (!$codeGoogleAuth) {
                    return returnCustom("Err Code AR-SO : can't generate secret key");
                }

                // Generate QrCode
                $codeQrCode = $google2fa->getQRCodeInline(
                    'ANGGARAN.COM',
                    $admin['email'],
                    $codeGoogleAuth
                );

                //  Params Email
                $data = [
                    'recipient_mail' => $admin['email'],
                    'recipient_name' => $admin['name'],
                    'cc' => [],
                    'bcc' =>[],
                    'subject' => "OTP",
                    'title' => 'OTP',
                    'message' => " <span> Scan Your OTP With Google Authenticator Apps </span>
                                    <br>
                                    <img src='$codeQrCode'>
                                    <br>
                                    <span> If image not show on your email you can manually add on your apps with
                                    <b> Enter A setup Key </b> Method
                                    <br>
                                    <br>
                                    <h4>How To Add Manually :</h4>
                                    <br>
                                    <ul>
                                        <li>From your device, open the Google Authenticator app.</li>
                                        <li>Tap +.</li>
                                        <li>Enter a Account :" . $admin['email'] . "</li>
                                        <li>Enter a setup key :" . $codeGoogleAuth . "</li>
                                        <li>Verify that the key type is time based.</li>
                                        <li>Tap Add.</li>
                                    </ul>
                                  "
                ];
            }      
            
            if(!isset($codeGoogleAuth) && isset($codeEmail)){
                if($findUserOtp){
                    $codeGoogleAuth = $findUserOtp['code_google_auth'];
                }else{
                    $codeGoogleAuth = "";
                }
            }
            
            if(!isset($codeEmail) && isset($codeGoogleAuth)){
                if($findUserOtp){
                    $codeEmail = $findUserOtp['code_email'];
                }else{
                    $codeEmail = "";
                }
            }
    
            // Send Database
            $resultCreateOrUpdate = $this->createOrUpdateSendOtp($admin, $codeEmail, $codeGoogleAuth);
            if (!$resultCreateOrUpdate['status']) {
                return $resultCreateOrUpdate;
            }

            // Send Email
            $emailService = new EmailService();
            $emailService->curlPost($data);
            return returnCustom(['codeEmail' => $codeEmail, 'codeGoogleAuth' => $codeGoogleAuth], true);

        } catch (\Exception $e) {
            Log::error('Err Code AR-SO : ' . $e->getMessage());
            return returnCustom('Err code AR-SO : ' . $e->getMessage());
        }

    }

    public function getCompanyNameByJournalName($data)
    {
        $userCompany = UserCompany::where('title', 'LIKE', "%" . $data['search'] . '%')->get();

        return [
            "total_count" => $userCompany->count(),
            "incomplete_results" => false,
            "items" => $userCompany->toArray()
        ];
    }
}