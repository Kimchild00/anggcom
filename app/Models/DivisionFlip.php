<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DivisionFlip extends Model
{
    use SoftDeletes;
    protected $table = 'division_flips';
    public $timestamps  =  true;

    public function createOrUpdate($divisionId, $flipName, $idBigFlip, $flipKey, $flipToken) {
        $this->division_id = $divisionId;
        $this->flip_name = $flipName;
        $this->id_big_flip = $idBigFlip;
        $this->flip_key = $flipKey;
        $this->flip_token = $flipToken;
        $this->save();
        return $this;
    }
}
