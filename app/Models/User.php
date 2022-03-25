<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable
{
    use SoftDeletes;
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function user_company() {
        return $this->hasOne('App\Models\UserCompany', 'id', 'user_company_id');
    }

    public function user_otp() {
        return $this->hasOne('App\Models\UserOtp', 'id', 'users_id');
    }

    public function forgot_tokens(){
        return $this->hasOne('App\Models\UserForgotPassword', 'user_id');
    }

    public function createOrUpdate($companyId, $level, $name, $email, $password, $phone, $is_otp = 'Off') {
        $this->user_company_id = $companyId;
        $this->level = $level;
        $this->name = $name;
        $this->email = $email;
        $this->password = $password;
        $this->phone = $phone;
        $this->is_otp = $is_otp;
        $this->save();
        return $this;
    }
}
