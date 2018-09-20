<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/9
 * Time: 10:29
 */
namespace console\common;

class TestWorker extends \wh\asynctask\Worker
{
    protected static $queue = 'default';

    protected static $redis = 'asynctask';

    public static function run($a, $b)
    {
        echo "$a,$b\n";
        var_dump($a);
        var_dump($b);
    }

}