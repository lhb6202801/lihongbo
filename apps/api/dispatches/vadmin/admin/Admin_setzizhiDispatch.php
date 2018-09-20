<?php

namespace api\dispatches\vadmin\admin;

use api\base\Dispatch;

use common\models\License;


/**
 * Note: api
 * Package: api\dispatches\vadmin\admin\Admin_setzizhiDispatch
 * Alias: admin_setzizhi.dispatch
 * Classpath: admin\Admin_setzizhiDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 企业资质图片上传
 * Parmas: note-string-说明,license-string-图片key
 * Return: msg-string-上传成功
 * Returnerr: null
 * Detail: 企业资质图片上传
 * Type: 企业资质图片上传
 */
class Admin_setzizhiDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if (!isset($params['note'], $params['license'])) {
            return $this->paramsErrorReturn();
        }
        $license = new License();
        $license->companyId = $params['user_id'];
        $license->note = $params['note'];
        $license->license = $params['license'];
        if ($license->save(false)) {
            return $this->successReturn([
                'msg' => '上传成功'
            ]);
        }
        return $this->errorReturn(1007);
    }
}