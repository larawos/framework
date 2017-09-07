<?php

if (! function_exists('includeRouteFiles')) {
    /**
     * 导入$folder目录下面所有路由文件
     */
    function includeRouteFiles($folder)
    {
        try {
            $rdi = new recursiveDirectoryIterator($folder);
            $it = new recursiveIteratorIterator($rdi);

            while ($it->valid()) {
                if (! $it->isDot() && $it->isFile() && $it->isReadable() && $it->current()->getExtension() === 'php') {
                    require $it->key();
                }

                $it->next();
            }
        } catch (Exception $e) {
            echo $e->getMessage();
        }
    }
}

if (! function_exists('api')) {
    /**
     * 返回json格式的api数据
     */
    function api($body = [], $status = 200, $message = '请求成功')
    {
        return response()->json(
                compact('status', 'message', 'body')
            );
    }
}

if (! function_exists('carbon')) {
    /**
     * 返回 carbon 时间处理类
     */
    function carbon($date = '')
    {
        return Carbon\Carbon::parse($date);
    }
}

if (! function_exists('get_cover')) {
    /**
     * 获取封面地址，图片不存在时返回默认图片
     */
    function get_cover($id)
    {
        $image = App\Models\Image::find($id);
        return !is_null($image) ?
        trim(config('app.url'), '//'). '/storage/'. trim($image->path, '//') :
        trim(config('app.url'), '//'). '/image.png';
    }
}

if (! function_exists('get_avatar')) {
    /**
     * 获取头像地址，图片不存在时返回默认图片
     */
    function get_avatar($id)
    {
        $image = App\Models\Image::find($id);
        return !is_null($image) ?
        trim(config('app.url'), '//'). '/storage/'. trim($image->path, '//') :
        trim(config('app.url'), '//'). '/avatar.png';
    }
}
