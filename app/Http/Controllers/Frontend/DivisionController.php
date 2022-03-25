<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\DivisionUser;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\DivisionRepository;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DivisionController extends Controller
{
    protected $divisionRepository;

    public function __construct()
    {
        $this->divisionRepository = new DivisionRepository();
    }

    public function index(Request $request) {
        $filters = $request->only(['title', 'director_email']);
        $divisions = $this->divisionRepository->getByFilter($filters);
        $pageName = 'Division';
        return view('frontend.division.index', compact('divisions', 'filters', 'pageName'));
    }

    public function create(Request $request) {
        if (Auth::user()->level != 'master') {
            alertNotify(false, "Err-code DC-C: You don't have access!", $request);
            return redirect()->back();
        }

        $users = User::where('user_company_id', Auth::user()->user_company_id)
            ->get();
        $pageName = 'Create Division';
        $typeDisbursement = '';

        return view('frontend.division.form', compact('users', 'pageName', 'typeDisbursement'));
    }

    public function store(Request $request) { 
        if ($request->type_disbursement == 'xendit') {
            $this->validate($request, [
                'director' => 'required',
                'title' => 'required',
                'type_disbursement' => 'required',
                'xendit_name' => 'required',
                'xendit_key' => 'required'
            ]);
        } else {
            $this->validate($request, [
                'director' => 'required',
                'title' => 'required',
                'type_disbursement' => 'required',
                'flip_name' => 'required',
                'id_big_flip' => 'required',
                'flip_key' => 'required',
                'flip_token' => 'required'
            ]);
        }
        $result = $this->divisionRepository->createPost($request->all());
        if ($result['status']) {
            alertNotify(true, 'Data saved successfully.', $request);
            return redirect(url('division/' . $result['message']));
        }
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function edit(Request $request, $id = null) {
        if (Auth::user()->level != 'master') {
            alertNotify(false, "Err-code DC-E: You don't have access!", $request);
            return redirect()->back();
        }

        $users = User::where('user_company_id', Auth::user()->user_company_id)
            ->get();
        $division = Division::with(['division_flip', 'division_journal', 'division_xendit'])->find($id);
        $typeDisbursement = $division->type_disbursement;
        if (!$division) {
            alertNotify(false, 'Err-code DC-E: Division data not found!', $request);
            return redirect()->back();
        }

        if ($division->division_flip || $division->division_xendit) {
            $pageName = 'Edit ' . $division->title . ' Division';
            return view('frontend.division.form',compact('users', 'division', 'pageName' , 'typeDisbursement'));
        }

        alertNotify(false, "Err-code DC-E: Flip data not found, please contact our Support to help you.", $request);
        return redirect()->back();
    }

    public function updatePost(Request $request) {
        if ($request->type_disbursement == 'xendit') {
            $this->validate($request, [
                'director' => 'required',
                'title' => 'required',
                'type_disbursement' => 'required',
                'xendit_name' => 'required',
                'xendit_key' => 'required'
            ]);
        } else {
            $this->validate($request, [
                'director' => 'required',
                'title' => 'required',
                'flip_name' => 'required',
                'id_big_flip' => 'required',
                'flip_key' => 'required',
                'flip_token' => 'required'
            ]);
        }
        if (Auth::user()->level != 'master') {
            alertNotify(false, "Err-code DC-UP: You don't have access!", $request);
            return redirect()->back();
        }
        $result = $this->divisionRepository->updatePost($request->all());
        if ($result['status']) {
            alertNotify(true, 'Data saved successfully.', $request);
            return redirect(url('division/' . $result['message']));
        }
        alertNotify(false, $result['message'], $request);
        return redirect()->back();
    }

    public function delete(Request $request, $id = null) {
        if (Auth::user()->level != 'master') {
            alertNotify(false, "Err-code DC-D: You don't have access this action!", $request);
            return redirect()->back();
        }
        $division = Division::find($id);
        if (!$division) {
            alertNotify(false, 'Err-code DC-D: Data not found.', $request);
            return redirect()->back();
        }
        $division->delete();
        alertNotify(true, 'Data deleted successfully.', $request);
        return redirect()->back();
    }

    public function view(Request $request, $id = null) {
        $division = Division::with(['division_flip', 'division_journal', 'division_xendit'])
            ->find($id);
        if (!$division) {
            alertNotify(false, 'Err-code DC-D: data is not found', $request);
            return redirect()->back();
        }

        $isAdmin = false;
        $divisionUserMe = DivisionUser::where('division_id', $division->id)
            ->where('user_id', Auth::user()->id)
            ->first();
        if ($divisionUserMe) {
            if ($divisionUserMe->role == 'admin') {
                $isAdmin = true;
            }
        }

        $divisionUsers = DivisionUser::with(['user'])
            ->whereHas('user')
            ->where('division_id', $division->id)
            ->get();

        $users = User::where('user_company_id', Auth::user()->user_company_id)
            ->get();

        /*$result = $this->divisionRepository->sortirUser($divisionUsers, $users);
        if (!$result['status']) {
            alertNotify(false, $result['message'], $request);
            return redirect()->back();
        }
        $users = $result['message'];*/

        $transactions = Transaction::where('division_id', $division->id)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        $pageName = 'View Division ' . $division->title;
        return view('frontend.division.view', compact('division', 'divisionUsers', 'users', 'transactions', 'isAdmin', 'pageName'));
    }

    public function createUser(Request $request) {
        $this->validate($request, [
            'user_id' => 'required',
            'role' => 'required'
        ]);
        $result = $this->divisionRepository->createUser($request->all());
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function deleteUser(Request $request, $id = null, $userId = null, $role = null) {
        $division = Division::find($id);
        if (!$division) {
            alertNotify(false, 'Err-code DC-DU: Data is not found', $request);
            return redirect()->back();
        }

        $isAdmin = false;
        $divisionUserMe = DivisionUser::where('division_id', $division->id)
            ->where('user_id', Auth::user()->id)
            ->first();
        if ($divisionUserMe) {
            if ($divisionUserMe->role == 'admin') {
                $isAdmin = true;
            }
        }
        if (!$isAdmin) {
            alertNotify(false, "Err-code DC-DU: You don't have access!", $request);
            return redirect()->back();
        }

        $divisionUser = DivisionUser::where('user_id', $userId)
            ->where('division_id', $division->id)->where('role', $role)
            ->first();
        if (!$divisionUser) {
            alertNotify(false, 'Err-code DC-DU: Data is not found', $request);
            return redirect()->back();
        }
        $divisionUser->delete();
        alertNotify(true, 'Data deleted successfully', $request);
        return redirect()->back();
    }
}
