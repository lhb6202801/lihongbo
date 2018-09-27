<?php
namespace api\dispatches\vadmin\auction;

use api\base\Dispatch;

use common\models\AuctionImage;
use common\models\Auction;
use common\models\Notes;
use common\models\Notice;


/**
 * Note: api
 * Package: api\dispatches\vadmin\auction\Auction_updateDispatch
 * Alias: auction_update.dispatch
 * Classpath: auction\Auction_updateDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖会修改
 * Parmas: id-string-拍卖会id,auctionname-string-拍卖会名称,begindate-time-开始时间,biddersTicket-float-竞拍人入门费用,watchTicket-float-围观人入门费用,teacherId-string-拍卖师id,title-string-竞拍须知名称,content-string-竞拍须知内容,noticetitle-string-竞拍公告名称,noticecontent-string-竞拍公告内容,images-string-拍卖会图片
 * Return: msg-json-添加成功
 * Returnerr: null
 * Detail: 拍卖会修改
 * Type: 拍卖会修改
 */
class Auction_updateDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['id'],$params['auctionname'],$params['begindate'],$params['biddersTicket'],$params['watchTicket'],$params['teacherId'],$params['title'],$params['content'],$params['noticetitle'],$params['noticecontent'],$params['images'])){
            return $this->paramsErrorReturn();
        }
        //拍卖会主体信息
        $auction = Auction::find()->where(['id'=>$params['id']])->one();
        if(is_null($auction)){
            return $this->errorReturn(1008);
        }
        $auction->name = $params['auctionname'];
        $auction->begindate = $params['begindate'];
        $auction->companyId = $params['user_id'];
        $auction->biddersTicket = $params['biddersTicket'];
        $auction->watchTicket = $params['watchTicket'];
        $auction->teacherId = $params['teacherId'];
        $auction->save(false);
        //拍卖会公告
        $notice = Notice::find()->where(['id'=>$auction->noticeId]);
        if(!is_null($notice)){
            $notice->noticeName = $params['noticetitle'];
            $notice->content = urldecode($params['noticecontent']);
            $notice->save(false);
        }
        //拍卖会须知
        $notes = Notes::find()->where(['id'=>$auction->notesId]);
        if(!is_null($notes)){
            $notes->title = $params['title'];
            $notes->content = $params['content'];
            $notes->save(false);
        }
        if($auction->save(false)){
            //拍卖会图片
            $images = AuctionImage::find()->where(['auctionId'=>$params['id']])->one();
            if(!is_null($images)){
                $images->delete();
            }
            $images = new AuctionImage();
            $images->images = $params['images'][0];
            $images->auctionId = $auction->id;
            $images->save(false);
            if($images->save(false)) {
                return $this->successReturn([
                    'msg' => '修改成功'
                ]);
            }
        }
        return $this->errorReturn(1009);
    }
}