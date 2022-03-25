<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserOtp extends Model
{
    protected $table    = 'user_otpes';

    protected $fillable = [
        'user_id', 'code_email', 'code_google_auth', 'time_expired',
    ];

    public function users() {
        return $this->belongsTo('App\Models\User', 'users_id', 'id');
    }

    public function createOrUpdateOtp($userId, $codeEmail, $codeGoogleAuth , $timeExpired) {
        $this->users_id = $userId;
        $this->code_email = $codeEmail;
        $this->code_google_auth = $codeGoogleAuth;
        $this->time_expired =$timeExpired;
        $this->save();
        return $this;
    }
}
