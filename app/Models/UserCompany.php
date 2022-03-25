<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserCompany extends Model
{
    use SoftDeletes;
    protected $table = 'user_companies';
    public $timestamps  =  true;

    public function users() {
        return $this->hasMany('App\Models\User', 'user_company_id', 'id');
    }

    public function user_member_orders() {
        return $this->hasMany('App\Models\UserMemberOrder', 'user_company_id', 'id');
    }

    public function user_member_order_active() {
        return $this->hasOne('App\Models\UserMemberOrder', 'user_company_id', 'id')->where('is_active', 1);
    }
}
