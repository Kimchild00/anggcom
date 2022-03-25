<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Inquiry extends Model
{
    use SoftDeletes;
    protected $table = 'inquiry';
    public $timestamps  =  true;

    public function createOrUpdate($userCompId, $nameByInput, $nameByServer, $status, $accountNumber, $bankCode, $bankCityId, $bankCityText) {
        $this->user_company_id = $userCompId;
        $this->name_by_input = $nameByInput;
        $this->name_by_server = $nameByServer;
        $this->status = $status;
        $this->account_number = $accountNumber;
        $this->bank_code = $bankCode;
        $this->bank_city_id = $bankCityId;
        $this->bank_city_text = $bankCityText;
        $this->save();
    }

    public function getStatusLabelAttribute() {
        if ($this->status == 'INVALID_ACCOUNT_NUMBER') {
            return "<span class=\"label label-danger\">" . $this->status . "</span>";
        }
        if ($this->status == 'SUCCESS') {
            return "<span class=\"label label-primary\">" . $this->status . "</span>";
        }
        return "<span class=\"label label-info\">" . $this->status . "t</span>";
    }
}
