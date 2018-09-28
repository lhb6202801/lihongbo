<?php
namespace api\dispatches\vfroned\phonecode;

use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\models\redis\YtxCode;
use common\models\Wx;

/**
 * Note: api
 * Package: api\dispatches\vfroned\phonecode\Phonecode_getcodeDispatch
 * Alias: phonecode_getcode.dispatch
 * Classpath: phonecode\Phonecode_getcodeDispatch
 * Version: froned
 * Allowed: true
 * Istoken: false
 * Describe: 获取手机验证码
 * Parmas: phone-string-手机号
 * Return: msg-string-发送结果
 * Returnerr: null
 * Detail: 获取手机验证码
 * Type: 获取手机验证码
 */
class Phonecode_getcodeDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['phone'])){
            return $this->paramsErrorReturn();
        }
        $phone = $params['phone'];
        $isphone = Wx::find()->where(['phone'=>$phone])->one();
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
            return $this->errorReturn(1016);
        }
        return $this->errorReturn(1017);
    }
}