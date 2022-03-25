<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Repositories\InquiryRepository;
use Illuminate\Http\Request;

class InquiryController extends Controller
{
    protected $inquiryRepository;

    public function __construct()
    {
        $this->inquiryRepository = new InquiryRepository();
    }

    public function index(Request $request) {
        $filters = $request->only(['name', 'account_number']);
        $inquiries = $this->inquiryRepository->getByFilter($filters, 'backend');
        return view('backend.inquiry.index', compact('inquiries', 'filters'));
    }
}
