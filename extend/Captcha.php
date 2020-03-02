<?php
/**
 * Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
 * Author: liu21st <liu21st@gmail.com>
 * Modify: jayter <jayter2@qq.com>
 */

namespace webpe\extend;

use think\facade\Session;


/**
 * 验证码类
 * Class Captcha
 * @package webpe\extend
 */
class Captcha
{
	/**验证码过期时间（s）*/
	public $expire = 1800;
	/**使用中文验证码*/
	public $useZh = false;
	/**是否使用背景图片*/
	public $useImageBg = false;
	/**验证码字体大小(px)*/
	public $fontSize = 25;
	/**是否画混淆曲线*/
	public $useCurve = true;
	/**是否添加杂点*/
	public $useNoise = true;
	/**验证码图片宽度*/
	public $imageWidth = 0;
	/**验证码图片高度*/
	public $imageHeight = 0;
	/**验证码位数*/
	public $length = 5;
	/**背景颜色(RGB数组)*/
	public $bgcolors = [243,251,254];

	public $key = 'webpe';
	//验证码字符串
	private $enSet = '123678abcdefhijkmnpqrstuvwxyzABCDEFGHJKLMNPQRTUVWXY';
	//验证码字符串中文
	private $zhSet = '们以我到他会作时要动的一是工就年阶发成部可出能方进在了不和有大这中人上为来分生对于学下级地个用同行面说种过度而多子后自社加小机也经力线本电高量长得实家定深法表着水理化争现所二起政三好十无农使性前等体合路图把结第里正新开论之物从当两些还天资事队批点';
	private $reset   = true;// 验证成功后是否重置
	private $fontttf = '';  // 验证码字体路径
    private $_image = null; // 验证码图片实例
    private $_color = null; // 验证码字体颜色

    
    /**
     * 用路由注册该方法 (@|::)
     * Route::get('extend/captcha/[:id]', "\\webpe\\extend\\Captcha::controller");
     */
    public static function controller($id = ''){
        $captcha = new Captcha();
        $captcha->fontSize = 25;
    	$captcha->length = 5;
    	$captcha->useCurve = true;
    	$captcha->useNoise = true;
        return $captcha->entry($id);
    }

    /**
     * 验证验证码是否正确
     * @access public
     * @param string $code 用户验证码
     * @param string $id   验证码标识
     * @return bool 用户验证码是否正确
     */
    public function check($code, $id = '')
    {
        $key = $this->authcode($this->key) . $id;
        // 验证码不能为空
        $secode = Session::get($key, '');
        if (empty($code) || empty($secode)) {
            return false;
        }
        // session 过期
        if (time() - $secode['verify_time'] > $this->expire) {
            Session::delete($key, '');
            return false;
        }

        if ($this->authcode(strtoupper($code)) == $secode['verify_code']) {
            $this->reset && Session::delete($key, '');
            return true;
        }

        return false;
    }

    /**
     * 输出验证码并把验证码的值保存的session中
     * 验证码保存到session的格式为： array('verify_code' => '验证码值', 'verify_time' => '验证码创建时间');
     * @access public
     * @param string $id 要生成验证码的标识
     * @return \think\Response
     */
    public function entry($id = '')
    {
        // 图片宽(px)
        $this->imageWidth || $this->imageWidth = $this->length * $this->fontSize * 1.5 + $this->length * $this->fontSize / 2;
        // 图片高(px)
        $this->imageHeight || $this->imageHeight = $this->fontSize * 2.5;
        // 建立一幅 $this->imageWidth x $this->imageHeight 的图像
        $this->_image = imagecreate($this->imageWidth, $this->imageHeight);
        // 设置背景
        imagecolorallocate($this->_image, $this->bgcolors[0], $this->bgcolors[1], $this->bgcolors[2]);

        // 验证码字体随机颜色
        $this->_color = imagecolorallocate($this->_image, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150));
        // 验证码使用随机字体
        $this->fontttf = __DIR__ . '/captcha/' . ($this->useZh ? '/zh_font.ttf' : '/en_font'.mt_rand(1,4).'.ttf');

        if ($this->useImageBg) {
            $this->background();
        }

        if ($this->useNoise) {
            // 绘杂点
            $this->writeNoise();
        }
        if ($this->useCurve) {
            // 绘干扰线
            $this->writeCurve();
        }

        // 绘验证码
        $code   = []; // 验证码
        $codeNX = 0; // 验证码第N个字符的左边距
        if ($this->useZh) {
            // 中文验证码
            for ($i = 0; $i < $this->length; $i++) {
                $code[$i] = iconv_substr($this->zhSet, floor(mt_rand(0, mb_strlen($this->zhSet, 'utf-8') - 1)), 1, 'utf-8');
                imagettftext($this->_image, $this->fontSize, mt_rand(-40, 40), $this->fontSize * ($i + 1) * 1.5, $this->fontSize + mt_rand(10, 20), $this->_color, $this->fontttf, $code[$i]);
            }
        } else {
            for ($i = 0; $i < $this->length; $i++) {
                $code[$i] = $this->enSet[mt_rand(0, strlen($this->enSet) - 1)];
                $codeNX += mt_rand($this->fontSize * 1.2, $this->fontSize * 1.6);
                imagettftext($this->_image, $this->fontSize, mt_rand(-40, 40), $codeNX, $this->fontSize * 1.6, $this->_color, $this->fontttf, $code[$i]);
            }
        }

        // 保存验证码
        $key                   = $this->authcode($this->key);
        $code                  = $this->authcode(strtoupper(implode('', $code)));
        $secode                = [];
        $secode['verify_code'] = $code; // 把校验码保存到session
        $secode['verify_time'] = time(); // 验证码创建时间
        Session::set($key . $id, $secode, '');

        ob_start();
        // 输出图像
        imagepng($this->_image);
        $content = ob_get_clean();
        imagedestroy($this->_image);

        return response($content, 200, ['Content-Length' => strlen($content)])->contentType('image/png');
    }

    /**
     * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数)
     *
     *      高中的数学公式咋都忘了涅，写出来
     *        正弦型函数解析式：y=Asin(ωx+φ)+b
     *      各常数值对函数图像的影响：
     *        A：决定峰值（即纵向拉伸压缩的倍数）
     *        b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
     *        φ：决定波形与X轴位置关系或横向移动距离（左加右减）
     *        ω：决定周期（最小正周期T=2π/∣ω∣）
     *
     */
    private function writeCurve()
    {
        $px = $py = 0;

        // 曲线前部分
        $A = mt_rand(1, $this->imageHeight / 2); // 振幅
        $b = mt_rand(-$this->imageHeight / 4, $this->imageHeight / 4); // Y轴方向偏移量
        $f = mt_rand(-$this->imageHeight / 4, $this->imageHeight / 4); // X轴方向偏移量
        $T = mt_rand($this->imageHeight, $this->imageWidth * 2); // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0; // 曲线横坐标起始位置
        $px2 = mt_rand($this->imageWidth / 2, $this->imageWidth * 0.8); // 曲线横坐标结束位置

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageHeight / 2; // y = Asin(ωx+φ) + b
                $i  = (int)($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->_image, $px + $i, $py + $i, $this->_color); // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    $i--;
                }
            }
        }

        // 曲线后部分
        $A   = mt_rand(1, $this->imageHeight / 2); // 振幅
        $f   = mt_rand(-$this->imageHeight / 4, $this->imageHeight / 4); // X轴方向偏移量
        $T   = mt_rand($this->imageHeight, $this->imageWidth * 2); // 周期
        $w   = (2 * M_PI) / $T;
        $b   = $py - $A * sin($w * $px + $f) - $this->imageHeight / 2;
        $px1 = $px2;
        $px2 = $this->imageWidth;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if (0 != $w) {
                $py = $A * sin($w * $px + $f) + $b + $this->imageHeight / 2; // y = Asin(ωx+φ) + b
                $i  = (int)($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->_image, $px + $i, $py + $i, $this->_color);
                    $i--;
                }
            }
        }
    }

    /**
     * 画杂点
     * 往图片上写不同颜色的字母或数字
     */
    private function writeNoise()
    {
        $codeSet = '2345678abcdefhijkmnpqrstuvwxyz';
        for ($i = 0; $i < 6; $i++) {
            //杂点颜色
            $noiseColor = imagecolorallocate($this->_image, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
            for ($j = 0; $j < 3; $j++) {
                // 绘杂点
                imagestring($this->_image, 5, mt_rand(-10, $this->imageWidth), mt_rand(-10, $this->imageHeight), $codeSet[mt_rand(0, 29)], $noiseColor);
            }
        }
    }

    /**
     * 绘制背景图片
     * 注：如果验证码输出图片比较大，将占用比较多的系统资源
     */
    private function background()
    {
        $path = dirname(__FILE__) . '/captcha/bg';
        $bgfile = $path.mt_rand(1,8).'.jpg';
        list($width, $height) = @getimagesize($bgfile);
        $bgImage = @imagecreatefromjpeg($bgfile);
        @imagecopyresampled($this->_image, $bgImage, 0, 0, 0, 0, $this->imageWidth, $this->imageHeight, $width, $height);
        @imagedestroy($bgImage);
    }

    /* 加密验证码 */
    private function authcode($str)
    {
        $key = substr(md5($this->key), 5, 8);
        $str = substr(md5($str), 8, 10);
        return md5($key . $str);
    }


 
}