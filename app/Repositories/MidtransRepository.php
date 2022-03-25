<?php

namespace App\Repositories;

use App\Models\UserMemberOrder;
use App\Models\UserShortCart;
use App\ThirdParty\EmailService;
use Illuminate\Support\Facades\DB;

class MidtransRepository {
    public function checkout($data) {
        try {
            $userMemberOrder = UserMemberOrder::find($data->order_id);
            if (!$userMemberOrder) {
                return returnCustom("Invoice payment data is not found");
            }

            if ($userMemberOrder->paid_at) {
                return returnCustom("Invoice payment is already paid");
            }

            $userShortCart = new UserShortCart();
            $userShortCart->user_member_order_id = $userMemberOrder->id;
            $userShortCart->invoice_payment = $userMemberOrder->invoice_number . date("YmdHis");
            $userShortCart->status = 'REQUEST';
            $userShortCart->trx_type = 'MIDTRANS';

            $post = [
                'type' => 'ANGGARAN',
                'from' => 'ANGGARAN.COM',
                'transidmerchant'   => $userShortCart->invoice_payment,
                'amount'   => $userMemberOrder->package_price,
            ];

            $ch = curl_init(env('SERVER_PAYMENT_URL') . '/payment/midtrans/request-snap-token');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
            $response = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if (!$response['status']) {
                return returnCustom($response['data']);
            }
            $userShortCart->save();

            return returnCustom($response['data'], true);
        } catch (\Exception $e) {
            return returnCustom("Err-cdoe MR-C: " . $e->getMessage());
        }
    }

    public function callback($data) {
        try {
            DB::beginTransaction();

            $invoicePayment = $data['orderId'];
            $type = 'MIDTRANS - ' . $data['type'];

            $userShortCart = UserShortCart::with(['user_member_order.user_company', 'user_member_order.user_master'])
                ->where('invoice_payment', $invoicePayment)
                ->first();
            if (!$userShortCart) {
                return returnCustom("User short cart is not found");
            }

            if ($userShortCart->status == 'SUCCESS') {
                return returnCustom("User short cart is already paid");
            }

            if (!$userShortCart->user_member_order) {
                return returnCustom("User member order data is not found");
            }

            if (!$userShortCart->user_member_order->user_company) {
                return returnCustom("User company data is not found");
            }

            if ($userShortCart->user_member_order->paid_at) {
                return returnCustom("Invoice payment is already paid");
            }

            $userShortCart->status = 'SUCCESS';
            $userShortCart->save();

            $userMemberOrder = $userShortCart->user_member_order;

            $this->updateExpiredTimeUserCompany($userMemberOrder, $type, $userShortCart->user_member_order->user_company);


            $data = [
                'recipient_mail' => $userMemberOrder->user_master->email,
                'recipient_name' => $userMemberOrder->user_master->name,
                'cc' => [],
                'bcc' => [],
                'subject' => "Member Order Paid",
                'title' => 'Member Order Paid',
                'message' => $userMemberOrder->invoice_number . ' invoice has paid successfully, please logged in to ' . url('/') . ' to use our platform.'

            ];
            $emailService = new EmailService();
            $emailService->curlPost($data);

            DB::commit();
            return returnCustom('Ok', true);
        } catch (\Exception $exception) {
            return returnCustom("Err-code MR-C: " . $exception->getMessage());
        }
    }

    public function updateExpiredTimeUserCompany($userMemberOrder, $type, $userCompany) {
        $userMemberOrderActive = UserMemberOrder::where('user_company_id', $userMemberOrder->user_company_id)
            ->where('is_active', 1)
            ->first();
        if ($userMemberOrderActive) {
            $userMemberOrderActive->is_active = 1;
            $userMemberOrderActive->save();
        }

        $dateNow = date("Y-m-d H:i:s");
        $userMemberOrder->paid_at = $dateNow;
        $userMemberOrder->paid_with = $type;
        $userMemberOrder->is_active = 1;
        $userMemberOrder->save();

        $userCompany->package_name = $userMemberOrder->package_name;
        $userCompany->expired_at = date('Y-m-d H:i:s',strtotime($dateNow . "+" . $userMemberOrder->long_expired));
        $userCompany->save();
    }
}