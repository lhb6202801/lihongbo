<?php

namespace api\dispatches\vadmin\admin;

use api\base\Dispatch;

use common\models\Admin;


/**
 * Note: api
 * Package: api\dispatches\vadmin\admin\Admin_setstateDispatch
 * Alias: admin_setstate.dispatch
 * Classpath: admin\Admin_setstateDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 设置管理员状态
 * Parmas: adminid-int-管理员id,state-int-状态(0待审核1审核通过2审核失败3异常关闭),
 * Return: msg-string-设置成功
 * Returnerr: null
 * Detail: 设置管理员状态
 * Type: 设置管理员状态
 */
class Admin_setstateDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if (!isset($params['adminid'], $params['state'])) {
            return $this->paramsErrorReturn();
        }
        $adminid = intval($params['adminid']);
        $state = intval($params['state']);
        $admins = Admin::find()->where(['id'=>$adminid])->one();
        if(is_null($admins)){
            return $this->errorReturn(1008);
        }
        $admins->state = $state;
        if($admins->save(false)) {
            return $this->successReturn([
                'msg' => '设置成功'
            ]);
        }
        return $this->errorReturn(1007);
    }
}