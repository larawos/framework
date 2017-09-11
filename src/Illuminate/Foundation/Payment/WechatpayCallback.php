<?php

namespace App\Http\Controllers\Frontend;

use Illuminate\Http\Request;
use Omnipay;
use Log;

class WechatpayCallback
{
    protected function paidSuccess(Request $request)
    {
        $gateway = Omnipay::gateway('wechatpay_callback');

        $response = $gateway->completePurchase([
                'request_params' => $request->getContent()
            ])->send();

        try {
            if($response->isPaid()){
                return true;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            Log::info('驱动[wechatpay]回调解析失败');
            return false;
        }
    }
}
