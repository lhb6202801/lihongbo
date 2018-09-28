<?php
namespace api\dispatches\vadmin\auction;

use api\base\Dispatch;


use common\models\Auction;
use common\models\AuctionInviter;
use Yii;
use common\helpers\QiniuHelper;


/**
 * Note: api
 * Package: api\dispatches\auction\auction\Auction_peoplelistDispatch
 * Alias: auction_peoplelist.dispatch
 * Classpath: auction\Auction_peoplelistDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖会竞拍人列表
 * Parmas: auctionId-string-拍卖会ID
 * Return: model-json-拍卖会竞拍人列表
 * Returnerr: null
 * Detail: 拍卖会竞拍人列表
 * Type: 拍卖会竞拍人列表
 */
class Auction_peoplelistDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['auctionId'])){
            return $this->paramsErrorReturn();
        }
        $auctionId = intval($params['auctionId']);

        $sql = "select  from t_auction_inviter as ir left join t_inviterSeting as is on is.auctionId = ".$auctionId." and is.wxId = ir.wxId where ir.auctionId=".$auctionId." and ir.type=0";

        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $auctioninviters = $command->queryAll();
//        foreach ($auctions as $key=>$value){
//            if($value['images'] != null){
//                $admins[$key]['images'] = QiniuHelper::getImageUrl($value['images']);
//            }
//        }
        $total =  AuctionInviter::find()->where(['auctionId'=>$auctionId,'type'=>0])->count();
        return $this->successReturn([
            'models' => $auctioninviters,
            'total'=>$total,
            'msg'=>'获取成功'
        ]);
    }
}