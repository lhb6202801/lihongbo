<?php
namespace api\dispatches\vfroned\teacher;

use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\models\AuctionProduct;
use common\models\OfferRecord;
use common\models\Teacher;
use common\models\TeacherMessage;


/**
 * Note: api
 * Package: api\dispatches\vfroned\teacher\Teacher_getmessageDispatch
 * Alias: teacher_getmessage.dispatch
 * Classpath: teacher\Teacher_getmessageDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 获取拍卖师公告
 * Parmas: id-string-标的ID
 * Return: models-json-公告信息,
 * Returnerr: null
 * Detail: 获取拍卖师公告
 * Type: 获取拍卖师公告
 */
class Teacher_getmessageDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'])){
            return $this->paramsErrorReturn();
        }
        //是否是拍卖师身份
        $aup = AuctionProduct::find()->where(['id' => $params['id']])->one();
        if (!is_null($aup)) {
            if($aup->state!=1){
                return $this->errorReturn(1020);
            }
            if (CommonHelper::getIy($params['user_id'], $aup->auctionId) != 'tr') {
                return $this->errorReturn(1018);
            }
        }else{
            return $this->errorReturn(1019);
        }
        //获取拍卖师ID
        $teacher = Teacher::find()->where(['wx_id' => $params['user_id']])->one();
        if (is_null($teacher)) {
            return $this->errorReturn(1012);
        }
        //最高价格
        $hamount = $aup->beginValue;

        $offer = "";
        //当前最高价格 编号
        $offerRecord = OfferRecord::find()->where(['auctionProductId'=>$params['id']])->orderBy('offer desc')->all();
        if(count($offerRecord)>0){
            $offer = $offerRecord[0];
            $hamount =$offerRecord[0]->offer;
        }
        $messages = TeacherMessage::find()->select('id,created_at,content')->where(['teacherId'=>$teacher->id,'auctionProductId'=>$params['id']])->orderBy('created_at DESC')->all();


        //是否已成交


        return $this->successReturn([
            'messages' => $messages,
            'teacherinfo'=>$teacher,
            'offer'=>$offer,
            'stepValue'=>$aup->stepValue,
            'hamount'=>$hamount
        ]);
    }
}