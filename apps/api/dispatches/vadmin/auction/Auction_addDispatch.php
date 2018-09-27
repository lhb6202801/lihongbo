<?php
namespace api\dispatches\vadmin\auction;

use api\base\Dispatch;

use common\models\Auction_image;
use common\models\Auction;
use common\models\Notes;
use common\models\Notice;


/**
 * Note: api
 * Package: api\dispatches\vadmin\auction\Auction_addDispatch
 * Alias: auction_add.dispatch
 * Classpath: auction\Auction_addDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖会添加
 * Parmas: auctionname-string-拍卖会名称,begindate-time-开始时间,biddersTicket-float-竞拍人入门费用,watchTicket-float-围观人入门费用,teacherId-string-拍卖师id,title-string-竞拍须知名称,content-string-竞拍须知内容,noticetitle-string-竞拍公告名称,noticecontent-string-竞拍公告内容,images-string-拍卖会图片
 * Return: msg-json-添加成功
 * Returnerr: null
 * Detail: 拍卖会添加
 * Type: 拍卖会添加
 */
class Auction_addDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if(!isset($params['auctionname'],$params['begindate'],$params['biddersTicket'],$params['watchTicket'],$params['teacherId'],$params['title'],$params['content'],$params['noticetitle'],$params['noticecontent'],$params['images'])){
            return $this->paramsErrorReturn();
        }
        //拍卖会主体信息
        $auction = new Auction();
        $auction->name = $params['auctionname'];
        $auction->begindate = $params['begindate'];
        $auction->companyId = $params['user_id'];
        $auction->biddersTicket = $params['biddersTicket'];
        $auction->watchTicket = $params['watchTicket'];
        $auction->teacherId = $params['teacherId'];
        $auction->state = 1;

        //拍卖会公告
        $notice = new Notice();
        $notice->noticeName = $params['noticetitle'];
        $notice->content = urldecode($params['noticecontent']);
        $notice->save(false);
        //拍卖会须知
        $notes = new Notes();
        $notes->title = $params['title'];
        $notes->content = $params['content'];
        $notes->save(false);
        $auction->noticeId = $notice->id;
        $auction->notesId = $notes->id;
        if($auction->save(false)){
            //拍卖会图片
            $images = new Auction_image();
            $images->images = $params['images'][0];
            $images->auctionId = $auction->id;
            $images->save(false);
            if($images->save(false)) {
                return $this->successReturn([
                    'msg' => '添加成功'
                ]);
            }
        }
        return $this->errorReturn(1007);
    }
}