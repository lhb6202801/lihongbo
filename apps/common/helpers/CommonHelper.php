<?php

namespace common\helpers;

use common\components\YtxRestSdk;
use common\models\Auction;
use common\models\AuctionInviter;
use common\models\Pay;
use common\models\Teacher;
use Yii;


/**
 * Class CommonHelper
 * @package common\helpers
 * @author
 * @date 15-5-22
 */
class CommonHelper
{
    //扫描文件下所有文件
    //return map
    public static function ScanFile($path)
    {
        global $result;
        $fileslist = scandir($path);
        foreach ($fileslist as $file) {
            if ($file != '.' && $file != '..' && $file != '.DS_Store') {
                if (is_dir($path . '/' . $file)) {
                    CommonHelper::ScanFile($path . '/' . $file);
                } else {
                    $result[] = $path . '/' . $file;
                }
            }
        }
        return $result;
    }

    /**
     * 发送验证码通过ytx
     * @param $mobile
     * @param $code
     * @return
     */
    public static function sendCodeByYtx($mobile, $code)
    {
        $ytx = Yii::$app->params['ytx'];
        $rest = new YtxRestSdk($ytx['serverIP'], $ytx['serverPort'], $ytx['softVersion']);
        $rest->setAccount($ytx['accountSid'], $ytx['accountToken']);
        $rest->setAppId($ytx['appId']);
        if ($rest->sendTemplateSMS($mobile, [strval($code), strval(20)], 196272)) { //20分钟未添加
            return true;
        }
    }

    /**
     * 产生随机数串
     * @param integer $len 随机数字长度
     * @return string
     */
    public static function randString($len = 6)
    {
        $chars = str_repeat('0123456789', 3);
        // 位数过长重复字符串一定次数
        $chars = str_repeat($chars, $len);
        $chars = str_shuffle($chars);
        $str = substr($chars, 0, $len);
        return $str;

    }

    /**
     * 获取用户身份
     */
    public static function getAuctionType($vtype)
    {
        switch ($vtype) {
            case 0:
                $vtype = 'br';
                break;
            case 1:
                $vtype = 'or';
                break;
            case 2:
                $vtype = 'ct';
                break;
            case 3:
                $vtype = 'tr';
                break;
        }
        return $vtype;
    }
    /**
     * 二维数组去重
     *
     */
    //$arr->传入数组   $key->判断的key值
    public static function array_unset_tt($arr, $key)
    {
        //建立一个目标数组
        $res = array();
        foreach ($arr as $value) {
            //查看有没有重复项
            if (isset($res[$value[$key]])) {
                unset($value[$key]);
            } else {

                $res[$value[$key]] = $value;
            }
        }
        return $res;
    }
    /***
     * 根据用户id,会场id,获取用户身份
     */
    public static function getIy($uid,$aid)
    {
        $auction = Auction::find()->where(['id' => $aid, 'state' => 1])->one();
        if (!is_null($auction)) {
            $auctioninviter = AuctionInviter::find()->where(['wxId' => $uid])->one();
            if (is_null($auctioninviter)) {
                $teacher = Teacher::find()->where(['id' => $auction->teacherId])->one();
                if (!is_null($teacher)) {
                    if ($teacher->wx_id == $uid) {
                        $iy = 3;
                    }
                }
            }else{
                $iy = $auctioninviter->type;
            }
        }
        if($iy!=''){
            return CommonHelper::getAuctionType($iy);
        }
        return '';
    }
    /***
     * 根据用户id,会场id,身份,获取支付状态
     * return bool
     */
    public static function getPayState($uid,$aid,$iy)
    {
        $p = false;
        switch ($iy){
            case 'ct':
                $p = true;
                break;
            case 'tr':
                $p = true;
                break;
            default:
                $p = false;
        }
        if(!$p){
            $pay = Pay::find()->where(['wxId'=>$uid,'auctionId'=>$aid])->one();
            if(is_null($pay)){
                $p =  false;
            }
            if($pay->state == 1){
                $p =  true;
            }
        }
        return $p;
    }
}


