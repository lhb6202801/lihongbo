<?php
/**
 * Created by PhpStorm.
 * User: weishubao
 * Date: 17/8/15
 * Time: 下午15:50
 */
namespace common\models\redis;

class YtxCode extends \yii\redis\ActiveRecord
{
    public function attributes()
    {
        return [
            'id',
            'saler_id',
            'phone',
            'code' ,
            'time',
        ];
    }
}