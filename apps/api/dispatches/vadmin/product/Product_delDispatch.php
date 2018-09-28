<?php
namespace api\dispatches\vadmin\product;

use api\base\Dispatch;
use common\models\AuctionProduct;
use common\models\ProductImages;


/**
 * Note: api
 * Package: api\dispatches\vadmin\product\Product_delDispatch
 * Alias: product_del.dispatch
 * Classpath: product\Product_delDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍品删除
 * Parmas: id-string-拍品id
 * Return: msg-json-删除成功
 * Returnerr: null
 * Detail: 拍品删除
 * Type: 拍品删除
 */
class Product_delDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'])){
            return $this->paramsErrorReturn();
        }
        //拍品主体信息
        //$auctionproduct = new AuctionProduct();
        $auctionproduct = AuctionProduct::find()->where(['id'=>$params['id']])->one();
        if(is_null($auctionproduct)){
            return $this->errorReturn(1008);
        }
        $productimages = ProductImages::find()->where(['productId'=>$auctionproduct->id])->all();
        if(count($productimages)>0){
            foreach ($productimages as $value){
                $value->delete();
            }
        }
        if($auctionproduct->delete()) {
            return $this->successReturn(['msg' => '删除成功']);
        }
        return $this->errorReturn(1010);
    }
}