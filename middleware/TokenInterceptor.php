<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2020 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------
// WebPE系统后台中间件
namespace webpe\middleware;

class TokenInterceptor
{
    public function handle($request, \Closure $next)
    {
        if ($request->param('name') == 'webpe') {
            //return redirect('index/think');
            echo '|webpe|';
        }

        return $next($request);
    }
}