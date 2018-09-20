<?php

namespace api\dispatches\vadmin\configapi;

use api\base\Dispatch;
use common\models\ConfigApiDispatch;

/**
 * Note: api
 * Package: api\dispatches\vadmin\configapi\ConfigApi_listDispatch
 * Alias: configapi_list.dispatch
 * Classpath: configapi\ConfigApi_listDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 获取api列表
 * Parmas: start-int-页数,limit-int-每页数量
 * Return: model-json-api列表
 * Returnerr: null
 * Detail: 获取api列表
 * Type: 获取api列表
 */
class ConfigApi_listDispatch extends Dispatch
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
          $apilist = ConfigApiDispatch::find()->select('id,dispatch,class,version,allowed,token,description')->limit($limit)->offset($start)->orderBy('id DESC')->asArray()->all();
          $total = ConfigApiDispatch::find()->count();
          return $this->successReturn([
              'model' => $apilist,
              'total'=>$total,
              'msg'=>'获取成功'
          ]);
    }
}