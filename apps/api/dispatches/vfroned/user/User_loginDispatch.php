<?php
namespace api\dispatches\vfroned\user;

use api\base\Dispatch;
use abei2017\wx\Application;
use common\models\Wx;
use api\helpers\ApiHelper;
use Yii;



/**
 * Note: api
 * Package: api\dispatches\vfroned\user\User_loginDispatch
 * Alias: user_login.dispatch
 * Classpath: user\User_loginDispatch
 * Version: froned
 * Allowed: true
 * Istoken: false
 * Describe: 用户登陆
 * Parmas: code-string-用户注册
 * Return: model-json-用户信息, token-string-用户token
 * Returnerr: null
 * Detail: 用户登陆
 * Type: 用户登陆
 */
class User_loginDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['code'])){
            return $this->paramsErrorReturn();
        }
        $nickname = $params['nickname'];
        $avatarurl = $params['avatarurl'];
        $gender = $params['gender'];
        $city = $params['city'];
        $province = $params['province'];
        $app = new Application(['conf'=>Yii::$app->params['wx']['mini']]);
        $user = $app->driver("mini.user");
        $result = $user->codeToSession($params['code']);
        $openid = $result['openid'];
        $session_key = $result['session_key'];
        $userinfo = Wx::find()->where(['wx_id'=>$openid])->one();
        if(is_null($userinfo)){
            //新用户
            $userinfo = new Wx();
            $userinfo->nickname = $nickname;
            $userinfo->avatar = $avatarurl;
            $userinfo->sex = $gender;
            $userinfo->address = $province.'-'.$city;
            $userinfo->created_at = time();
            $userinfo->wx_id = $openid;
            $userinfo->save(false);
        }
        //缓存信息
        $tokenClass = ApiHelper::getTokenClass($this->version);
        if (empty($tokenClass)) {
            return $this->errorReturn(1005);
        }
        $salertoken = $tokenClass::find()->where(['id'=>$userinfo->id])->one();
        if(!is_null($salertoken)){
            $salertoken->delete();
        }
        $salertoken =  new $tokenClass;
        $salertoken->id = $userinfo->id;
        $salertoken->nickname = $userinfo->nickname;
        $salertoken->sex = $userinfo->sex;
        $salertoken->session_key = $session_key;
        $salertoken->created_at = time();
        $salertoken->state = time();
        $salertoken->avatar = $userinfo->avatar = $avatarurl;
        $salertoken->access_token = Yii::$app->security->generateRandomString();
        $salertoken->insert();
        return $this->successReturn([
            //'userinfo' => $userinfo,
            'token' =>$salertoken->access_token
        ]);
    }
}