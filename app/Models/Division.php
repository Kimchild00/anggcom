<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Division extends Model
{
    use SoftDeletes;
    protected $table = 'divisions';
    public $timestamps  =  true;

    public function createOrUpdate($companyId, $title, $dirEmail, $dirPhone, $typeDisbursement) {
        $this->user_company_id = $companyId;
        $this->title = $title;
        $this->director_email = $dirEmail;
        $this->director_phone = $dirPhone;
        $this->type_disbursement = $typeDisbursement;
        $this->save();
        return $this;
    }

    public function division_users() {
        return $this->hasMany('App\Models\DivisionUser', 'division_id', 'id');
    }

    public function division_flip() {
        return $this->hasOne('App\Models\DivisionFlip', 'division_id', 'id');
    }

    public function division_xendit() {
        return $this->hasOne('App\Models\DivisionXendit', 'division_id', 'id');
    }

    public function division_journal() {
        return $this->hasOne('App\Models\DivisionJournal', 'division_id', 'id');
    }

    public function division_user_by_me() {
        return $this->hasOne('App\Models\DivisionUser', 'division_id', 'id')
            ->where('user_id', Auth::user()->id);
    }

    public function division_user_all_finance() {
        return $this->hasMany('App\Models\DivisionUser', 'division_id', 'id')
            ->where('role', 'finance');
    }

    public function division_user_by_master_finance() {
        return $this->hasOne('App\Models\DivisionUser', 'division_id', 'id')
            ->where('role', 'master_finance');
    }
}
