<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2016/3/5 0005
 * Time: 下午 2:16
 */
namespace common\helpers;

class CommonMd5
{


    //加密
    public static function string2secret($string)
    {
        $key = "9981qitiandasheng";
        $crypttext = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
        $encrypted = trim(self::safe_b64encode($crypttext));//对特殊字符进行处理
        return $encrypted;
    }

//解密
    public static function secret2string($string)
    {
        $key = "9981qitiandasheng";
        $crypttexttb = self::safe_b64decode($string);//对特殊字符解析
        $decryptedtb = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($crypttexttb), MCRYPT_MODE_CBC, md5(md5($key))), "\0");//解密函数
        return trim($decryptedtb);
    }

    private static function safe_b64encode($string)
    {
        $data = base64_encode($string);
        $data = str_replace(array('+', '/', '='), array('-', '_', ''), $data);
        return $data;
    }

//解析特殊字符

    private static function safe_b64decode($string)
    {
        $data = str_replace(array('-', '_'), array('+', '/'), $string);
        $mod4 = strlen($data) % 4;
        if ($mod4) {
            $data .= substr('====', $mod4);
        }
        return base64_decode($data);
    }
}