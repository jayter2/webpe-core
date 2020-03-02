<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2019 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------

namespace webpe\extend;
/**
 * WebPE常用工具类
 * Class Utils
 * @package webpe\extend
 */
class Utils {

	
	/**
	 * 数组转3级树形下拉框
	 * @param array $list
	 * @param string $defname 第一项名称
	 * @param string $keyname PK名
	 * @param string $valname 显示字段名
	 * @param string $pidname 父ID名
	 */
	public static function array2opt($list,$defname='-', $keyname='id',$valname='name', $pidname = 'pid',$level=2){
		$tree = self::array2tree($list,$keyname,$pidname);
		$option = array(0=>$defname);
		foreach ($tree as $vo){
			$option[$vo[$keyname]] = '&nbsp;'.$vo[$valname];
			$i=0;$count = isset($vo['_child'])?count($vo['_child']):0;
			if(isset($vo['_child'])) foreach ($vo['_child'] as $vo2){
				$i++;$spec = $i==$count ? '└─':'├─';
				$option[$vo2[$keyname]] = '　　'.$spec.$vo2[$valname];
				$j=0;$jcount = isset($vo2['_child'])?count($vo2['_child']):0;
				if($level==3 && isset($vo2['_child'])) foreach ($vo2['_child'] as $vo3){
					$j++;$spec = $j==$jcount ? '└─':'├─';
					$option[$vo3[$keyname]] = '　　　 '.$spec.$vo3[$valname];
				}
			}
		}
		return $option;
	}
	 /**
	  * 
	  * 数组转多级树形结构
	  * @param array $list 要转换的数据集
	  * @param string $pk 
	  * @param string $pid
	  * @param number $rootid 指定获取某个id的树形 
	  * @param string $child
	  * @return array
	  */
	public static function array2tree($list, $keyname='id', $pidname = 'pid', $rootid = 0, $child = '_child') {
		// 创建Tree
		$tree = array();
		if(is_array($list)) {
			// 创建基于主键的数组引用
			$refer = array();
			foreach ($list as $key => $data) {
				$refer[$data[$keyname]] =& $list[$key];
			}
			foreach ($list as $key => $data) {
				// 判断是否存在parent
				$parentId =  $data[$pidname];
				if ($rootid == $parentId) {
					$tree[] =& $list[$key];
				}else{
					if (isset($refer[$parentId])) {
						$parent =& $refer[$parentId];
						$parent[$child][] =& $list[$key];
					}
				}
			}
		}
		return $tree;
	}
	

	
	/**
	 * 获取随机字符串
	 * @param int $len  长度
	 * @param int $addtime  是否加入当前时间戳
	 * @param int $inNumber   是否包含数字
	 * @return string
	 */
	public static function randStr($randlen = 6,$addtime = 1, $inNumber = 0) {
		$chars = $inNumber?'ABCDEFGHIJKLMNPQRSTUVWXYZ0123456789':'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
		$len = strlen($chars);
		$randlen = $addtime?$randlen-4:$randlen;
		$randStr = '';
		for($i = 0; $i < $randlen; $i ++) {
			$randStr .= $chars[rand(0,$len - 1)];
		}
		if($addtime){
			list(,$mic) = explode('.', microtime(true));
			$randStr = $randStr.$mic;
			$randStr = str_pad($randStr, $randlen+4,rand(0,9),STR_PAD_RIGHT);
		}
		return $randStr;
	}
	/**
	 * 获取中文字符拼音首字母
	 * @param string $str
	 * @return string|NULL
	 */
	public static function firstLetter($str) {
		if (empty($str)) return '';
		$fchar = ord($str{0});
		if ($fchar >= ord('A') && $fchar <= ord('z')) {
            return strtoupper($str{0});
        }
		$s1 = iconv('UTF-8','gb2312',$str);
		$s2 = iconv('gb2312','UTF-8',$s1);
		$s = $s2 == $str ? $s1 : $str;
		$asc = ord($s{0}) * 256 + ord($s{1}) - 65536;
		if ($asc >= - 20319 && $asc <= - 20284) return 'A';
		if ($asc >= - 20283 && $asc <= - 19776) return 'B';
		if ($asc >= - 19775 && $asc <= - 19219) return 'C';
		if ($asc >= - 19218 && $asc <= - 18711) return 'D';
		if ($asc >= - 18710 && $asc <= - 18527) return 'E';
		if ($asc >= - 18526 && $asc <= - 18240) return 'F';
		if ($asc >= - 18239 && $asc <= - 17923) return 'G';
		if ($asc >= - 17922 && $asc <= - 17418) return 'H';
		if ($asc >= - 17417 && $asc <= - 16475) return 'J';
		if ($asc >= - 16474 && $asc <= - 16213) return 'K';
		if ($asc >= - 16212 && $asc <= - 15641) return 'L';
		if ($asc >= - 15640 && $asc <= - 15166) return 'M';
		if ($asc >= - 15165 && $asc <= - 14923) return 'N';
		if ($asc >= - 14922 && $asc <= - 14915) return 'O';
		if ($asc >= - 14914 && $asc <= - 14631) return 'P';
		if ($asc >= - 14630 && $asc <= - 14150) return 'Q';
		if ($asc >= - 14149 && $asc <= - 14091) return 'R';
		if ($asc >= - 14090 && $asc <= - 13319) return 'S';
		if ($asc >= - 13318 && $asc <= - 12839) return 'T';
		if ($asc >= - 12838 && $asc <= - 12557) return 'W';
		if ($asc >= - 12556 && $asc <= - 11848) return 'X';
		if ($asc >= - 11847 && $asc <= - 11056) return 'Y';
		if ($asc >= - 11055 && $asc <= - 10247) return 'Z';
		return null;
	}

	
	
}