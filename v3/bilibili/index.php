<?php
//ini_set("display_errors", "On");
error_reporting(0);

//https://api.bilibili.com/x/v2/dm/history?type=1&date=xxxx-xx-xx&oid=xxxxx  历史弹幕接口，需要cookies

header("Content-Type:application/json; charset=utf-8");
header("Access-Control-Allow-Origin: *");

if(!empty($_GET['bv'])) $_GET['av'] = bvDec($_GET['bv']);

$cid = is_numeric($_GET['cid']) ? $_GET['cid'] : 0;
$av = is_numeric($_GET['av']) ? $_GET['av'] : 0;
$p = is_numeric($_GET['p']) ? $_GET['p'] : 1;

if ($cid > 0) {
    $xml = get('https://api.bilibili.com/x/v1/dm/list.so?oid=' . $cid);
    echo xml_json($xml);
} elseif ($av > 0 and $cid <= 0) {
    $cid = get_cid($av, $p);
    $xml = get('https://api.bilibili.com/x/v1/dm/list.so?oid=' . $cid);
    echo xml_json($xml);
} else {
    echo '{"code":1,"data":"参数错误"}';
}


function get($url, $gzip=1)
{
    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($curl, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
    if ($gzip) curl_setopt($curl, CURLOPT_ENCODING, "gzip");
    $content = curl_exec($curl);
    curl_close($curl);
    return $content;
}

function xml_json($xml)
{
    $xml = simplexml_load_string($xml);//将文件转换成 对象
    $xmljson = json_encode($xml);
    $data = json_decode($xmljson, true);

    foreach ($data['d'] as $k => $v) {
        foreach ($xml->d[$k]->attributes() as $_v) {
            $data['d'][$k] = [$v];
            $data['d'][$k][] = explode(",", (string)$_v);
        }
    }

    $data = $data['d'];
    $json = ['code' => 0, 'data' => []];
    foreach ($data as $k => $v) {
        $json['data'][$k][] = (float)$v[1][0];  //弹幕出现时间(s)
        //弹幕样式

        if ($v[1][1] <= 3) { //滚动
            $json['data'][$k][] = 0;
        }

        if ($v[1][1] == 5) { //顶端
            $json['data'][$k][] = 1;
        }

        if ($v[1][1] == 4) { //底端
            $json['data'][$k][] = 2;
        }

        $json['data'][$k][] = (int)$v[1][3]; //字体的颜色

        $json['data'][$k][] = "";  //bilibili 弹幕发送者的ID
        $json['data'][$k][] = $v[0];  //弹幕文本
        //$json['total'] = $k + 1;  //就当它不存在吧，弹幕总数
    }

    return json_encode($json);
}

function get_cid($av, $p)
{
    $data = json_decode(get('https://api.bilibili.com/x/player/pagelist?aid=' . $av), true);
    if (empty($data['data'][$p - 1]['cid'])) {
        die('{"code":1,"data":"参数错误"}');
    }
    return $data['data'][$p - 1]['cid'];
}

function bvDec($x)
{
    $table = 'fZodR9XQDSUm21yCkr6zBqiveYah8bt4xsWpHnJE7jL5VG3guMTKNPAwcF';
    $tr = [];
    for ($i = 0; $i !== 58; $i++){
	    $tr[$table[$i]] = $i;
    }
    $s = [11,10,3,8,4,6];
    $add = 8728348608;
    $r = 0;
	for ($i = 0; $i !== 6; $i++){
		$r += $tr[$x[$s[$i]]]*pow(58,$i);
    }
	return ($r-$add)^177451812;
}
   
