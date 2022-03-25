<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class TransactionFinanceNoted extends Model
{
    use SoftDeletes;

    protected $table = 'transaction_finance_noteds';
    public $timestamps  =  true;

    public function transaction()
    {
        return $this->belongsTo('App\Models\Transaction', 'transaction_id');
    }
}
