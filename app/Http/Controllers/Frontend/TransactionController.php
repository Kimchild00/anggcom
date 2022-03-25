<?php

namespace App\Http\Controllers\Frontend;

use App\AwsSdk\S3Service;
use App\Flip\FlipService;
use App\Http\Controllers\Controller;
use App\Journal\ExpenseService;
use App\Models\Division;
use App\Models\DivisionJournal;
use App\Models\DivisionUser;
use App\Models\Inquiry;
use App\Models\Transaction;
use App\Repositories\TransactionRepository;
use App\Models\TransactionFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Api\ApiController;
use App\Repositories\JurnalRepositories;

class TransactionController extends Controller
{
    protected $transaction, $flip, $apiController, $jurnalRepo;

    public function __construct()
    {
        $this->transaction  = new TransactionRepository();
        $this->flip         = new FlipService();
        $this->jurnalRepo   = new JurnalRepositories();
    }

    public function index(Request $request) {
        $divisions = Division::with(['division_user_by_me'])
            ->where('user_company_id', Auth::user()->user_company->id);

        if (Auth::user()->level == 'child') {
            $divisions = $divisions->whereHas('division_user_by_me');
        }
        $divisions = $divisions->get();
        if (count($divisions) == 0) {
            $divisionIds = [];
        } else {
            $divisionIds = array_column($divisions->toArray(), 'id');
        }

        $filters = $request->only(['id', 'title', 'remark', 'division', 'status', 'start_transferred_date', 'end_transferred_date', 'is_push', 'created_by', 'filter_for']);
        $transactions = $this->transaction->getByFilter($filters, $divisionIds ,'' , 20);
        $pageName = 'Transactions';
        return view('frontend.transaction.index', compact('transactions', 'filters', 'divisions', 'pageName'));
    }

    public function countTransaction(Request $request)
    {
        $divisions = Division::with(['division_user_by_me'])
            ->where('user_company_id', Auth::user()->user_company->id);

        if (Auth::user()->level == 'child') {
            $divisions = $divisions->whereHas('division_user_by_me');
        }
        $divisions = $divisions->get();
        if (count($divisions) == 0) {
            $divisionIds = [];
        } else {
            $divisionIds = array_column($divisions->toArray(), 'id');
        }

        $filters = $request->only(['id', 'title', 'remark', 'division', 'status', 'transferred_date', 'is_push', 'created_by']);
        $filtersSort = array_filter($filters);
        $filtersSort['countTransaction'] = true;
        $result = $this->transaction->getByFilter($filtersSort, $divisionIds);
        return $result;
    }

    public function create() {
        $userCompanyId = Auth::user()->user_company_id;
        $pageName = 'Create Transaction';

        $inquiries = Inquiry::where('user_company_id', $userCompanyId)
            ->orderBy('id', 'desc')
            ->get();
        $divisions = Division::with(['division_user_by_me'])
            ->where('user_company_id', $userCompanyId);
        $divisions = $divisions->whereHas('division_user_by_me');
        $divisions = $divisions->get();
        return view('frontend.transaction.form', compact('inquiries', 'divisions', 'pageName'));
    }

    public function store(Request $request) {
        $this->validate($request, [
            'title' => 'required',
            'division' => 'required',
            'inquiry' => 'required',
            'amount' => 'required',
            'remark' => 'required',
            'description' => 'required',
        ]);
        $result = $this->transaction->createPost($request->all());
        if (!$result['status']) {
            alertNotify($result['status'], $result['message'], $request);
            return redirect()->back();
        }
        alertNotify($result['status'], "Your data has been successfully submitted", $request);

        return redirect(url('transaction/' . $result['message']->id));
    }

    public function edit(Request $request, $id = null)
    {
        $transaction = Transaction::with(['transaction_tax', 'transaction_files'])->find($id);
        if (!$transaction) {
            alertNotify(false, 'Err-code TC-E: Data not found', $request);
            return redirect()->back();
        }

        if (!in_array($transaction->current_status,['', 'approved_user'])) {
            alertNotify(false, 'Err-code TC-E: The data cannot be changed, because it has gone through the approval stage', $request);
            return redirect()->back();
        }
        
        $transactionTax = 0;
        $transactionTypeTax = 'no_tax';
        if($id != null){
            if (!$transaction->transaction_tax) {
                $transactionTax = 0;
                $transactionTypeTax = 'no_tax';
            }else{
                $transactionTax = $transaction->transaction_tax->amount;
                $transactionTypeTax = $transaction->transaction_tax->type;
            }
        }

        $transactionReport = null;
        if($id != null){
            $transactionReport = $transaction->transaction_files;
        }

        $pageName = 'Edit Transaction';
        $userCompanyId = Auth::user()->user_company_id;

        $inquiries = Inquiry::where('user_company_id', $userCompanyId)
            ->orderBy('id', 'desc')
            ->get();
        $divisions = Division::with(['division_user_by_me'])
            ->where('user_company_id', $userCompanyId);
        $divisions = $divisions->whereHas('division_user_by_me');
        $divisions = $divisions->get();

        return view('frontend.transaction.edit-transaction', compact('inquiries', 'divisions', 'pageName', 'transaction', 'transactionTax', 'transactionReport', 'transactionTypeTax'));

    }

    public function update(Request $request, $id)
    {
        $this->validate($request, [
            'title' => 'required',
            'division' => 'required',
            'inquiry' => 'required',
            'amount' => 'required',
            'remark' => 'required',
        ]);
        $result = $this->transaction->updatePost($request->all(), $id);
        if (!$result['status']) {
            alertNotify($result['status'], $result['message'], $request);
            return redirect()->back();
        }
        alertNotify($result['status'], "Your data has been successfully edited", $request);
        return redirect(url('transaction/' . $result['message']->id));
    }

    public function delete(Request $request, $id = null) {
        $transactionFile = TransactionFile::find($id);
        if (!$transactionFile) {
            return [
                'status' => false,
                'message' => 'Data Not Fund'
            ];
        }

        $transactionFile->delete();
        return [
            'status' => true,
            'message' => 'Data deleted successfully.'
        ];
    }

    public function deleteTransaction(Request $request, $id = null) {
        $result = $this->transaction->deleteTransaction($id);
        alertNotify($result['status'], $result['message'], $request);
        return redirect(url('transaction'));
    }

    public function view(Request $request, $id = null) {
        $transaction = Transaction::with(['division.division_users', 'inquiry', 'transaction_files', 'transaction_tax', 'transaction_flip', 'division.division_journal', 'division.division_flip', 'division.division_xendit', 'transaction_finance_noted'])->find($id);

        if (!$transaction) {
            alertNotify(false, 'Your transaction data is not found', $request);
            return redirect()->back();
        }
        $pageName = 'Transactions detail ID' . $transaction->id;
        $isFinance = false;
        $isAdminOrOperator = true;
        $isMasterFinance = false;

        foreach ($transaction->division->division_users as $user) {
            if ($user->role == 'finance') {
                if ($user->user_id == Auth::user()->id) {
                    $isFinance = true;
                }
            }

            if ($transaction->division->director_email == Auth::user()->email) {
                $isAdminOrOperator = false;
            }

            if ($transaction->division->director_email != Auth::user()->email) {
                if ($user->user_id == Auth::user()->id) {
                    if (!in_array($user->role, ['admin', 'operator'])) {
                        $isAdminOrOperator = false;
                    }
                }
            }

            if ($user->role == 'master_finance') {
                if ($user->user_id == Auth::user()->id) {
                    $isMasterFinance = true;
                }
            }
        }

        if (Auth::user()->level != 'master') {
            if ($isAdminOrOperator) {
                if ($transaction->user_id != Auth::user()->id) {
                    alertNotify(false, 'You don\'t allowed to access this page, because your role is admin or operator', $request);
                    return redirect()->back();
                }
            }
        }
        return view('frontend.transaction.view', compact('transaction', 'pageName', 'isFinance', 'isMasterFinance'));
    }

    public function financeReport(Request $request){
        $filters = $request->all();

        $divisions = Division::with(['division_user_by_me'])
            ->where('user_company_id', Auth::user()->user_company->id);

        if (Auth::user()->level == 'child') {
            $divisions = $divisions->whereHas('division_user_by_me');
        }
        
        $divisions = $divisions->get();
        
        if (count($divisions) == 0) {
            $divisionIds = [];
        } else {
            $divisionIds = array_column($divisions->toArray(), 'id');
        }

        $resultTransactions = $this->transaction->getByFilter($filters, $divisionIds ,'' , 25);
        $divisions = Division::with(['division_user_by_me'])
            ->where('user_company_id', Auth::user()->user_company->id)
            ->get();
        $pageName = 'Report Finance';
        return view('frontend.transaction.finance-report', compact('resultTransactions', 'divisions', 'filters', 'pageName'));
    }

    public function exportFinanceReport(Request $request){
        $filters            = $request->all();

        $divisions = Division::with(['division_user_by_me'])
            ->where('user_company_id', Auth::user()->user_company->id);

        if (Auth::user()->level == 'child') {
            $divisions = $divisions->whereHas('division_user_by_me');
        }
        
        $divisions = $divisions->get();
        
        if (count($divisions) == 0) {
            $divisionIds = [];
        } else {
            $divisionIds = array_column($divisions->toArray(), 'id');
        }

        $resultTransactions = $this->transaction->getByFilter($filters, $divisionIds ,'' , 0);
        
        $date               = date('Y-m-d');

        $result = $resultTransactions->map(function ($item, $key) {
            $reportAmount  = '';
            $reportFiles   = '';

            foreach ($item->transaction_files as $data) {
                $reportAmount  .= "RP $data->amount, ";
                $reportFiles   .= "$data->cdn_file_path , ";
            }

            return [
                'Id'                    => $item->id,
                'Server id'             => ($item->transaction_flip ? $item->transaction_flip->server_id : '' ),
                'Division'              => ($item->division ? $item->division->title : '' ),
                'Title'                 => $item->title,
                'Description'           => $item->description,
                'Amount'                => $item->amount,
                'fee'                   => ($item->transaction_flip ? $item->transaction_flip->fee : ''),
                'ott_name'              => $item->ott_name,
                'created_by'            => ($item->created_by ? $item->created_by->name : ''), // cek relasi 
                'tanggal Dibuat'        => $item->created_at,
                'Remark'                => $item->remark,
                'status_server'         => ($item->transaction_flip ? $item->transaction_flip->server_status : ''), // done
                'Receipt_server'        => ($item->transaction_flip ? $item->transaction_flip->server_receipt : ''),
                'transferred_by'        => ($item->transaction_statuses ? $this->transaction->getTransactionStatusesTitleOrCreatedAtByTittle($item->transaction_statuses, 'transferred') : ''),
                'Push_at'               => ($item->transaction_statuses ? $this->transaction->getTransactionStatusesTitleOrCreatedAtByTittle($item->transaction_statuses, 'transferred', true) : ''),
                'Approved_director_by'  => ($item->transaction_statuses ? $this->transaction->getTransactionStatusesTitleOrCreatedAtByTittle($item->transaction_statuses, 'approved_director') : ''),
                'Manager_Approve_at'    => ($item->transaction_statuses ? $this->transaction->getTransactionStatusesTitleOrCreatedAtByTittle($item->transaction_statuses, 'approved_director', true) : ''),
                'approved_admin_by'     => ($item->transaction_statuses ? $this->transaction->getTransactionStatusesTitleOrCreatedAtByTittle($item->transaction_statuses, 'approved_user'): ''),
                'approved_admin_at'     => ($item->transaction_statuses ? $this->transaction->getTransactionStatusesTitleOrCreatedAtByTittle($item->transaction_statuses, 'approved_user', true) : ''),
                'approved_finance_by'   => ($item->transaction_statuses ? $this->transaction->getTransactionStatusesTitleOrCreatedAtByTittle($item->transaction_statuses, 'approved_finance') : ''),
                'approved_finace_at'    => ($item->transaction_statuses ? $this->transaction->getTransactionStatusesTitleOrCreatedAtByTittle($item->transaction_statuses, 'approved_finance', true) : ''),
                'admin_note'            => (count($item->transaction_files) >= 1 ? $item->transaction_files[0]->note : '' ),
                'reported_at'           => '', // blom ada
                'reports_amount'        => $reportAmount,
                'reports_file'          => $reportFiles,
                'Penerima Dana'         => ($item->inquiry ? $item->inquiry->name_by_server :''),
            ];
        });

        $fileName = "Finance Report $date";
        
        $result = $this->transaction->exportExcel($result, $fileName);

        return redirect()->back();
    }


    public function approveUser(Request $request, $id = null) {
        $result = $this->transaction->approveUser($id);
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function approveDirector(Request $request, $id = null) {
        $result = $this->transaction->approveOrRejectDirector($id, 'approved');
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function rejectDirector(Request $request, $id = null) {
        $result = $this->transaction->approveOrRejectDirector($id, 'rejected', $request->note);
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function approveFinance(Request $request, $id = null) {
        $result = $this->transaction->approveOrRejectFinance($id, 'approved');
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function rejectFinance(Request $request, $id = null) {
        $result = $this->transaction->approveOrRejectFinance($id, 'rejected', $request->note);
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function pushPayment(Request $request, $id = null) {
        $result = $this->transaction->pushPayment($id);
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function rejectMasterFinance(Request $request, $id = null) {
        $result = $this->transaction->pushPayment($id, 'rejected', $request->note);
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function getChartOfAccounts($id = null) {
        $division = DivisionJournal::where('division_id', $id)
            ->first();
        if (!$division) {
            return response()->json(returnCustom([]));
        }
        $expenseService = new ExpenseService();
        $chartOfAccounts = $expenseService->getAllCAonlyParent($division->journal_key);
        return response()->json(returnCustom($chartOfAccounts, true));
    }

    public function uploadFile(Request $request) {
        $data = $request->all();
        $image = $data['file'];
        $s3Service = new S3Service();
        $result = $s3Service->putObject($image, $image->getClientOriginalName(), $image->getMimeType());
        return $result;
    }

    public function changeChartOfAccounts(Request $request) {
        $result = $this->transaction->changeChartOfAccounts($request->all());
        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function getSaldo($id){
        $transaction = Transaction::with(['division.division_flip'])->find($id);
        if(!$transaction){
            return returnCustom("Err-code TC-GS: Your transaction data is not found");
        }
        if(!$transaction->division){
            return returnCustom("Err-code TC-GS: Your division data is not found");
        }
        if(!$transaction->division->division_flip){
            return returnCustom("Err-code TC-GS: Your division_flip data is not found");
        }
        $GSF = $this->flip->getSaldoFlip($transaction->division->division_flip->flip_key);
        return $GSF;
    }

    public function financeNoteStoreOrEdit(Request $request) {
        $data = request()->all();

        $result = $this->transaction->financeNoteStoreOrEdit($data);

        alertNotify($result['status'], $result['message'], $request);
        return redirect()->back();
    }

    public function pushManualJournal(Request $request, $id = null) 
    {   
        $auth_id = Auth::user()->id;
        if ($auth_id == null) {
            return response()->json([
                "status" => false,
                "data" => "your id is missing, you have to login first"
            ]);
        }
        $transaction = Transaction::with(['division.division_journal', 'division.division_users', 'transaction_tax'])->find($id);
        if(!$transaction) {
            return response()->json([
                "status" => false,
                "data" => "transaction is not found"
            ]);
        }

        if (!$transaction->check_button_push) {
            return response()->json([
                "status" => false,
                "data" => "You are not entitled to continue the order, because you are not a master finance or finance, and it could be that your transaction is not suitable for continuing orders"
            ]);
        }

        $result = $this->jurnalRepo->createJournalEntries($transaction);
        return response()->json($result);
    }
}