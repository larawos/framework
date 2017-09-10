<?php

namespace Larawos\Illuminate\Foundation\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;

trait Deletedable
{
    public function deleted(Model $model, $data = 'only')
    {
        return $data == 'with' ? $query->withTrashed() : $query->onlyTrashed();
    }
}
