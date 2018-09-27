<?php
namespace api\dispatches\vadmin\auction;

use api\base\Dispatch;


use common\models\Auction;



/**
 * Note: api
 * Package: api\dispatches\auction\auction\Auction_bindDispatch
 * Alias: auction_bind.dispatch
 * Classpath: auction\Auction_bindDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖会绑定拍卖师单独接口
 * Parmas: id-string-拍卖会id,teacherid-string-拍卖师id
 * Return: msg-json-删除成功
 * Returnerr: null
 * Detail: 拍卖会绑定拍卖师单独接口
 * Type: 拍卖会绑定拍卖师单独接口
 */
class Auction_bindDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['id'],$params['teacherid'])){
            return $this->paramsErrorReturn();
        }
        $auction = Auction::find()->where(['id'=>$params['id']])->one();
        if(is_null($auction)){
            return $this->errorReturn(1008);
        }
        $auction->teacherid = $params['teacherid'];
        if($auction->save(false)){
            return $this->successReturn([
                'msg' => '绑定成功'
            ]);
        }
        return $this->errorReturn(1009);
    }
}