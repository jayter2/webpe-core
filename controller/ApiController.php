<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2020 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------

namespace webpe\controller;

use think\App;
use think\Container;
use think\Response;
use think\exception\ValidateException;

/**
 * API控制器基类(用于高性能场景)
 * @package webpe\library
 */
class ApiController
{
    /**
     * 应用实例
     * @var \think\App
     */
    protected $app;

    /**
     * Request实例
     * @var \think\Request
     */
    protected $request;

    /**
     * 构造方法
     * @access public
     */
    public function __construct(App $app = null) {
        $this->app = $app ?: Container::get('app');
        $this->request = $this->app['request'];
        // 控制器初始化
        $this->initialize();

    }

    // 初始化
    protected function initialize() {
    }

    /**
     * 返回封装后的数据(适用于API等场景)
     * @param $data
     * @param int $code
     * @param string $msg
     * @param array $header
     * @param string $type
     * @return Response
     */
    protected function result($data, $code = 1, $msg = '', $type = '', array $header = []) {
        $result = [
            'code' => $code,
            'msg' => $msg,
            'time' => time(),
            'data' => $data,
        ];
        $type = $type == 'xml' ? 'xml' : 'json';
        return Response::create($result, $type)->header($header);
    }

    /**
     * 验证数据
     * @access protected
     * @param  array $data 数据
     * @param  string|array $validate 验证器名或者验证规则数组
     * @param  array $message 提示信息
     * @param  bool $batch 是否批量验证
     * @param  mixed $callback 回调方法（闭包）
     * @return array|string|true
     * @throws ValidateException
     */
    protected function validate($data, $validate, $message = [], $batch = false, $callback = null) {
        if (is_array($validate)) {
            $v = $this->app->validate();
            $v->rule($validate);
        } else {
            if (strpos($validate, '.')) {
                // 支持场景
                list($validate, $scene) = explode('.', $validate);
            }
            $v = $this->app->validate($validate);
            if (!empty($scene)) {
                $v->scene($scene);
            }
        }
        if ($batch) { // 是否批量验证
            $v->batch(true);
        }
        if (is_array($message)) {
            $v->message($message);
        }
        if ($callback && is_callable($callback)) {
            call_user_func_array($callback, [$v, &$data]);
        }
        if (!$v->check($data)) {
            return $v->getError();
        }
        return true;
    }

    public function __debugInfo() {
        $data = get_object_vars($this);
        unset($data['app'], $data['request']);

        return $data;
    }
}
