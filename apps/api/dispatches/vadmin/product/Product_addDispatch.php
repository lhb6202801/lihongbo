<?php
namespace api\dispatches\vadmin\product;

use api\base\Dispatch;
use common\models\AuctionProduct;
use common\models\ProductImages;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vadmin\product\Product_addDispatch
 * Alias: product_add.dispatch
 * Classpath: product\Product_addDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍品添加
 * Parmas: name-string-拍品名称,beginValue-string-起拍价,stepValue-string-加价幅度,freeOffer-string-自由竞拍时间,timeLimitOffer-string-限时竞拍时间,auctionId-string-拍卖会id,introduce-string-商品介绍,images-array-图片数组
 * Return: msg-json-添加成功
 * Returnerr: null
 * Detail: 拍品添加
 * Type: 拍品添加
 */
class Product_addDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['name'],$params['beginValue'],$params['stepValue'],$params['freeOffer'],$params['timeLimitOffer'],$params['auctionId'],$params['images'],$params['introduce'])){
            return $this->paramsErrorReturn();
        }
        //拍品主体信息
        $auctionproduct = new AuctionProduct();
        $auctionproduct->name = $params['name'];
        $auctionproduct->beginValue = $params['beginValue'];
        $auctionproduct->stepValue = $params['stepValue'];
        $auctionproduct->freeOffer = $params['freeOffer'];
        $auctionproduct->timeLimitOffer = $params['timeLimitOffer'];
        $auctionproduct->auctionId = $params['auctionId'];
        $auctionproduct->introduce = urldecode($params['introduce']);
        $auctionproduct->companyId = $params['user_id'];
        $auctionproduct->state = 1;
        if($auctionproduct->save(false)){
            //添加图片
            $images = $params['images'];
            $columnarray =['productId','images'];
            $valuearray = [];
            foreach ($images as $value){
                $valueinfo[] = $auctionproduct->id;
                $valueinfo[] = $value;
                $valuearray[] = $valueinfo;
                unset($valueinfo);
            }
            $save = Yii::$app->db->createCommand()->batchInsert(ProductImages::tableName(), $columnarray, $valuearray)->execute();
            return $this->successReturn(['msg' => '添加成功']);
        }
        return $this->errorReturn(1007);
    }
}