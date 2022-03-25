<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\UserMemberOrder;
use App\Repositories\MidtransRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MidtransController extends Controller {

    public function checkout() {
        $data = file_get_contents('php://input');
        $dataJson = \GuzzleHttp\json_decode($data);
        $midtransRepo = new MidtransRepository();
        $result = $midtransRepo->checkout($dataJson);
        return response()->json($result);
    }

    public function callback(Request $request) {
        $midtranRepo = new MidtransRepository();
        $method = $request->getRealMethod();
        Log::error("Midtrans Callback AnggaranCOM: " . json_encode([$method, $request->all()]));
        if ($method == 'POST') {
            return response()->json($midtranRepo->callback($request->all()));
        }
    }

    public function saveInfo($id, Request $request) {

        $order = UserMemberOrder::find($id);
        if (!$order) {
            return '';
        }

        $order->dump = json_encode($request['data']);
        $order->save();

        return '';
    }
}