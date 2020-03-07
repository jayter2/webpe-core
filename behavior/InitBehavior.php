<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2020 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------

namespace webpe\behavior;

use think\facade\Config;
use think\facade\Request;

class InitBehavior
{
    public function run(Request $request, $params) {
        // 行为逻辑
        $host = $request::host();
        if(strpos($host,'api.')===0){
            //substr($host,0,strpos($host,'.'))
            Config::set('url_controller_layer','api');
        }
    }
}