<?php

namespace Larawos\Illuminate\Foundation\Repositories\Traits;

use Larawos\Illuminate\Foundation\Models\File;
use Illuminate\Database\Eloquent\Model;

trait Uploadable
{
    /**
     * 模型批量上传文件
     */
    public function upload(Model $model, $data)
    {
        collect($data)->map(function($item, $key) use($model, $data) {
            if (ends_with($key, '__files')) {
                $fileIds = isset($data[$key. '_id']) && is_array($data[$key. '_id']) ?
                    $data[$key. '_id'] : [];

                $this->_uploadFiles($model, rtrim($key, '__files'), $item, $fileIds);
            }
        });
    }

    protected function _uploadFiles($model, $data)
    {
        if (isset($model->{str_finish($key, '_id')})) {
            $fileId = $this->_storeFile(array_pop($item));
            $model->{str_finish($key, '_id')} = $fileId;
            return $model->save();
        }

        if (isset($model->{str_finish($key, '_ids')})) {
            foreach ($item as $val) {
                $fileIds[] = $this->_storeFile($val);
            }
            $model->{str_finish($key, '_ids')} = implode(',', array_filter($fileIds));
            return $model->save();
        }

        foreach ($item as $val) {
            $fileIds[] = $this->_storeFile($val);
        }

        $model->files()->sync($fileIds);

        return $model->files;
    }

    protected function _storeFile($file)
    {
        $md5  = md5_file($file->getPathname());
        $mime = $file->getMimeType() ? $file->getMimeType() : 'application/octet-stream';
        $file = File::where('md5', $md5)->first();

        if (is_null($file)) {
            $path = $file->store('public/upload/files');
            $file = File::create(['path' => '/storage/'. $path, 'md5'  => $md5, 'mime'  => $mime]);
            return $file->id;
        }

        return $file->id;
    }
}
