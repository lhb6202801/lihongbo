<?php
namespace console\controllers;

/**
 * Created by PhpStorm.
 * User: limingming
 * Date: 17/8/1
 * Time: 下午3:17
 */
use common\models\Bills;
use common\models\Saler;
use yii\console\Controller;

class DataController extends Controller
{
    public function actionIssueDate()
    {
        $salers = Saler::find()->all();
        foreach ($salers as $key => $value) {
            $max = Bills::find()->select('max(confirmd_at) as max')->where(['state'=>3,'saler_id'=>$value->id])->asArray()->one();
            $value->issue_date = $max['max'];
            $value->save(false);
        }
    }
}