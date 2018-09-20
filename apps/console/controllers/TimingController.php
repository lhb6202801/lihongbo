<?php
namespace console\controllers;

/**
 * 定时执行的功能
 * Created by PhpStorm.
 * User: limingming
 * Date: 17/7/13
 * Time: 下午3:17
 */
use common\models\Dictionary;
use common\models\Discount;
use common\models\Reward;
use common\models\Saler;
use common\models\TimingLog;
use yii\console\Controller;
use Yii;

class TimingController extends Controller
{

    /*
     * 获取30个下属的当月保费的百分之一
     * 经商定为需求理解错误
     * */
    public function actionCommossion()
    {
        $sql = 'select count(a.id) as count,b.id as parent from t_saler a join t_saler b on(a.parent = b.id) where a.issue_count>0 and b.issue_count>0 group by a.parent';
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        $dictionary = Dictionary::find()->where(['type' => 1003])->asArray()->one();
        foreach ($result as $value) {
            if ($value['count'] > $dictionary['value']) {
                $sql = 'select sum(c.jqx) as jqx,sum(c.ccsys) as ccsys,sum(c.sy) as sy ';
                $sql .= ' from (t_bills a left join t_saler b on(a.saler_id = b.id)) left join t_reply c on(a.count_id = c.count_id) ';
                $sql .= ' where b.parent=' . $value['parent'] . ' and a.state = 3 and (unix_timestamp(now())>=a.confirmd_at) and a.confirmd_at>= unix_timestamp(date_sub(now(),interval 1 month))  group by b.parent';
            }
            $command = $connection->createCommand($sql);
            $result = $command->queryAll();
            $rewards = [];
            foreach ($result as $key => $item) {
                $rewards[$key]['saler_id'] = $value['parent'];
                $rewards[$key]['created_at'] = time();
                $rewards[$key]['reward_where_id'] = -1;
                $rewards[$key]['money'] = floatval($item['jqx']) + floatval($item['sy']);
            }
            //var_dump($rewards);exit();
            Yii::$app->db->createCommand()->batchInsert(Reward::tableName(), ['saler_id', 'created_at', 'reward_where_id', 'money'],
                $rewards
            )->execute();
        }
    }

    /*
     * 每天执行自动减一
     * */
    public function actionDay()
    {
        $salers = Saler::find()->where(['>=', 'issue_count', 1])->all();
        foreach ($salers as $key => $saler) {
            //先减一   判断是不是 等于1  如果等于1   对上级减少1
            //小于上个月
            $lastMonth = date(strtotime("last month"));
            if ($saler->issue_date < $lastMonth) {
                if ($saler->issue_count == 1) {
                    $parent = Saler::find()->where(['id' => $saler->parent])->one();
                    if(!is_null($parent)){
                        $parent->level -= 1;
                        $parent->save();
                    }
                }
                $saler->issue_date = time();
                $saler->issue_count -= 1;
                $saler->save();
            }
        }
        $tl = new TimingLog();
        $tl->date = time();
        $tl->content = '业务员级别自动处理';
        $tl->save();
    }
}