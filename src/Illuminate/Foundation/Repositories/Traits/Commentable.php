<?php

namespace Larawos\Illuminate\Foundation\Repositories\Traits;

use Illuminate\Database\Eloquent\Model;
use Larawos\Illuminate\Foundation\Models\Comment;
use Larawos\Illuminate\Foundation\Repositories\CommentRepository;

trait Commentable
{
    /**
     * 模型批量上传文件
     */
    public function comment(Model $model, $data)
    {
        return (new CommentRepository(new Comment))->update(
                $model->comments()->create(new Comment($data)), $data
            );
    }
}
