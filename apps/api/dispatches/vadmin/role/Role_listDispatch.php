<?php

namespace api\dispatches\vadmin\role;

use api\base\Dispatch;

use common\models\Role;
use common\models\Menu;


/**
 * Note: api
 * Package: api\dispatches\vadmin\role\Role_listDispatch
 * Alias: role_list.dispatch
 * Classpath: role\Role_listDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 角色列表
 * Parmas: start-int-页数,limit-int-每页数量
 * Return: role-stirng-角色列表
 * Returnerr: null
 * Detail: 角色列表
 * Type: 角色列表
 */
class Role_listDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if (!isset($params['start'], $params['limit'])) {
            return $this->paramsErrorReturn();
        }
        $start = intval($params['start']);
        $limit = intval($params['limit']);
        $start = $start * $limit;
        $roles = Role::find()->select('name,menus,id')->limit($limit)->offset($start)->orderBy('id DESC')->asArray()->all();
        $total = Role::find()->count();
        return $this->successReturn([
            'roles' => $roles,
            'total' => $total
        ]);
        return $this->errorReturn(1007);
    }
}