<?php
// ----------------------------------------------------------------------
// WebPE
// Copyright (c) 2016-2019 http://www.webpe.cn All rights reserved.
// Author: jayter <jayter2@qq.com>
// ----------------------------------------------------------------------

namespace webpe\extend;

/**
 * Http工具类(提供一系列的Http方法)
 * Class UtilHttp
 * @package webpe\extend
 */
class Http
{
	/**
	 * 发起curl请求
	 * @param string $url 访问路径
	 * @param string|array $params 该数组多于1个表示为POST
	 * @param int $expire 请求超时时间
	 * @param array $headers array('key: value','key: value')
	 * @param array $extend 请求伪造包头参数
	 * @return array  array(data,code,info)
	 */
	public static function curlRequest($url, $params = array(), $headers = array(), $expire = 30,$extend = array()){
		if (empty($url)) return array('code' => '100');
		$curl = curl_init();
		$headers = empty($headers) ? array() : $headers;
		$headers = array_merge($headers,array(
				'Accept-Language: zh-CN',
				'Connection: Keep-Alive',
				'Cache-Control: no-cache'
		));
		// 只要第二个参数传了值之后，就是POST的
		if (!empty($params)) {
			$params = is_array($params) ? http_build_query($params) : $params;
			curl_setopt($curl, CURLOPT_POSTFIELDS, $params);
			curl_setopt($curl, CURLOPT_POST, true);
		}
		if (substr($url, 0, 8) == 'https://') {
			curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // https请求 不验证证书和hosts
			curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // 不从证书中检查SSL加密算法是否存在
		}
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl,CURLOPT_FOLLOWLOCATION,1);
		curl_setopt($curl,CURLOPT_MAXREDIRS,2); 	//指定最多的HTTP重定向的数量，和CURLOPT_FOLLOWLOCATION一起使用
		curl_setopt($curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; WOW64; rv:60.0) Gecko/20100101 Firefox/60.0");
		curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($curl,CURLINFO_HEADER_OUT,true);
		//curl_setopt($curl, CURLOPT_COOKIE, $Cookie); //COOKIE带过去
		if ($expire > 0) {
			curl_setopt($curl, CURLOPT_TIMEOUT, $expire); // 允许执行的最长秒数
			curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, $expire/2); // 建立连接超时时间
		}
		// 额外的配置
		if (!empty($extend)) {
			curl_setopt_array($curl, $extend);
		}
		$result['data'] = curl_exec($curl);
		$result['code'] = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$result['info'] = curl_getinfo($curl);
		if ($result['data'] === false) {
			$result['info'] = curl_error($curl);
			$result['code'] = -curl_errno($curl);
		}
		curl_close($curl);
		return $result;
	}
    /**
     * 采集远程文件
     * @access public
     * @param string $remote 远程文件名
     * @param string $local 本地保存文件名
     * @return mixed
     */
    public static function curlDownload($remote, $local)
    {
        ini_set('default_socket_timeout',1);//@todo 下载微信头像慢
        $fp = fopen($local, "w");
        if($fp) {
        	//解决 Connection: keep-alive后CURL下载慢
        	$headers = array('Connection: Keep-Alive','Keep-Alive: 100','Cache-Control: no-cache');
	        $curl = curl_init($remote);
	        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
	        curl_setopt($curl, CURLOPT_FILE, $fp);
	        curl_setopt($curl, CURLOPT_HEADER, 0);
	        curl_exec($curl);
	        curl_close($curl);
        }
        fclose($fp);
        return file_exists($local) ? true:false;
    }

    /**
     * 下载文件
     * 可以指定下载显示的文件名，并自动发送相应的Header信息
     * 如果指定了content参数，则下载该参数的内容
     * @param string $filename 下载文件名
     * @param string $showname 下载显示的文件名
     * @param string $content  下载的内容
     * @param integer $expire  下载内容浏览器缓存时间
     */
    public static function sendDownload($filename, $showname = '', $content = '', $expire = 180)
    {
        if (is_file($filename)) {
            $length = filesize($filename);
        } elseif ('' != $content) {
            $length = strlen($content);
        } else {
            exception($filename . '下载文件不存在！');
        }
        if (empty($showname)) {
            $showname = $filename;
        }
        $showname = preg_replace('/^.+[\\\\\\/]/', '', $showname);
        if (!empty($filename)) {
            $finfo = new \finfo(FILEINFO_MIME);
            $type  = $finfo->file($filename);
        } else {
            $type = "application/octet-stream";
        }
        //发送Http Header信息 开始下载
        header("Pragma: public");
        header("Cache-control: max-age=" . $expire);
        //header('Cache-Control: no-store, no-cache, must-revalidate');
        header("Expires: " . gmdate("D, d M Y H:i:s", time() + $expire) . "GMT");
        header("Last-Modified: " . gmdate("D, d M Y H:i:s", time()) . "GMT");
        header("Content-Disposition: attachment; filename=" . $showname);
        header("Content-Length: " . $length);
        header("Content-type: " . $type);
        header('Content-Encoding: none');
        header("Content-Transfer-Encoding: binary");
        if ('' == $content) {
            readfile($filename);
        } else {
            echo ($content);
        }
        exit();
    }


} 
