<?php

namespace App\ThirdParty;

class SmsService {
    public function curlPost() {
        $curl = curl_init();

        curl_setopt_array($curl, [
            CURLOPT_URL => 'https://third-party.importir.com/send-short-message',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS =>'',
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
            ),
        ]);

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;
    }
}