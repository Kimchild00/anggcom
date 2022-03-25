<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserForgotPassword;
use App\Models\UserMemberOrder;
use DateTime;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use App\Models\UserCompany;
use App\ThirdParty\EmailService;

class IndexRepository
{
    public function payment($invoiceNumber)
    {
        try {
            // Check input invoice number is empty
            if (!$invoiceNumber) {
                return returnCustom('Err-code IR-P: invoice number is empty');
            }

            // Get user member order based on invoice number
            $userMemberOrder = UserMemberOrder::where('invoice_number', $invoiceNumber)
                ->first();
            // cannot empty
            if (!$userMemberOrder) {
                return returnCustom('Err-code IR-P: the user member order is empty');
            }

            if (Auth::user()->user_company->id != $userMemberOrder->user_company_id) {
                return returnCustom("This invoice is not belongs to you");
            }

            return returnCustom($userMemberOrder, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code IR-P: " . $e->getMessage());
        }
    }

    public function changePassword($data)
    {
        try {
            $user = Auth::user();
            if (!$user) {
                return returnCustom("Err-code IR-CP: user not found");
            }

            if (!Hash::check($data['old_password'], $user->password)) {
                return returnCustom("Err-code IR-CP: The old password is wrong");
            }

            if ($data['password'] != $data['password_confirmation']) {
                return returnCustom("Err-code IR-CP: confirm password is not the same as new password");
            }

            $user->password = Hash::make($data['password']);
            $user->save();

            return returnCustom("Change of data was successfully changed", true);
        } catch (\Exception $e) {
            return returnCustom("Err code IR-CP: " . $e->getMessage());
        }
    }
    public function forgotPasswordProcess($request)
    {
        try {
            $userCompany = UserCompany::find($request['company_name']);
            if (!$userCompany) {
                return returnCustom('Err-code AR-LP: The company name is not exist, please make sure your company name has been registered.');
            }
            $user = User::with([])
                ->where('email',  $request->email)
                ->where('user_company_id', $userCompany->id)
                ->first();
            if (!$user) {
                return returnCustom("Err-code IR-FPP: email not found in company");
            }

            $emailHashing = Hash::make($user["email"]);
            $email = $user["email"];

            // Add token in forgot_token colom
            DB::beginTransaction();
            // Set general
            $resetLink                  = url('/request-password/?token=' . $emailHashing . '&email=' . $email);
            //Send token
            $this->createOrUpdateForTok($request, $emailHashing);
            DB::commit();
            $data = [
                "recipient_name" => $user->name,
                'cc' => [],
                'bcc' => [],
                "recipient_mail" => $user->email,
                'subject' => "anggaran.com",
                'title' => 'reset password anggaran',
                "message" => "Silakan " . "<button><a href='$resetLink'> klik </a></button>" . "Untuk Mengganti Password," . "<br>" . "<br>" . "Atau copy link ini ke browser Anda " . "<br>" . "<br>" . $resetLink
            ];
            $emailService = new EmailService();
            $emailService->curlPost($data);
            return returnCustom("Please check your email to reset password.", true);
        } catch (\Exception $e) {
            return returnCustom("Err code IR-FPP: " . $e->getMessage());
        }
    }

    public function requestPassword($data)
    {

        try {
            if (Hash::check($data["email"], $data["token"])) {
                $user = User::where('email', $data["email"])->first();

                if ($data['new_password'] != $data['confirm_password']) {
                    return returnCustom("Err-code IR-AFPP: confirm password is not the same as new password");
                }

                $user->password = Hash::make($data['new_password']);
                $user->save();

                return returnCustom("Password Successfully Reset, Please log in with a new password.", true);
            }
        } catch (\Exception $e) {
            return returnCustom("Err code IR-AFPP: " . $e->getMessage());
        }
    }

    public function createOrUpdateForTok($request, $hash)
    {
        try {
            $user = User::where("email", $request->email)->first();
            if (!$user) {
                return returnCustom("Err-code COUFT: Email Not Found");
            }

            if (empty($user["forgot_tokens"])) {
                $createToken = new UserForgotPassword();
                $tme = date('Y-m-d H:i:s');
                $time = new DateTime($tme);
                $createToken->user_id = $user->id;
                $createToken->token = $hash;
                $createToken->end_date = $time->modify("+1 day");
                $createToken->save();
            } else {
                $updateToken = UserForgotPassword::where("user_id", $user->id)->first();
                $tme = date('Y-m-d H:i:s');
                $time = new DateTime($tme);
                $updateToken->token = $hash;
                $updateToken->end_date = $time->modify("+1 day");
                $updateToken->save();
            }
            return returnCustom('Send data successfully', true);
        }  catch (\Exception $e) {
            return returnCustom("Err code IR-COUFT: " . $e->getMessage());
        }
    }

    public function getUserForTok($token)
    {
        if(!$token){
            return returnCustom('Err-code IR-GUFT: Token is not define', false);
        }

        if(!isset($token['token']) || !isset($token['email'])){
            return returnCustom('Err-code IR-GUFT: Token is not define', false);
        }

        $user = User::with(["forgot_tokens"])
        ->where('email', $token["email"])
        ->first();

        if (!$user) {
            return returnCustom("Err-code IR-GUFT: data Not Found");
        }

        if($token['email'] != $user['email']){
            return returnCustom('Err-code IR-GUFT: Email is not define', false);
        }

        if (strtotime(date("Y-m-d H:i:s")) < strtotime($user["forgot_tokens"]->end_date)) {
            if ($user["forgot_tokens"]->token == $token["token"]) {
                return returnCustom($user, true);
            }else{
                return returnCustom('Err-code IR-GUFT: Token is Wrong', false);
            }
        }else{
            return returnCustom('Err-code IR-GUFT: Token is Expired', false);
        }
    }
}
