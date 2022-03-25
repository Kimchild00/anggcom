<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Inquiry;
use App\Repositories\InquiryRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class InquiryController extends Controller
{
    protected $inquiryRepository;

    public function __construct()
    {
        $this->inquiryRepository = new InquiryRepository();
    }

    public function index(Request $request) {
        $filters = $request->only(['name', 'account_number', 'status']);
        $inquiries = $this->inquiryRepository->getByFilter($filters);
        $pageName = 'Inquiry';
        return view('frontend.inquiry.index', compact('inquiries', 'filters', 'pageName'));
    }

    public function create() {
        $data = json_decode(file_get_contents('https://importir.com/api/list-bank-inquiry?token=syigdfjhagsjdf766et4wff6'), true);
        $banks = $data['message']['banks'];
        $cities = $data['message']['city'];
        $pageName = 'Create Inquiry';

        return view('frontend.inquiry.form', compact('banks', 'cities', 'pageName'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'name_by_input' => 'required',
            'account_number' => 'required',
            'bank' => 'required',
            'bank_city' => 'required',
        ]);
        $result = $this->inquiryRepository->createPost($request->all());
        if ($result['status']) {
            alertNotify(true, "Inquiry saved successfully", $request);
            return redirect(url('inquiry?name=&account_number=' . $result['message']));
        }
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function update(Request $request, $id = null) {
        $data = json_decode(file_get_contents('https://importir.com/api/list-bank-inquiry?token=syigdfjhagsjdf766et4wff6'), true);
        $banks = $data['message']['banks'];
        $cities = $data['message']['city'];
        $inquiry = Inquiry::find($id);
        if (!$inquiry) {
            alertNotify(false, 'Err-code IC-U: Data not found', $request);
            return redirect()->back();
        }
        if ($inquiry->status == 'SUCCESS') {
            alertNotify(false, "Err-code IC-U: Inquiry cannot modified", $request);
            return redirect()->back();
        }
        return view('frontend.inquiry.form', compact('inquiry', 'banks', 'cities'));
    }

    public function updatePost(Request $request) {
        $result = $this->inquiryRepository->updatePost($request->all());
        if ($result['status']) {
            alertNotify(true, "Inquiry updated successfully", $request);
            return redirect(url('inquiry?name=&account_number=' . $result['message']));
        }
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function delete(Request $request, $id = null) {
        $inquiry = Inquiry::find($id);
        if (!$inquiry) {
            alertNotify(false, 'Err-code IC-D: Data not found', $request);
            return redirect()->back();
        }

        if ($inquiry->name_by_server) {
            alertNotify(false, 'Err-code IC-D: Data cannot be deleted', $request);
        }
        
        if(Auth::user()->level == 'master' || Auth::user()->level == 'finance'){
            $inquiry->delete();
            alertNotify(true, "Data deleted successfully", $request);
        }else{
            alertNotify(false, "Err-code IC-D: You don't have permission to delete data", $request);
        }
        return redirect()->back();
    }
}
