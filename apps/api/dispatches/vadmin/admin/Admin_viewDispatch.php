<?php

namespace api\dispatches\vadmin\admin;

use api\base\Dispatch;

use common\models\Admin;
use common\models\License;
use common\models\Role;


/**
 * Note: api
 * Package: api\dispatches\vadmin\admin\Admin_viewDispatch
 * Alias: admin_view.dispatch
 * Classpath: admin\Admin_viewDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 管理员详情
 * Parmas: adminid-int-管理员id
 * Return: admin-array-管理员详情,license-array-企业图片上传,
 * Returnerr: null
 * Detail: 管理员详情
 * Type: 管理员详情
 */
class Admin_viewDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if (!isset($params['adminid'])) {
            return $this->paramsErrorReturn();
        }
        $admins = Admin::find()->where(['id'=>$params['adminid']])->one();
        if(is_null($admins)){
            return $this->errorReturn(1008);
        }
        //获取角色名称
        $role = Role::find()->where(['id'=>$admins->roleids])->one();
        if(is_null($role)){
            $admins->roleids = '未设置角色';
        }else{
            $admins->roleids = $role->name;
        }
        //获取资质
        $license = License::find()->where(['companyId'=>$params['adminid']])->one();
        return $this->successReturn([
            'admin' => $admins,
            'license' => $license
        ]);
        return $this->errorReturn(1007);
    }
}