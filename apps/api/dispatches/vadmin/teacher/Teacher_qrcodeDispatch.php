<?php
namespace api\dispatches\vadmin\teacher;

use api\base\Dispatch;

use common\models\redis\QrCodeStr;
use common\models\Teacher;
use abei2017\wx\Application;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vadmin\teacher\Teacher_qrcodeDispatch
 * Alias: teacher_qrcode.dispatch
 * Classpath: teacher\Teacher_qrcodeDispatch
 * Version: admin
 * Allowed: true
 * Istoken: true
 * Describe: 拍卖师绑定微信二维码生成
 * Parmas: id-string-拍卖师id
 * Return: msg-json-生产成功,images-base64-二维码
 * Returnerr: null
 * Detail: 拍卖师绑定微信二维码生成
 * Type: 拍卖师绑定微信二维码生成
 */
class Teacher_qrcodeDispatch extends Dispatch
{
    public function run()
    {
        //拿到参数
        $params = $this->params;
        if(!isset($params['id'])){
            return $this->paramsErrorReturn();
        }
        //拍卖师主体信息
        $teacher = Teacher::find()->where(['id'=>$params['id']])->one();
        if(is_null($teacher)){
            return $this->errorReturn(1008);
        }
        $app = new Application(['conf'=>Yii::$app->params['wx']['mini']]);

        $qrcode = $app->driver("mini.qrcode");
        $qrcodestr = substr(md5(microtime(true)), 0, 6);
        //参数
        $scene = 'iy=tr&yid='.$params['id'].'&s='.$qrcodestr;
        //保存唯一
        $qstr = new QrCodeStr();
        $qstr->str = $qrcodestr;
        $qstr->insert();
        return $this->successReturn([
            'msg' => '生产成功',
            'images' => $scene
        ]);
        //页面
        $page = 'pages/index/index';
        $qrcodeiamge = $qrcode->unLimit($scene,$page,$extra = []);
        return $this->successReturn([
            'msg' => '生产成功',
            'images' => $qrcodeiamge
        ]);
    }
}