<?php
namespace api\dispatches\vfroned\bind;

use api\base\Dispatch;
use common\models\redis\YtxCode;
use common\models\Wx;


/**
 * Note: api
 * Package: api\dispatches\vfroned\bind\Bind_userDispatch
 * Alias: bind_user.dispatch
 * Classpath: bind\Bind_userDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 绑定用户信息
 * Parmas: phone-string-手机号,code-string-手机验证码
 * Return: msg-string-绑定成功
 * Returnerr: null
 * Detail: 绑定用户信息
 * Type: 绑定用户信息
 */
class Bind_userDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['phone'],$params['code'])){
            return $this->paramsErrorReturn();
        }
        $iscode =  YtxCode::find()->where(['phone'=>$params['phone'],'code'=>$params['code']])->one();
        if($iscode->time < time()){
            return $this->errorReturn(1013);
        }
        if(!is_null($iscode)){
            //验证码通过
           $wx =  Wx::find()->where(['id'=>$params['user_id']])->one();
           if(is_null($wx)){
               return $this->errorReturn(1014);
           }
           $wx->phone = $params['phone'];
           if($wx->save(false)){
               $iscode->delete();
               return $this->successReturn([
                   'msg'=>'绑定成功'
               ]);
           }
        }
    }
}