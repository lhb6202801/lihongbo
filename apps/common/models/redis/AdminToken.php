<?php
/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 17/4/19
 * Time: 下午1:08
 */
namespace common\models\redis;

class AdminToken extends \common\components\RedisActiveRecord
{
    public function attributes()
    {
        return [
            'id',
            'created_at',
            'token',
            'user_id'
        ];
    }
}