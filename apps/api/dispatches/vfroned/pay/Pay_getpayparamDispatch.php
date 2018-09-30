<?php
namespace api\dispatches\vfroned\pay;

use abei2017\wx\Application;
use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\models\Auction;
use common\models\redis\QrCodeStr;
use common\models\Teacher;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vfroned\pay\Pay_getpayparamDispatch
 * Alias: pay_getpayparam.dispatch
 * Classpath: pay\Pay_getpayparamDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 获取支付支付订单
 * Parmas: token-string-token,id-string-会场ID
 * Return: model-josn-支付订单信息
 * Returnerr: null
 * Detail: 获取支付支付订单
 * Type: 获取支付支付订单
 */
class Pay_getpayparamDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        //权限校验
        $iy = CommonHelper::getIy($params['user_id'], $params['id']);
        if ($iy =='') {
            return $this->errorReturn(1018);
        }
        //判断身份  获取支付状态
        $ispay = CommonHelper::getPayState($params['user_id'],$params['id'],$iy);
        if($ispay){
            return $this->errorReturn(1022);
        }
        //获取支付价格
        $auction = Auction::find()->where(['id'=>$params['id']])->all();
        if(is_null($auction)){
            return $this->errorReturn(1011);
        }
        $amount = 0;
        if($iy == 'br'){
            //竞拍者
            $amount = $auction->biddersTicket;
        }else if($iy == 'or'){
            //围观者
            $amount = $auction->watchTicket;
        }else{
            return $this->errorReturn(1018);
        }
        //生成支付订单
        $pay = new Pay();
        $pay->wxId = $params['user_id'];
        $pay->auctionId = $params['id'];
        //$pay->state = 0;
        $pay->state = 1;
        $pay->payDate = time();
        //$pay->payValue = 0;
        $pay->payValue = $amount;
        if($pay->save(false)){
            return $this->successReturn([
                'models' => "支付订单信息",
                'msg' => '获取成功'
            ]);
        }
    }
}