<?php
namespace api\dispatches\vfroned\auction;

use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\helpers\QiniuHelper;
use common\models\Auction;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vfroned\auction\Auction_getlistforvDispatch
 * Alias: auction_getlistfrov.dispatch
 * Classpath: auction\Auction_getlistforvDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 获取会场列表froned
 * Parmas: token-string-token
 * Return: models-string-会场列表
 * Returnerr: null
 * Detail: 获取会场列表froned
 * Type: 获取会场列表froned
 */
class Auction_getlistforvDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        $sql = "select t.name as auctionname,ta.companyName as companyname,ti.images as images,t.begindate as begindate,tv.type as vtype,t.id as id from t_auction_inviter as tv left join t_auction as t on t.id=tv.auctionId left join t_admin as ta on t.companyId=ta.id left join t_auction_image ti on ti.auctionId=t.id where tv.wxId=".$params['user_id']." and t.state=1";

        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $models = $command->queryAll();

        if(count($models)<=0){
            //确认是否是拍卖师
            $sqlinfo = "select t.name as auctionname,ta.companyName as companyname,ti.images as images,t.begindate as begindate,t.state as vtype,t.id as id from t_auction as t left join t_admin as ta on ta.id = t.companyId left join t_auction_image as ti on ti.auctionId = t.id where t.teacherId=".$params['user_id']." and t.state=1";
            $command = $connection->createCommand($sqlinfo);
            $models = $command->queryAll();
            foreach ($models as $key=>$value){
                $models[$key]['vtype'] = 3;
            }
        }
        if(count($models)>0){
            foreach ($models as $key=>$value){
                if($value['images'] != null){
                    $models[$key]['images'] = QiniuHelper::getImageUrl($value['images']);
                }
            }
            foreach ($models as $key=>$value){
                $models[$key]['vtype'] = CommonHelper::getAuctionType($value['vtype']);
            }
            return $this->successReturn([
                'models' => $models,
                'msg'=>'获取成功'
            ]);
        }else{
            return $this->successReturn([
                'models' => $models,
                'msg'=>'获取成功'
            ]);
        }
    }
}