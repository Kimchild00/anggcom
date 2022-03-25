<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DivisionUser extends Model
{
    use SoftDeletes;
    protected $table = 'division_users';
    public $timestamps  =  true;

    public function createOrUpdate($divisionId, $userId, $role) {
        $this->division_id = $divisionId;
        $this->user_id = $userId;
        $this->role = $role;
        $this->save();
        return $this;
    }

    public function user() {
        return $this->hasOne('App\Models\User', 'id', 'user_id');
    }
}
