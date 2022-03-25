<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionStatus extends Model
{
    use SoftDeletes;
    protected $table = 'transaction_statuses';
    public $timestamps  =  true;

    public function createNewOrUpdate($transactionId, $title, $userId, $message = '') {
        $this->transaction_id = $transactionId;
        $this->title = $title;
        $this->user_id = $userId;
        $this->message = $message;
        $this->save();
    }

    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }

    public function getStatusLabelAttribute() {
        if ($this->title == 'FAILED') {
            return 'FAILED';
        }
        if ($this->title == 'transferred') {
            return 'Transferred';
        }
        if ($this->title == 'DONE') {
            return 'DONE';
        }
        $explode = explode('_', $this->title);
        return ucwords($explode[0]) . ' ' . ucwords($explode[1]);
    }
}
