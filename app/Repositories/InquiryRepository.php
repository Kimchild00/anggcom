<?php

namespace App\Repositories;

use App\Models\Inquiry;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\ThirdParty\SentryService;

class InquiryRepository {
    public function createPost($data) {
        try {
            // Get inquiry based on account number and user company id
            $inquiry = Inquiry::where('user_company_id', Auth::user()->user_company_id)
                ->where('account_number', $data['account_number'])
                ->first();
            // Cannot duplicate
            if ($inquiry) {
                return returnCustom("Err-code IR-CP: Account number has been registered!");
            }

            // Separate city input
            $bankCityId = explode('|', $data['bank_city']);
            $data['bank_city'] = $bankCityId[1];

            // Validate account number and bank code
            $checkValid = json_decode(file_get_contents('https://importir.com/api/check-inquiry/' . $data['account_number'] . '/' . $data['bank'] . '?token=syigdfjhagsjdf766et4wff6'), true);
            // Cannot invalid
            if (!$checkValid['status']) {
                return $checkValid;
            }

            // Save inquiries table
            $inquiry = new Inquiry();
            $inquiry->createOrUpdate(
                Auth::user()->user_company_id, $data['name_by_input'], $checkValid['message']['account_holder'], $checkValid['message']['status'],
                $data['account_number'], $data['bank'], $bankCityId[0], $data['bank_city']);

            return returnCustom($inquiry->account_number, true);
        } catch (\Exception $e) {
            $sentryService = new SentryService();
            $sentryService->getError('Anggaran Error', 'InquiryRepository func CreatePost', $e->getMessage(), $data);
            return returnCustom("Err-code IR-CP: " . $e->getMessage());
        }
    }

    public function updatePost($data) {
        try {
            if (Auth::user()->level == 'master' || Auth::user()->level == 'finance') {
                $userCompanyId = Auth::user()->user_company_id;
                $inquiry = Inquiry::find($data['id']);
                if (!$inquiry) {
                    return returnCustom("Err-code IR-UP: Data not found");
                }

                if ($inquiry->user_company_id != $userCompanyId) {
                    return returnCustom("Err-code IR-UP: This inquiry is not have to your company");
                }
    
                if ($inquiry->name_by_server) {
                    return returnCustom("Err-code IR-UP: Inquiry cannot modified");
                }
    
                // Separate city input
                $bankCityId = explode('|', $data['bank_city']);
                $data['bank_city'] = $bankCityId[1];
    
                // Validate account number and bank code
                $checkValid = json_decode(file_get_contents('https://importir.com/api/check-inquiry/' . $data['account_number'] . '/' . $data['bank'] . '?token=syigdfjhagsjdf766et4wff6'), true);
                if (!$checkValid['status']) {
                    return $checkValid;
                }
    
                $inquiry->createOrUpdate(
                    $userCompanyId, $data['name_by_input'], $checkValid['message']['account_holder'], $checkValid['message']['status'],
                    $data['account_number'], $data['bank'], $bankCityId[0], $data['bank_city']);
    
                return returnCustom($inquiry->account_number, true);
            } else {
                return returnCustom("Err-code IR-UP: You don't have permission to update inquiry!");
            }
        } catch (\Exception $e) {
            return returnCustom("Err-code IR-UP: " . $e->getMessage());
        }
    }

    public function getByFilter($filters, $site = '') {
        $inquiries = Inquiry::with([]);
        if (empty($site)) {
            $inquiries = $inquiries->where('user_company_id', Auth::user()->user_company_id);
        }

        if (!empty($filters['name'])) {
            $inquiries = $inquiries->where('name_by_input', 'like', '%' . $filters['name'] . '%');
        }

        if (!empty($filters['account_number'])) {
            $inquiries = $inquiries->where('account_number', 'like', '%' . $filters['account_number'] . '%');
        }

        if (!empty($filters['status'])) {
            $inquiries = $inquiries->where('status', $filters['status']);
        }

        $inquiries = $inquiries->orderBy('id', 'desc');
        return $inquiries->paginate(25);
    }
}