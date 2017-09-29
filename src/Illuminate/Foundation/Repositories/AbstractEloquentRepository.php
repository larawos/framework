<?php

namespace Larawos\Illuminate\Foundation\Repositories;

use Closure;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;
use Larawos\Illuminate\Foundation\Exceptions\GeneralException;

abstract class AbstractEloquentRepository
{
    /**
     * Illuminate\Database\Eloquent\Model
     */
    protected $query;

    /**
     * 获取模型实例，不可修改。
     *
     * @return void | \Larawos\Illuminate\GeneralException
     */
    final public function query()
    {
        if ($this->query instanceof Model) {
            return $this->query;
        }

        throw new GeneralException('模型实例已不存在。');
    }

    /**
     * 创建一条数据。
     *
     * @param  array  $data
     * @param  \Closure  $callback
     * @return void | \Larawos\Illuminate\GeneralException
     */
    public function create(array $data, $callback = null)
    {
        $model = $this->query()->make($data);

        DB::transaction(function () use ($model, $data, $callback) {
            if ($model->save()) {

                if ($callback instanceof Closure) {
                    $callback($model, $data);
                }

                return true;
            }

            throw new GeneralException($model, '创建时出现问题，请再试一次。');
        });

        return $model;
    }

    /**
     * 更新一条数据。
     *
     * @param  Illuminate\Database\Eloquent\Model  $model
     * @param  array  $data
     * @param  \Closure  $callback
     * @return void | \Larawos\Illuminate\GeneralException
     */
    public function update(Model $model, array $data, $callback = null)
    {
        $model = $model->fill($data);

        DB::transaction(function () use ($model, $data, $callback) {
            if ($model->save()) {

                if ($callback instanceof Closure) {
                    $callback($model, $data);
                }

                return true;
            }

            throw new GeneralException($model, '更新时出现问题，请再试一次。');
        });

        return $model;
    }

    /**
     * 删除一条数据。
     *
     * @param  array  $data
     * @param  \Closure  $callback
     * @return void | \Larawos\Illuminate\GeneralException
     */
    public function delete(Model $model, $callback = null)
    {
        DB::transaction(function () use ($model, $callback) {
            if ($model->delete()) {
                if ($callback instanceof Closure) {
                    $callback($model);
                }

                return true;
            }

            throw new GeneralException($model, '删除时出现问题，请再试一次。');
        });
    }

    /**
     * 永久删除一条数据。
     *
     * @param  array  $data
     * @param  \Closure  $callback
     * @return void | \Larawos\Illuminate\GeneralException
     */
    public function forceDelete($id, $callback = null)
    {
        $model = $this->query()->withTrashed()->find($id);

        if (is_null($model->deleted_at)) {
            throw new GeneralException('必须先删除，才能永久销毁。');
        }

        DB::transaction(function () use ($model, $callback) {
            if ($model->forceDelete()) {
                if ($callback instanceof Closure) {
                    $callback($model);
                }

                return true;
            }

            throw new GeneralException($model, '删除时出现问题，请再试一次。');
        });
    }

    /**
     * 恢复一条数据。
     *
     * @param  array  $data
     * @param  \Closure  $callback
     * @return void | \Larawos\Illuminate\GeneralException
     */
    public function restore($id, $callback = null)
    {
        $model = $this->query()->withTrashed()->find($id);

        if (is_null($model->deleted_at)) {
            throw new GeneralException('该数据未删除，因此无法恢复。');
        }

        DB::transaction(function () use ($model, $callback) {
            if ($model->restore()) {
                if ($callback instanceof Closure) {
                    $callback($model);
                }

                return true;
            }

            throw new GeneralException($model, '还原时出现问题，请再试一次。');
        });

        return $model;
    }

    public function __call($method, $args)
    {
        $query  = $this->query();
        $method = $method == 'all' ? 'get' : $method;

        if (method_exists($this, 'deleted') && request()->has('deleted')) {
            $query = $this->deleted($query, request('deleted'));
        }

        if (method_exists($this, 'fullSearch') && request()->has('full_search')) {
            $query = $this->fullSearch($query, request('full_search'));
        }

        if (method_exists($this, 'exactSearch') && is_array(request('exact_search'))) {
            $query = $this->exactSearch($query, request('exact_search'));
        }

        if (method_exists($this, 'orderBy') && is_array(request('order_by'))) {
            $query = $this->orderBy($query, array_values(request('order_by')));
        }

        if (is_numeric(request('limit')) && 'get' == $method) {
            $query = $query->limit(request('limit'));
        }

        if (is_numeric(request('per_page')) && 'paginate' == $method) {
            $args = array_merge([request('per_page')], array_slice($args, 1));
        }

        return call_user_func([$query, $method], ...$args);
    }
}
