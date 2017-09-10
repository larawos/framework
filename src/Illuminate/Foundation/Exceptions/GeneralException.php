<?php

namespace Larawos\Illuminate\Foundation\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\Model;

/**
 * Class GeneralException.
 */
class GeneralException extends Exception
{
    protected $message = '服务器异常';
    protected $model = [];

    public function __construct(...$parameters)
    {
        foreach ($parameters as $parameter) {
            $this->model = $parameter instanceof Model ? $parameter : $this->model;
            $this->message = is_string($parameter) ? $parameter : $this->message;
        }
    }

    public function render($request)
    {
        if ($request->expectsJson()) {
            return response()->json(['status' => 500, 'message' => $this->message, 'data' => $this->model]);
        }

        return response($this->message, 500);
    }
}
