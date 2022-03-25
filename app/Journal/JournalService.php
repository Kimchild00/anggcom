<?php

namespace App\Journal;

use GuzzleHttp\Client;

class JournalService {

    protected $jurnalUrl;
    protected $header;
    protected $client;

    public function __construct()
    {
        $this->jurnalUrl = env('JURNAL_ENDPOINT').'/core/api/v1';
        $this->client = new Client();
        $this->header = [
            'headers' => [
                'content-type' => 'application/json',
                'apikey' => env('JURNAL_API_KEY_EDRUS_EDUKASI_UTAMA')
            ]
        ];
    }

    public function change($params)
    {
        switch ($params) {
            case "EEU": //edrusedukasiutama
                return [
                        'content-type' => 'application/json',
                        'apikey' => env('JURNAL_API_KEY_EDRUS_EDUKASI_UTAMA')
                ];
                break;
        }
    }



    public function getProduct()
    {
        $res    = $this->client->request('GET', $this->jurnalUrl.'/products',$this->header);
        $result = $res->getBody()->getContents();
        return json_decode($result);
    }

    public function getCostumer()
    {
        $res    = $this->client->request('GET', $this->jurnalUrl.'/customers', $this->header);
        $result = $res->getBody()->getContents();
        return json_decode($result);
    }


    public function receivePayment($params)
    {
        $data = [
            "receive_payment"=> [
                "transaction_date"=> date("Y-m-d"),
                "records_attributes"=> [
                    [
                        "transaction_no"=> $params->sales_invoice->transaction_no,
                        "amount"=> $params->sales_invoice->subtotal
                    ]
                ],
                "deposit_to_name"=> "BANK",
                "payment_method_name"=>"Transfer Bank",
                "payment_method_id"=>608177,
                "is_draft"=>false
            ]
        ];
        
        try {
            //code...
            $res = $this->client->request('POST', $this->jurnalUrl.'/receive_payments', [
                'headers' => [
                    'content-type' => 'application/json',
                    'apikey' => env('JURNAL_API_KEY')
                ],
                'body' => json_encode($data)
            ]);

            $result = $res->getBody()->getContents();
            return [
                'status' => true,
                'data' => $result
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'data' => $e->getMessage()
            ];
        }
    }


    public function findCustomIdTagByName($myTag = "", $companyCode = "EEA"){
        $tagList    = $this->getListTags($companyCode);
        if($tagList['status'] == false){
            return false;
        }

        foreach ($tagList['data']->tags as $tag){
            if($tag->name == $myTag){
                return $tag->custom_id;
            }
        }

        // if not exist, then create a new one
        $result     = $this->addTags($myTag, $companyCode);
        if($result['status'] == false){
            return false;
        }

        return $result['data']->tag->custom_id;
    }

    public function getListTags($companyCode = "EEA"){
        // DEFAULT EXPO ANUGERAH
        try {
            $res = $this->client->request('GET', $this->jurnalUrl.'/tags', [
                'headers' => $this->change($companyCode)
            ]);
            $result = $res->getBody()->getContents();

            return [
                'status'    => true,
                'data'      => json_decode($result)
            ];
        } catch (\Exception $e) {

            return [
                'status'    => false,
                'data'      => $e->getMessage()
            ];
        }
    }

    public function addTags($tag = "",$companyCode = "EEA"){
        // DEFAULT EXPO ANUGERAH
        $data   = [
            "tag"   => [
                "name"      => $tag,
                "custom_id" => str_slug($tag)
            ]
        ];
        try {
            $res = $this->client->request('POST', $this->jurnalUrl.'/tags', [
                'headers'   => $this->change($companyCode),
                'body'      => json_encode($data)
            ]);
            $result = $res->getBody()->getContents();

            return [
                'status'    => true,
                'data'      => json_decode($result)
            ];
        } catch (\Exception $e) {

            return [
                'status'    => false,
                'data'      => $e->getMessage()
            ];
        }
    }

}