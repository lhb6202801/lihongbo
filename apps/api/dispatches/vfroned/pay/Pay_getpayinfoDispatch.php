<?php

namespace api\dispatches\vfroned\pay;

use abei2017\wx\Application;
use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\models\Auction;
use common\models\Pay;
use common\models\redis\QrCodeStr;
use common\models\Teacher;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vfroned\pay\Pay_getpayinfoDispatch
 * Alias: pay_getpayinfo.dispatch
 * Classpath: pay\Pay_getpayinfoDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 获取支付信息
 * Parmas: token-string-token,id-string-会场ID
 * Return: amount-int-支付金额,msg-string-获取成功
 * Returnerr: null
 * Detail: 获取支付信息
 * Type: 获取支付信息
 */
class Pay_getpayinfoDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        //权限校验
        $iy = CommonHelper::getIy($params['user_id'], $params['id']);
        if ($iy == '') {
            return $this->errorReturn(1018);
        }
        //判断身份  获取支付状态
        $ispay = CommonHelper::getPayState($params['user_id'], $params['id'], $iy);
        if ($ispay) {
            return $this->errorReturn(1022);
        }
        //获取支付价格
        $auction = Auction::find()->where(['id' => $params['id']])->all();
        if (is_null($auction)) {
            return $this->errorReturn(1011);
        }
        $amount = 0;
        if ($iy == 'br') {
            //竞拍者
            $amount = $auction->biddersTicket;
        } else if ($iy == 'or') {
            //围观者
            $amount = $auction->watchTicket;
        } else {
            return $this->errorReturn(1018);
        }
        return $this->successReturn([
            'models' => $amount,
            'msg' => '获取成功'
        ]);

    }
}