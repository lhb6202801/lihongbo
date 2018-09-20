<?php
namespace api\dispatches\vadmin\login;

use api\base\Dispatch;
use api\helpers\ApiHelper;
use common\models\Admin;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vadmin\login\LoginDispatch
 * Alias: login.dispatch
 * Classpath: login\LoginDispatch
 * Version: admin
 * Allowed: true
 * Istoken: false
 * Describe: 管理员登陆
 * Parmas: username-string-用户账号, password-stirng-用户密码
 * Return: model-json-用户信息, token-string-用户token
 * Returnerr: null
 * Detail: 管理员登陆
 * Type: 管理员登陆
 */
class LoginDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['username'],$params['password'])){
            return $this->paramsErrorReturn();
        }
        $model = Admin::find()->where(['username'=>$params['username']])->andWhere(['<>','state','3'])->one();
        if($model){
            $bool = $model->validatePassword($params['password']);
            if($bool){

                 //生成token
                 $tokenClass = ApiHelper::getTokenClass($this->version);
                 if (empty($tokenClass)) {
                    return $this->errorReturn(1005);
                 }
                $tmp =  new $tokenClass;
                $data = $tokenClass::find()
                    ->where(['user_id' => $model['id']])
                    ->asArray()
                    ->one();
                if(!is_null($data)){
                    $tokenClass::deleteAll(['user_id' => $model['id']]);
                }
                $data =$tmp;
                $data->token = Yii::$app->security->generateRandomString();
                $data->created_at = time();
                $data->user_id = $model['id'];
                $data->insert();
                return $this->successReturn([
                    'model' => $model,
                    'token'=>$tmp->token
                ]);
            }else{
                return $this->errorReturn(1001);
            }
        }else{
            return $this->errorReturn(1000);
        }
    }
}