<?php

namespace api\dispatches\vadmin\login;

use api\base\Dispatch;
use common\models\Admin;

/**
 * Note: api
 * Package: api\dispatches\vadmin\login\Change_passwordDispatch
 * Alias:  change_password.dispatch
 * Classpath: login\Change_passwordDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 管理员修改密码
 * Parmas: password-string-新密码, token-stirng-用户token
 * Return: msg-string-信息
 * Returnerr: null
 * Detail: 管理员修改密码
 * Type: 管理员修改密码
 */
class Change_passwordDispatch extends Dispatch
{
    public function run()
    {
        $params = $this->params;
        if(!isset($params['password'])){
            return $this->paramsErrorReturn();
        }
        $user_id = $params['user_id'];
        $model = Admin::find()->where(['id'=>$user_id,'state'=>1])->one();
        if($model){
            //$bool = $model->validatePassword($params['password']);
            //if($bool){
                $model->setPassword($params['password']);
                if($model->save()){
                    return $this->successReturn([
                        'msg' => '修改成功'
                    ]);
                }
            //}else{
            //    return $this->errorReturn(1001);
            //}
        }else{
            return $this->errorReturn(1000);
        }
    }
}