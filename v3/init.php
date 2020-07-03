<?php
if (!$_config['安装']){
    header("Location: install/");
    die();
}

date_default_timezone_set('Asia/Shanghai'); 
header("Access-Control-Allow-Origin: ". AllowOrigin($_config['允许url']));  
header("Content-Type:application/json; charset=utf-8");

require_once('class/exception.class.php');

if ($_config['数据库']['类型'] === 'mysql'){
    if ($_config['数据库']['方式'] === 'pdo'){
        require_once('class/pdo.class.php');
        new DB('mysql');
    }
    
    if ($_config['数据库']['方式'] === 'mysqli'){
        require_once('class/mysqli.class.php');
        new DB;
    }

} elseif ($_config['数据库']['类型'] === 'sqlite'){
    
    $_config['数据库']['地址'] = __DIR__.'/db/'.$_config['数据库']['地址'];
    if ($_config['数据库']['方式'] === 'pdo'){
        require_once('class/pdo.class.php');
        new DB('sqlite');
    } 
    
    if ($_config['数据库']['方式'] === 'sqlite3'){
        require_once('class/sqlite3.class.php');
        new DB;
    }
}

function AllowOrigin($domains = []){
    if (empty($_SERVER['HTTP_ORIGIN'])) return '*';
    if (empty($domains)) return '*';
    $domain = $domains[0];
    
    foreach ($domains as $v) {
        if($v == $_SERVER['HTTP_ORIGIN']) {
            $domain = $v;
            break;
        }
    }

    return $domain;
}

function getIp(){
    global $_config ;
    if($_config['is_cdn']){
        if(preg_match('/,/',$_SERVER['HTTP_X_FORWARDED_FOR'])){
            return array_pop(explode(',',$_SERVER['HTTP_X_FORWARDED_FOR']));
        } else {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        }
    } else{
        return $_SERVER['REMOTE_ADDR'];
    }
}