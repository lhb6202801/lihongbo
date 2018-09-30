<?php
namespace api\dispatches\vfroned\teacher;

use abei2017\wx\Application;
use api\base\Dispatch;
use common\helpers\CommonHelper;
use common\models\Auction;
use common\models\redis\QrCodeStr;
use common\models\Teacher;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vfroned\teacher\Teacher_getqrcodeDispatch
 * Alias: teacher_getqrcode.dispatch
 * Classpath: teacher\Teacher_getqrcodeDispatch
 * Version: froned
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖师邀请人二维码生成
 * Parmas: id-string-会场ID,iy-string-身份类型0竞拍者1围观者2委托人
 * Return: image-string-二维码,
 * Returnerr: null
 * Detail: 拍卖师邀请人二维码生成
 * Type: 拍卖师邀请人二维码生成
 */
class Teacher_getqrcodeDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'],$params['iy'])){
            return $this->paramsErrorReturn();
        }
        //确认身份是拍卖师 并且存在此会场中
        $auction = Auction::find()->where(['id'=>$params['id']])->one();
        if(is_null($auction)){
            return $this->errorReturn(1011);
        }
        $teacher = Teacher::find()->where(['id'=>$auction->teacherId])->one();
        if(is_null($teacher)){
            return $this->errorReturn(1012);
        }
        if( $teacher->wx_id != $params['user_id']){
            return $this->errorReturn(1018);
        }
        //生成二维码
        $app = new Application(['conf'=>Yii::$app->params['wx']['mini']]);

        $qrcode = $app->driver("mini.qrcode");
        $qrcodestr = substr(md5(microtime(true)), 0, 6);
        //参数
        //判断身份
        $iy = CommonHelper::getAuctionType($params['iy']);
        $scene = 'iy='.$iy.'&yid='.$params['id'].'&s='.$qrcodestr;
        //保存唯一
        $qstr = new QrCodeStr();
        $qstr->str = $qrcodestr;
        $qstr->insert();
        return $this->successReturn([
            'msg' => '生产成功',
            'images' => $scene
        ]);

//        $page = 'pages/index/index';
//        $qrcodeiamge = $qrcode->unLimit($scene,$page,$extra = []);
//        return $this->successReturn([
//            'msg' => '生产成功',
//            'images' => $qrcodeiamge
//        ]);
    }
}