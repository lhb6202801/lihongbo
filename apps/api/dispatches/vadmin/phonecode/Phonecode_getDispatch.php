<?php
namespace api\dispatches\vadmin\phonecode;

use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\models\Admin;
use common\models\redis\YtxCode;

/**
 * Note: api
 * Package: api\dispatches\vadmin\phonecode\Phonecode_getDispatch
 * Alias: phonecode_get.dispatch
 * Classpath: phonecode\Phonecode_getDispatch
 * Version: admin
 * Allowed: true
 * Istoken: false
 * Describe: 获取手机验证码
 * Parmas: phone-string-手机号
 * Return: msg-string-发送结果
 * Returnerr: null
 * Detail: 获取手机验证码
 * Type: 获取手机验证码
 */
class Phonecode_getDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['phone'])){
            return $this->paramsErrorReturn();
        }
        $phone = $params['phone'];
        $isphone = Admin::find()->where(['linkphone'=>$phone])->one();
        if(is_null($isphone)) {
            $code = CommonHelper::randString(6);
            $res = CommonHelper::sendCodeByYtx($phone,$code);
            if($res){
                $ytxcode = new YtxCode();
                $ytxcode->phone = $phone;
                $ytxcode->code = $code;
                $ytxcode->time = time() + 20 * 60;
                $ytxcode->save();
                return $this->successReturn([
                    'msg' => '短信验证吗发送成功'
                ]);
            }
        }else{
            return $this->errorReturn(1004);
        }
        return $this->errorReturn(1002);
    }
}