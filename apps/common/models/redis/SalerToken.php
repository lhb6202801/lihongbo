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
            'saler_id',
            'wx_id',
            'sex' ,
            'state',
            'avatar',
            'nickname',
            'access_token',
            'expires_in',
            'refresh_token',
            'create_at',
            'agency_id',
            'level'
        ];
    }
}