<?php

namespace Larawos\Illuminate\Foundation\Image;

use Illuminate\Http\Request;
use QrCode;

trait ResponseQrcodeForRequest
{
    /**
     * 打印二维码图片
     *
     * @return \Illuminate\Http\Response
     */
    public function qrcode(Request $request)
    {
        return response()->make(
                QrCode::format('png')
                    ->margin(1)
                    ->size(300)
                    ->errorCorrection('H')
                    ->encoding('UTF-8')
                    ->merge(url('logo.png'), .3, true)
                    ->generate(is_array($request->input('data')) ?
                            json_encode($request->input('data')) :
                            $request->input('data')),
                    200, ['Content-Type' => 'image/png']);
    }
}
