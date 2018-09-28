<?php
namespace api\dispatches\vadmin\product;

use api\base\Dispatch;
use common\helpers\QiniuHelper;
use common\models\AuctionProduct;
use common\models\ProductImages;


/**
 * Note: api
 * Package: api\dispatches\vadmin\product\Product_viewDispatch
 * Alias: product_view.dispatch
 * Classpath: product\Product_viewDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍品查看
 * Parmas: id-string-拍品id
 * Return: model-json-拍品信息
 * Returnerr: null
 * Detail: 拍品查看
 * Type: 拍品查看
 */
class Product_viewDispatch extends Dispatch
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
        $imagesinfo = [];
        if(count($productimages)>0){
            foreach ($productimages as $value){
                $images['key'] = $value->images;
                $images['url'] = QiniuHelper::getImageUrl($value->images);
                $imagesinfo[] = $images;
                unset($images);
            }
        }

        return $this->successReturn([
            'model' => $auctionproduct,
            'image' => $imagesinfo
        ]);


    }
}