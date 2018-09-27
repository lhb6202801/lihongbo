<?php
namespace api\dispatches\vadmin\teacher;

use api\base\Dispatch;

use common\models\Teacher;


/**
 * Note: api
 * Package: api\dispatches\vadmin\teacher\Teacher_delDispatch
 * Alias: teacher_del.dispatch
 * Classpath: teacher\Teacher_delDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖师删除
 * Parmas: id-string-拍卖师id
 * Return: msg-json-添加成功
 * Returnerr: null
 * Detail: 拍卖师删除
 * Type: 拍卖师删除
 */
class Teacher_delDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'])){
            return $this->paramsErrorReturn();
        }
        $teacher = Teacher::find()->where(['id'=>$params['id']])->one();
        if(is_null($teacher)){
            return $this->errorReturn(1008);
        }
        if($teacher->delete()){
            return $this->successReturn(['msg' => '删除成功']);
        }
        return $this->errorReturn(1010);
    }
}