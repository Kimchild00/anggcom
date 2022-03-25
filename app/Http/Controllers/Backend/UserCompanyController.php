<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\UserCompany;
use App\Repositories\UserCompanyRepository;
use Illuminate\Http\Request;

class UserCompanyController extends Controller
{
    protected $userCompanyRepository;

    public function __construct()
    {
        $this->userCompanyRepository = new UserCompanyRepository();
    }

    public function index(Request $request) {
        $filters = $request->only(['title', 'package_name']);
        $userCompanies = $this->userCompanyRepository->getByFilter($filters);
        return view('backend.user-company.index', compact('filters', 'userCompanies'));
    }
}
