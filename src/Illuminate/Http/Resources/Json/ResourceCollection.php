<?php

namespace Larawos\Illuminate\Http\Resources\Json;

use Illuminate\Http\Resources\Json\ResourceCollection as Resource;

class ResourceCollection extends Resource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        return $this->resource->map(function($value) use($request) {
            return 'local' == config('app.env') && $request->has('__message') ?
            $this->transform($this->message()) : $this->transform($this->template($value));
        });
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
