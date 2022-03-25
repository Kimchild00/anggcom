<?php

namespace App\Repositories;

use App\AwsSdk\S3Service;
use App\Flip\FlipService;
use App\Xendit\XenditService;
use App\Models\Division;
use App\Models\DivisionJournal;
use App\Models\Inquiry;
use App\Models\Transaction;
use App\Models\TransactionFile;
use App\Models\TransactionFinanceNoted;
use App\Models\TransactionFlip;
use App\Models\TransactionXendit;
use App\Models\TransactionStatus;
use App\Models\TransactionTax;
use App\ThirdParty\EmailService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionRepository {
    public function createPost($data) {
        try {
            DB::beginTransaction();

            $inquiry = Inquiry::find($data['inquiry']);
            if (!$inquiry) {
                return returnCustom("Err-code TR-CP: Account number not found");
            }

            if ($inquiry->status != 'SUCCESS') {
                return returnCustom("Err-code TR-CP: Account number not valid.");
            }

            $division = Division::with(['division_journal'])
                ->find($data['division']);
            if (!$division) {
                return returnCustom("Err-code TR-CP: Division not found");
            }

            if ($division->division_journal) {
                if (!isset($data['journal'])) {
                    return returnCustom("Journal input not found");
                }
            }

            if( strlen($data['remark']) > 18 ) {
                return returnCustom("Err-code TR-CP: Remark more than 18 character");
            }

            if(preg_match('/[^a-z0-9 _]+/i', $data['remark'])) {
                return returnCustom("Err-code TR-CP: not allowed special character");
            }

            $ottCode = 0;
            $ottName = '';
            if (isset($data['journal'])) {
                $journal = explode('^^', $data['journal']);
                $ottCode = $journal[0];
                $ottName = $journal[1];
            }
            
            if(isset($data['exist_tax']) != 'no_tax'){
                if ($data['amount_net'] != array_sum($data['amount_file'])) {
                    return returnCustom("Err-code TR-CP: Amount After Tax is not same with total amount of files");
                }
            }

            $amountTransaction = str_replace(',', '', $data['amount']);
            $amountTax = str_replace(',', '', $data['amount_tax']);
            $amountNet = str_replace(',', '', $data['amount_net']);
            $amountReport = array_sum(str_replace(',', '', $data['amount_file']));
            
            if ($data['exist_tax'] == 'no_tax') {
                if ($amountTransaction != $amountReport) {
                    return returnCustom("Err-code TR-CP: Amount Total is not same with total amount of files");
                }
            }else if ($data['exist_tax'] != 'no_tax') {
                $amountTransaction -= $amountTax;
                $resultAfterTax = $amountTransaction;
                if ($amountNet != $amountReport) {
                    return returnCustom("Err-code TR-CP: Amount After Tax is not same with total amount of files");
                }else if($amountNet == $amountReport){
                    if ($amountReport != $resultAfterTax) {
                        return returnCustom("Err-code TR-CP: Amount After Tax False (Amount - Tax Amount)");
                    }
                }
            }

            $transaction = new Transaction();
            $transaction->createOrUpdate(Auth::user()->id, $inquiry->id, $division->id, $ottCode, $ottName, $data['title'], $data['description'], $amountTransaction, $data['remark']);

            if ($data['exist_tax'] != 'no_tax') {
                $transactionTax = new TransactionTax();
                $transactionTax->createNewOrUpdate($transaction->id, $data['exist_tax'], $data['amount_tax']);
            }

            foreach ($data['files'] as $key => $file) {
                $transactionFile = new TransactionFile();
                $amountReport = str_replace(',', '', $data['amount_file'][$key]);
                $transactionFile->createNewOrUpdate($transaction->id, $data['file_name_' . $key], $amountReport, $data['note'][$key]);
            }

            DB::commit();
            return returnCustom($transaction, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code TR-CP: " . $e->getMessage());
        }
    }

    public function updatePost($data, $id) {
        try {
            $transaction = Transaction::find($id);
            // Cannot empty or null
            if (!$transaction) {
                return returnCustom("Err-code TR-UP: Transaction data not found");
            }

            DB::beginTransaction();

            $inquiry = Inquiry::find($data['inquiry']);
            if (!$inquiry) {
                return returnCustom("Err-code TR-UP: Account number not found");
            }

            if ($inquiry->status != 'SUCCESS') {
                return returnCustom("Err-code TR-UP: Account number not valid.");
            }

            $division = Division::with(['division_journal'])
                ->find($data['division']);
            if (!$division) {
                return returnCustom("Err-code TR-UP: Division not found");
            }

            if ($division->division_journal) {
                if (!isset($data['journal'])) {
                    return returnCustom("Journal input not found");
                }
            }

            if( strlen($data['remark']) > 18 ) {
                return returnCustom("Err-code TR-UP: Remark more than 18 character");
            }

            if(preg_match('/[^a-z0-9 _]+/i', $data['remark'])) {
                return returnCustom("Err-code TR-UP: not allowed special character");
            }

            $ottCode = 0;
            $ottName = '';
            if (isset($data['journal'])) {
                $journal = explode(',', $data['journal']);
                $ottCode = $journal[0];
                $ottName = $journal[1];
            }

            $amountReport = 0;
            if(isset($data['delete-file'])){
                foreach($data['delete-file'] as $deleteFile){
                    $transactionFile = TransactionFile::find($deleteFile);
                    $transactionFile->delete();
                }
            }
            if (count($data["transaction_files"]) > 0) {
                foreach ($data["transaction_files"] as $key => $trxFile) {
                    if (isset($trxFile["new_file"])) {
                        $transactionFile = new TransactionFile();
                        $transactionFile->transaction_id = $id;
                    }else{
                        $transactionFile = TransactionFile::find($trxFile["id"]);
                    }
                    if ($transactionFile) {
                        if(isset($data['exist_tax']) != 'no_tax'){
                            $amountReport += str_replace(',', '', $trxFile["amount_file"]);
                        }
                        $amountReport += str_replace(',', '', $trxFile["amount_file"]);
                        $transactionFile->file_name = is_null($trxFile["new_file"]) ? $trxFile["file_name"] : $trxFile["new_file"];
                        $transactionFile->amount = str_replace(',', '', $trxFile["amount_file"]);
                        $transactionFile->note = is_null($trxFile["note"]) ? '' : $trxFile["note"];
                        $transactionFile->save();
                    }
                }
            }
            
            if(isset($data['exist_tax']) != 'no_tax'){
                if ($data['amount_net'] != $amountReport) {
                    return returnCustom("Err-code TR-UP: Amount After Tax is not same with total amount of files");
                }
            }

            $amountTransaction = str_replace(',', '', $data['amount']);
            $amountTax = str_replace(',', '', $data['amount_tax']);
            $amountNet = str_replace(',', '', $data['amount_net']);

            if ($data['exist_tax'] == 'no_tax') {
                if (intval($amountTransaction) != $amountReport) {
                    return returnCustom("Err-code TR-UP: Amount Total is not same with total amount of files");
                }
            }else if ($data['exist_tax'] != 'no_tax') {
                $amountTransaction -= $amountTax;
                $resultAfterTax = $amountTransaction;
                if ($amountNet != $amountReport) {
                    return returnCustom("Err-code TR-UP: Amount After Tax is not same with total amount of files");
                }else if($amountNet == $amountReport){
                    if ($amountReport != $resultAfterTax) {
                        return returnCustom("Err-code TR-UP: Amount After Tax False (Amount - Tax Amount)");
                    }
                }
            }

            $transaction->title = $data['title'];
            $transaction->inquiry_id = $data['inquiry'];
            $transaction->division_id = $data['division'];
            $transaction->remark = $data['remark'];
            $transaction->amount = $amountTransaction;
            $transaction->ott_code = $ottCode;
            $transaction->ott_name = $ottName;
            $transaction->description = $data['description'];
            $transaction->save();
            
            $transactionTax = TransactionTax::where(['transaction_id' => $id])->first();

            if (!$transactionTax) {
                $transactionTax = new TransactionTax();
            }

            if ($data['exist_tax'] != 'no_tax') {
                $transactionTax->transaction_id = $id;
                $transactionTax->type = $data['exist_tax'];
                $transactionTax->amount = $data['amount_tax'];
                $transactionTax->save();
            }else{
                if ($transactionTax) {
                    $transactionTax->delete();
                }
                
            }

            DB::commit();
            return returnCustom($transaction, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code TR-UP: " . $e->getMessage());
        }
    }

    public function approveUser($id) {
        try {
            DB::beginTransaction();

            $transaction = Transaction::with(['division'])
                ->find($id);
            if (!$transaction) {
                return returnCustom("Err-code TR-AU: Data not found");
            }

            if ($transaction->current_status <> '') {
                return returnCustom("Err-code TR-AU: Transaction has been approved before");
            }

            if ($transaction->user_id != Auth::user()->id) {
                return returnCustom("Err-code TR-AU: You don't allow to approved this transaction because this is not belongs you.");
            }

            if (!$transaction->division) {
                return returnCustom("Err-code TR-AU: Transaction don't have division");
            }
        
            $this->updateCurrentStatus($transaction, 'approved_user', Auth::user()->id);

            $data = [
                'recipient_mail' => $transaction->division->director_email,
                'recipient_name' => "Director",
                'cc' => [],
                'bcc' => [],
                'subject' => "New Request Transaction ID".$transaction->id,
                'title' => 'New Request Transaction',
                'message' => 'There is new transaction with ID' . $transaction->id . ' on ' . $transaction->division->title . ' division, please logged in to ' . url('/transaction/' . $transaction->id) . ' to verify the transaction.'

            ];

            $emailService = new EmailService();
            $emailService->curlPost($data);

            DB::commit();
            return returnCustom('Transaction updated successfully', true);
        } catch (\Exception $e) {
            return returnCustom("Err-code TR-AU: " . $e->getMessage());
        }
    }

    public function approveOrRejectDirector($id, $status, $message= '') {
        try {
            $transaction = Transaction::with(['division.division_user_all_finance.user', 'created_by'])
                ->find($id);
            
            if(!$message){
                $message = '';
            }
    
            if (!$transaction) {
                return returnCustom("Err-code TR-AD: Data not found");
            }

            if ($transaction->current_status != "approved_user") {
                return returnCustom("Err-code TR-AD: This transaction has been approved at the next level of approval");
            }

            if (!$transaction->division) {
                return returnCustom("Err-code TR-AD: Division data not found");
            }

            if (!$transaction->created_by) {
                return returnCustom("Err-code TR-AD: Users data not found");
            }

            if ($transaction->division->director_email != Auth::user()->email) {
                return returnCustom("Err-code TR-AD: You don't have access");
            }
          
            $this->updateCurrentStatus($transaction, $status . '_director', Auth::user()->id, $message);

            if ($status == 'approved') {
                if (count($transaction->division->division_user_all_finance) == 0) {
                    return returnCustom("Err-code TR-AD: Division don't have finance to approve this transaction, please contact your director to set finance first");
                }

                foreach ($transaction->division->division_user_all_finance as $finance) {
                    if ($finance->user) {
                        $data = [
                            'recipient_mail' => $finance->user->email,
                            'recipient_name' => $finance->user->name,
                            'cc' => [],
                            'bcc' => [],
                            'subject' => "Approved Director for transaction ID".$transaction->id,
                            'title' => 'Approved Director',
                            'message' => 'There is a transaction with ID' . $transaction->id . ' on ' . $transaction->division->title . ' division, 
                        and has been approved by director
                        please logged in to ' . url('/transaction/' . $transaction->id) . ' to verify the transaction.'

                        ];
                        $emailService = new EmailService();
                        $emailService->curlPost($data);
                    }
                }
            } else {
                $data = [
                    'recipient_mail' => $transaction->created_by->email,
                    'recipient_name' => $transaction->created_by->name,
                    'cc' => [],
                    'bcc' => [],
                    'subject' => "Rejected Transaction by the director",
                    'title' => 'Rejected Transaction',
                    'message' => 'Sorry, your transaction request was rejected by the director. ' . 'There is your transaction with ID' . $transaction->id . ' on ' . $transaction->division->title . ' division, please logged in to ' . url('/transaction/' . $transaction->id).'.'. $message

                ];

                $emailService = new EmailService();
                $emailService->curlPost($data);
            }

            return returnCustom('Transaction updated successfully', true);
        } catch (\Exception $e) {
            return returnCustom("Err-code TR-AD: " . $e->getMessage());
        }
    }

    public function approveOrRejectFinance($id, $status, $message= '') {
        try {
            $transaction = Transaction::with(['division.division_users', 'division.division_user_by_master_finance.user', 'created_by'])
                ->find($id);
            
            if(!$message){
                $message = '';
            }

            if (!$transaction) {
                return returnCustom("Err-code TR-AF: Data not found");
            }

            if ($transaction->current_status != 'approved_director') { 
                return returnCustom("Err-code TR-AF: This transaction has been approved at the next level of approval");
            }

            $userFinance = '';
            foreach ($transaction->division->division_users as $divisionUser) {
                if ($divisionUser->role == 'finance') {
                    if ($divisionUser->user_id == Auth::user()->id) {
                        $userFinance = $divisionUser;
                    }
                }
            }
            if (!$userFinance) {
                return returnCustom("Err-code TR-AF: You don't have access");
            }

            if (!$transaction->division->division_user_by_master_finance) {
                return returnCustom("Err-code TR-AF: Division data not have master finance, please contact your director to add master finance to this division");
            }

            if (!$transaction->division->division_user_by_master_finance) {
                return returnCustom("Err-code TR-AF: Master finance user data not found, please contact your director to change master finance to this division");
            }
         
            $this->updateCurrentStatus($transaction, $status . '_finance', Auth::user()->id, $message );

            if ($status == 'approved') {
                $user = $transaction->division->division_user_by_master_finance->user;
                $data = [
                    'recipient_mail' => $user->email,
                    'recipient_name' => $user->name,
                    'cc' => [],
                    'bcc' => [],
                    'subject' => "Approved Finance by " . Auth::user()->email . " for transaction ID".$transaction->id,
                    'title' => 'Approved Finance by ' . Auth::user()->email,
                    'message' => 'There is a transaction with ID' . $transaction->id . ' on ' . $transaction->division->title . ' division, 
                        and has been approved by Finance
                        please logged in to ' . url('/transaction/' . $transaction->id) . ' to verify the transaction.'

                ];
                $emailService = new EmailService();
                $emailService->curlPost($data);
            } else {
                $data = [
                    'recipient_mail' => $transaction->created_by->email,
                    'recipient_name' => $transaction->created_by->name,
                    'cc' => [],
                    'bcc' => [],
                    'subject' => "Rejected Transaction by finance",
                    'title' => 'Rejected Transaction',
                    'message' => 'Sorry, your transaction request was rejected by finance. ' . 'There is your transaction with ID' . $transaction->id . ' on ' . $transaction->division->title . ' division, please logged in to ' . url('/transaction/' . $transaction->id).'.'. $message

                ];

                $emailService = new EmailService();
                $emailService->curlPost($data);
            }

            return returnCustom('Transaction updated successfully', true);
        } catch (\Exception $e) {
            return returnCustom("Err-code TR-AF: " . $e->getMessage());
        }
    }

    public function pushPayment($id, $status = "transferred", $message= '') {
        try {
            DB::beginTransaction();

            $transaction = Transaction::with(['inquiry', 'division.division_flip', 'division.division_users'])
                ->find($id);

            if(!$message){
                $message = '';
            }

            if (!$transaction) {
                return returnCustom("Err-code TR-PP: Data is not found");
            }

            if(!$transaction->aprove_finance_is_master_finance) {
                return returnCustom('Err-code TR-PP: This transaction has been approved at the next level of approval or you are not a master finance ');
            }

            if (!$transaction->division) {
                return returnCustom("Err-code TR-PP: Division data of transaction not found");
            }

            switch ($status) {
                case 'rejected':
                    $this->updateCurrentStatus($transaction, $status . '_master_finance', Auth::user()->id, $message);
    
                    $data = [
                        'recipient_mail' => $transaction->created_by->email,
                        'recipient_name' => $transaction->created_by->name,
                        'cc' => [],
                        'bcc' => [],
                        'subject' => "Rejected Transaction by Master Finance",
                        'title' => 'Rejected Transaction',
                        'message' => 'Sorry, your transaction request was rejected by Master Finance. '. 'There is your transaction with ID' . $transaction->id . ' on ' . $transaction->division->title . ' division, please logged in to ' . url('/transaction/' . $transaction->id).'.'. $message
        
                    ];
        
                    $emailService = new EmailService();
                    $emailService->curlPost($data);

                    break;
                case 'transferred':
                    $this->updateCurrentStatus($transaction, $status, Auth::user()->id);
                    break;
            }
            
            if($status == "transferred") {
                $divisionJournal = DivisionJournal::with([])->where('division_id', $transaction->division_id)->first();
                if ($divisionJournal) {
                    $financeNoted = TransactionFinanceNoted::with([])->where('transaction_id', $transaction->id)->first();
                    if(!$financeNoted) {
                        return returnCustom("Err-code TR-PP: finance noted is not found!, you have to add finance note first");
                    }
                }

                if ($transaction->division->type_disbursement == "flip") {
                    $divisionFlip = $transaction->division->division_flip;
                    $flipService = new FlipService($divisionFlip->flip_key, $divisionFlip->flip_token);
                    $response = $flipService->createDisbursement($transaction);
                    if (!$response['status']) {
                        return $response;
                    }
        
                    $transactionFlip = new TransactionFlip();
                    $transactionFlip->createNewOrUpdate($transaction->id, $response['message']->id, $response['message']->status, $response['message']->fee,
                        $response['message']->receipt, json_encode($response['message']));
                    $transaction->save();
        
                    if ($response['message']->status == 'DONE') {
                        $this->updateCurrentStatus($transaction,'DONE', Auth::user()->id);
                    }
                } else if($transaction->division->type_disbursement == "xendit") {
                    $divisionXendit = $transaction->division->division_xendit;
                    $xenditService = new XenditService('anggaran', $divisionXendit->xendit_key);
                    $externalId = 'AGR'.$transaction->id.date("YmdHis");
                    $response = $xenditService->createDisbursement($externalId, $transaction->amount, $transaction->inquiry->bank_code, $transaction->inquiry->name_by_server, $transaction->inquiry->account_number);
                    if (!$response['status']) {
                        return $response;
                    }
                    $transactionXendit = new TransactionXendit();
                    $xenditRes = $response['message'];
                    $transactionXendit->createNewOrUpdate($transaction->id, $xenditRes['id'], $xenditRes['status'], 0, json_encode($response['message']));
                    $transaction->save();

                    if($xenditRes['status'] == 'COMPLETED') {
                        $this->updateCurrentStatus($transaction, 'DONE', Auth::user()->id);
                    }
                }
            }

            DB::commit();
            return returnCustom('Transaction updated successfully', true);
        } catch (\Exception $e) {
            return returnCustom("Err-code TR-PP: " . $e->getMessage());
        }
    }

    public function getByFilter($filters, $divisions, $site = '' ,$paginate = 20) {
        $transactions = Transaction::with(['division', 'transaction_status_transferred', 'created_by', 'transaction_tax'])
            ->whereHas('division');

        if (!empty($filters['id'])) {
            $idFil = str_ireplace("id","", $filters['id']);
            $transactions = $transactions->where('id', 'like', '%' . $idFil . '%');
        }

        if (!empty($filters['title'])) {
            $transactions = $transactions->where('title', 'like', '%' . $filters['title'] . '%');
        }

        if (!empty($filters['remark'])) {
            $transactions = $transactions->where('remark', 'like', '%' . $filters['remark'] . '%');
        }

        if(!empty($filters['created_by'])) {
            $transactions = $transactions->whereHas('created_by', function($q) use($filters){
                $q->where('name', 'like', '%'  . $filters['created_by'] . '%');
            });
        }

        if (!empty($filters['start_transferred_date']) && !empty($filters['end_transferred_date'])) {
            $startTransferredAt = date('Y-m-d H:i:s', strtotime($filters['start_transferred_date']));
            $endTransferredAt = date('Y-m-d H:i:s', strtotime($filters['end_transferred_date']));
            $limit_date = date('Y-m-d H:i:s', strtotime($startTransferredAt . ' +30 days'));

            $diff = round(abs(strtotime($startTransferredAt) - strtotime($endTransferredAt))/86400);
            $params = [
                'start' => $startTransferredAt,
                'end' => $endTransferredAt
            ];
            if($diff > 30) {
                $params['end'] = $limit_date;
            }

            $transactionStatuses = TransactionStatus::with([])
                ->where('title', 'transferred')
                ->whereBetween('created_at', [$params['start'], $params['end']])
                ->select('transaction_id')
                ->get();

            $transactions = $transactions->whereIn('id', array_column($transactionStatuses->toArray(), 'transaction_id'));

            /*$typeFilter = 'transaction_status_transferred';
            if(!empty($filters['filter_for'])) {
                $transactions = $transactions->whereBetween('created_at', [$params['start'], $params['end']]);
            } else {
                $transactions = $transactions->whereHas($typeFilter, function($q) use($params){
                    $q->whereBetween('created_at', [$params['start'], $params['end']]);
                });
            }  */
        }

        if (!empty($filters['is_push'])) {
            if ($filters['is_push'] == 'push') {
                $transactions = $transactions->whereHas('transaction_status_transferred');
            }
            if ($filters['is_push'] == 'not') {
                $transactions = $transactions->doesntHave('transaction_status_transferred');
            }
        }
//        if(!empty($filters['start']) && !empty($filters['end'])) {
//            $startDate = date('Y-m-d H:i:s', strtotime($filters['start']));
//            $endDate = date('Y-m-d H:i:s', strtotime($filters['end'] . '23:59:59'));
//            $params = [
//                'start' => $startDate,
//                'end'   => $endDate
//            ];
//            $transactions = $transactions->whereHas('transaction_status_transferred', function($q) use($params){
//                $q->whereBetween('created_at', [$params['start'], $params['end']]);
//            });
//        }
    
        if (empty($site)) {
            if (!empty($filters['division'])) {
                $transactions = $transactions->where('division_id', $filters['division']);
            } else {
                $transactions = $transactions->whereIn('division_id', $divisions);

                // Check if level is child, shows all transactions made
                if (Auth::user()->level == 'child') {
                    $transactions = $transactions->where('user_id', Auth::user()->id);
                }
            }
        }

        if (!empty($filters['status'])) {
            $status = $filters['status'];
            if ($filters['status'] == 'new') {
                $status = '';
            }
            $transactions = $transactions->where('current_status', $status);
        }

        if(!empty($filters['countTransaction'])) {
            $data = [
                'countData' => $transactions->count(),
                'sumAmount' => $transactions->sum('amount')
            ];

            return returnCustom($data,  true);
        }

        $transactions = $transactions->orderBy('id', 'desc');

        if($paginate > 0){
            return $transactions->paginate($paginate);
        }else{
            return $transactions->get();
        }

    }

    public function updateTransactionFromServer($dump, $token, $idBigFlip) {
        $data = TransactionFlip::with(['transaction.transaction_flip', 'transaction.division.division_flip', 'transaction.division.division_journal',
            'transaction.transaction_finance_noted', 'transaction.transaction_tax'])
            ->where('server_id', $dump->id)
            ->first();
        if (!$data) {
            return returnCustom("Err-code TR-UTFS: Data not found", false, true);
        }

        if (!$data->transaction) {
            return returnCustom("Err-code TR-UTFS: Data transaction not found", false, true);
        }

        if (!$data->transaction->transaction_flip) {
            return returnCustom("Err-code TR-UTFS: Flip data not found for this transaction", false, true);
        }

        if (!$data->transaction->division) {
            return returnCustom("Err-code TR-UTFS: Flip data not found for this transaction", false, true);
        }

        if ($data->transaction->division->division_flip->id_big_flip != $idBigFlip) {
            return returnCustom("Err-code TR-UTFS: ID Big Flip mismatch", false, true);
        }

        if ($data->transaction->division->division_flip->flip_token != $token) {
            return returnCustom("Err-code TR-UTFS: Token mismatch", false, true);
        }

        if ($data->server_status == 'DONE') {
            return returnCustom("Err-code TR-UTFS: The transaction has been already success before", false, true);
        }

        $imageBase64 = $this->saveImage($dump->receipt);
        $name = "receipt-flip-" . date("Y-m-d-H-i-s") . '-AC' . $data->id . '.png';
        $upload  = new S3Service();

        $resultImage['name'] = $dump->receipt;
        if ($imageBase64) {
            $resultImage = $upload->putObject($imageBase64, $name, "image/png");
        }
        $data->server_receipt   = ($resultImage == true) ? $resultImage['name'] : '';
        $data->response_dump    = json_encode($dump);
        if (isset($dump->fee)) {
            $data->fee = $dump->fee;
        }
        $data->server_status    = $dump->status;
        $data->save();

        if ($data->server_status == 'DONE') {
            $this->updateCurrentStatus($data->transaction, 'DONE');
        }
        Log::error('Callback flip dump: ' . json_encode($dump));
        if (env('APP_ENV') == 'production'){
            if (isset($data->transaction->division->division_journal) && !is_null($data->transaction->division->division_journal)){
                $jurnalRepo     = new JurnalRepositories();
                $autopostJurnal = $jurnalRepo->createJournalEntries($data->transaction);
                if (!$autopostJurnal['status']){
                    Log::error('Err-code TR-UTFS: Autoposting Jurnal Failed ' . json_encode($autopostJurnal['data']));
                }
            }
        }
        return [
            'status'    => (bool) ($data->current_status == 'DONE'),
            'message'   => ($data->current_status == 'DONE') ? $data : "Transaksi pindah status menjadi " . $data->current_status
        ];
    }

    public function updateTransactionFromServerXendit($dump)
    {
        $data = TransactionXendit::with(['transaction.transaction_xendit', 'transaction.division.division_xendit', 'transaction.division.division_journal',
            'transaction.transaction_finance_noted', 'transaction.transaction_tax'])
        ->where('server_id', $dump['id'])
        ->first();

        if (!$data) {
            return returnCustom("Err-code TR-UTFSX: Data not found");
        }

        if (!$data->transaction) {
            return returnCustom("Err-code TR-UTFSX: Data transaction not found");
        }

        if (!$data->transaction->transaction_xendit) {
            return returnCustom("Err-code TR-UTFSX: Xendit data not found for this transaction");
        }

        if (!$data->transaction->division) {
            return returnCustom("Err-code TR-UTFSX: Deivision Xendit data not found for this transaction");
        }

        $data->response_dump = json_encode($dump);
        $data->server_status = ($dump['status'] == 'COMPLETED' ? 'DONE' : 'FAILED');
        $data->save();
    
        switch ($data->server_status) {
            case 'DONE':
                $this->updateCurrentStatus($data->transaction, 'DONE');
                break;
            case 'FAILED':
                $this->updateCurrentStatus($data->transaction, 'FAILED');
                break;
        }

        Log::error('Callback Xendit dump: ' . json_encode($dump));
        if (env('APP_ENV') == 'production'){
            if (isset($data->transaction->division->division_journal) && !is_null($data->transaction->division->division_journal)){
                $jurnalRepo     = new JurnalRepositories();
                $autopostJurnal = $jurnalRepo->createJournalEntries($data->transaction);
                if (!$autopostJurnal['status']){
                    Log::error('Err-code TR-UTFS: Autoposting Jurnal Failed ' . json_encode($autopostJurnal['data']));
                }
            }
        }
        return [
            'status'    => (bool) ($data->current_status == 'DONE'),
            'message'   => ($data->current_status == 'DONE') ? $data : "Transaksi pindah status menjadi " . $data->current_status
        ];
    }

    public function saveImage($url = '')
    {
        $image = @file_get_contents($url);
        if ($image !== false) {
            return 'data:image/png;base64,' . base64_encode($image);
        }

        return false;
    }

    public function updateCurrentStatus($transaction, $currentStatus, $userId = 0, $message = '') {
        $transactionStatus = new TransactionStatus();
        $transactionStatus->createNewOrUpdate($transaction->id, $currentStatus, $userId, $message);

        $transaction->current_status = $currentStatus;
        if ($currentStatus == 'transferred' || $currentStatus == 'DONE') {
            $transaction->type_disbursement = $transaction->division->type_disbursement; 
        }
        $transaction->save();
    }

    public function changeChartOfAccounts($data) {
        try {
            $transaction = Transaction::find($data['transaction_id']);
            if (!$transaction) {
                return returnCustom("Err-code TR-CCOA:  Data not found");
            }

            $ottCode = 0;
            $ottName = '';
            if (isset($data['coa'])) {
                $journal = explode('^^', $data['coa']);
                $ottCode = $journal[0];
                $ottName = $journal[1];
            }
            if (!$ottCode OR !$ottName) {
                return returnCustom("Err-code TR-CCOA:  COA input is not valid");
            }

            $transaction->ott_code = $ottCode;
            $transaction->ott_name = $ottName;
            $transaction->save();

            return returnCustom('Data updated successfully', true);
        } catch (\Exception $e) {
            return returnCustom("Err-code TR-CCOA:  " . $e->getMessage());
        }
    }

    public function exportExcel($data , $fileName = ''){
 
        return \Maatwebsite\Excel\Facades\Excel::create($fileName, function($excel) use ($data) {{
            $excel->sheet('mySheet', function($sheet) use ($data) {
                $sheet->fromArray($data);
            });
        }})->download('xlsx');
        die();

        return returnCustom('Download Successfully', true);
    }

    public function getTransactionStatusesTitleOrCreatedAtByTittle($dataTransactionStatuses , $title , $date = false){
        try {
            foreach($dataTransactionStatuses as $data){
                if($data->title == $title){
                    if($date){
                        return $data->created_at;
                    }
                    
                    return $data->user->name;
                }
            }

            return '';
        }catch (\Exception $e){
            return null;
        }
    }

    public function financeNoteStoreOrEdit($data){
        try {
            if(!$data['note']) {
                return returnCustom("Err-code TR-FNS: There in no noted given for this transaction, make sure you have filled in the finance noted ");
            }
            
            if(!is_null($data['finance_note_id'])){
                $financeNote = TransactionFinanceNoted::find($data['finance_note_id']);
                $financeNote->delete();
            }
            
            $financeNote = new TransactionFinanceNoted();
            $financeNote->transaction_id = $data['transaction_id'];
            $financeNote->noted = $data['note'];
            $financeNote->created_by = Auth::user()->name;
            $financeNote->save();

            if(!is_null($data['finance_note_id'])){
                return returnCustom('Update note finance successfully', true);
            }else{
                return returnCustom('Create note finance successfully', true);
            }

        } catch (\Exception $e) {
            return returnCustom("Err-code TR-FNS:  " . $e->getMessage());
        }
    }

    public function deleteTransaction($id)
    {
        try {
            if($id == null) {
                return returnCustom("Err-code TR-DT : There is no id used to delete transaction");
            }
            $transaction =  Transaction::with([])->find($id);
            if(!$transaction) {
                return returnCustom("Err-code TR-DT : There is no same id to use to delete transactions");
            }

            if(!$transaction->enable_delete){
                return returnCustom("Err-code TR-DT : This transaction cannot be deleted due to several conditions");
            }
    
            $transaction->delete();
            return returnCustom('Data deleted successfully', true);
        } catch (\Exception $e) {
            return returnCustom("Err-code TR-DT : ". $e->getMessage());
        }
    }
}
