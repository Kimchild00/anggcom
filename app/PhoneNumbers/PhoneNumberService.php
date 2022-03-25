<?php

namespace App\PhoneNumbers;


class PhoneNumberService
{
    public function standardPhone($phone, $countryCode = 'ID'){
        $phoneUtil  = \libphonenumber\PhoneNumberUtil::getInstance();
        try{
            $parsePhone = $phoneUtil->parse($phone, strtoupper($countryCode));
        } catch (\libphonenumber\NumberParseException $e) {
            return false;
        }

        $isValid    = $phoneUtil->isValidNumber($parsePhone);
        if(!$isValid){
            return false;
        }

        try {
            $correctPhone   = $phoneUtil->format($parsePhone, \libphonenumber\PhoneNumberFormat::E164);
        } catch (\libphonenumber\NumberParseException $e) {
            $correctPhone   = false;
        }
        
        return $correctPhone;
    }

    public function validatePhone($phone = '')
    {
        $returnStandPhone   = $this->standardPhone($phone);
        if (!$returnStandPhone) {
            return returnCustom("Err-code PNS-VP: invalid phone number");
        }
        return returnCustom($returnStandPhone, true);
    }
}
