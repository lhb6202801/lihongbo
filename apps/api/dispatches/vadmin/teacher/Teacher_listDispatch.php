<?php
namespace api\dispatches\vadmin\teacher;

use api\base\Dispatch;

use common\models\Teacher;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vadmin\teacher\Teacher_listDispatch
 * Alias: teacher_list.dispatch
 * Classpath: teacher\Teacher_listDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖师列表
 * Parmas: start-int-页数,limit-int-每页数量
 * Return: model-json-拍卖师列表
 * Returnerr: null
 * Detail: 拍卖师列表
 * Type: 拍卖师列表
 */
class Teacher_listDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['start'],$params['limit'])){
            return $this->paramsErrorReturn();
        }
        //拍卖会主体信息
        $start = intval($params['start']);
        $limit = intval($params['limit']);
        $start = $start * $limit;

        $sql = "select * from t_teacher where companyId=".$params['user_id']." order by id desc limit ".$limit." offset ".$start;
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $teachers = $command->queryAll();
        $total =  Teacher::find()->where(['companyId'=>$params['user_id']])->count();
        return $this->successReturn([
            'models' => $teachers,
            'total'=>$total,
            'msg'=>'获取成功'
        ]);
    }
}