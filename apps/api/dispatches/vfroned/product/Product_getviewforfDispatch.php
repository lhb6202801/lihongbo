<?php

namespace api\dispatches\vfroned\product;

use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\helpers\QiniuHelper;
use common\models\AuctionProduct;
use common\models\ProductImages;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vfroned\product\Product_getviewforfDispatch
 * Alias: product_getviewfrof.dispatch
 * Classpath: product\Product_getviewforfDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 获取标的详情
 * Parmas: id-string-标的id
 * Return: models-string-标的目录
 * Returnerr: null
 * Detail: 获取标的详情
 * Type: 获取标的详情
 */
class Product_getviewforfDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'])){
            return $this->paramsErrorReturn();
        }
        $sql = "select tap.id as id,tap.state as state,tap.name as productname,tap.beginValue as beginValue,tap.stepValue as stepValue,tap.introduce as introduce,tap.freeOffer as freeOffer,tap.timeLimitOffer as timeLimitOffer,tne.title as notestitle,tne.content as notescontent,tnc.noticeName as noticetitle,tnc.content as noticescontent from t_auction_product as tap left join t_auction as ta on ta.id = tap.auctionId left join t_notes as tne on tne.id = ta.notesId left join t_notice as tnc on tnc.id = ta.noticeId where tap.id = ".$params['id'];
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $models = $command->queryAll();
        if(is_null($models)){
            return $this->errorReturn(1019);
        }
        $images = ProductImages::find()->where(['productId'=>$params['id']])->all();
        foreach ($images as $item => $value) {
            if ($value['images'] != null) {
                $images[$item]['images'] = QiniuHelper::getImageUrl($value['images']);
            }
        }
        if(count($images)<=0){
            $images = '';
        }
        //获取竞价记录
        //$records = OfferRecord::find()->where(['auctionProductId'=>$params['id']])->all();
        $sqlinfo = "select tor.id as id,tor.state as state,tor.biddersNumber as biddersNumber,tor.offer as offer,tor.offerDate as offerDate,wx.nickname as nickname from t_offer_record as tor left join t_wx as wx on wx.id = tor.wxId where tor.auctionProductId=".$params['id'];
        $command = $connection->createCommand($sqlinfo);
        $records = $command->queryAll();
        if(count($records)<=0){
            $records='';
        }
        //回传身份
        $iy = '';
        $aup = AuctionProduct::find()->where(['id'=>$params['id']])->one();
        if(!is_null($aup)){
            $iy = CommonHelper::getIy($params['user_id'],$aup->auctionId);
        }
        return $this->successReturn([
            'models' => $models[0],
            'images' => $images,
            'record' => $records,
            'iy' => $iy,
            'msg' => '获取成功'
        ]);
    }
}