<?php

namespace App\Models;

use Cartalyst\Sentinel\Users\EloquentUser;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;

class AdminUser extends EloquentUser
{
    use Notifiable;
    use SoftDeletes;

    protected $table    = 'admin_users';
    public $timestamps  = true;

    protected $fillable = [
        'email',
        'password',
        'last_name',
        'first_name',
        'phone',
    ];
}
