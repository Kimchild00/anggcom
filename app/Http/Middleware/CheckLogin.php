<?php

namespace App\Http\Middleware;

use App\Models\UserMemberOrder;
use Closure;
use Illuminate\Support\Facades\Auth;

class CheckLogin
{
    public function handle($request, Closure $next)
    {
        // Check user has logged in, if not redirect to login page
        $isLogin    = Auth::check();
        if (!$isLogin) {
            return redirect(url('login'));
        }

        // Check account master not paid
        $user = Auth::user();
        if (!$user->user_company) {
            alertNotify(false,'Err-code CL-H: You don\'t have user company, please contact our customer service.', $request);
            return redirect(url('error-page'));
        }

        if (!$user->user_company->expired_at) {
            // Get last user member order who not paid
            $lastUserMemberOrder = UserMemberOrder::where('user_company_id', $user->user_company->id)
                ->whereNull('paid_at')
                ->orderBy('id', 'desc')
                ->select('invoice_number')
                ->first();
            if (!$lastUserMemberOrder) {
                alertNotify(false, 'Err-code CL-H: There is a problem with your history user member order, please contact our customer service', $request);
                return redirect(url('error-page'));
            }

            if (Auth::user()->level == 'master') {
                // Redirect to payment page based on last user member order not paid
                alertNotify(false, "Please make a payment before using this platform", $request);
                return redirect(url('payment/' . $lastUserMemberOrder->invoice_number));
            } else {
                alertNotify(false, 'Err-code CL-H: This master account has not paid, please contact your master account email to pay it', $request);
                return redirect(url('error-page'));
            }
        }
        return $next($request);
    }
}