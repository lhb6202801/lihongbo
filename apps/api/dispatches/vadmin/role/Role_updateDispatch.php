<?php
namespace api\dispatches\vadmin\role;

use api\base\Dispatch;

use common\models\Role;


/**
 * Note: api
 * Package: api\dispatches\vadmin\role\Role_updateDispatch
 * Alias: role_update.dispatch
 * Classpath: role\Role_updateDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 角色修改
 * Parmas: rolename-string-角色名称, menus-stirng-权限列表,roleid-string-角色ID
 * Return: msg-json-添加成功
 * Returnerr: null
 * Detail: 角色修改
 * Type: 角色修改
 */
class Role_updateDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['rolename'],$params['menus'],$params['roleid'])){
            return $this->paramsErrorReturn();
        }
        $menulist = $params['menus'];
        $rolename = $params['rolename'];
        //组建数组
        $role = Role::find()->where(['id'=>$params['roleid']])->one();
        if(is_null($role)){
            return $this->errorReturn(1008);
        }
        $role->name = $rolename;
        $role->menus = implode(',',$menulist);
        if($role->save(false)){
            return $this->successReturn([
                'msg' => '修改成功'
            ]);
        }
        return $this->errorReturn(1009);
    }
}