<?php
/**
 * 微信公众号token
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/5/5
 * Time: 20:39
 */
namespace common\models\redis;

class WxAccessToken extends \yii\redis\ActiveRecord
{
    public function attributes()
    {
        return ['id','agency_id','app_id','app_secret','access_token'];
    }
}