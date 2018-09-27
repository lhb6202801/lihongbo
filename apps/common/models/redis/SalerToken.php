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
            'session_key',
            'sex' ,
            'state',
            'avatar',
            'nickname',
            'access_token',
            'created_at'
        ];
    }
}