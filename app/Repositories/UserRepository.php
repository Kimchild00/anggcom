<?php

namespace App\Repositories;

use App\Models\User;
use App\Models\UserMemberOrder;
use App\PhoneNumbers\PhoneNumberService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserRepository {
    public function createPost($data) {
        try {
            if (Auth::user()->level != 'master') {
                return returnCustom("Err-code UR-CP: You don't have access this action");
            }

            if ($data['level'] == 'master') {
                return returnCustom("Err-code UR-CP: Master level be allowed only one account on one company");
            }


            // Get user based on user company id and email
            $user = User::where('user_company_id', Auth::user()->user_company->id)
                ->where('email', $data['email'])
                ->count();
            // Cannot duplicate in on one company
            if ($user) {
                return returnCustom("Err-code UR-CP: Email has been registered.");
            }

            // Validate phone number
            $phoneNumberValid = new PhoneNumberService();
            $isValidPhone = $phoneNumberValid->validatePhone($data['phone']);
            if (!$isValidPhone['status']) {
                return $isValidPhone;
            }

            // Save users table
            $user = new User();
            $data['password'] = Hash::make($data['password']);
            $user->createOrUpdate(Auth::user()->user_company->id, $data['level'], $data['name'], $data['email'], $data['password'], $isValidPhone['message'], $data['is_otp']);

            return returnCustom('Data saved successfully', true);
        } catch (\Exception $e) {
            return returnCustom("Err-code UR-CP: " . $e->getMessage());
        }
    }

    public function updatePost($data, $id) {
        try {
            if (Auth::user()->level != 'master') {
                return returnCustom("Err-code UR-UP: You don't have access this action");
            }

            // Get users based on email
            $user = User::find($id);
            // Cannot empty or null
            if (!$user) {
                return returnCustom("Err-code UR-UP: User data not found");
            }

            if ($user->level == 'master') {
                if ($data['level'] != 'master') {
                    return returnCustom("Err-code UR-UP: Master account cannot be changed to other level");
                }
            }

            // Validate phone number
            $phoneNumberValid = new PhoneNumberService();
            $isValidPhone = $phoneNumberValid->validatePhone($data['phone']);
            if (!$isValidPhone['status']) {
                return $isValidPhone;
            }
            // Set db password if empty input
            if (isset($data['password'])) {
                $data['password'] = Hash::make($data['password']);
            } else {
                $data['password'] = $user->password;
            }
            $user->createOrUpdate($user->user_company_id, $data['level'], $data['name'], $user->email, $data['password'], $isValidPhone['message'], $user->is_otp);
            return returnCustom('Data updated successfully', true);
        } catch (\Exception $e) {
            return returnCustom("Err-code UR-UP: " . $e->getMessage());
        }
    }

    public function getByFilter($filters, $site = '') {
        $users = User::with(['user_company']);
        if (empty($site)) {
            $users = $users->where('user_company_id', Auth::user()->user_company_id);
        } else {
            if (!empty($filters['company_name'])) {
                $users = $users->where('user_company_id', $filters['company_name']);
            }
        }

        if (!empty($filters['email'])) {
            $users = $users->where('email', 'like', '%' . $filters['email'] . '%');
        }

        if (!empty($filters['name'])) {
            $users = $users->where('name', 'like', '%' . $filters['name'] . '%');
        }

        $users = $users->orderBy('id', 'desc');
        return $users->paginate(25);
    }

    public function paidManually($id) {
        try {
            DB::beginTransaction();

            $userMemberOrder = UserMemberOrder::with(['user_company'])
                ->find($id);
            if (!$userMemberOrder) {
                return returnCustom("Err-code UR-PM: User member order data is not found");
            }

            if (!$userMemberOrder->user_company) {
                return returnCustom("Err-code UR-PM: User company data is not found");
            }

            if ($userMemberOrder->paid_at) {
                return returnCustom("Err-code UR-PM: This user member order is already paid");
            }

            $midtransRepository = new MidtransRepository();
            $type = 'MANUALLY-' . Auth::user()->id . '-by:' . Auth::user()->name;
            $midtransRepository->updateExpiredTimeUserCompany($userMemberOrder, $type, $userMemberOrder->user_company);

            DB::commit();
            return returnCustom("Your data has been successfully updated", true);
        } catch (\Exception $e) {
            return returnCustom("Err-code UR-PM: " . $e->getMessage());
        }
    }

    public function updateOtp($id, $otp) {
        try {
            $user = User::find($id);
            if (!$user) {
                return returnCustom("Err-code UR-UO: User data is not found");
            }

            $user->is_otp = $otp;
            $user->save();

            return returnCustom("Your data has been successfully updated", true);
        } catch (\Exception $e) {
            return returnCustom("Err-code UR-UOT: " . $e->getMessage());
        }
    }
    
    public function resetPasswordProcess($id, $password, $confPassword) {
        try {
            $user = User::find($id);
            if (!$user) {
                return returnCustom("Err-code UR-RPP: User data is not found");
            }

            if ($password != $confPassword) {
                return returnCustom('Password and confirm password does not match');
            }elseif(!$password || !$confPassword){
                return returnCustom('Password and confirm password cannot be empty');
            }
    

            $user->password = Hash::make($password);
            $user->save();

            return returnCustom("Your data has been successfully updated", true);
        } catch (\Exception $e) {
            return returnCustom("Err-code UR-RPP: " . $e->getMessage());
        }
    }
    
}