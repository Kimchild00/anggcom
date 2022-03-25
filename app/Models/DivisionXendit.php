<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DivisionXendit extends Model
{
    use SoftDeletes;
    protected $table = 'division_xendits';
    public $timestamps  =  true;

    public function createOrUpdate($divisionId, $xenditName, $xenditKey) {
        $this->division_id = $divisionId;
        $this->xendit_name = $xenditName;
        $this->xendit_key = $xenditKey;
        $this->save();
        return $this;
    }
}
