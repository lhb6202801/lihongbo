<?php

namespace api\dispatches\vfroned\people;

use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\models\Auction;
use common\models\AuctionInviter;
use common\models\AuctionProduct;
use common\models\InviterSeting;
use common\models\OfferRecord;



/**
 * Note: api
 * Package: api\dispatches\vfroned\people\People_addofferDispatch
 * Alias: people_addoffer.dispatch
 * Classpath: people\People_addofferDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 参与竞价
 * Parmas: id-string-标的ID,num-string-竞价倍数,
 * Return: msg-string-参与成功
 * Returnerr: null
 * Detail: 参与竞价
 * Type: 参与竞价
 */
class People_addofferDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'],$params['num'])){
            return $this->paramsErrorReturn();
        }
        $iy ='';
        //判断权限
        $aup = AuctionProduct::find()->where(['id' => $params['id']])->one();
        if (!is_null($aup)) {
            //判断标的是否可进行拍卖
            if($aup->state != 1){
                return $this->errorReturn(1028);
            }
            $iy = CommonHelper::getIy($params['user_id'], $aup->auctionId);
            if ($iy =='') {
                return $this->errorReturn(1018);
            }
            if ($iy =='tr') {
                return $this->errorReturn(1018);
            }
            if ($iy =='ct') {
                return $this->errorReturn(1018);
            }
            if ($iy =='or') {
                return $this->errorReturn(1018);
            }
        }else{
            return $this->errorReturn(1019);
        }
        //判断身份  获取支付状态
        $ispay = CommonHelper::getPayState($params['user_id'],$params['id'],$iy);
        if(!$ispay){
            return $this->errorReturn(1021);
        }
        //判断拍卖会状态 是否开始 是否结束 是否流拍
        $auction = Auction::find()->where(['id'=>$aup->auctionId])->one();
        if(is_null($auction)){
            return $this->errorReturn(1011);
        }
        if($auction->state !=1){
            return $this->errorReturn(1023);
        }
        if($auction->begindate < time()){
            return $this->errorReturn(1024);
        }
        //判断出价资格  是否参与此商品
        $invier = InviterSeting::find()->where(['auctionId'=>$aup->auctionId])->one();
        if(is_null($invier)){
            return $this->errorReturn(1025);
        }
        if($invier->product){
            $invierarr = explode(",", $invier->product);
            if(!in_array($params['id'], $invierarr)){
                return $this->errorReturn(1025);
            }
        }
        //判断成交次数
        $offernum = OfferRecord::find()->where(['auctionProductId'=>$params['id'],'wxId'=>$params['user_id'],'state'=>1])->count();
        if($invier->biddingHow <= $offernum){
            return $this->errorReturn(1026);
        }
        //判断是否过了上次出价时间
        $uptime = OfferRecord::find()->where(['auctionProductId'=>$params['id']])->orderBy('offerDate desc')->all();
        if(count($uptime)>0){
            if(($uptime[0]+$aup->timeLimitOffer)>time()){
                //出价时间已过
                return $this->errorReturn(1028);
            }
        }
        //可以出价
        //计算报价
        //取出出价表中当前最高价格
        $offeramount = $aup->beginValue;
        //当前最高价格 编号
        $offerRecord = OfferRecord::find()->where(['auctionProductId'=>$params['id']])->orderBy('offer desc')->all();
        if(count($offerRecord)>0){
            $offeramount =$offerRecord[0]->offer;
        }
        $offeramount = $offeramount+($aup->stepValue*$params['num']);
        //获取编号
        $wx = AuctionInviter::find()->where(['wxId'=>$params['user_id']])->one();
        //准备出价
        $invierinfo = new OfferRecord();
        $invierinfo->offer =$offeramount;
        $invierinfo->biddersNumber =$wx->auctionNumber;
        $invierinfo->state =1;
        $invierinfo->offerDate = time();
        $invierinfo->wxId =$params['user_id'];
        $invierinfo->auctionProductId = $params['id'];
        if($invierinfo->save(false)){
            //获取出价信息 将本次以上信息状态设置为0
            $offerhz = OfferRecord::find()->where(['auctionProductId'=>$params['id']])->andWhere(['<>','id',$invierinfo->id])->orderBy('offer desc')->all();
            $offerhz[0]->srate = 0;
            $offerhz[0]->save(false);
            return $this->successReturn([
                'msg' => '竞价成功'
            ]);
        }
        return $this->errorReturn(1027);

    }
}