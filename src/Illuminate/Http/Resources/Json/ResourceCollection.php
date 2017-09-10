<?php

namespace Larawos\Illuminate\Http\Resources\Json;

use Illuminate\Http\Resources\Json\ResourceCollection as Resource;

class ResourceCollection extends Resource
{
    public static function successTemplate()
    {
        return [
            'status'  => 200,
            'message' => '成功'
        ];
    }
}
