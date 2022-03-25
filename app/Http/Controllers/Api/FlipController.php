<?php

namespace App\Http\Controllers\Api;

use App\Flip\FlipService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FlipController extends Controller {
    public function callback(Request $request, $idBigFlip = null) {
        $token = $request->get('token');
        $data = $request->get('data');
        Log::error('Log FC-C: ' . json_encode(['token' => $token, 'data' => $data, 'id_big_flip' => $idBigFlip]));
        $flipService = new FlipService();
        $result = $flipService->callBackDisbursement($token, $data, $idBigFlip);
        return response()->json($result);
    }

    public function createPwf($token = null) {
        if (!$token) {
            return response()->json(returnCustom("Not valid"));
        }
        if ($token != 'fliptest') {
            return response()->json(returnCustom("Not valid"));
        }

        $flipService = new FlipService('JDJ5JDEzJDlSREFKZzRxUHY2NVRKaFBrUXdJUi5oUEdxQnJSQUQyd2FMN2Rtb0lRbFlxUnZ2WlZBZHpT', '$2y$13$kW.65Bbf5UrC8yIlW8FS7OcevKy6xBAz0Cwdg6WwCF6bjyfOfK6kG');
        return response()->json($flipService->createbill());
    }
}