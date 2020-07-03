<?php
date_default_timezone_set('Asia/Shanghai');


$_config = require_once('../config.inc.php');
if (!$_config['安装']) {
    header("Location: ../install/");
    die();
}


if ($_config['数据库']['类型'] === 'mysql') {
    if ($_config['数据库']['方式'] === 'pdo') {
        require_once('../class/pdo.class.php');
        new sql('mysql');
    }

    if ($_config['数据库']['方式'] === 'mysqli') {
        require_once('../class/mysqli.class.php');
        new sql;
    }
}

if ($_config['数据库']['类型'] === 'sqlite') {

    $_config['数据库']['地址'] = __DIR__ . '/db/' . $_config['数据库']['地址'];
    if ($_config['数据库']['方式'] === 'pdo') {
        require_once('../class/pdo.class.php');
        new sql('sqlite');
    }

    if ($_config['数据库']['方式'] === 'sqlite3') {
        require_once('../class/sqlite3.class.php');
        new sql;
    }
}


function showmessage($code = 0, $mes = null)
{
    $json = [
        'code' => $code,
        'data' => $mes
    ];
    die(json_encode($json));
}

