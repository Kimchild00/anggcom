<?php

namespace App\Xendit;

use App\Repositories\TransactionRepository;
use XenditClient\XenditPHPClient;
use Illuminate\Support\Facades\Log;

class XenditService
{
    private $xenditPHPClient;

    public function __construct($site = '', $secretKey = '')
    {
        if ($site == 'anggaran') {
            $key['secret_api_key'] = $secretKey;
        } elseif($site == 'expo') {
            $key['secret_api_key'] = env('XENDIT_SECRET_API_KEY_EXPO');
        } elseif($site == 'cv berlian') {
            $key['secret_api_key'] = env('XENDIT_SECRET_API_KEY_CV_BERLIAN');
        } else {
            $key['secret_api_key'] = env('XENDIT_SECRET_API_KEY_ESD');
        }
        $this->xenditPHPClient = new XenditPHPClient($key);
    }

    public function createDisbursement($external_id, $amount, $bank_code, $account_holder_name, $account_number)
    {
        try {
            $response = $this->xenditPHPClient->createDisbursement($external_id, $amount, $bank_code, $account_holder_name, $account_number);
            Log::error('Xendit Response : '. json_encode($response));
            if(array_key_exists("error_code", $response)) {
                Log::error("Response Xendit: " . json_encode($response));
                return returnCustom("Err-code XS-CD : " . $response['error_code'] . " " . $response['message']);
            }   
            return returnCustom($response, true);
        } catch (\Exception $e) {
            return returnCustom("Err-code XS-CD: " . $e->getMessage());
        }
    }

    public function callBackDisbursement($dump)
    {
        $transactionRepository = new TransactionRepository();
        $result = $transactionRepository->updateTransactionFromServerXendit($dump);
        return $result;
    }

    function createDisbursementRefund($external_id, $amount, $bank_code, $account_holder_name, $account_number, $email, $disbursement_options = null) {
        $curl = curl_init();

        $headers = array();
        $headers[] = 'Content-Type: application/json';

        if (!empty($disbursement_options['X-IDEMPOTENCY-KEY'])) {
            array_push($headers, 'X-IDEMPOTENCY-KEY: '.$disbursement_options['X-IDEMPOTENCY-KEY']);
        }

        $end_point = 'https://api.xendit.co/disbursements';

        $data['external_id'] = $external_id;
        $data['amount'] = (int)$amount;
        $data['bank_code'] = $bank_code;
        $data['account_holder_name'] = $account_holder_name;
        $data['account_number'] = $account_number;
        $data['email_to'] = ['imam@importir.com', $email, 'finance@forwarder.id'];

        if ( is_array($disbursement_options) ) {
            foreach ( $disbursement_options as $key => $value ) {
                $data[$key] = $value;
            }
        }

        $payload = json_encode($data);

        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($curl, CURLOPT_USERPWD, env('XENDIT_SECRET_API_KEY_CV_BERLIAN').":");
        curl_setopt($curl, CURLOPT_URL, $end_point);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $response = curl_exec($curl);
        curl_close($curl);

        $responseObject = json_decode($response, true);
        Log::error('ANGGARAN: response create disbursement refund: ' . json_encode($responseObject));
        return returnCustom($responseObject, true);
    }
}