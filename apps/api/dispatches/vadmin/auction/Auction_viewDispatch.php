<?php

namespace api\dispatches\vadmin\auction;

use api\base\Dispatch;


use common\helpers\QiniuHelper;
use common\models\Auction;
use common\models\AuctionImage;
use common\models\Notes;
use common\models\Notice;

/**
 * Note: api
 * Package: api\dispatches\auction\auction\Auction_viewDispatch
 * Alias: auction_view.dispatch
 * Classpath: auction\Auction_viewDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖会查看
 * Parmas: id-string-拍卖会id
 * Return: modelinfo-json-拍卖会信息,imagesinfo-json-拍卖会图片信息,notesinfo-json-拍卖会须知信息,noticeinfo-json-拍卖会公告信息
 * Returnerr: null
 * Detail: 拍卖会查看
 * Type: 拍卖会查看
 */
class Auction_viewDispatch extends Dispatch
{
    public function run()
    {

        //拿到参数
        $params = $this->params;
        if (!isset($params['id'])) {
            return $this->paramsErrorReturn();
        }
        $auction = Auction::find()->where(['id' => $params['id']])->one();
        if (is_null($auction)) {
            return $this->errorReturn(1008);
        }
        //图片
        $iamges = AuctionImage::find()->where(['auctionId' => $auction->id])->one();
        $imagesinfo = [];
        if (!is_null($iamges)) {
            $imagesinfo['key'] = $iamges->images;
            $imagesinfo['url'] = QiniuHelper::getImageUrl($iamges->images);
        }
        //须知
        $notes = Notes::find()->where(['id' => $auction->notesId])->one();
        $notesinfo = [];
        if (!is_null($notes)) {
            $notesinfo['title'] = $notes->title;
            $notesinfo['content'] = $notes->content;
        }
        //公告
        $notice = Notice::find()->where(['id' => $auction->noticeId])->one();
        $noticeinfo = [];
        if (!is_null($notice)) {
            $noticeinfo['noticetitle'] = $notice->noticeName;
            $noticeinfo['noticecontent'] = $notice->content;
        }
        return $this->successReturn([
            'modelinfo' => $auction,
            'imagesinfo' => $imagesinfo,
            'notesinfo' => $notesinfo,
            'noticeinfo' => $noticeinfo
        ]);
    }
}