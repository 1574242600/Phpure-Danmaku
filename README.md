# Phpure-Danmaku

用于垃圾php虚拟主机的dplayer弹幕后端,哪怕垃圾的连mysql不支持也没关系
因为还有sqlite，如果连sqlite都不支持，那么请联系我，我去跟服务商客服对线

(因为某些虚拟主机不支持伪静态，所以我直接用的目录)

## 安装要求
php 版本 > 5.3 
pdo 或 sqlite3 或 mysqli 模块


## 安装步骤
 1. 上传到网站目录
 2. 用浏览器访问
 3. 按照提示安装（别吐槽前端）
 4. 完事

## 演示

demo:[https://demo.nworm.cf/danmu/](https://demo.nworm.cf/danmu/)
比如:
https://demo.nworm.cf/danmu/v3/?id=23333
https://demo.nworm.cf/danmu/v3/bilibili/?cid=100577238  (B站弹幕暂时还不支持用aid)

## 注意事项
1.用了cdn，请一定要把config.inc.php中的is_cdn改为1
2.如果要限制跨域，请修改config.inc.php的允许url,如果此项为空，则不限制跨的域
3.发送频率默认限制为60秒内5条 ，如果要修改，算了，改哪个文件都猜的到了


## 版权说明
该项目签署了MIT 授权许可，详情请参阅 LICENSE

## 管理面板
我觉得我不会写
