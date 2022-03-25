<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionFile extends Model
{
    use SoftDeletes;
    protected $table = 'transaction_files';
    protected $guarded = [];
    public $timestamps  =  true;

    public function createNewOrUpdate($transactionId, $fileName, $amount, $note) {
        $this->transaction_id = $transactionId;
        $this->file_name = $fileName;
        $this->amount = $amount;
        $this->note = $note ? $note : '';
        $this->save();
    }

    public function getCdnFilePathAttribute() {
        return env('CDN_URL') . 'files/' . $this->file_name;
    }
}
