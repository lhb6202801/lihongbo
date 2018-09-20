<?php

namespace api\dispatches\vadmin\admin;

use api\base\Dispatch;

use common\models\Admin;


/**
 * Note: api
 * Package: api\dispatches\vadmin\admin\Admin_setroleDispatch
 * Alias: admin_setrole.dispatch
 * Classpath: admin\Admin_setroleDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 管理员设置角色
 * Parmas: adminid-int-管理员id,roleid-int-角色id,
 * Return: msg-string-设置成功
 * Returnerr: null
 * Detail: 管理员设置角色
 * Type: 管理员设置角色
 */
class Admin_setroleDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if (!isset($params['adminid'], $params['roleid'])) {
            return $this->paramsErrorReturn();
        }
        $adminid = intval($params['adminid']);
        $roleid = intval($params['roleid']);
        $admins = Admin::find()->where(['id'=>$adminid])->one();
        if(is_null($admins)){
            return $this->errorReturn(1008);
        }
        $admins->roleids = $roleid;
        if($admins->save(false)) {
            return $this->successReturn([
                'msg' => '设置成功'
            ]);
        }
        return $this->errorReturn(1007);
    }
}