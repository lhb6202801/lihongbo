<?php
/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 17/4/19
 * Time: 下午1:08
 */
namespace common\models\redis;

class SalerToken extends \common\components\RedisActiveRecord
{
    public function attributes()
    {
        return [
            'id',
            'nickname',
            'user_id',
            'session_key',
            'sex' ,
            'state',
            'avatar',
            'nickname',
            'token',
            'created_at'
        ];
    }
}