<?php

namespace Larawos\Illuminate\Http\Resources\Json;

use Illuminate\Http\Resources\Json\Resource as BaseResource;

class Resource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return 'local' == config('app.env') && $request->has('__message') ?
            $this->transform($this->message()) : $this->transform($this->template($this->resource));
    }

    /**
     * The resource template.
     *
     * @param  \Illuminate\Database\Eloquent\Model $model
     * @return array
     */
    protected function template($model)
    {
        return [];
    }

    /**
     * The resource message.
     *
     * @return array
     */
    protected function message()
    {
        return [];
    }
}
