<?php
namespace extend\captcha;

class VCode
{
    protected $first = 0;      //第一个数
    protected $second = 0;     //第二个数
    protected $operator = '';    //运算符
    protected $code = '';       //运算后的验证码
    protected $useImgBg = false;        // 使用背景图片
    protected $fontSize = 32;           // 验证码字体大小(px)
    protected $useCurve = true;        // 是否画混淆曲线
    protected $useNoise = true;         // 是否添加杂点
    protected $width = 0;              // 验证码图片宽度
    protected $height = 0;              // 验证码图片高度
    protected $length = 6;              // 字符串长度
    protected $fontttf = 'FZXBSJW.TTF'; // 验证码字体，不设置随机获取
    protected $bg = NULL;// 背景颜色

    private $_image = NULL;     // 验证码图片实例
    private $_color = NULL;     // 干扰颜色
    private $_colorf = NULL;     // 验证码字体颜色

    private $num = 4;  //数量
    private $img;  //图像的资源

    //构造方法， 三个参数
    public function __construct()
    {
        $this->code = $this->createcode(); //调用自己的方法
    }

    /**
     * 设置验证码配置
     * @param  string $name 配置名称
     * @param  string $value 配置值
     */
    public function __set($name, $value)
    {
        if (isset($this->$name)) {
            $this->$name = $value;
        }
    }

    /**
     * 检查配置
     * @param  string $name 配置名称
     * @return bool
     */
    public function __isset($name)
    {
        return isset($this->$name);
    }

    //获取字符的验证码， 用于保存在服务器中
    public function getCode()
    {
        return $this->code;
    }

    //输出图像
    public function outImg()
    {
        //创建背景 (颜色， 大小， 边框)
        $this->createBack();
        //设置字体
        $this->_setTypeface();

        if ($this->useImgBg) {
            //设置背景图片
            //$this->_background();
        }

        if ($this->useNoise) {
            // 绘杂点
            $this->_writeNoise();
        }
        if ($this->useCurve) {
            // 绘干扰线
            $this->_writeCurve();
        }

        //画字 (大小， 字体颜色)
        $this->setColor();
        $this->outString();


        //输出图像
        return $this->printImg();
    }

    //创建背景
    private function createBack()
    {
        // 图片宽(px)
        $this->width || $this->width = $this->length * $this->fontSize * 1.2 + $this->length * $this->fontSize / 2;

        // 图片高(px)
        $this->height || $this->height = $this->fontSize * 2.5;

        //创建资源
        $this->_image = imagecreate($this->width, $this->height);

        // 设置背景
        if($this->bg) {
            $bg = $this->hex2rgb($this->bg);
            $this->bg = explode(',', $bg);
        } else {
            $this->bg = array(243, 251, 254);
        }
        imagecolorallocate($this->_image, $this->bg[0], $this->bg[1], $this->bg[2]);

        // 验证码字体随机颜色
        $this->_color = imagecolorallocate($this->_image, mt_rand(1, 150), mt_rand(1, 150), mt_rand(1, 150));
    }

    /**
     * 验证码字体
     * */
    private function setColor() {
        if(!$this->_colorf) {
            return;
        }
        $color = $this->hex2rgb($this->_colorf);
        $colora = explode(',', $color);
        $this->_color = imagecolorallocate($this->_image, $colora[0], $colora[1], $colora[2]);
    }

    /**
     * 十六进制颜色转rgb
     * */
    private function hex2rgb($hex){
        $hex=str_replace('#','',$hex);
        $length=strlen($hex);
        $rgb='';
        if($length==3){
            $rgb.=hexdec($hex[0].$hex[0]).',';
            $rgb.=hexdec($hex[1].$hex[1]).',';
            $rgb.=hexdec($hex[2].$hex[2]);
        }
        if($length==6){
            $rgb.=hexdec($hex[0].$hex[1]).',';
            $rgb.=hexdec($hex[2].$hex[3]).',';
            $rgb.=hexdec($hex[4].$hex[5]);
        }
        return $rgb;
    }

    //画字
    private function outString1()
    {
        $this->first = rand(1, 10);
        $this->second = rand(1, 10);
        $this->operator = $this->first > $this->second ? '减' : '加';
        $this->code = $this->first > $this->second ? $this->first - $this->second : $this->first + $this->second;

        $codeNX = 0; // 验证码第N个字符的左边距
        $string = $this->first . $this->operator . $this->second . '=?';
        $codeArr = preg_split('/(?<!^)(?!$)/u', $string );
        foreach($codeArr as $value) {
            $codeNX += mt_rand($this->fontSize * 1.2, $this->fontSize * 1.6);
            imagettftext($this->_image, $this->fontSize, mt_rand(-40, 40), $codeNX, $this->fontSize * 1.6, $this->_color, $this->fontttf, $value);
        }
    }

    //画字
    private function outString()
    {
        $codeNX = 0; // 验证码第N个字符的左边距
        $codeArr = preg_split('/(?<!^)(?!$)/u', $this->code );
        foreach($codeArr as $value) {
            $codeNX += mt_rand($this->fontSize * 1.6, $this->fontSize * 1.8);
            imagettftext($this->_image, $this->fontSize*1.8, mt_rand(-40, 40), $codeNX, $this->fontSize * 1.8, $this->_color, $this->fontttf, $value);
        }
    }

    /**
     * 画一条由两条连在一起构成的随机正弦函数曲线作干扰线(你可以改成更帅的曲线函数)
     *      正弦型函数解析式：y=Asin(ωx+φ)+b
     *      各常数值对函数图像的影响：
     *        A：决定峰值（即纵向拉伸压缩的倍数）
     *        b：表示波形在Y轴的位置关系或纵向移动距离（上加下减）
     *        φ：决定波形与X轴位置关系或横向移动距离（左加右减）
     *        ω：决定周期（最小正周期T=2π/∣ω∣）
     *
     */
    private function _writeCurve()
    {
        $px = $py = 0;

        // 曲线前部分
        $A = mt_rand(1, $this->height / 2);                  // 振幅
        $b = mt_rand(-$this->height / 4, $this->height / 4);   // Y轴方向偏移量
        $f = mt_rand(-$this->height / 4, $this->height / 4);   // X轴方向偏移量
        $T = mt_rand($this->height, $this->width * 2);  // 周期
        $w = (2 * M_PI) / $T;

        $px1 = 0;  // 曲线横坐标起始位置
        $px2 = mt_rand($this->width / 2, $this->width * 0.8);  // 曲线横坐标结束位置

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + $this->height / 2;  // y = Asin(ωx+φ) + b
                $i = (int)($this->fontSize / 5);
                while ($i > 0) {
                    imagesetpixel($this->_image, $px + $i, $py + $i, $this->_color);  // 这里(while)循环画像素点比imagettftext和imagestring用字体大小一次画出（不用这while循环）性能要好很多
                    $i--;
                }
            }
        }

        // 曲线后部分
        $A = mt_rand(1, $this->height / 2);                  // 振幅
        $f = mt_rand(-$this->height / 4, $this->height / 4);   // X轴方向偏移量
        $T = mt_rand($this->height, $this->width * 2);  // 周期
        $w = (2 * M_PI) / $T;
        $b = $py - $A * sin($w * $px + $f) - $this->height / 2;
        $px1 = $px2;
        $px2 = $this->width;

        for ($px = $px1; $px <= $px2; $px = $px + 1) {
            if ($w != 0) {
                $py = $A * sin($w * $px + $f) + $b + $this->height / 2;  // y = Asin(ωx+φ) + b
                $i = (int)($this->fontSize / 5);
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
    private function _writeNoise()
    {
        $codeSet = '123456789';
        for ($i = 0; $i < 10; $i++) {
            //杂点颜色
            $noiseColor = imagecolorallocate($this->_image, mt_rand(150, 225), mt_rand(150, 225), mt_rand(150, 225));
            for ($j = 0; $j < 5; $j++) {
                // 绘杂点
                imagestring($this->_image, 15, mt_rand(-10, $this->width), mt_rand(-10, $this->height), $codeSet[mt_rand(0, 6)], $noiseColor);
            }
        }
    }

    /**
     * 绘制背景图片
     * 注：如果验证码输出图片比较大，将占用比较多的系统资源
     */
    private function _background()
    {
//        $path = dirname(__FILE__) . '/Verify/bgs/';
        $path = dirname(__FILE__);      //背景图片地址，后续可改
        $dir = dir($path);

        $bgs = array();
        while (false !== ($file = $dir->read())) {
            if ($file[0] != '.' && substr($file, -4) == '.jpg') {
                $bgs[] = $path . $file;
            }
        }
        $dir->close();

        $gb = $bgs[array_rand($bgs)];

        list($width, $height) = @getimagesize($gb);
        // Resample
        $bgImage = @imagecreatefromjpeg($gb);
        @imagecopyresampled($this->_image, $bgImage, 0, 0, 0, 0, $this->width, $this->height, $width, $height);
        @imagedestroy($bgImage);
    }

    /**
     * 设置字体
     * */
    private function _setTypeface()
    {
        // 验证码使用随机字体
        $ttfPath = dirname(__FILE__). '/fonts/';

        if (empty($this->fontttf)) {
            $dir = dir($ttfPath);
            $ttfs = array();
            while (false !== ($file = $dir->read())) {
                if ($file[0] != '.' && substr($file, -4) == '.ttf') {
                    $ttfs[] = $file;
                }
            }
            $dir->close();
            $this->fontttf = $ttfs[array_rand($ttfs)];
        }
        $this->fontttf = $ttfPath . $this->fontttf;
    }

    //输出图像
    private function printImg()
    {
        /*ob_start ();
        imagejpeg ($this->_image);
        $image_data = ob_get_contents ();
        ob_end_clean ();
        $image_data_base64 = base64_encode ($image_data);
        return $image_data_base64;*/

        ob_start();
        imagejpeg($this->_image);
        $fileContent = ob_get_contents();
        ob_end_clean();

        return 'data:image/jpeg;base64,'.base64_encode($fileContent);
        /*if (imagetypes() & IMG_GIF) {
            header("Content-type: image/gif");
            imagegif($this->_image);
        } elseif (function_exists("imagejpeg")) {
            header("Content-type: image/jpeg");
            imagegif($this->_image);
        } elseif (imagetypes() & IMG_PNG) {
            header("Content-type: image/png");
            imagegif($this->_image);
        } else {
            die("No image support in this PHP server");
        }*/

    }

    //生成验证码字符串
    private function createCode()
    {
        $codes = "3456789abcdefghijkmnpqrstuvwxyABCDEFGHIJKLMNPQRSTUVWXY";

        $code = "";

        for ($i = 0; $i < $this->num; $i++) {
            $code .= $codes{rand(0, strlen($codes) - 1)};
        }

        return $code;
    }

    //用于自动销毁图像资源
    function __destruct()
    {
        imagedestroy($this->_image);
    }

}
