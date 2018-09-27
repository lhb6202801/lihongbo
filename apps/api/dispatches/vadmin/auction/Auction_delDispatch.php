<?php
namespace api\dispatches\vadmin\auction;

use api\base\Dispatch;


use common\models\Auction;
use Yii;
use common\helpers\QiniuHelper;


/**
 * Note: api
 * Package: api\dispatches\auction\auction\Auction_delDispatch
 * Alias: auction_del.dispatch
 * Classpath: auction\Auction_delDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖会删除
 * Parmas: id-string-拍卖会id
 * Return: msg-json-删除成功
 * Returnerr: null
 * Detail: 拍卖会删除
 * Type: 拍卖会删除
 */
class Auction_delDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['id'])){
            return $this->paramsErrorReturn();
        }
        $auction = Auction::find()->where(['id'=>$params['id']])->one();
        if(is_null($auction)){
            return $this->errorReturn(1008);
        }
        $auction->state = 0;
        if($auction->save(false)){
            return $this->successReturn([
                'msg' => '删除成功'
            ]);
        }
        return $this->errorReturn(1010);
    }
}