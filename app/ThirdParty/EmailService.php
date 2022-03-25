<?php

namespace App\ThirdParty;

class EmailService
{
    public function curlPost($data)
    {
        try {
            if (env('APP_ENV') != 'production') {
                $data['title'] = '(Testing) ' . $data['title'];
            }

            $data['platform_name'] = 'anggaran.com';
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => 'https://service.importir.com/email',
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
            ]);
            $response = curl_exec($curl);

            curl_close($curl);
            return json_decode($response, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code ES-CP: " . $e->getMessage());
        }
    }
}