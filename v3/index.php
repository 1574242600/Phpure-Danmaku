<?php
ini_set("display_errors", "On");
error_reporting(-1);

$_config = require_once('config.inc.php');
require_once('init.php');
require_once('main.php');

set_exception_handler('topException');

$main = new DMain();

$main->addMethod('GET',function($setResponse){
    $Danmaku = new Danmaku();
    
    if(empty($_GET['id']) or !is_numeric($_GET['id'])) {
        return DMain::$errorResponse = [
            'code'=> 400, 
            'msg' => 'Param error'
        ];
    }
    
    $id =  $_GET['id'];
    $data = $Danmaku->弹幕池($id);

    
    if(!$data) {
        return $setResponse([
            'code'=> 0,
            'data'=> []
        ]);
    }

    $setResponse([
        'code'=> 0,
        'data'=> $data
    ]);

});

$main->addMethod('POST', function($setResponse){
    $Danmaku = new Danmaku();

    $postDataArray = json_decode(file_get_contents('php://input'),true);

    if($Danmaku->检查频率() === false){
        $Danmaku->添加弹幕($postDataArray);

        return $setResponse([
            'code'=> 0,
            'msg'=> true
        ]);

    } else {

        return $setResponse([
            'code'=> 400,
            'msg'=> '你tm发送的太频繁了,请问你单身几年了？'
        ]);
    }

});

$main->addMethod('OPTIONS', function($setResponse){
    //header("Access-Control-Allow-Credentials: true");  暂时不会用到
    header("Access-Control-Allow-Methods: POST, GET, OPTIONS");  //允许的请求方法
    header("Access-Control-Allow-Headers: content-type");   //允许携带的首部字段
    $setResponse([
        'code' => 0, 
        'data'=> true
    ]);
});

echo $main->run();


function topException($exception)
{
    die(
        json_encode([
            'code'=> 500,
            'msg'=> 'Uncaught Exception: '. $exception->getMessage()
        ])
    );

}
