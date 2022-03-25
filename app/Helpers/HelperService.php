<?php

namespace App\Helpers;

class HelperService {

    public static function dumpComInvoice($dump)
    {
        $result = [
            'flag'              => '',
            'kode_perusahaan'   => '',
            'account_number'    => '',
            'gross_amount'      => '',
            'pdf_url'           => ''
        ];

        $dumpArr    = json_decode($dump, true);
        if (isset($dumpArr['va_numbers'])) {
            $result['flag']             = $dumpArr['va_numbers'][0]['bank'];
            $result['account_number']   = $dumpArr['va_numbers'][0]['va_number'];
            $result['gross_amount']     = $dumpArr['gross_amount'];
            $result['pdf_url']          = $dumpArr['pdf_url'];
        } elseif (isset($dumpArr['permata_va_number'])) {
            $result['flag']             = 'Permata';
            $result['account_number']   = $dumpArr['permata_va_number'];
            $result['gross_amount']     = $dumpArr['gross_amount'];
            $result['pdf_url']          = $dumpArr['pdf_url'];
        }else {
            if(count($dumpArr) > 0){
                $result['flag']             = 'Mandiri';
                $result['account_number']   = isset($dumpArr['bill_key']) ? $dumpArr['bill_key'] : 0;
                $result['gross_amount']     = isset($dumpArr['gross_amount']) ? $dumpArr['gross_amount'] : 0;
                $result['pdf_url']          = isset($dumpArr['pdf_url']) ? $dumpArr['pdf_url'] : 'javascript:;';
                if (!empty($dumpArr['biller_code'])) {
                    $result['kode_perusahaan']  = $dumpArr['biller_code'];
                }
            }
        }

        return $result;
    }
}
