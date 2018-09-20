<?php
/**
 * 微信公众号ticket
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 20:39
 */
namespace common\models\redis;

class WxJsapiTicket extends \yii\redis\ActiveRecord
{
    public function attributes()
    {
        return ['id','jsapi_ticket'];
    }
}