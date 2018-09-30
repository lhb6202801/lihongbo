<?php

namespace api\dispatches\vfroned\teacher;

use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\models\AuctionProduct;
use common\models\Teacher;
use common\models\TeacherMessage;

/**
 * Note: api
 * Package: api\dispatches\vfroned\teacher\Teacher_sendmessageDispatch
 * Alias: teacher_sendmessage.dispatch
 * Classpath: teacher\Teacher_sendmessageDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖师发布公告
 * Parmas: id-string-标的ID,messages-string-公告信息,
 * Return: msg-string-发布成功,
 * Returnerr: null
 * Detail: 拍卖师发布公告
 * Type: 拍卖师发布公告
 */
class Teacher_sendmessageDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'],$params['messages'])){
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
        //创建公告
        $teatchermessage = new TeacherMessage();
        $teatchermessage->content = $params['messages'];
        $teatchermessage->auctionId = $aup->auctionId;
        $teatchermessage->auctionProductId = $params['id'];
        $teatchermessage->teacherId = $teacher->id;
        $teatchermessage->created_at = time();
        if ($teatchermessage->save(false)) {
            return $this->successReturn([
                'msg' => '公告发布成功'
            ]);
        }
    }
}