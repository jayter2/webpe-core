<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2019 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------

namespace webpe\extend;
/**
 * CSV工具类
 * Class Csv
 * @package xweb\extend
 */
class Csv {
	/**
	 * 读取CSV文件
	 * @param string $csvfile csv文件路径
	 * @param int $offset      起始行数
	 * @param int $lines       读取行数 0不限
	 * @return array|bool
	 */
	public static function read($csvfile, $offset = 0, $lines = 0)
	{
		if (!$fp = fopen($csvfile, 'r')) {
			return false;
		}
		$i = $j = 0;
		while (false !== ($line = fgets($fp))) {
			if ($i++ < $offset) {
				continue;
			}
			break;
		}
		$data = array();
		if($lines>0){
			while (($j++ < $lines) && !feof($fp)) {
				$data[] = fgetcsv($fp);
			}
		}else{
			while (!feof($fp)) {
				$data[] = fgetcsv($fp);
			}
		}
		fclose($fp);
		return $data;
	}
	/**
	 * 导出CSV文件
	 * @param array $data        数据
	 * @param string $filename  文件名称
	 * @param array $headers 首行数据
	 * @return string
	 */
	public static function export($data, $filename, $headers)
	{
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename='.$filename);
		header('Cache-Control: max-age=0');
		$fp = fopen('php://output', 'a');
		if (!empty($headers)) {
			foreach ($headers as $key => $value) {
				$headers[$key] = iconv('utf-8', 'gbk', $value);
			}
			fputcsv($fp, $headers);
		}
		$num = 0;
		//每隔$limit行，刷新一下输出buffer，不要太大，也不要太小
		$limit = 10000;
		//逐行取出数据，不浪费内存
		$count = count($data);
		if ($count > 0) {
			for ($i = 0; $i < $count; $i++) {
				$num++;
				//刷新一下输出buffer，防止由于数据过多造成问题
				if ($limit == $num) {
					ob_flush();
					flush();
					$num = 0;
				}
				$row = $data[$i];
				foreach ($row as $key => $value) {
					$row[$key] = iconv('utf-8', 'gbk', $value);
				}
				fputcsv($fp, $row);
			}
		}
		fclose($fp);
	}

	/**
	 * 导出CSV文件
	 * @param array $data        数据
	 * @param string $filename  文件名称
	 * @param array $headers 首行数据
	 * @return string
	 */
	public static function export2($data, $filename, $headers)
	{
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=' . $filename);
		if (!empty($headers)) {
			echo iconv('utf-8','gbk//TRANSLIT','"'.implode('","',$headers).'"'."\n");
		}
		foreach ($data as $key => $value) {
			$output = array();
			$output[] = $value['id'];
			$output[] = $value['name'];
			echo iconv('utf-8','gbk//TRANSLIT','"'.implode('","', $output)."\"\n");
		}
	}
}

?>