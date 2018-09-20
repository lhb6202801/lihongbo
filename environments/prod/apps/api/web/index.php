<?php
defined('YII_DEBUG') or define('YII_DEBUG', false);
defined('YII_ENV') or define('YII_ENV', 'prod');

defined('VENDOR_DIR') or define('VENDOR_DIR', __DIR__ . '/../../../vendor');
defined('APPS_DIR') or define('APPS_DIR', __DIR__ . '/../../../apps');
defined('ROOT_DIR') or define('ROOT_DIR', __DIR__ . '/../../..');

require(VENDOR_DIR .'/autoload.php');
require(VENDOR_DIR .'/yiisoft/yii2/Yii.php');
require(__DIR__ . '/../../common/config/bootstrap.php');
require(__DIR__ . '/../config/bootstrap.php');

$config = yii\helpers\ArrayHelper::merge(
    require(__DIR__ . '/../../common/config/main.php'),
    require(__DIR__ . '/../../common/config/main-local.php'),
    require(__DIR__ . '/../config/main.php'),
    require(__DIR__ . '/../config/main-local.php')
);

$application = new yii\web\Application($config);
$application->run();
