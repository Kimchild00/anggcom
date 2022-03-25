<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use App\Models\DivisionJournal;

class Transaction extends Model
{
    use SoftDeletes;
    protected $table = 'transactions';
    public $timestamps  =  true;

    public function createOrUpdate($userId, $inquiryId, $divisionId, $ottCode, $ottName, $title, $description, $amount, $remark) {
        $this->user_id = $userId;
        $this->inquiry_id = $inquiryId;
        $this->division_id = $divisionId;
        $this->ott_code = $ottCode;
        $this->ott_name = $ottName;
        $this->title = $title;
        $this->description = $description;
        $this->amount = $amount;
        $this->remark = $remark;
        $this->save();
    }

    public function division() {
        return $this->hasOne('App\Models\Division', 'id', 'division_id');
    }

    public function inquiry() {
        return $this->hasOne('App\Models\Inquiry', 'id', 'inquiry_id');
    }

    public function transaction_statuses() {
        return $this->hasMany('App\Models\TransactionStatus', 'transaction_id', 'id');
    }

    public function getStatusLabelAttribute() {
        switch ($this->current_status) {
            case "":
                return "<span class='text-orange-700'>Not Approved Creator Yet</span>";
                break;
            case "approved_user":
                return "<span class='text-green-900'>Approved User</span>";
                break;
            case "approved_director":
                return "<span class='text-blue-700'>Approved Director</span>";
                break;
            case "rejected_director":
                return "<span class='text-red-500'>Rejected Director</span>";
                break;
            case "approved_finance":
                return "<span class='text-cyan-700'>Approved Finance</span>";
                break;
            case "rejected_finance":
                return "<span class='text-red-500'>Rejected Finance</span>";
                break;
            case "rejected_master_finance":
                return "<span class='text-red-500'>Rejected Master Finance</span>";
                break;
            case "transferred":
                return "<span class='text-info'>Transferred</span>";
                break;
            case "DONE":
                return "<span class='text-yellow-700'>DONE</span>";
                break;
            case "FAILED":
                return "<span class='text-red-700'>FAILED</span";
                break;
        }
    }

    public function transaction_files() {
        return $this->hasMany('App\Models\TransactionFile', 'transaction_id', 'id');
    }

    public function transaction_flip() {
        return $this->hasOne('App\Models\TransactionFlip', 'transaction_id', 'id');
    }

    public function transaction_xendit() {
        return $this->hasOne('App\Models\TransactionXendit', 'transaction_id', 'id');
    }

    public function transaction_tax() {
        return $this->hasOne('App\Models\TransactionTax', 'transaction_id', 'id');
    }

    public function transaction_status_transferred() {
        return $this->hasOne('App\Models\TransactionStatus', 'transaction_id', 'id')
            ->where('title', 'transferred');
    }

    public function created_by() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function getShowBalanceAttribute() {
        $role = 'operator';
        if ($this->division) {
            foreach ($this->division->division_users as $divisionUser) {
                if (Auth::user()->id == $divisionUser->user_id) {
                    if ($divisionUser->role == 'admin') {
                        if ($this->division->director_email == Auth::user()->email) {
                            $role = $divisionUser->role;
                            break;
                        }
                    } else {
                        $role = $divisionUser->role;
                        break;
                    }
                }
            }
        }
        if ($role == 'operator') {
            return false;
        }
        return true;
    }

    public function transaction_finance_noted() {
        return $this->hasOne('App\Models\TransactionFinanceNoted', 'transaction_id','id');
    }

    public function getCheckStatusFinanceNoteAttribute() {
        $transactionStatus = $this->transaction_statuses;

        if($transactionStatus->contains('title','approved_user') && $transactionStatus->contains('title','approved_director')) {
            return true;
        }
        
        return false;
    }

    public function getCheckButtonPushAttribute()
    {
        $isMasterFinance = false;
        if ($this->transaction_flip) {
            if($this->transaction_flip->server_status == "DONE" &&  $this->current_status == "DONE" ) {
                foreach ($this->division->division_users as $user) {
                    if ($user->role == 'master_finance' || $user->role == 'finance') {
                        if ($user->user_id == Auth::user()->id) {
                            $isMasterFinance = true;
                        }
                    }
                }
            }
        } 
        return $isMasterFinance;
    }

    public function getEnableDeleteAttribute()
    {
        if(empty($this->current_status) || $this->current_status == 'approved_user') {
            if($this->user_id == Auth::user()->id) {
                return true;
            }
        }
        return false;
    }

    public function getAproveFinanceIsMasterFinanceAttribute()
    {
        $isMasterFinance = false;
            if($this->current_status == "approved_finance" ) {
                foreach ($this->division->division_users as $user) {
                    if ($user->role == 'master_finance') {
                        if ($user->user_id == Auth::user()->id) {
                            $isMasterFinance = true;
                        }
                    }
                }
            }
        return $isMasterFinance;
    }

    public function getFinanceNotedHaveJournalAttribute()
    {
        $result = true;
        $divisionJournal = DivisionJournal::with([])->where('division_id', $this->division_id)->first();
        if(is_null($this->transaction_finance_noted)) {
            $result = (is_null($divisionJournal)) ? true : false;
        }
        return $result;
    }
}
