<?php
namespace api\dispatches\vadmin\teacher;

use api\base\Dispatch;

use common\models\Teacher;


/**
 * Note: api
 * Package: api\dispatches\vadmin\teacher\Teacher_updateDispatch
 * Alias: teacher_update.dispatch
 * Classpath: teacher\Teacher_updateDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖师修改
 * Parmas: id-string-拍卖师id,username-string-拍卖师姓名,number-string-证书编号,phone-string-联系电话
 * Return: msg-json-修改成功
 * Returnerr: null
 * Detail: 拍卖师修改
 * Type: 拍卖师修改
 */
class Teacher_updateDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'],$params['username'],$params['number'],$params['phone'])){
            return $this->paramsErrorReturn();
        }
        //拍卖会主体信息
        $teacher = Teacher::find()->where(['id'=>$params['id']])->one();
        if(is_null($teacher)){
            return $this->errorReturn(1008);
        }
        $teacher->username = $params['username'];
        $teacher->number = $params['number'];
        $teacher->phone = $params['phone'];
        if($teacher->save(false)){
            return $this->successReturn(['msg' => '修改成功']);
        }
        return $this->errorReturn(1009);
    }
}