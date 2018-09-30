<?php

namespace api\dispatches\vfroned\product;

use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\helpers\QiniuHelper;
use common\models\Auction;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vfroned\product\Product_getlistforfDispatch
 * Alias: product_getlistfrof.dispatch
 * Classpath: product\Product_getlistforfDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 获取标的目录
 * Parmas: id-string-拍卖会id
 * Return: models-string-标的目录state=0流拍1进行中2成交
 * Returnerr: null
 * Detail: 获取标的目录
 * Type: 获取标的目录
 */
class Product_getlistforfDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'])){
            return $this->paramsErrorReturn();
        }
        //取拍卖会
        $auction = Auction::find()->where(['id' => $params['id'], 'state' => 1])->one();
        if (is_null($auction)) {
            return $this->errorReturn(1011);
        }
        //权限校验
        $iy = CommonHelper::getIy($params['user_id'], $params['id']);
        if ($iy =='') {
            return $this->errorReturn(1018);
        }
        //判断身份  获取支付状态
        $ispay = CommonHelper::getPayState($params['user_id'],$params['id'],$iy);
        if(!$ispay){
            return $this->errorReturn(1021);
        }
        //取当场拍卖会商品列表
//        $auconptiroduct = AuctionProduct::find()->where(['companyId' => $params['id']])->all();
        $sql = "select tap.id as id,tap.state as state,tap.name as productname,tap.beginValue as beginValue,tpi.images as images,ta.name as auctionname,ta.begindate as begindate from t_auction_product as tap left join t_product_images as tpi on tpi.productId = tap.id left join t_auction as ta on ta.id = tap.auctionId where tap.auctionId = " . $params['id']." and tap.state = 1";
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $models = $command->queryAll();
        $modellist = CommonHelper::array_unset_tt($models, 'id');
        foreach ($modellist as $item => $value) {
            if ($value['images'] != null) {
                $modellist[$item]['images'] = QiniuHelper::getImageUrl($value['images']);
            }
        }
        return $this->successReturn([
            'models' => $modellist,
            'msg' => '获取成功',
            'iy' =>$iy
        ]);
    }
}