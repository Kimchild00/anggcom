<?php

namespace App\Repositories;

use App\Flip\FlipService;
use App\Models\Division;
use App\Models\DivisionFlip;
use App\Models\DivisionXendit;
use App\Models\DivisionJournal;
use App\Models\DivisionUser;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class DivisionRepository {
    public function createPost($data) {
        try {
            DB::beginTransaction();

            // Master account that can access
            if (Auth::user()->level != 'master') {
                return returnCustom("Err-code DR-CP: You don't have access!");
            }

            // Get users data based on input director
            $directorUser = User::find($data['director']);
            // Cannot empty or null
            if (!$directorUser) {
                return returnCustom("Err-code DR-CP: Your director data is not found");
            }

            if($data['type_disbursement'] == 'flip') {
            // Verify flip key
                $verified = $this->verifyFlipKey($data['flip_key'], $data['flip_token']);
                // Cannot wrong flip key
                if (!$verified['status']) {
                    return $verified;
                }
            }

            // Save #1 divisions table
            $division = new Division();
            $division->createOrUpdate(Auth::user()->user_company_id, $data['title'], $directorUser->email, $directorUser->phone, $data['type_disbursement']);

            // Save #2 division_flips table
            if($data['type_disbursement'] == 'flip') {
                $divisionFlip = new DivisionFlip();
                $divisionFlip->createOrUpdate($division->id, $data['flip_name'], $data['id_big_flip'], $data['flip_key'], $data['flip_token']);
            } else {
                $divisionXendit = new DivisionXendit();
                $divisionXendit->createOrUpdate($division->id, $data['xendit_name'], $data['xendit_key']);
            }

            if ($data['journal_name']) {
                // Save #3 division_journals table (optional)
                $divisionJournal = new DivisionJournal();
                $divisionJournal->createOrUpdate($division->id, $data['journal_name'], $data['journal_key']);
            }

            // Save #4 division_users table
            $divisionUser = new DivisionUser();
            $divisionUser->createOrUpdate($division->id, $directorUser->id, 'admin');

            DB::commit();
            return returnCustom($division->id, true);
        } catch (\Exception $e) {
            return returnCustom('Err-code DR-CP: ' .  $e->getMessage());
        }
    }

    public function updatePost($data) {
        try {
            DB::beginTransaction();

            // Master account that can access
            if (Auth::user()->level != 'master') {
                return returnCustom("Err-code DR-UP: You don't have access!");
            }

            // Get users data based on input director
            $directorUser = User::find($data['director']);
            // Cannot empty or null
            if (!$directorUser) {
                return returnCustom("Err-code DR-UP: Director data not found.");
            }

            // Verify flip key
            if($data['type_disbursement'] == "flip") {
                $verified = $this->verifyFlipKey($data['flip_key'], $data['flip_token']);
                // Cannot wrong flip key
                if (!$verified['status']) {
                    return $verified;
                }
            }

            // Get divisions based on id
            $division = Division::find($data['division_id']);
            // Cannot empty or null
            if (!$division) {
                return returnCustom("Err-code DR-UP: Division not found.");
            }

            $typeDisburmentBefore = $division->type_disbursement;
            if($typeDisburmentBefore == "flip") {
                if($typeDisburmentBefore == "flip" && $data['type_disbursement'] == "flip") {
                    $divisionFlip = DivisionFlip::where('division_id', $data['division_id'])->first();
                    if(!$divisionFlip) {
                        return returnCustom("Err-code DR-UP: Division flip not found.");
                    }
                } elseif ($typeDisburmentBefore == "flip" && $data['type_disbursement'] == "xendit") {
                    $divisionXendit = DivisionXendit::where('division_id', $data['division_id'])->first();
                    if(!$divisionXendit) {
                        $divisionXendit = new DivisionXendit();
                    }
                }
            }
            
            if($typeDisburmentBefore == "xendit") {
                if($typeDisburmentBefore == "xendit" && $data['type_disbursement'] == "xendit") {
                    $divisionXendit = DivisionXendit::where('division_id', $data['division_id'])->first();
                    if(!$divisionXendit) {
                        return returnCustom("Err-code DR-UP: Division xendit not found.");
                    }
                } elseif ($typeDisburmentBefore == "xendit" && $data['type_disbursement'] == "flip") {
                    $divisionFlip = DivisionFlip::where('division_id', $data['division_id'])->first();
                    if(!$divisionFlip) {
                        $divisionFlip = new DivisionFlip();
                    }
                }
            }
            
            // Get division_users based on user_id and division_id
            $divisionUser = DivisionUser::where('division_id', $data['division_id'])
                ->where('user_id', $data['director'])
                ->first();


            // Update #1 divisions table
            $division->createOrUpdate(Auth::user()->user_company_id, $data['title'], $directorUser->email, $directorUser->phone, $data['type_disbursement']);

            switch ($data['type_disbursement']) {
                case 'flip':
                    $divisionFlip->createOrUpdate($division->id, $data['flip_name'], $data['id_big_flip'], $data['flip_key'], $data['flip_token']);
                    break;
                case 'xendit':
                    $divisionXendit->createOrUpdate($division->id, $data['xendit_name'], $data['xendit_key']);
                    break;
            }

            if ($data['journal_name']) {
                // Update #3 division_journals table (optional)
                if ($division->division_journal) {
                    $divisionJournal = $division->division_journal;
                } else {
                    $divisionJournal = new DivisionJournal();
                }
                $divisionJournal->createOrUpdate($division->id, $data['journal_name'], $data['journal_key']);
            }


            // Save #4 division_users table
            if (!$divisionUser) {
                $divisionUser = new DivisionUser();
            }
            $divisionUser->createOrUpdate($division->id, $directorUser->id, 'admin');

            DB::commit();
            return returnCustom($division->id, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code DR-UP: " . $e->getMessage());
        }
    }

    public function sortirUser($divisionUsers, $users) {
        try {
            $userIds = array_column($divisionUsers->toArray(), 'user_id');
            foreach ($users as $key => $user) {
                if (in_array($user->id, $userIds)) {
                    $users->forget($key);
                }
            }

            return returnCustom($users, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code DR-SU: " . $e->getMessage());
        }
    }

    public function createUser($data) {
        try {
            // Check division data is exist
            $division = Division::find($data['division_id']);
            if (!$division) {
                return returnCustom("Err-code DR-CU: Division data is not found");
            }

            $divisionUserMe = DivisionUser::where('division_id', $division->id)
                ->where('user_id', Auth::user()->id)
                ->first();
            if (!$divisionUserMe) {
                return returnCustom("Err-code DR-CU: You cannot do this action");
            }

            if ($data['role'] == 'operator') {
                if ($divisionUserMe->role != 'admin') {
                    return returnCustom("Err-code DR-CU: You don't allow to do this action");
                }
            } elseif ($data['role'] == 'master_finance') {
                if ($division->director_email != Auth::user()->email) {
                    return returnCustom("Err-code DR-CU: Director only that can add user who can push transaction to flip");
                }
            }

            // Check user data is exist
            $user = User::find($data['user_id']);
            if (!$user) {
                return returnCustom("Err-code DR-CU: User data is not found");
            }

            if ($data['role'] == 'master_finance') {
                // Check user id who role master finance already exist
                $masterFinanceUser = DivisionUser::where('division_id', $data['division_id'])
                    ->where('role', 'master_finance')
                    ->first();
                if ($masterFinanceUser) {
                    return returnCustom("Err-code DR-CU: Master finance is exist before and master finance only one user on one division");
                }
            }
    
            $divisionUser = DivisionUser::where('division_id', $data['division_id'])
                ->where('user_id', $data['user_id'])
                ->first();

            if($data['role'] == 'master_finance') {
                    $divisionUser = ($user->email != $division->director_email) ? $divisionUser : null;
            } else {
                $divisionUser;
            }
            // Check user id is not redundant on one role on division user
            if ($divisionUser) {
                return returnCustom("Err-code DR-CU: This account is already exist on this division");
            }
            
            $divisionUser = new DivisionUser();
            $divisionUser->createOrUpdate($division->id, $user->id, $data['role']);

            return returnCustom("Your data has been successfully submitted", true);
        } catch (\Exception $e) {
            return returnCustom("Err-code DR-CU: " . $e->getMessage());
        }
    }

    public function getByFilter($filters, $site = '') {
        $divisions = Division::with(['division_flip', 'division_journal', 'division_xendit']);
        if (empty($site)) {
            $divisions = $divisions->where('user_company_id', Auth::user()->user_company_id);
        }

        if (!empty($filters['title'])) {
            $divisions = $divisions->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['director_email'])) {
            $divisions = $divisions->where('director_email', 'like', '%' . $filters['director_email'] . '%');
        }

        $divisions = $divisions->orderBy('id', 'desc');
        return $divisions->paginate(10);
    }

    public function verifyFlipKey($key, $token) {
        try {
            $flipService = new FlipService($key, $token);
            $payloads = [
                'account_number' => '1111111',
                'bank_code' => 'bca'
            ];
            $url = "disbursement/bank-account-inquiry";
            return $flipService->callPostInq($key, $url, $payloads);
        } catch (\Exception $e) {
            return returnCustom("Err-code DR-VKF: " . $e->getMessage());
        }
    }
}