<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/9
 * Time: 10:29
 */
namespace console\common;
use yii;
use common\helpers\QiniuHelper;

class WriteWorker extends \wh\asynctask\Worker
{
    protected static $queue = 'default';

    protected static $redis = 'asynctask';

    public static function run($i)
    {
        $cacheDate = Yii::$app->cache;

        $cacheDate->set('json', time());

        QiniuHelper::putJsonFile($cacheDate->get('json'));
    }
}