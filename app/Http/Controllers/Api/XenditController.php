<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Xendit\XenditService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;


class XenditController extends Controller {
    public function disbursementCallback(Request $request) {
        Log::error('Xendit callback ' . json_encode($request->all()));
        $externalId = substr($request->external_id, 0, 3);
        
        $params = [
            "id" => $request->id,
            "created" => $request->created,
            "updated" => $request->updated,
            "external_id" => $request->external_id,
            "user_id" => $request->user_id,
            "amount" => $request->amount,
            "bank_code" => $request->bank_code,
            "account_holder_name" => $request->account_holder_name,
            "disbursement_description" => $request->disbursement_description,
            "status" => $request->status,
            "is_instant" => $request->is_instant
        ];
        
        if($request->status == "FAILED") {
            $params = array_slice($params, 0, 10, true) + array("failure_code" => $request->failure_code) +
            array_slice($params, 10, count($params) - 1, true);
        }

        if($externalId == "AGR") {
            $xenditService = new XenditService();
            $xenditService->callBackDisbursement($request->all());
        } elseif ($externalId == "RYG" || $externalId == "RSP") {
            $url =  (env('APP_ENV') == 'production' ) ? 'importir.com' : 'stage.importir.com';
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://'.$url.'/api/callback-xendit-from-anggran',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS => json_encode($params),
                CURLOPT_HTTPHEADER => array(
                  'Content-Type: application/json',
                  'Cookie: currency=idr; lang=en'
                ),
              ));
              
              $response = curl_exec($curl);
              
              curl_close($curl);
              return $response;
        } else {
            return response()->json(['status' => false, 'message' => 'perfix external id not recognized']);
        }
    }
    public function disbursementCreate(Request $request) {
        $xenditService = new XenditService('cv berlian');
        $response = $xenditService->createDisbursementRefund(
            $request->external_id, $request->amount, $request->bank_code, $request->account_holder_name,
            $request->account_number, $request->email
        );
        return response()->json($response);
    }
}