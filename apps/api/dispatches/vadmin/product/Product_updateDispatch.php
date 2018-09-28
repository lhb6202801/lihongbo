<?php
namespace api\dispatches\vadmin\product;

use api\base\Dispatch;
use common\models\AuctionProduct;
use common\models\ProductImages;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vadmin\product\Product_updateDispatch
 * Alias: product_update.dispatch
 * Classpath: product\Product_updateDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍品修改
 * Parmas: id-string-拍品id,name-string-拍品名称,beginValue-string-起拍价,stepValue-string-加价幅度,freeOffer-string-自由竞拍时间,timeLimitOffer-string-限时竞拍时间,auctionId-string-拍卖会id,introduce-string-商品介绍,images-array-图片数组
 * Return: msg-json-修改成功
 * Returnerr: null
 * Detail: 拍品修改
 * Type: 拍品修改
 */
class Product_updateDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'],$params['name'],$params['beginValue'],$params['stepValue'],$params['freeOffer'],$params['timeLimitOffer'],$params['auctionId'],$params['images'],$params['introduce'])){
            return $this->paramsErrorReturn();
        }
        //拍品主体信息
        //$auctionproduct = new AuctionProduct();
        $auctionproduct = AuctionProduct::find()->where(['id'=>$params['id']])->one();
        if(is_null($auctionproduct)){
            return $this->errorReturn(1008);
        }
        $auctionproduct->name = $params['name'];
        $auctionproduct->beginValue = $params['beginValue'];
        $auctionproduct->stepValue = $params['stepValue'];
        $auctionproduct->freeOffer = $params['freeOffer'];
        $auctionproduct->timeLimitOffer = $params['timeLimitOffer'];
        $auctionproduct->auctionId = $params['auctionId'];
        $auctionproduct->introduce = urldecode($params['introduce']);
        if($auctionproduct->save(false)){
            //添加图片
            $productimages = ProductImages::find()->where(['productId'=>$auctionproduct->id])->all();
            if(count($productimages)>0){
                foreach ($productimages as $value){
                    $value->delete();
                }
            }
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
            if($save){
                return $this->successReturn(['msg' => '修改成功']);
            }
        }
        return $this->errorReturn(1007);
    }
}