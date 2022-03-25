<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TransactionXendit extends Model
{
    use SoftDeletes;
    protected $table = 'transaction_xendits';
    public $timestamps  =  true;

    public function createNewOrUpdate($transactionId, $serverId, $serverStatus, $fee, $responseDump) {
        $this->transaction_id = $transactionId;
        $this->server_id = $serverId;
        $this->server_status = $serverStatus;
        $this->fee = $fee;
        $this->response_dump = $responseDump;
        $this->save();
    }

    public function transaction() {
        return $this->hasOne('App\Models\Transaction', 'id', 'transaction_id');
    }
}
