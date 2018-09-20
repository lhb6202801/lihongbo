<?php
namespace api\dispatches\vadmin\role;

use api\base\Dispatch;

use common\models\Role;


/**
 * Note: api
 * Package: api\dispatches\vadmin\role\Role_delDispatch
 * Alias: role_del.dispatch
 * Classpath: role\Role_delDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 角色删除
 * Parmas: roleid-string-角色id
 * Return: msg-json-删除成功
 * Returnerr: null
 * Detail: 角色删除
 * Type: 角色删除
 */
class Role_delDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['roleid'])){
            return $this->paramsErrorReturn();
        }
        $role = Role::find()->where(['id'=>$params['roleid']])->one();
        if(is_null($role)){
            return $this->errorReturn(1008);
        }
        if($role->delete()){
            return $this->successReturn([
                'msg' => '删除成功'
            ]);
        }
        return $this->errorReturn(1009);
    }
}