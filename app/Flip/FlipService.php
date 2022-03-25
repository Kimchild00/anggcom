<?php

namespace App\Flip;

use App\Models\Division;
use App\Repositories\TransactionRepository;
use Illuminate\Support\Facades\Log;

class FlipService {
    protected $flipKey, $flipEndpoint, $flipToken, $operational;

    public function __construct($key = '', $token = '') {
        $this->flipKey = empty($key) ? env('FLIP_KEY', null) : $key;
        $this->flipToken = empty($token) ? env('FLIP_TOKEN', null) : $token;
        $this->flipEndpoint = env('FLIP_ENDPOINT', 'https://sandbox.flip.id/api/v2/');
    }

    public function createDisbursement($data)
    {
        try {
            if (!$data) {
                return returnCustom("Transaction data is not found");
            }

            if ($data->inquiry->name_by_server == '') {
                return returnCustom("Account number is not valid, please check it again");
            }

            $payloads = [
                "account_number" => $data->inquiry->account_number,
                "bank_code" => $data->inquiry->bank_code,
                "amount" => (int) $data->amount,
                "remark" => $data->remark,
                "recipient_city" => $data->inquiry->bank_city_id
            ];

            Log::error('Create disbursement: ' . json_encode(['data' => $data, 'payload' => $payloads]));
            $url = 'disbursement';
            $idempotencyKey = 'anggaran-' . $data->id;

            return $this->_callPost($url, $payloads, $idempotencyKey);
        } catch (\Exception $e) {
            return returnCustom("Err-code FS-CD: " . $e->getMessage());
        }
    }

    private function _callPost($eventUrl = 'disbursement', $payloads = [], $idempotencyKey = ''){
        try {
            $ch = curl_init();

            $linkEnv = $this->flipEndpoint;
            $secret_key = $this->flipKey;

            curl_setopt($ch, CURLOPT_URL, $linkEnv . $eventUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_POST, TRUE);
            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payloads));

            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            if ($idempotencyKey) {
                $headers[] = "idempotency-key: " . $idempotencyKey;
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":");

            $response = json_decode(curl_exec($ch));
            curl_close($ch);
            if (!empty($response->name)) {
                Log::error("Response flip: " . json_encode($response));
                if ($response->name == 'Unauthorized') {
                    return returnCustom("Err-code FS-CP: 401 Unauthorized");
                }

                if ($response->name == 'Not Found') {
                    return returnCustom("Err-code FS-CP: 404 Not Found");
                }

                return returnCustom("Err-code FS-CP: no message received");
            }


            // Race Condition
            if (!empty($response->code)) {
                return returnCustom("Err-code FS-CP: " . $response->code . ' - ' . $response->errors[0]->message);
            }

            return returnCustom($response, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code FS-CP: " . $e->getMessage());
        }
    }

    public function callBackDisbursement($token = '', $data = '', $idBigFlip){
        $response = json_decode($data);
        if(empty($response->status)){
            return returnCustom("Err-code FS-CBD: Response is empty", false, true);
        }

        $transactionRepository = new TransactionRepository();
        $result = $transactionRepository->updateTransactionFromServer($response, $token, $idBigFlip);

        return $result;
    }

    public function callPostInq($flipKey, $eventUrl = 'disbursement', $payloads = [], $idempotencyKey = '' /* unique id string 255 */){
        try {
            $ch = curl_init();
            $linkEnv = env('FLIP_ENDPOINT', null);
            $secret_key = $flipKey;

            curl_setopt($ch, CURLOPT_URL, $linkEnv . $eventUrl);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);

            curl_setopt($ch, CURLOPT_POST, TRUE);

            curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($payloads));

            $headers[] = "Content-Type: application/x-www-form-urlencoded";
            if ($idempotencyKey) {
                $headers[] = "idempotency-key: " . $idempotencyKey;
            }

            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

            curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":");
            $response = json_decode(curl_exec($ch));

            if (curl_error($ch)) {
                $error_msg = curl_error($ch);
                Log::error('Error flip ' . $error_msg);
            }
            curl_close($ch);
            if (!empty($response->name)) {
                if ($response->name == 'Unauthorized') {
                    return returnCustom("Err-code FS-CPI: 401 " . $response->message);
                }

                if ($response->name == 'Not Found') {
                    return returnCustom("Err-code FS-CPI: 404 Not Found");
                }

                return returnCustom("Err-code FS-CPI: no message received");
            }


            // Race Condition
            if (!empty($response->code)) {
                return returnCustom("Err-code FS-CPI: " . $response->code . ' - ' . $response->errors[0]->message);
            }

            return returnCustom($response, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code FS-CPI: " . $e->getMessage());
        }
    }

    public function createBill()
    {
        try {

            $payloads = [
                'title' => 'testing',
                'type' => 'SINGLE',
                'amount' => (int) 100000,
                'redirect_url' => 'https://stage.anggaran.com/login',
                'status' => 'ACTIVE',
                'expired_date' => '2021-11-11 12:00',
                'is_address_required' => 0,
                'is_phone_number_required' => 0
            ];

            $url = 'pwf/bill';
            $idempotencyKey = 'anggaran-id';

            return $this->_callPost($url, $payloads, $idempotencyKey);
        } catch (\Exception $e) {
            return returnCustom("Err-code FS-CD: " . $e->getMessage());
        }
    }

    public function getSaldoFlip($flipKey){
        try {
            $ch = curl_init();

            $linkEnv = $this->flipEndpoint;
            $secret_key = $flipKey ; 

            curl_setopt($ch, CURLOPT_URL, $linkEnv . 'general/balance');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
            curl_setopt($ch, CURLOPT_HEADER, FALSE);
            curl_setopt($ch, CURLOPT_USERPWD, $secret_key . ":");
            $response = json_decode(curl_exec($ch));
            curl_close($ch);
            if (!empty($response->name)) {
                Log::error("Response flip: " . json_encode($response));
                if ($response->name == 'Unauthorized') {
                    return returnCustom("Err-code FS-GSF: 401 Unauthorized");
                }

                if ($response->name == 'Not Found') {
                    return returnCustom("Err-code FS-GSF: 404 Not Found");
                }

                return returnCustom("Err-code FS-GSF: no message received");
            }

            // Race Condition
            if (!empty($response->code)) {
                return returnCustom("Err-code FS-GSF: " . $response->code . ' - ' . $response->errors[0]->message);
            }

            return returnCustom(number_format($response->balance), true);
        } catch (\Exception $e) {
            return returnCustom("Err-code FS-GSF: " . $e->getMessage());
        }
    }
}