<?php

namespace api\dispatches\vadmin\configapi;

use api\base\Dispatch;
use common\models\ConfigApiDispatch;
use common\helpers\CommonHelper;
use api\helpers\ApiHelper;
use Yii;

/**
 * Note: api
 * Package: api\dispatches\vadmin\configapi\ConfigApi_addDispatch
 * Alias: configapi_add.dispatch
 * Classpath: configapi\ConfigApi_addDispatch
 * Version: admin
 * Allowed: true
 * Istoken: false
 * Describe: api初始化
 * Parmas: username-string-用户账号, password-stirng-用户密码
 * Return: model-json-用户信息, token-string-用户token
 * Returnerr: null
 * Detail: api初始化
 * Type: api初始化
 */
class ConfigApi_addDispatch extends Dispatch
{
    public function run()
    {
        $path = dirname(dirname(dirname(__FILE__)));
        $fileslist = CommonHelper::ScanFile($path);
        $columnarray = ['dispatch', 'class', 'version', 'allowed', 'token', 'description', 'request_eg', 'response_eg', 'response_error_eg', 'detail', 'type','created_at'];
        $value = [];
        foreach ($fileslist as $file) {
            $classname = explode("apps/", $file);
            $classname = explode(".", end($classname))[0];
            $classname = str_replace("/", '\\', $classname);
            $rc = new \ReflectionClass($classname);
            $rc->getDocComment();
            $rc = explode(PHP_EOL, $rc);
            $data = [];
            for ($i = 3; $i < 14; $i++) {
                if (trim(explode(':', trim(str_replace('*', '', $rc[1])))[1]) == 'api') {
                    $infos = explode(':', trim(str_replace('*', '', $rc[$i])));
                    $Version = explode(':', trim(str_replace('*', '', $rc[5])))[1];
                    $Alias = explode(':', trim(str_replace('*', '', $rc[3])))[1];
                    if ($infos[0] == 'Allowed') {
                        if (trim($infos[1]) == 'false') {
                            $data[] = 0;
                        } else {
                            $data[] = 1;
                        }
                    } else if ($infos[0] == 'Istoken') {
                        if (trim($infos[1]) == 'false') {
                            $data[] = 0;
                        } else {
                            $data[] = 1;
                        }
                    } else if ($infos[0] == 'Parmas') {
                        if (trim($infos[1]) != 'null') {
                            //不需要参数
                            //参数个数
                            $parmasarray = explode(',', $infos[1]);
                            $pvalue = '{';
                            foreach ($parmasarray as $parmas) {
                                $parmasinfo = explode('-', $parmas);
                                //参数类型
                                //参数说明
                                $pvalue .= $parmasinfo[0] . ' :     ' . $parmasinfo[1] . ' ' . $parmasinfo[2].',';
                            }
                            $pvalue .= '}';

                        }
                        $data[] = '{
    "extras": {},
    "requests":[
        {
            "params": ' . $pvalue . ',
            "version": "'.trim($Version).'",
            "dispatch": "'.trim($Alias).'"
        }
    ],
    "timestamp":1534580910292,
    "signature":"d66f437f6e224a75cf07a8702176f39c"
}';
                    } else if ($infos[0] == 'Return') {
                        if (trim($infos[1]) != 'null') {

                            $parmasarray = explode(',', $infos[1]);
                            $pvalue = '{';
                            foreach ($parmasarray as $parmas) {
                                $parmasinfo = explode('-', $parmas);
                                //参数类型
                                //参数说明
                                $pvalue .= $parmasinfo[0] . ' :     ' . $parmasinfo[1] . ' ' . $parmasinfo[2].',';
                            }
                            $pvalue .= '}';
                        }

                            $data[] ='{
    "code": 0,
    "timestamp": 0,
    "deviation": 0,
    "message": "成功",
    "responses": [
        {
            "code": 0,
            "message": "成功",
            "data": '. $pvalue .',
            "dispatch": "'.trim($Alias).'"
        }
    ]
}';

                    }else if ($infos[0] == 'Returnerr') {
                        if (trim($infos[1]) == 'null') {
                            $data[] = $infos[1];
                        } else {
                            //有返回值
                        }
                    } else {
                        $data[] = trim($infos[1]);
                    }
                    //$data[] = explode(':',trim(str_replace('*','',$rc[$i])));

                }
            }
            $data[] = time();
            $value[] = $data;
        }
        //查询数组
        $configapis = ConfigApiDispatch::find()->all();
        foreach ($value as $key => $values){
            foreach ($configapis as $keys => $valuess){
                if($values[0] == $valuess['dispatch']){
                    unset($value[$key]);
                }
            }
        }
        //ConfigApiDispatch::deleteAll();
        $saveapi = Yii::$app->db->createCommand()->batchInsert(ConfigApiDispatch::tableName(), $columnarray, $value)->execute();
        if(!$saveapi){
            return $this->successReturn([
                'msg' => '初始化失败'
            ]);
        }
        //$model = new ConfigApiDispatch();
        ApiHelper::clearAll();
        return $this->successReturn([
            'model' => $value,
            'msg'=>'初始化成功'
        ]);
    }
}