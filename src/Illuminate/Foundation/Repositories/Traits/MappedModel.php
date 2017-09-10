<?php

namespace Larawos\Illuminate\Foundation\Repositories\Traits;

trait MappedModel
{
    public function __get($arg)
    {
        return $this->query()->{$arg};
    }
}
