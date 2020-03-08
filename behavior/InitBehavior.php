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
        $host = $request::host();
        $baseurl = $request::baseUrl();
        if ($baseurl === '/api' || substr($host, 0, 3) === 'api') {
            Config::set('url_controller_layer', 'api');
        }
    }
}