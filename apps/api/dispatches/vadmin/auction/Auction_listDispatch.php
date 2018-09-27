<?php
namespace api\dispatches\vadmin\auction;

use api\base\Dispatch;


use common\models\Auction;
use Yii;
use common\helpers\QiniuHelper;


/**
 * Note: api
 * Package: api\dispatches\auction\auction\Auction_listDispatch
 * Alias: auction_list.dispatch
 * Classpath: auction\Auction_listDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖会列表
 * Parmas: start-int-页数,limit-int-每页数量
 * Return: model-json-拍卖会列表
 * Returnerr: null
 * Detail: 拍卖会列表
 * Type: 拍卖会列表
 */
class Auction_listDispatch extends Dispatch
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

        $sql = "select image.images as images,a.id as id,a.name as name,a.begindate as begindate,a.biddersTicket as biddersTicket,a.watchTicket as watchTicket,notes.title as title,notes.content as content,notice.noticeName as noticeName,notice.content as noticecontent,admin.companyName as companyName from t_auction as a left join t_auction_image as image on image.auctionId = a.id  left join t_admin as admin on admin.id = a.companyID left join t_notes as notes on notes.id = a.notesId left join t_notice as notice on notice.id = a.noticeId where a.companyID = ".$params['user_id']." order by a.id desc limit ".$limit." offset ".$start;
        //$admins = Admin::find()->select('id,username,state,linkman,linkphone,companyName')->limit($limit)->offset($start)->orderBy('created_at DESC')->asArray()->all();
        $connection = Yii::$app->db;
        $command = $connection->createCommand($sql);
        $auctions = $command->queryAll();
        foreach ($auctions as $key=>$value){
            if($value['images'] != null){
                $admins[$key]['images'] = QiniuHelper::getImageUrl($value['images']);
            }
        }
        $total =  Auction::find()->where(['companyId'=>$params['user_id']])->count();
        return $this->successReturn([
            'models' => $auctions,
            'total'=>$total,
            'msg'=>'获取成功'
        ]);
    }
}