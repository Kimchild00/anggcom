<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Journal\ExpenseService;
use App\Models\Division;
use App\Models\DivisionUser;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\JurnalRepositories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ApiController extends Controller {
    public function addCompanyCom($id = null, $token = null) {
        if ($token != env('TOKEN_ORG')) {
            return 'Wrong';
        }
        $data = json_decode(file_get_contents('https://importir.com/api/list-user-in-company/' . $id . '/' . env('TOKEN_ORG')));
        if (count($data) == 0) {
            return 'empty';
        }
        DB::beginTransaction();
        $users = [];
        foreach ($data->operational_user as $user) {
            if ($user->user) {
                $users[] = [
                    'user_company_id' => 1,
                    'level' => 'child',
                    'name' => $user->user->first_name . ' ' . $user->user->last_name,
                    'email' => $user->user->email,
                    'password' => $user->user->password,
                    'phone' => $user->user->phone,
                ];
            }
        }

        $emailUsers = array_column($users, 'email');
        $userData = User::whereIn('email', $emailUsers)->select('email')->get();
        $userData = array_column($userData->toArray(), 'email');

        foreach ($users as $key => $user) {
            if (in_array($user['email'], $userData)) {
                unset($users[$key]);
            }
        }
        $response = User::insert($users);
        $userNext = User::whereIn('email', $emailUsers)->get();

        $division = Division::where('title', $data->title)
            ->first();

        $divisionUsers = [];
        foreach ($data->operational_user as $user) {
            if ($user->user) {
                $userChoose = '';
                foreach ($userNext as $userN) {
                    if ($userN->email == $user->user->email) {
                        $userChoose = $userN;
                        break;
                    }
                }
                if ($userChoose) {
                    $divisionUsers[] = [
                        'division_id' => $division->id,
                        'user_id' => $userChoose->id,
                        'role' => $user->role,
                    ];
                }
            }
        }
        DivisionUser::insert($divisionUsers);

//        DB::commit();
        return response()->json();
    }

    public function pushManualJournal($id = null, Request $request) {
        $token = $request->get('token');
        if ($token != env('TOKEN_ORG')) {
            return response()->json([
                "status" => false,
                "data" => "Token ORG not found"
            ]);
        }

        $transaction = Transaction::with(['division.division_journal', 'transaction_finance_noted', 'transaction_tax'])
            ->find($id);
     
        if (!$transaction) {
            return response()->json([
                "status" => false,
                "data" => "Transaction not found"
            ]);
        }

        if ($transaction->current_status != "DONE") {
            return response()->json([
                "status" => false,
                "data" => "This transaction is not Done yet"
            ]);
        }

        $jurnalRepo = new JurnalRepositories();
        $res = $jurnalRepo->createJournalEntries($transaction);
        return response()->json($res);
    }

    public function listCoaByDivision($id = null, $all = 1,  Request $request) {
        $token = $request->get('token');
        if ($token != env('TOKEN_ORG')) {
            return 'False';
        }
        $expenseService = new ExpenseService();
        $result = $expenseService->getAllCAonlyParent($id, $all);
        return response()->json($result);
    }
}