<?php

// NOTE: Make sure this file is not accessible when deployed to production
if (!in_array(@$_SERVER['REMOTE_ADDR'], ['127.0.0.1', '::1'])) {
    die('You are not allowed to access this file.');
}

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'test');

defined('VENDOR_DIR') or define('VENDOR_DIR', __DIR__ . '/../../../vendor');
defined('TESTS_DIR') or define('TESTS_DIR', __DIR__ . '/../../../tests');
defined('APPS_DIR') or define('APPS_DIR', __DIR__ . '/../../../apps');
defined('ROOT_DIR') or define('ROOT_DIR', __DIR__ . '/../../..');

require(VENDOR_DIR. '/autoload.php');
require(VENDOR_DIR. '/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');


$config = require(TESTS_DIR .'/codeception/config/api/acceptance.php');

(new yii\web\Application($config))->run();
