<?php

namespace Larawos\Illuminate\Foundation\Repositories;

use Larawos\Illuminate\Foundation\Models\Comment;
use Larawos\Illuminate\Foundation\Repositories\Sortable;
use Larawos\Illuminate\Foundation\Repositories\Uploadable;
use Larawos\Illuminate\Foundation\Repositories\MappedModel;
use Larawos\Illuminate\Foundation\Repositories\AbstractEloquentRepository;

class CommentRepository extends AbstractEloquentRepository
{
    use Sortable, Uploadable, MappedModel;

    public function __construct(Comment $query)
    {
        $this->query = $query;
    }
}
