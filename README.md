改进PHPixie插件，使无法使用composer安装该插件的mvc框架使用此插件进行图片处理。
改进功能如下：
1.强制将图片调整为指定尺寸，不再保持原有宽高比
2.增加文字水印自动换行
3.图片水印设置水印透明度
<?php

#引入文件
require "PHPixie/Image.php";  


#实例化一个图片操作对象
$image = new \PHPixie\Image();

#创建图像
// 创建一个 宽100 高200 的透明白色图片
$img = $image->create(100, 200);

//创建一个款宽100 ，高200，红色  透明度 0.5的图片
$img = $image->create(100, 200, 0xff0000, 0.5);

#读取图像 
//直接从文件读取
$img = $image->read('./2.jpg');

// 通过file_get_contents 读取
$data = file_get_contents('./2.jpg');
$img = $image->load($data);


//#保存图像
$img->save('pixie.png');

//手动指定格式和图片质量
$img->save('pixie.jpg', 'jpg', 90);

#图像微缩
// 宽400 高等比例调整
$img->resize(400);

// 高200 宽等比例调整
$img->resize(null, 200);

// 最小尺寸（2：1）
$img->resize(200, 100);

// 最大尺寸 （2：1）
$img->resize(200, 100, false);


//强制调整为指定尺寸（不裁剪） 第四个参数为true即可
$img->resize(510,510,null,true);

//宽高放大2倍
$img->scale(2);


#图像裁剪
// 从(10,15) 点开始 裁剪 100*100的区域 
$img->crop(100, 100, 10, 15);

//连贯操作
$img->resize(100, 100,false)
 	  ->crop(100, 100) 
 	  ->save('avatar.png');


// 从中心裁剪 400 * 400 的区域
$img->fill(400, 400)->save('avatar.png'); 


#图像旋转
//逆时针旋转 45° 并且背景用透明度 0.5 的白色填充
$img->rotate(45, 0xffffff, 0.5);

#图像翻转
$img->flip(true); //flip horizontally  水平翻转
$img->flip(false, true); //flip vertically  垂直翻转
$img->flip(true, true); //flip bloth   水平 垂直都翻转

#图片叠加（图片水印）
$meadow = $image->read('./2.jpg');    //读取读片1
$fairy  = $image->read('./qq.png');  //读取图片2
$flower = $image->read('./weibo.png');  //读取图片3

$meadow->overlay($fairy, 40, 50)   //将图片2从图片1的（40，50）位置开始叠加
  ->overlay($flower, 100, 200,30)   //将图片3从图片1的（100,200）位置开始叠加 透明度 70 （透明度0-100 注：假如需要30的透明度，则需要传值 100-30=70；原PHPixie类使用的imagecopy方法进行的图片进行叠加，不支持透明度调整，而单纯使用imagecopymerge虽然支持透明度调整，但是背景色为透明的png图片会变成白色背景，此处为改进方法，但是透明度的值需要反着取，实际透明度即100-该值（结果必须在0-100之间））
 	->save('meadow2.png');  //保存图片


#图片叠加（扩展）
$large = $image->read('2.jpg');        //读取图片1
$small = $image->read('weibo.png');    //读取图片2（水印图）

//中间
//$x=($large->width())/2-($small->width())/2;
//$y=($large->height())/2-($small->height())/2;
// 左上角
//$x=0;
//$y=0;
//
//右上角
//$x=$large->width()-$small->width();
//$y=0;

//左下角
//$x=0;
//$y=$large->height()-$small->height();

//右下角
// $x=$large->width()-$small->width();
// $y=$large->height()-$small->height();

$canvas = $image->create($large->width(), $large->height());  //创建一个宽高与目标图片一致的透明图片
$canvas
  ->overlay($large)   //先将目标图片叠加
  ->overlay($small,$x,$y,80)  //将水印图在指定位置以指定透明度叠加
  ->save('merged.png');  //保存图片


#文字水印
$text="人生若只如初见，何事秋风悲画扇。等闲变却故人心，却道故人心易变。";
//text($text,$font,$x,$y,$colo=0x000000,$opacity=1,$angle=0,$clean=true,$wrapWidth=null,$charse='utf8'，$lineSpacing=1);
//将文字以 30字号，font.ttf字体  在（50，60）位置  颜色红色（16进制） 0.5的透明度（0-1,默认1）叠加到图片                                    
$img->text($text, 30, './fonts/hanyixiaolishufanti.ttf', 50, 60, 0xff0000, 0.5);

//自动换行 （改进功能，原插件是空格视为换行）
//透明度以后增加3个参数控制 $wrapWidth 行宽度（必填，默认null不换行）$clean（是否清除字符串中原有空格符，默认true）$charset（字符编码，默认utf8）   
$img->text($text, 30, './fonts/font.ttf', 0, 25, 0xff0000, 1,0,true,280,'utf8'); 


//换行时增加行间距 $lineSpacing(默认1倍行间距）
$img->text($text, 30, './fonts/font.ttf', 0, 25, 0xff0000, 0.8,0,true,280,'utf8',2);

//文字旋转（增加旋转角）
//（逆时针）旋转10°
$img->text($text, 30, './fonts/font.ttf', 50, 60, 0xff0000, 0.5,10,true,280,'utf8',1.5);



?>
