<?php
class Captcha{

    private $captcha;
    private $cold;
    public $gif;

    public function __construct()
    {
        $this->createImageCaptcha();
        $_SESSION['captcha']=$this->cold;
        $this->gif = '<img id="captchaimg" src="'.App::$url.'img/imgcapcha.php?img='.$this->captcha.'" >';
    }

    private function createImageCaptcha()
    {
        // create a 100*30 image
        $im = imagecreate(88, 20);
        // random colored background and text
        $bg = imagecolorallocate($im, rand(200,255) , rand(200,255), rand(200,255));
        $textcolor = imagecolorallocate($im, rand(0,85), rand(0,85), rand(0,85));
        // write the random 4 digits number at a random locaton (x= 0-20, y=0-20),
        $random=rand(1000,9999);
        $black = imagecolorallocate($im,rand(100,200),rand(100,200),rand(100,200));
        imagearc($im, rand(0,80), rand(0,10), 100, 10, 0, 360, $black);
        imagearc($im, rand(0,80), rand(0,10), 10, 100, 0, 360, $black);
        imagearc($im, rand(0,80), rand(0,10), 20, 20, 0, 360, $black);
        imageline($im, rand(40,80), 20, rand(0,80), rand(0,20), $black);
        imagestring($im, rand(5,8), rand(1,51), rand(1,7), $random , $textcolor);
        $filenametemp=WEBROOT.'img/'.time().'.gif';
        ImageGIF($im, $filenametemp);
        $ImageData = file_get_contents($filenametemp);
        $this->captcha = $filenametemp;
        $this->cold = $random;
    }
}
?>
