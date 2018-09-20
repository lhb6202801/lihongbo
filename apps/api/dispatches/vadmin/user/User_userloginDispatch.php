<?php
namespace api\dispatches\vadmin\user;

use api\base\Dispatch;
use abei2017\wx\Application;
use Yii;



/**
 * Note: api
 * Package: api\dispatches\vadmin\user\User_userloginDispatch
 * Alias: user_userlogin.dispatch
 * Classpath: user\User_userloginDispatch
 * Version: admin
 * Allowed: true
 * Istoken: false
 * Describe: 用户登陆
 * Parmas: code-string-用户注册
 * Return: model-json-用户信息, token-string-用户token
 * Returnerr: null
 * Detail: 用户登陆
 * Type: 用户登陆
 */
class User_userloginDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['code'])){
            return $this->paramsErrorReturn();
        }
        $app = new Application(['conf'=>Yii::$app->params['wx']['mini']]);
        $user = $app->driver("mini.user");
        $result = $user->codeToSession($params['code']);
        //$info = $user->info($result->userinfo->openid);

        return $this->successReturn([
            'userinfo' => $result
        ]);
    }
}