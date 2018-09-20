<?php

namespace api\dispatches\vadmin\configapi;

use api\base\Dispatch;
use common\models\ConfigApiDispatch;

/**
 * Note: api
 * Package: api\dispatches\vadmin\configapi\ConfigApi_viewDispatch
 * Alias: configapi_view.dispatch
 * Classpath: configapi\ConfigApi_viewDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 获取api详情
 * Parmas: id-int-数据id, token-stirng-用户token
 * Return: model-json-api详情
 * Returnerr: null
 * Detail: 获取api详情
 * Type: 获取api详情
 */
class ConfigApi_viewDispatch extends Dispatch
{
    public function run()
    {
          $params = $this->params;
          if(!isset($params['id'])){
              return $this->paramsErrorReturn();
          }
          $apimodel = ConfigApiDispatch::find()->where(['id'=>$params['id']])->asArray()->all();
          return $this->successReturn([
              'model' => $apimodel,
              'msg'=>'获取成功'
          ]);
    }
}