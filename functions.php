<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2020 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------
// 应用公共文件


use \think\facade\Route;
use \think\facade\Request;
use \think\facade\Config;
use \think\facade\Hook;

//系统行为
Hook::add('app_init','\\webpe\\behavior\\InitBehavior');


//系统路由
Route::get('extend/captcha/[:id]', "\\webpe\\extend\\Captcha::controller");



function webpe_test(){

}