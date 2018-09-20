<?php

namespace api\dispatches\vadmin\common;

use api\base\Dispatch;

use common\helpers\QiniuHelper;


/**
 * Note: api
 * Package: api\dispatches\vadmin\common\Common_delDispatch
 * Alias: common_del.dispatch
 * Classpath: common\Common_delDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 图片删除
 * Parmas: key-string-图片key
 * Return: msg-string-删除成功
 * Returnerr: null
 * Detail: 图片删除
 * Type: 图片删除
 */
class Common_delDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['key'])){
            return $this->paramsErrorReturn();
        }
        $res = QiniuHelper::deleteImageFile($params['key']);
        if($res){
            return $this->errorReturn(1001,null,[
                'err' => $res
            ]);
        }else{
            return $this->successReturn([
                'msg'=>$res
            ]);
        }
    }
}