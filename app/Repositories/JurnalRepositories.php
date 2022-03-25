<?php

namespace App\Repositories;

use App\Models\DivisionJournal;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class JurnalRepositories
{

    protected $jurnalUrl;
    protected $client;

    public function __construct()
    {
        $this->jurnalUrl = env('JURNAL_ENDPOINT').'core/api/v1';
        $this->client = new Client();
    }

    public function createJournalEntries($transaction)
    {
        try {
            Log::error('Jurnal start: ' . json_encode($transaction));
            if (!$transaction->transaction_finance_noted) {
                return [
                    'status' => false,
                    'data' => "this Anggaran transaction failed autoposting, because there is not any finance noted"
                ];
            }
            $journalName    = $transaction->ott_name;
            $journalKey     = $transaction->division->division_journal->journal_key;
            $description = '#Description: ' . $transaction->transaction_finance_noted->noted;
            $params = [
                "bank_withdrawal" => [
                    "refund_from_name" => "Flip",
                    "person_name" => "",
                    "transaction_date" => $transaction->transaction_flip->updated_at,
                    "transaction_no" => 'ID'.$transaction->id,
                    "memo" => '',
                    "custom_id" => '',
                    "transaction_account_lines_attributes" => [
                        [
                            "account_name" => $journalName,
                            "description" => $description,
                            "debit" => $transaction->amount
                        ],
                        [
                            "account_name" => "Flip Administration Expense",
                            "description" => "Flip Administration Expense",
                            "debit" => (int) $transaction->transaction_flip->fee,
                        ]
                    ],
                ]
            ];

            if ($transaction->transaction_tax) {
                if (!in_array($transaction->transaction_tax->type, ['no_tax', 'No Tax'])) {
                    $accountTax = '';
                    if ($transaction->transaction_tax->type == 'pph 21') {
                        $accountTax = 'Tax Payable - Article 21';
                    } else if ($transaction->transaction_tax->type == 'pph 23') {
                        $accountTax = 'Tax Payable - Article 23/26';
                    } else if ($transaction->transaction_tax->type == 'pph 26') {
                        $accountTax = 'Tax Payable - Article 26/26';
                    } else if ($transaction->transaction_tax->type == 'pph 4(2)') {
                        $accountTax = 'Tax Payable - Article 4(2)';
                    }
                    if ($accountTax) {
                        $params['expense']['transaction_account_lines_attributes'][] = [
                            "account_name" => $accountTax,
                            "description" => $accountTax,
                            "debit" => $transaction->transaction_tax->amount
                        ];
                    }
                }
            }
            try {
                $res = $this->client->request('POST', $this->jurnalUrl . '/bank_withdrawals', [
                    'headers' => [
                        'content-type' => 'application/json',
                        'apikey' => $journalKey
                    ],
                    'body' => json_encode($params)
                ]);
                $res = $res->getBody()->getContents();
                Log::error('Anggaran Autoposting Callback Success: Transaction ID' . $transaction->id);
                return [
                    'status' => true,
                    'data' => json_decode($res)
                ];
            } catch (\Exception $e) {
                return [
                    'status' => false,
                    'data' => $e->getMessage()
                ];
            }
        } catch (\Exception $e) {
            return [
                'status' => false,
                'data' => 'Err create expense: ' . $e->getMessage() . ', data: ' . json_encode($transaction)
            ];
        }
    }
}

