<?php

namespace Larawos\Illuminate\Http\Resources\Json;

use Illuminate\Http\Resources\Json\Resource as BaseResource;

class Resource extends BaseResource
{
    public static function successTemplate()
    {
        return [
            'status'  => 200,
            'message' => '成功'
        ];
    }
}
