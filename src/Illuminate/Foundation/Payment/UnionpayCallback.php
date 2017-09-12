<?php

namespace Larawos\Illuminate\Foundation\Payment;

use Illuminate\Http\Request;
use Omnipay;
use Log;

trait UnionpayCallback
{
    protected function paidSuccess(Request $request)
    {
        $gateway = Omnipay::gateway('unionpay');

        $request = $gateway->completePurchase(['request_params'=> $request->all()]);

        try {
            $response = $request->send();

            if($response->isPaid()){
                return true;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            Log::info('驱动[unionpay]回调解析失败');
            return false;
        }
    }
}
