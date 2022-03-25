<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Division;
use App\Models\Inquiry;
use App\Models\Transaction;
use App\Models\User;
use App\Repositories\TransactionRepository;
use App\Repositories\UserRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class TransactionController extends Controller
{
    protected $transaction;

    public function __construct()
    {
        $this->transaction = new TransactionRepository();
    }

    public function index(Request $request) {
        $divisions = Division::all();
        $divisionIds = [];
        $filters = $request->only(['title', 'remark', 'division_name']);
        $transactions = $this->transaction->getByFilter($filters, $divisionIds, 'backend');
        return view('backend.transaction.index', compact('transactions', 'filters', 'divisions'));
    }

    public function detail(Request $request, $id = null) {
        $transaction = Transaction::with(['division.division_users', 'inquiry', 'transaction_statuses.user'])->find($id);
        if (!$transaction) {
            alertNotify(false, 'Your transaction data is not found', $request);
            return redirect()->back();
        }
        return view('backend.transaction.detail', compact('transaction'));
    }
}
