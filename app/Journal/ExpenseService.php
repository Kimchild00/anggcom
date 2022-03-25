<?php
namespace App\Journal;

use App\Journal\JournalService;
use Illuminate\Support\Facades\Log;

class ExpenseService extends JournalService
{
    public function getAllCAonlyParent($journalKey, $all = 0)
    {
        try {
            $accounts = $this->getAllAccountsByJurnalKey($journalKey);
        } catch (\Exception $e) {
            return [];
        }
        if ($all) {
            return $accounts;
        }
        $result = [];
        foreach ($accounts as $account) {
//            if (in_array($account['category'], ['Expenses', 'Other Expense', 'Cost of Sales', 'Beban', 'Harga Pokok Penjualan'])) {
                if ($account['is_parent'] == false) {
                    $result[] = [
                        'id' => $account['id'] . "^^" . $account['name'],
                        'text' => $account['name'] . "  | " . $account['category']
                    ];

                    if ($account['name'] == 'Sample') {
                        Log::error('Coa detail ' . json_encode($account));
                    }
                }
//            }
        }
        return $result;
    }

    public function getAllAccountsByJurnalKey($key)
    {
        $this->header['headers']['apikey']  = $key;
        $res = $this->client->request('GET', $this->jurnalUrl.'/accounts', $this->header);
        $res = $res->getBody()->getContents();
        $progress = json_decode($res, true);
        $result = $progress['accounts'];
        return $result;
    }

    public function createExpenses($data)
    {
        try {

            // Comment sending a post to jurnal temporarely
            if (in_array($data->company->jurnal_pt, ['EEU', 'ESD', 'EID','TDL'])) {
                Log::error('Info: the company list block for auto posting ' . $data->company->jurnal_pt . '. Data: ' . json_encode($data));
                return [
                    'status' => false,
                    'data' => "Jurnal EEU, ESD, EID, TDL are commented, requester is finance"
                ];
            }

            $id = $data->ott_code;
            $fee = $data->fee;
            $debt = $data->amount;
            $description = $data->description;
            $switchHeader = $data->company->jurnal_pt;
            $expenseCode = ['5', '6', '8', '9'];
            $varAccount = '';
            $additional = '';
            // Checking Process (using - and using id on ott_code)
            if (strpos($id, '-') == true) {
                $process = $this->_processAccountOttCode($data);
                if (!empty($process['data']['account'])) {
                    $account = $process['data'];
                } else {
                    return [
                        'status' => false,
                        'data' => $process['data']['message'],
                    ];
                }
                if (!is_null($data->opt_type_trans)) {
                    $additional = '#Message : Akun berikut sudah tidak ada. #Kode Akun : ' . $data->opt_type_trans->code . ' #Nama Akun : ' . $data->opt_type_trans->title;
                } else if (isset($account['message'])) {
                    if ($account['message']) {
                        $additional = '#Message : Akun yang dipilih merupakan akun berkelompok. ' . $account['message'];
                    }
                }
            } else {
                for ($i = 0; $i < 3; $i++) {
                    try {
                        $varAccount = $this->getAnAccount($id, $switchHeader);
                    } catch (\Exception $e) {
                    }
                    if (empty($varAccount)) {
                        break;
                    }
                }
                if (!empty($varAccount)) {
                    $account = json_decode($varAccount, true);
                    if (!in_array(substr($account['account']['number'], 0, 1), $expenseCode)) {
                        if ($account['account']['is_parent'] === true) {
                            $additional = '#Message : Akun yang dipilih merupakan akun berkelompok dan bukan merupakan kategori expense. #Kode Akun : ' . $account['account']['number'] . ' #Nama Akun : ' . $account['account']['name'];
                        } else {
                            $additional = '#Message : Akun bukan merupakan kategori beban. #Kode Akun : ' . $account['account']['number'] . ' #Nama Akun : ' . $account['account']['name'];
                        }
                        $varAccount = $this->dumpAccountJurnal($switchHeader);
                        $account = $varAccount;
                    } else if ($account['account']['is_parent'] === true) {
                        $additional = '#Message : Akun yang dipilih merupakan akun berkelompok. #Kode Akun : ' . $account['account']['number'] . ' #Nama Akun : ' . $account['account']['name'];
                        $varAccount = $this->dumpAccountJurnal($switchHeader);
                        $account = $varAccount;
                    }
                } else {
                    $additional = '#Message : Account tidak cocok. #Title : ' . $data->title;
                    $varAccount = $this->dumpAccountJurnal($switchHeader);
                    if (!$varAccount['status']) {
                        return [
                            'status' => false,
                            'data' => 'Dump Account Failed to Push'
                        ];
                    }
                    $account = $varAccount;
                }
            }
            $params = [
                "expense" => [
                    "refund_from_name" => "Flip",
                    "person_name" => "Operational Flip",
                    "transaction_date" => $data->transaction_status_transferred->created_at,
                    "payment_method_name" => "Transfer Bank",
                    "expense_payable" => false, //pay now = false, pay later = true
                    "address" => "Green Lake City Ruko Crown Block D 17, Petir, Cipondoh, Kota Tangerang, Banten, 15157", //optional tambahan dari kolom person
                    "transaction_account_lines_attributes" => [
                        [
                            "account_name" => $account['account']['name'],
                            "description" => $additional . ' #Description : ' . $description . ' #Remark : ' . $data->remark,
                            "debit" => $debt
                        ],
                        [
                            "account_name" => "Flip Administration Expense",
                            "description" => "Flip Administration Expense",
                            "debit" => $fee,
                        ]
                    ],
                    "tags" => [
                        "OPERATIONAL " . $data->company->title,
                        "AUTO-INPUT"
                        // Must create manually on jurnal,
                        // it can be the next feature to develop by adding tag on operational transaction form
                    ],
                ]
            ];
            if (!in_array($data->type_tax, ['no_tax', 'No Tax'])) {
                $accountTax = '';
                if ($data->type_tax == 'pph 21') {
                    $accountTax = 'Tax Payable - Article 21';
                } else if ($data->type_tax == 'pph 23') {
                    $accountTax = 'Tax Payable - Article 23/26';
                } else if ($data->type_tax == 'pph 26') {
                    $accountTax = 'Tax Payable - Article 26/26';
                } else if ($data->type_tax == 'pph 4(2)') {
                    $accountTax = 'Tax Payable - Article 4(2)';
                }
                if ($accountTax) {
                    $params['expense']['transaction_account_lines_attributes'][] = [
                        "account_name" => $accountTax,
                        "description" => $accountTax,
                        "debit" => $data->amount_tax
                    ];
                }
            }

            try {
                $res = $this->client->request('POST', $this->jurnalUrl . '/expenses', [
                    'headers' => $this->change($switchHeader),
                    'body' => json_encode($params)
                ]);
                $res = $res->getBody()->getContents();
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
                'data' => 'Err create expense: ' . $e->getMessage() . ', data: ' . json_encode($data)
            ];
        }
    }

    public function getAllAccounts()
    {
      $res = $this->client->request('GET', $this->jurnalUrl.'/accounts', $this->header);
      $res = $res->getBody()->getContents();
      $progress = json_decode($res, true);
      $result = $progress['accounts'];
      return $result;
    }

    public function getAnAccount($id, $switchHeader)
    {
      $res = $this->client->request('GET', $this->jurnalUrl.'/accounts/'.$id, [
        'headers' => $this->change($switchHeader)
      ]);
      $res = $res->getBody()->getContents();
      return $res;
    }

    public function getAnAccountDecode($id, $switchHeader)
    {
      $res = $this->client->request('GET', $this->jurnalUrl.'/accounts/'.$id, [
        'headers' => $this->change($switchHeader)
      ]);
      $res = $res->getBody()->getContents();
      $progress = json_decode($res, true);
      $result = $progress['account'];
      return $result;
    }

    public function getAllAccountsByJurnalKeyAndHeader($key)
    {
        $this->header['headers']  = $this->change($key);
        $res = $this->client->request('GET', $this->jurnalUrl.'/accounts', $this->header);
        $res = $res->getBody()->getContents();
        $progress = json_decode($res, true);
        $result = $progress['accounts'];
        return $result;
    }
  
    public function dumpAccountJurnal($switchHeader)
    {
        $varAccount = '';
        $account    = [];
        for($i = 0; $i < 3; $i++){
            try{
                $varAccount   = $this->getAllAccountsByJurnalKeyAndHeader($switchHeader);
            } catch(\Exception $e){}
            if(!empty($varAccount)){
                break;
            }
        }
        if(empty($varAccount)) {
            return $result = [
                'status'  => false,
                'account' => ''
            ];
        }
        foreach ($varAccount as $item) {
            if ($item['name'] == "Dump Account") {
                $account = $item;
            }
        }
        if(isset($account['id'])) {
            return [
                'status'    => true,
                'account'   => $account
            ];
        } else {
            return $result = [
                'status'  => false,
                'account' => ''
            ];
        }
    }

    public function _processAccountOttCode($data)
    {
        $result         = [
            'status'    => false,
            'data'      => [
                'account'           => '',
                'message'           => ''
            ]
        ];
        $varAccount     = '';
        for($i = 0; $i < 3; $i++) {
            try{
                $varAccount   = $this->getAllAccountsByJurnalKeyAndHeader($data->company->jurnal_pt);
            }
            catch(\Exception $e){}
            if(!empty($varAccount)) {
              break;
            }
        }
        if(empty($varAccount[0])){
            $result['data']['message'] = 'Gagal memuat akun';
            return $result;
        }
        foreach ($varAccount as $item) {
            if ($item['number'] == $data->ott_code) {
                $account = [
                  "account" => $item
                ];
            }
        }
        if(isset($account['account']['id'])){
            if($account['account']['is_parent'] === true) {
                $description = '#Kode Akun : ' . $account['account']['number'] . ' #Nama Akun : ' . $account['account']['name'];
                $account      = $this->dumpAccountJurnal($data->company->jurnal_pt);
                if ($account['status']) {
                    $result = [
                        'status'    => true,
                        'data'      => [
                            'account'  =>  $account['account'],
                            'message'  =>  $description
                        ]
                    ];
                } else {
                  $result = [
                      'status' => false,
                      'data' => ['account' => '']
                  ];
                }
            } else {
                $result = [
                    'status'    => true,
                    'data'      => ['account'  =>  $account['account']]
                ];
            }
        }else{
            $account  = $this->dumpAccountJurnal($data->company->jurnal_pt);
            if ($account['status']) {
                $result = [
                    'status'    => true,
                    'data'      => ['account'  =>  $account['account']]
                ];
            } else {
                $result = [
                    'status' => false,
                    'data' => ['account' => '']
                ];
            }
        }
        return $result;
    }
}
