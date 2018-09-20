<?php
date_default_timezone_set('PRC');
// 自动加载相关
require_once __DIR__ . '/Lib/Autoloader.php';
// 常用的函数
require_once __DIR__ . '/Lib/Functions.php';
// GatewayClient，用于在php后台向客户端推送数据以及获取在线用户信息
require_once __DIR__ . '/Lib/GatewayClient/Gateway.php';

use common\components\laychat\Lib;
// 聊天服务器注册地址，如果聊天服务在其它服务器，请填写对应服务器的ip和端口
Lib\GatewayClient\Gateway::$registerAddress = '127.0.0.1:1238';

