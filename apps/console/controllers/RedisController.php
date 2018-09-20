<?php
namespace console\controllers;

use common\models\redis\SalerToken;
use common\models\Saler;
use yii\console\Controller;

/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 17/4/19
 * Time: 下午2:11
 */
class RedisController extends Controller
{
    public function actionCreate()
    {
        $salerToken = SalerToken::find()->where(['saler_id'=>'103'])->one();
        if(!is_null($salerToken)){
            SalerToken::deleteAll(['saler_id' => '103']);
        }
        $salerToken = new SalerToken();
        $salerToken->saler_id = "103";
        $salerToken->wx_id = 'wx_id';
        $salerToken->sex = 'sex';
        $salerToken->state = 'state';
        $salerToken->avatar = 'avatar';
        $salerToken->nickname = 'nickname';
        $salerToken->access_token = 'access_token';
        $salerToken->expires_in = 'expires_in';
        $salerToken->refresh_token = 'refresh_token';
        $salerToken->create_at = time();
        $salerToken->insert();
        
        //$salerToken = SalerToken::find()->where(['wx_id' => 'ozIfgw80UvlY4xmU4CAec70rIy3U'])->one();
       // if (is_null($salerToken)) {
        //    $saler = Saler::find()->where(['wx_id' => 'ozIfgw80UvlY4xmU4CAec70rIy3U'])->one();
        //    $salerToken = new SalerToken();
            //$salerToken->id = $saler->id;
        //    $salerToken->wx_id = $saler->wx_id;
        //    $salerToken->insert();
       // }
    }
    public function actionDelete()
    {
        SalerToken::deleteAll();
        
        //$salerToken = SalerToken::find()->where(['wx_id' => 'ozIfgw80UvlY4xmU4CAec70rIy3U'])->one();
       // if (is_null($salerToken)) {
        //    $saler = Saler::find()->where(['wx_id' => 'ozIfgw80UvlY4xmU4CAec70rIy3U'])->one();
        //    $salerToken = new SalerToken();
            //$salerToken->id = $saler->id;
        //    $salerToken->wx_id = $saler->wx_id;
        //    $salerToken->insert();
       // }
    }
}