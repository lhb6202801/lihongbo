<?php
namespace console\controllers;

use common\models\redis\SalerToken;
use common\models\Saler;
use common\models\Document;

use yii\console\Controller;
use wx\helpers\ResponsePassive;
use wx\helpers\TemplateMessage;
use yii\helpers\ArrayHelper;
use common\helpers\CommonDocument;
/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 17/4/19
 * Time: 下午2:11
 */
class TestController extends Controller
{
    public function actionTest()
    {
       $str = 'a 12345';
       $str = trim($str);
      echo strtoupper($str);
    }

    public function actionSendMsg()
    {
       // $data = array(
         //   'first' => array('value'=>'4052','color'=>'red'),
        //    'title' => array('value'=>'黑A88888','color'=>'red'),
         //   'company' => array('value'=>'哈尔滨中保','color'=>'red'),
         //   'project' => array('value'=>'交强险（车船使用税）','color'=>'red'),
         //   'type' => array('value'=>'新保','color'=>'red'),
        //    'time' => array('value'=>'2017-8-21 15:41:23','color'=>'red'),
         //   'status' => array('value'=>'已回复','color'=>'red'),
       //     'remark' => array('value'=>'点击查看','color'=>'red')
      //  );
     
      //$doc = new Document();
      //foreach ($doc as $key => $value) {
      //    if($key != 'id'){
       //     $array[] = $key;
      //    }
     // }
      //foreach ($doc as $key => $value) {
      //  if($key != 'id'){
      //      $arrayvalue[] = $value;
      //  }
      //}
      $doc = CommonDocument::GetIsGrant(40,40,2,1);

      var_dump($doc);
        //ozIfgw8QKLRSKA4i3TfYzcrlCBpE  个人测试账号魏述宝openid
        //ozIfgw7zb_JrGpYFvQh_fkhQu3cs
      //  $msg = TemplateMessage::sendTemplateMessage($data,'ozIfgw8QKLRSKA4i3TfYzcrlCBpE','HINEpXgZDogk5mD_ZofSlnAd40P3oo-FZuA3Anxmr_o',null);
     //   var_dump($msg);
        //ResponsePassive::text('gh_2cd052a54d69','ozIfgw7zb_JrGpYFvQh_fkhQu3cs','测试信息');
    }
}