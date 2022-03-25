<?php

namespace App\ThirdParty;

class SentryService
{
    public function getError($errorName, $shortDesc, $errorMessage, $data) 
    {
        $platform = (env('APP_ENV') != 'production') ? '' : 'anggaran';

        $params = [
            'error_name' => $errorName,
            'short_description_error' => $shortDesc,
            'error_message' => $errorMessage,
            'data' => $data,
            'platform' => $platform
        ];

        $this->curlPost($params);
    }

    public function curlPost($data)
    {
        try {

            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://service.importir.id/sentry/store-error',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($data),
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                ),
            ));
            $response = curl_exec($curl);

            curl_close($curl);
            return json_decode($response, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code SS-CP: " . $e->getMessage());
        }
    }
}