<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserShortCart extends Model
{
    protected $table = 'user_short_carts';
    public $timestamps  =  true;

    public function user_member_order() {
        return $this->hasOne('App\Models\UserMemberOrder', 'id', 'user_member_order_id');
    }
}
