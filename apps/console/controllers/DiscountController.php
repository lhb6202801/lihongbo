<?php
namespace console\controllers;

use common\models\AgencyDiscount;
use yii\console\Controller;
use yii;

/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 16/9/27
 * Time: 下午12:02
 * 点位到期自动失效的功能
 */
class DiscountController extends Controller
{
    public function actionState()
    {
        $discount = AgencyDiscount::find()->asArray()->all();
        $trans = Yii::$app->db->beginTransaction();
        $tt = strtotime(date('Y-m-d',time()));
        if($discount){
            foreach($discount as $key => $val){
                if($val['end_time'] < $tt){
                    $model = AgencyDiscount::find()->where(['id' => $val['id']])->one();
                    $model->state = 0;
                    $model->save(false);
                }
            }
        }
        $trans->commit();
    }
}