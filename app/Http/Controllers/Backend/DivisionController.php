<?php

namespace App\Http\Controllers\Backend;

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
        $divisions = $this->divisionRepository->getByFilter($filters, 'backend');
        return view('backend.division.index', compact('divisions', 'filters'));
    }

    public function detail(Request $request, $id = null) {
        $division = Division::with(['division_flip', 'division_journal'])
            ->find($id);
        if (!$division) {
            alertNotify(false, 'Your data is not found', $request);
            return redirect()->back();
        }
        $divisionUsers = DivisionUser::with(['user'])
            ->where('division_id', $division->id)
            ->get();

        $transactions = Transaction::where('division_id', $division->id)
            ->orderBy('id', 'desc')
            ->limit(5)
            ->get();

        return view('backend.division.detail', compact('division', 'divisionUsers', 'transactions'));
    }
}
