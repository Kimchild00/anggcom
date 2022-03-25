<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UserCompany;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function index(Request $request) {
        $filters = $request->only(['name', 'email', 'company_name']);
        $users = $this->userRepository->getByFilter($filters, 'backend');
        $userCompanies = UserCompany::all();
        return view('backend.user.index', compact('users', 'filters', 'userCompanies'));
    }

    public function detail(Request $request, $id = null) {
        $userCompany = UserCompany::with(['users', 'user_member_orders'])
            ->where('id', $id)
            ->orderBy('id', 'desc')
            ->first();
        if (!$userCompany) {
            alertNotify(false, 'User company is not found', $request);
            return redirect()->back();
        }
        return view('backend.user.detail', compact('userCompany'));
    }

    public function paidManually(Request $request, $id = null) {
        $result = $this->userRepository->paidManually($id);
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }
}
