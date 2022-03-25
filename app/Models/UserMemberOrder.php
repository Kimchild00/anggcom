<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserMemberOrder extends Model
{
    use SoftDeletes;

    protected $table = 'user_member_orders';
    public $timestamps  =  true;

    public function user_company() {
        return $this->hasOne('App\Models\UserCompany', 'id', 'user_company_id');
    }

    public function user_master() {
        return $this->hasOne('App\Models\User', 'id', 'user_company_id')
            ->where('level', 'master');
    }
}
