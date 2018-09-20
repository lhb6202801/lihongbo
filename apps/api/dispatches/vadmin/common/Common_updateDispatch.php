<?php

namespace api\dispatches\vadmin\common;

use api\base\Dispatch;

use common\helpers\QiniuHelper;


/**
 * Note: api
 * Package: api\dispatches\vadmin\common\Common_updateDispatch
 * Alias: common_update.dispatch
 * Classpath: common\Common_updateDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 图片上传
 * Parmas: image-base64iamge-图片,image_type-string-图片类型,
 * Return: msg-string-上传成功
 * Returnerr: null
 * Detail: 图片上传
 * Type: 图片上传
 */
class Common_updateDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['image'],$params['image_type'])){
            return $this->paramsErrorReturn();
        }
        $str = base64_encode(base64_decode($params['image'])) ? true : false;
        if(!$str){
            return $this->paramsErrorReturn();
        }
        $res = QiniuHelper::putImageFile($params['image'],$params['image_type']);
        if($res){
            $key = $res['key'];
            $url = QiniuHelper::getImageUrl($key);
            return $this->successReturn([
                'url'=>$url,
                'key'=>$key
            ]);
        }else{
            return $this->errorReturn(1001);
        }
    }
}