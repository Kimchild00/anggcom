<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserMemberOrder;
use App\Repositories\IndexRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class IndexController extends Controller
{
    protected $indexRepository;

    public function __construct()
    {
        $this->indexRepository = new IndexRepository();
    }

    public function dashboard(Request $request) {
        $filters = $request->only(['start_date', 'end_date']);
        $startDate = null;
        $endDate = null;

        if(!is_null($request->start_date) && !is_null($request->end_date)) {
            $startDate = date('Y-m-d H:i:s', strtotime($request->start_date)); 
            $endDate = date('Y-m-d H:i:s', strtotime($request->end_date));
        }
        
        $pageName = 'Dashboard';

        $new = Transaction::with(['division'])->where('current_status', '')->whereHas('division', function($q) use($filters){
            $q->where('user_company_id', Auth::user()->user_company->id);
        });
        if(!is_null($request->start_date) && !is_null($request->end_date)) {
            $new = $new->whereBetween('created_at', [$startDate, $endDate]);
        }
        $new = $new->select('id')->count(); 


        $approvedCreator = Transaction::with(['division'])->where('current_status', 'approved_user')->whereHas('division', function($q) use($filters){
            $q->where('user_company_id', Auth::user()->user_company->id);
        });
        if(!is_null($request->start_date) && !is_null($request->end_date)) {
            $approvedCreator = $approvedCreator->whereBetween('created_at', [$startDate, $endDate]);
        }
        $approvedCreator = $approvedCreator->select('id')->count();


        $approvedDirector = Transaction::with(['division'])->where('current_status', 'approved_director')->whereHas('division', function($q) use($filters){
            $q->where('user_company_id', Auth::user()->user_company->id);
        });
        if(!is_null($request->start_date) && !is_null($request->end_date)) {
            $approvedDirector = $approvedDirector->whereBetween('created_at', [$startDate, $endDate]);
        }
        $approvedDirector = $approvedDirector->select('id')->count();


        $rejectedDirector = Transaction::with(['division'])->where('current_status', 'rejected_director')->whereHas('division', function($q) use($filters){
            $q->where('user_company_id', Auth::user()->user_company->id);
        });
        if(!is_null($request->start_date) && !is_null($request->end_date)) {
            $rejectedDirector = $rejectedDirector->whereBetween('created_at', [$startDate, $endDate]);
        }
        $rejectedDirector = $rejectedDirector->select('id')->count();


        $approvedFinance = Transaction::with(['division'])->where('current_status', 'approved_finance')->whereHas('division', function($q) use($filters){
            $q->where('user_company_id', Auth::user()->user_company->id);
        });
        if(!is_null($request->start_date) && !is_null($request->end_date)) {
            $approvedFinance = $approvedFinance->whereBetween('created_at', [$startDate, $endDate]);
        }
        $approvedFinance = $approvedFinance->select('id')->count();


        $rejectedFinance = Transaction::with(['division'])->where('current_status', 'rejected_finance')->whereHas('division', function($q) use($filters){
            $q->where('user_company_id', Auth::user()->user_company->id);
        });
        if(!is_null($request->start_date) && !is_null($request->end_date)) {
            $rejectedFinance = $rejectedFinance->whereBetween('created_at', [$startDate, $endDate]);
        }
        $rejectedFinance = $rejectedFinance->select('id')->count();


        $transferred = Transaction::with(['division'])->where('current_status', 'transferred')->whereHas('division', function($q) use($filters){
            $q->where('user_company_id', Auth::user()->user_company->id);
        });
        if(!is_null($request->start_date) && !is_null($request->end_date)) {
            $transferred = $transferred->whereBetween('created_at', [$startDate, $endDate]);
        }
        $transferred = $transferred->select('id')->count();


        $done = Transaction::with(['division'])->where('current_status', 'DONE')->whereHas('division', function($q) use($filters){
            $q->where('user_company_id', Auth::user()->user_company->id);
        });
        if(!is_null($request->start_date) && !is_null($request->end_date)) {
            $done = $done->whereBetween('created_at', [$startDate, $endDate]);
        }
        $done = $done->select('id')->count();

        $users = User::where('user_company_id', Auth::user()->user_company_id)
            ->select('id')
            ->count();
        $divisions = Division::where('user_company_id', Auth::user()->user_company_id)
            ->select('id')
            ->count();
        return view('frontend.index.dashboard', compact(
            'pageName',
            'new',
            'approvedCreator',
            'approvedDirector',
            'rejectedDirector',
            'approvedFinance',
            'rejectedFinance',
            'transferred',
            'done',
            'users',
            'divisions',
            'filters'
        ));
    }

    public function payment($invoiceNumber = null)
    {
        $result = $this->indexRepository->payment($invoiceNumber);
        if (!$result['status']) {
            return $result['message'];
        }
        $userMemberOrder = $result['message'];
        $pageName = 'Payment Page for Membership';
        return view('frontend.index.payment', compact('userMemberOrder', 'pageName'));
    }

    public function cancelOption(Request $request, $id)
    {
        $userMemberOrder = UserMemberOrder::find($id);
        if (!$userMemberOrder) {
            alertNotify(false, "User member order data is not found", $request);
        } else {
            $userMemberOrder->dump = '';
            $userMemberOrder->save();
            alertNotify(true, "Payment option has been successfully delete", $request);
        }
        return redirect()->back();
    }

    public function profile()
    {
        $pageName = 'Profile Page';
        $user = Auth::user();
        return view('frontend.index.profile', compact('pageName', 'user'));
    }

    public function changePassword()
    {
        $pageName = '';
        return view('frontend.user.change-password', compact('pageName'));
    }

    public function changePasswordProcess(Request $request)
    {
        $result = $this->indexRepository->changePassword($request->all());
        if ($result['status']) {
            alertNotify(true, ($result['message']), $request);
            return redirect('/profile');
        } else {
            alertNotify(false, ($result['message']), $request);
            return redirect()->back();
        }
    }


    public function forgotPassword()
    {
        return view('frontend.auth.forgot-password');
    }

    public function forgotPasswordProcess(Request $request)
    {
        $result = $this->indexRepository->forgotPasswordProcess($request);
        alertNotify($result['status'], ($result['message']), $request);
        if ($result['status']) {
            return redirect('/login');
        } else {
            return redirect()->back();
        }
    }

    public function requestPassword(Request $request)
    {
        $data = [
            "token" => $request->query("token"),
            "email" => $request->query("email"),
        ];
        $result = $this->indexRepository->getUserForTok($request->all());
        if ($result['status']) {
            return view('frontend.user.forgot-password-user', $data);
        } else {
            alertNotify(false, ($result['message']), $request);
            return redirect("/login");
        }
    }

    public function requestPasswordProcess(Request $request)
    {
        $result = $this->indexRepository->requestPassword($request->all());
        alertNotify($result['status'], ($result['message']), $request);
        if ($result['status']) {
            return redirect('/login');
        } else {
            return redirect()->back();
        }
    }
}
