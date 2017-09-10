<?php

namespace Larawos\Illuminate\Foundation\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;

trait Sortable
{
    public function orderBy(Model $model, $data)
    {
        list($key, $sort) = $data;
        return  $query->orderBy($key, $sort);
    }
}
