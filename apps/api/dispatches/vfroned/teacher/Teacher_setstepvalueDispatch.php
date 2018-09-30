<?php

namespace api\dispatches\vfroned\teacher;

use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\models\AuctionProduct;
use common\models\Teacher;
use common\models\TeacherMessage;

/**
 * Note: api
 * Package: api\dispatches\vfroned\teacher\Teacher_setstepvalueDispatch
 * Alias: teacher_setstepvalue.dispatch
 * Classpath: teacher\Teacher_setstepvalueDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖师设置增幅价格
 * Parmas: id-string-标的ID,setpvalue-string-增幅价格,
 * Return: msg-string-设置成功,
 * Returnerr: null
 * Detail: 拍卖师设置增幅价格
 * Type: 拍卖师设置增幅价格
 */
class Teacher_setstepvalueDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'],$params['setpvalue'])){
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
        $aup->stepvalue = $params['setpvalue'];
        if($aup->save(false)){
            return $this->successReturn([
                'msg' => '设置成功'
            ]);
        }
        return $this->errorReturn(1011);
    }
}