<?php

namespace Larawos\Illuminate\Foundation\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;

trait Searchable
{
    public function exactSearch(Model $model, $data)
    {
        return $query->where($data);
    }

    public function fullSearch(Model $model, $data)
    {
        return $query->search($data);
    }
}
