<?php
namespace api\dispatches\vadmin\product;

use api\base\Dispatch;
use common\models\AuctionProduct;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vadmin\product\Product_listDispatch
 * Alias: product_list.dispatch
 * Classpath: product\Product_listDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍品列表
 * Parmas: start-int-页数,limit-int-每页数量,id-string-拍卖会ID
 * Return: model-json-拍品列表
 * Returnerr: null
 * Detail: 拍品列表
 * Type: 拍品列表
 */
class Product_listDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['start'],$params['limit'])){
            return $this->paramsErrorReturn();
        }
        //拍卖会主体信息
        $start = intval($params['start']);
        $limit = intval($params['limit']);
        $start = $start * $limit;

        $sql = "select * from t_auction_product as p  where p.companyId=".$params['user_id']." and p.auctionId".$params['id']." order by p.id desc limit ".$limit." offset ".$start;
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $auctionproduct = $command->queryAll();
        $total =  AuctionProduct::find()->where(['companyId'=>$params['user_id'],'auctionId'=>$params['id']])->count();
        return $this->successReturn([
            'models' => $auctionproduct,
            'total'=>$total,
            'msg'=>'获取成功'
        ]);
    }
}