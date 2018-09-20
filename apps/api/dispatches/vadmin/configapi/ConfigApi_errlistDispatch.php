<?php

namespace api\dispatches\vadmin\configapi;

use api\base\Dispatch;
use common\models\ConfigApiError;

/**
 * Note: api
 * Package: api\dispatches\vadmin\configapi\ConfigApi_errlistDispatch
 * Alias: configapi_errlist.dispatch
 * Classpath: configapi\ConfigApi_errlistDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 获取apierr列表
 * Parmas: start-int-页数,limit-int-每页数量
 * Return: model-json-apierr列表
 * Returnerr: null
 * Detail: 获取apierr列表
 * Type: 获取apierr列表
 */
class ConfigApi_errlistDispatch extends Dispatch
{
    public function run()
    {
          $params = $this->params;
          if(!isset($params['start'],$params['limit'])){
              return $this->paramsErrorReturn();
          }
          $start = intval($params['start']);
          $limit = intval($params['limit']);
          $start = $start * $limit;
          $apilist = ConfigApiError::find()->limit($limit)->offset($start)->asArray()->all();
          $total = ConfigApiError::find()->count();
          return $this->successReturn([
              'model' => $apilist,
              'total'=>$total,
              'msg'=>'获取成功'
          ]);
    }
}