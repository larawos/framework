<?php

namespace Larawos\Illuminate\Foundation\Models;

use Illuminate\Database\Eloquent\Model;

class File extends Model
{
    protected $fillable  = ['path', 'md5', 'mime', 'status'];
}
