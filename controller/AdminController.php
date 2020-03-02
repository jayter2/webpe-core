<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2020 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------

namespace webpe\controller;

use think\App;
use webpe\middleware\AdminInterceptor;

/**
 * 后台公共控制器
 * @package webpe\library
 */
class AdminController extends  Controller
{
    protected $middleware = [AdminInterceptor::class];
    /**
     * 应用实例
     * @var \think\App
     */
//    protected $app;


}
