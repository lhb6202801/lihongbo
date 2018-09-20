<?php

namespace api\dispatches\vadmin\admin;

use api\base\Dispatch;

use common\models\Admin;
use common\helpers\QiniuHelper;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vadmin\admin\Admin_listDispatch
 * Alias: admin_list.dispatch
 * Classpath: admin\Admin_listDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 管理员列表
 * Parmas: start-int-页数,limit-int-每页数量
 * Return: admins-array-管理员列表,total-string-总数
 * Returnerr: null
 * Detail: 管理员列表
 * Type: 管理员列表
 */
class Admin_listDispatch extends Dispatch
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
        $sql = "select a.id as id,a.username as username,a.state as state,a.linkman as linkman,a.linkphone as linkphone,a.companyName as companyName,l.license as license,l.note as note from t_admin as a left join t_license as l on l.companyId = a.id  order by a.created_at desc limit ".$limit." offset ".$start;
        //$admins = Admin::find()->select('id,username,state,linkman,linkphone,companyName')->limit($limit)->offset($start)->orderBy('created_at DESC')->asArray()->all();
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $admins = $command->queryAll();
        foreach ($admins as $key=>$value){
            if($value['license'] != null){
                $admins[$key]['license'] = QiniuHelper::getImageUrl($value['license']);
            }
        }
        $total = Admin::find()->count();
        return $this->successReturn([
            'admins' => $admins,
            'total' => $total
        ]);
        return $this->errorReturn(1007);
    }
}