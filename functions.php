<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2020 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------
// 应用公共文件


use \think\facade\Hook;

//系统行为
Hook::add('app_init','\\webpe\\behavior\\InitBehavior');





// ----------------------------------------------------------------------



/**
 * 格式化浮点数
 * @param int $num
 * @param int $length
 * @param boolen $type false不四舍五入
 * @return string
 */
function format_float($num,$length=2,$type=1){
	if($type){
		return sprintf("%.{$length}f", $num);
	}else{
		//不四舍五入
		$len = $length+1;
		return sprintf("%.{$length}f",substr(sprintf("%.{$len}f", $num), 0, -1));
	}
}
/**
 * 字节转换
 * @param number $bytes
 * @return string
 */
function format_byte($bytes) {
	$size_text = array(" B"," KB"," MB"," GB"," TB"," PB"," EB"," ZB"," YB" );
	return round($bytes / pow(1024,($i = floor(log($bytes,1024)))),2) . $size_text[$i];
}