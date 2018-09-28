<?php

namespace api\controllers;

use Yii;
use api\helpers\ApiHelper;
use yii\log\Logger;

class RestController extends \yii\web\Controller
{

    public $responses = [];
    public $requests = [];

    public $layout = false;

    public $enableCsrfValidation = false;

    public $callback = '';

    public $deviation = 0;

    public $timestamp = 0;

    public $errorCodes = "";

    public function actionError()
    {
        $exception = Yii::$app->getErrorHandler()->exception;
        if (!is_null($exception)) {
            return ['message' => $exception->getMessage()];
        } else {
            return [];
        }
    }

    public function responsesReturn($code, $message)
    {
        $data = [
            'code' => $code,
            'timestamp' => $this->timestamp, //服务器时间
            'deviation' => $this->deviation, //误差
            'message' => $message,
            'responses' => $this->responses
        ];
        if ($this->callback != '') {
            return ['data' => $data, 'callback' => $this->callback];
        } else {
            return $data;
        }
    }

    public function actionIndex()
    {
        header('Access-Control-Allow-Origin:*');
        \yii::getLogger()->log('开始日志异步消息' . time(), Logger::LEVEL_ERROR);
        $response = Yii::$app->response;
        $response->format = 'json';
        $request = Yii::$app->request;
        ApiHelper::clearAll();
        if (!$request->isPost) {
            return $this->responsesReturn(9000, '请求方法不对');
        }

        $body = $request->getRawBody();

        $body = @json_decode($body, true);


        if (is_null($body)) {
            return $this->responsesReturn(9001, 'payloads结构有误');
        }

        //多请求处理
        if (!isset($body['requests'])) {
            return $this->responsesReturn(9002, 'requests无效');
        }

        if (!is_array($body['requests']) || count($body['requests']) == 0) {
            return $this->responsesReturn(9003, 'requests结构有误');
        }

        if (isset($body['callback']) && trim($body['callback']) != '') {
            $this->callback = trim($body['callback']);
            Yii::$app->response->format = 'jsonp';
        }

        if (!isset($body['extras'])) {
            return $this->responsesReturn(9004, 'extras无效');
        }

        if (!is_array($body['extras'])) {
            return $this->responsesReturn(9005, 'extras结构有误');
        }

        //获取version  确定版本
        $allowedVersions = ApiHelper::getAllowedVersions();

        foreach ($body['requests'] as $request) {
            $dispatch = isset($request['dispatch']) ? $request['dispatch'] : null;
            if (!isset($request['dispatch'])) {
                $this->systemExceptionReturn(8003, '请求参数有误', $dispatch);   //请求参数有误
                continue;
            }
            $dispatch = $request['dispatch'];


            if (isset($request['version']) && in_array($request['version'], $allowedVersions)) {
                $versionName = 'v'.$request['version'];
                $version = $request['version'];
            } else {
                $this->systemExceptionReturn(8000, '版本有误', $dispatch); //版本有误
                continue;
            }

            $this->errorCodes = ApiHelper::getErrorCodes($version);

            //调度映射
            $dispatchMap = ApiHelper::getDispatchMap($version);
            if (!is_array($dispatchMap) || count($dispatchMap) == 0) {
                $this->systemExceptionReturn(8001, '映射有误', $dispatch); //映射有误
                continue;
            }

            //不需要token
            $dispatchNotToken = ApiHelper::getDispatchNotToken($version);
            if (!is_array($dispatchNotToken)) {
                $this->systemExceptionReturn(8002, 'token有误', $dispatch); //token有误
                continue;
            }
            $dispatchName = isset($dispatchMap[$request['dispatch']]) ? $dispatchMap[$request['dispatch']] : null;
            //判断调度名是否存在
            if (is_null($dispatchName)) {
                $this->systemExceptionReturn(8004, '调度名不可用', $dispatch);  //调度名不可用
                continue;
            }

            //判断token
            if (!in_array($dispatchName, $dispatchNotToken)) {
                $token = isset($request['params']['token']) ? $request['params']['token'] : '';

                $tokenClass = ApiHelper::getTokenClass($version);
                if (empty($tokenClass)) {
                    $this->systemExceptionReturn(8008, 'tokenClass未设置', $dispatch);  //tokenClass未设置
                    continue;
                }
                $tmp = new $tokenClass;

                $data = $tokenClass::find()
                    ->where(['token' => $token])
                    ->asArray()
                    ->one();

                if (is_null($data)) {
                    $this->systemExceptionReturn(8005, 'token失效', $dispatch);  //token失效
                    continue;
                } else {
                    $autoExpireDays = intval(ApiHelper::getAutoExpireDays($version));
                    if ($autoExpireDays != 0) {
                        //判断时间 多少天过期, 需要重新登录
                        if (time() - intval($data['created_at']) >= 60 * 60 * 24 * $autoExpireDays) {
                            $this->systemExceptionReturn(8006, 'token过期', $dispatch);  //
                            continue;
                        }
                    }
                    $request['params']['user_id'] = $data['user_id'];
                }
            }
            $request['extras'] = $body['extras'];
            $this->dispatch($dispatchName, $request, $dispatch);
        }
        return $this->responsesReturn(0, '成功');

    }

    /**
     * 异常返回
     * @param $errorCode
     * @return array
     */
    public function exceptionReturn($errorCode, $params = [], $data = [], $dispatch)
    {
        $message = "未知错误";
        if (isset($this->errorCodes[$errorCode])) {
            $message = $this->errorCodes[$errorCode];
            if (count($params) != 0) {
                $message = strtr($message, $params);
            }
        }
        if ($errorCode == 8000) {
            $message = "版本有误";
        }

        $ret = [
            'code' => $errorCode,
            'message' => $message,
        ];

        if (count($data) != 0) {
            $ret['data'] = $data;
        } else {
            $ret['data'] = new \stdClass();
        }
        $ret['dispatch'] = $dispatch;
        $this->responses[] = $ret;
    }

    public function systemExceptionReturn($errorCode, $message, $dispatch)
    {
        $ret = [
            'code' => $errorCode,
            'message' => $message,
            'data' => new \stdClass()
        ];

        if (!is_null($dispatch)) {
            $ret['dispatch'] = $dispatch;
        }
        $this->responses[] = $ret;
    }

    /**
     * 开始调度
     * @param $method 调度名称
     * @param array $params
     */
    public function dispatch($dispatchName, $request, $dispatch)
    {
        $classNameSpace = "api\\dispatches\\v" . $request['version'] . "\\";
        $classname = $classNameSpace . $dispatchName;
        $dispatchObj = Yii::createObject([
            'class' => $classname,
            'context' => $this,
            'params' => $request['params'],
            'extras' => $request['extras'],
            'version' => $request['version'],
            'dispatch' => $dispatch,
        ]);
        $dispatchObj->run();
    }

}
