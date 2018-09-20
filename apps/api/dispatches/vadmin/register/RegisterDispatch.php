<?php
namespace api\dispatches\vadmin\register;

use api\base\Dispatch;
use common\models\Admin;
use common\models\redis\YtxCode;

/**
 * Note: api
 * Package: api\dispatches\vadmin\register\RegisterDispatch
 * Alias: register.dispatch
 * Classpath: register\RegisterDispatch
 * Version: admin
 * Allowed: true
 * Istoken: false
 * Describe: 管理员注册
 * Parmas: username-string-用户账号, password-stirng-用户密码, linkman-stirng-联系人, linkphone-stirng-联系电话, companyName-stirng-公司名称, citycode-stirng-城市代码, provincecode-stirng-省份代码, code-stirng-短信验证码
 * Return: model-json-用户信息, token-string-用户token
 * Returnerr: null
 * Detail: 管理员注册
 * Type: 管理员注册
 */
class RegisterDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['username'],$params['password'],$params['linkman'],$params['linkphone'],$params['companyName'],$params['citycode'],$params['provincecode'],$params['code'])){
            return $this->paramsErrorReturn();
        }
        $username = $params['username'];
        $password = $params['password'];
        $linkman = $params['linkman'];
        $linkphone = $params['linkphone'];
        $companyName = $params['companyName'];
        $citycode = $params['citycode'];
        $provincecode = $params['provincecode'];
        $code  = $params['code'];
        $iscode =  YtxCode::find()->where(['phone'=>$linkphone,'code'=>$code])->one();
        if($iscode->time < time()){
            return $this->errorReturn(1003);
        }
        if(!is_null($iscode)){
            //判断用户名是否重复
            $isusername = Admin::find()->where(['username'=>$username])->one();
            if(!is_null($isusername)){
                return $this->errorReturn(1005);
            }
            $iscompanyName = Admin::find()->where(['companyName'=>$companyName])->one();
            if(!is_null($iscompanyName)){
                return $this->errorReturn(1006);
            }
            $admin = new Admin();
            $admin->username = $username;
            $admin->setPassword($password);
            $admin->linkman = $linkman;
            $admin->linkphone = $linkphone;
            $admin->companyName = $companyName;
            $admin->provincecode = $provincecode;
            $admin->citycode = $citycode;
            $admin->state = 0;
            $admin->created_at = time();
            $admin->updated_at = time();
            if($admin->save(false)){
                return $this->successReturn([
                    'msg' => '注册成功请等待后台审核'
                ]);
            }

        }else{
            return $this->errorReturn(1003);
        }
    }
}