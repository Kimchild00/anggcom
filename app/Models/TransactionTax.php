<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionTax extends Model
{
    use SoftDeletes;
    protected $table = 'transaction_taxes';
    public $timestamps  =  true;

    public function createNewOrUpdate($transactionId, $type, $amount) {
        $this->transaction_id = $transactionId;
        $this->type = $type;
        $this->amount = $amount;
        $this->save();
    }
}
