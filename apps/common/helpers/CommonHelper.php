<?php

namespace common\helpers;
use common\components\YtxRestSdk;
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
    public function randString($len = 6)
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
    public function getAuctionType($vtype)
    {
        switch ($vtype){
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
}


