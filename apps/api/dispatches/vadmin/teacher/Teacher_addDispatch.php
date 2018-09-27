<?php
namespace api\dispatches\vadmin\teacher;

use api\base\Dispatch;

use common\models\Teacher;


/**
 * Note: api
 * Package: api\dispatches\vadmin\teacher\Teacher_addDispatch
 * Alias: teacher_add.dispatch
 * Classpath: teacher\Teacher_addDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖师添加
 * Parmas: username-string-拍卖师姓名,number-string-证书编号,phone-string-联系电话
 * Return: msg-json-添加成功
 * Returnerr: null
 * Detail: 拍卖师添加
 * Type: 拍卖师添加
 */
class Teacher_addDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['username'],$params['number'],$params['phone'])){
            return $this->paramsErrorReturn();
        }
        //拍卖会主体信息
        $teacher = new Teacher();
        $teacher->username = $params['username'];
        $teacher->number = $params['number'];
        $teacher->phone = $params['phone'];
        $teacher->companyId = $params['user_id'];
        if($teacher->save(false)){
            return $this->successReturn(['msg' => '添加成功']);
        }
        return $this->errorReturn(1007);
    }
}