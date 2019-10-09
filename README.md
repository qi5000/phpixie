```
改进PHPixie插件，使无法使用composer安装该插件的mvc框架使用此插件进行图片处理。

改进功能如下：
1.强制将图片调整为指定尺寸，不再保持原有宽高比

2.增加文字水印自动换行

3.图片水印设置水印透明度

4.增加直接处理base64的图片数据

5.增加实时处理图片数据后并已base64返回（不在服务端存储）



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

//输出图像
$img->show();

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

#新增（扩展）客户端上传的图片不在服务端保存，直接进行处理的解决方案

//1.直接处理js以base64数据流传输到后台的图片数据，数据中须包含（data:image/jpeg;base64,  部分）
//eg:"data:image/jpeg;base64,/9j/4AAQSkZJRgABAQAAAQABAAD/2wBDAAgGBgcGBQgHBw";

//注意：base64格式的图片数据使用post一些的ajax异步传给服务端的数据中的 '+' 会被替换为 英文空格符,服务端在使用前需要替换回去

$base64="data:image/png;base64,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";

$image=new \PHPixie\Image();

$img=$image->read($base64,true);  //新增第二个参数  默认false   true：声明是base64的图片数据

$img->save('result.png');  //处理图像  所有连贯操作均可用

//2.直传到服务器端的图片处理

$image = new \PHPixie\Image();

$data = file_get_contents($_FILES['file']['tmp_name']);   //使用file_get_content 直接将临时文件读取

$img = $image->load($data);

$img->resize(500,200,null,true)->save('upload.png');  //处理图像  所有连贯操作均可用



//将图片保存为base64数据 (带 "data:image/png;base64," 头，)

//$result=$img->save_string()
?>
```
