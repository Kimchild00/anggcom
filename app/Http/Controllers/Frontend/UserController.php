<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    protected $userRepository;

    public function __construct()
    {
        $this->userRepository = new UserRepository();
    }

    public function index(Request $request) {
        $filters = $request->only(['name', 'email']);
        $pageName = 'User';
        $users = $this->userRepository->getByFilter($filters);
        return view('frontend.user.index', compact('users', 'filters', 'pageName'));
    }

    public function create(Request $request) {
        if(Auth::user()->level != 'master') {
            alertNotify(false, 'Err-code UC-C: You don\'t have access!', $request);
            return redirect()->back();
        }

        $pageName = 'Create User';
        return view('frontend.user.form', compact('pageName'));
    }

    public function store(Request $request) {

        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'phone' => 'required',
            'level' => 'required',
            'password' => 'required',
        ]);
        
        if(Auth::user()->level != 'master') {
            alertNotify(false, "Err-code UC-S: You don't have access!", $request);
            return redirect()->back();
        }
       
        $result = $this->userRepository->createPost($request->all());
        alertNotify($result['status'], $result['message'], $request);
        if ($result['status']) {
            return redirect(url('user'));
        }
        return redirect()->back();
    }

    public function edit(Request $request, $id = null) {
        if(Auth::user()->level != 'master') {
            alertNotify(false, 'Err-code UC-E: You don\'t have access', $request);
            return redirect()->back();
        }
        $user = User::find($id);
        if (!$user) {
            alertNotify(false, 'Err-code UC-E: Data not found', $request);
            return redirect()->back();
        }
        $pageName = 'Edit User';

        return view('frontend.user.form',compact('user','pageName'));
    }

    public function update(Request $request, $id) {
        $this->validate($request, [
            'name' => 'required|max:255',
            'email' => 'required|email',
            'phone' => 'required',
            'level' => 'required',
        ]);
        if(Auth::user()->level != 'master') {
            alertNotify(false, 'Err-code UC-U: You don\'t have access', $request);
            return redirect()->back();
        }
        $result = $this->userRepository->updatePost($request->all(), $id);
        alertNotify($result['status'], $result['message'], $request);
        if ($result['status']) {
            return redirect(url('user'));
        }
        return redirect()->back();
    }

    public function delete(Request $request, $id = null) {
        if (Auth::user()->level != 'master') {
            alertNotify(false, "Err-code UC-D: You don't have access!", $request);
            return redirect()->back();
        }
        $user = User::find($id);
        if (!$user) {
            alertNotify(false, 'Err-code UC-D: Data is not found', $request);
            return redirect()->back();
        }
        if ($user->level == 'master') {
            alertNotify(false, "Err-code UC-D: Account master cannot be deleted!", $request);
            return redirect()->back();
        }
        $user->delete();
        alertNotify(true, 'Data deleted successfully', $request);
        return redirect()->back();
    }

    public function updateOtp(Request $request, $id = null) {
        $otp = $request->get('otp');
        $result = $this->userRepository->updateOtp($id,$otp);

        return $result;
    }

    public function resetPasswordProcess(Request $request, $id = null) {
        $password = $request->get('resetPassword');
        $confPassword = $request->get('confPassword');

        $result = $this->userRepository->resetPasswordProcess($id, $password, $confPassword);

        return response()->json($result);
    }
}
