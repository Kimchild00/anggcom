<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DivisionJournal extends Model
{
    use SoftDeletes;
    protected $table = 'division_journals';
    public $timestamps  =  true;

    public function createOrUpdate($divisionId, $journalName, $journalKey) {
        $this->division_id = $divisionId;
        $this->journal_name = $journalName;
        $this->journal_key = $journalKey;
        $this->save();
        return $this;
    }
}
