<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\UserCompany;
use App\Repositories\IndexRepository;

class IndexController extends Controller
{
    protected $indexRepository;

    public function __construct()
    {
        $this->indexRepository = new IndexRepository();
    }

    public function dashboard() {
        $userCompanyCount = UserCompany::with([])
            ->count();
        $transactionCount = Transaction::with([])
            ->whereIn('current_status', ['transferred', 'DONE'])
            ->count();

        return view('backend.index.dashboard', compact('userCompanyCount', 'transactionCount'));
    }
}
