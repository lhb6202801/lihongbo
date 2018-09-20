<?php

namespace api\helpers;

use common\models\ConfigApiDispatch;
use common\models\ConfigApiError;
use common\models\ConfigApiVersion;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class ApiHelper
 * @package api\helpers
 * @author
 * @date
 */
class ApiHelper
{

    /**
     * @param $sk secret Key
     * @param $data  数据, 必需是json格式
     * @return bool|string
     */
    public static function sign($sk, $data)
    {
        $data = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
        $hmac = hash_hmac('sha1', $data, $sk, true);
        return md5($hmac);
    }

    /**
     * 通过token获取用户id  在不需要token的接口, 又需要确定用户时
     * @param $version
     * @param $token
     * @return null
     */
    public static function getUserIdByToken($version, $token)
    {
        $tokenClass = self::getTokenClass($version);
        if (empty($tokenClass)) {
            return null;
        }

        $tmp = new $tokenClass;
        if ($tmp instanceof \common\components\RedisActiveRecord) {
            $hash = substr(md5($token), 0, 1);
            $data = $tokenClass::find()
                ->where(['token' => $token])
                ->asArray()
                ->one($tokenClass::selectDb($hash));
        } else {
            $data = $tokenClass::find()
                ->where(['token' => $token])
                ->asArray()
                ->one();
        }

        if (!is_null($data)) {
            return $data['user_id'];
        } else {
            return null;
        }
    }

    /**
     * 获得允许调度的版本
     * @return array|\common\models\ConfigApiVersion[]
     */
    public static function getAllowedVersions()
    {
        $cache = Yii::$app->get('api cache');
        $key = 'API_VERSION';
        $versions = $cache->get($key);
        if (empty($versions)) {
            $versions = ConfigApiVersion::find()->where(['allowed' => 1])->asArray()->all();
            $versions = ArrayHelper::getColumn($versions, 'version');
            $cache->set($key, $versions);
        }
        return $versions;
    }

    /**
     * 获得调度映射
     * @param $version
     * @return array|\common\models\ConfigApiDispatch[]
     */
    public static function getDispatchMap($version)
    {
        $cache = Yii::$app->get('api cache');
        $key = 'API_DISPATCH_MAP_'.$version;

        $dispatchMap = $cache->get($key);
        if (empty($dispatchMap)) {
            $dispatchMap = ConfigApiDispatch::find()->where(['allowed' =>1, 'version' => $version])->asArray()->all();
            $dispatchMap = ArrayHelper::map($dispatchMap, 'dispatch', 'class');
            $cache->set($key, $dispatchMap);
        }
        return $dispatchMap;
    }

    public static function getTokenClass($version)
    {
        $cache = Yii::$app->get('api cache');
        $key = 'API_VERSION_TOKENCLASS_'.$version;

        $tokenClass = $cache->get($key);

        if (empty($classname)) {
            $apiVersion = ConfigApiVersion::find()->select('tokenClass')->where(['version' => $version])->asArray()->one();
            $tokenClass = $apiVersion['tokenClass'];
            $cache->set($key, $tokenClass);
        }
        return $tokenClass;
    }

    public static function getAutoExpireDays($version)
    {
        $cache = Yii::$app->get('api cache');
        $key = 'API_VERSION_TOKEN_AUTOEXPIREDAYS_'.$version;

        $tokenClass = $cache->get($key);

        if (empty($classname)) {
            $apiVersion = ConfigApiVersion::find()->select('autoExpireDays')->where(['version' => $version])->asArray()->one();
            $autoExpireDays = $apiVersion['autoExpireDays'];
            $cache->set($key, $autoExpireDays);
        }
        return $tokenClass;
    }


    /**
     * 获得不需要token的dispatch class
     * @param $version
     * @return array|\common\models\ConfigApiDispatch[]
     */
    public static function getDispatchNotToken($version)
    {
        $cache = Yii::$app->get('api cache');
        $key = 'API_DISPATCH_NOT_TOKEN_'.$version;

        $dispatchNotToken = $cache->get($key);

        if (empty($dispatchNotToken)) {
            $dispatchNotToken = ConfigApiDispatch::find()->where(['allowed' =>1, 'token' => 0, 'version' => $version])->asArray()->all();
            $dispatchNotToken = ArrayHelper::getColumn($dispatchNotToken, 'class');
            $cache->set($key, $dispatchNotToken);
        }
        return $dispatchNotToken;
    }

    /**
     * 获取错误码
     * @param $version
     * @return array|\common\models\ConfigApiError[]
     */
    public static function getErrorCodes($version)
    {
        $cache = Yii::$app->get('api cache');
        $key = 'API_ERRORS_'.$version;

        $errorCodes = $cache->get($key);

        if (empty($errorCodes)) {
            $errorCodes = ConfigApiError::find()->where(['version' => $version])->asArray()->all();
            $errorCodes = ArrayHelper::map($errorCodes, 'code', 'message');
            $cache->set($key, $errorCodes);
        }
        return $errorCodes;
    }

    public static function clear($version)
    {
        $cache = Yii::$app->get('api cache');
        $key = 'API_DISPATCH_MAP_'.$version;
        $cache->delete($key);

        $key = 'API_DISPATCH_NOT_TOKEN_'.$version;
        $cache->delete($key);

        $key = 'API_ERRORS_'.$version;
        $cache->delete($key);

        $key = 'API_VERSION_TOKENCLASS_'.$version;
        $cache->delete($key);

        $key = 'API_VERSION_TOKEN_AUTOEXPIREDAYS_'.$version;
        $cache->delete($key);

    }

    public static function clearAll()
    {
        $cache = Yii::$app->get('api cache');
        $cache->flush();
    }
}
