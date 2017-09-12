<?php

namespace Larawos\Illuminate\Foundation\Payment;

use Illuminate\Http\Request;
use Omnipay;
use Log;

trait AlipayCallback
{
    protected function paidSuccess(Request $request)
    {
        $gateway = Omnipay::gateway('alipay_app');

        $request = $gateway->completePurchase();
        $request->setParams($request->all());

        try {
            $response = $request->send();

            if($response->isPaid()){
                return true;
            }else{
                return false;
            }
        } catch (\Exception $e) {
            Log::info('驱动[alipay]回调解析失败');
            return false;
        }
    }
}
