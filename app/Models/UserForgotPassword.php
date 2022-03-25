<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserForgotPassword extends Model
{
    use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'token', 'end_date'
    ];

    protected $table = 'forgot_tokens';
    public $timestamps  =  true;

    public function user(){
        return $this->belongsTo('App\Models\User', 'id', 'user_id');
    }
}
