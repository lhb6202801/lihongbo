<?php
namespace api\base;

use Yii;
/**
 * Class Dispatch
 * @package api\base
 * @author
 * @date
 */
class Dispatch
{
    public $context;

    public $dispatch;
    /**
     * 参数
     * @var array
     */
    public $params = [];

    /**
     * 附加信息
     * @var array
     */
    public $extras = [];

    public $version = 1;

    /**
     * 验证参数
     * @param $name 参数名
     * @param $type 验证类型，只验成3种  required, string, numeric
     */
    public function validateParam($name, $type = 'required')
    {
        /*$types = explode(",", $type);  //可能有2个
        foreach($types as $validateType) {
            switch(trim($validateType)) {
                case 'required':
                    if (!isset($this->params[$name])) {
                        throw new BadRequestHttpException('Param: '. $name .' Not found');
                    }
                    break;
                case 'string':
                    if (isset($this->params[$name]) && !is_string($this->params[$name])) {
                        throw new BadRequestHttpException('Param: '. $name .' is not String');
                    }
                    break;
                case 'numeric':
                    if (isset($this->params[$name]) && !is_numeric($this->params[$name])) {
                        throw new BadRequestHttpException('Param: '. $name .' is not Numeric');
                    }
                    break;
            }
        }*/
    }

    /**
     * 验证参数
     * @param $names
     * @throws BadRequestHttpException
     */
    public function validateParams($names)
    {
        if (is_array($names)) {
            foreach($names as $key => $value) {
                if (is_numeric($key)) {
                    $this->validateParam($value);
                } else {
                    $this->validateParam($key, $value);
                }
            }
        }
    }


    /**
     * 错误返回
     * @param $errorCode
     * @return array
     */
    public function errorReturn($errorCode, $params = [], $data = [])
    {
        
        return $this->context->exceptionReturn($errorCode, $params, $data, $this->dispatch);
    }


    public function paramsErrorReturn()
    {
        return $this->context->systemExceptionReturn(8007, '请求参数有误', $this->dispatch);
    }
    /**
     * 成功返回
     * @param array $data
     * @return array
     */
    public function successReturn($data = [])
    {
        if (is_array($data) && count($data) == 0) {
            $data = new \stdClass();
        }
        $this->context->responses[] = [
            'code' => 0,
            'message' => '成功',
            'data' => $data,
            'dispatch' => $this->dispatch
        ];
        return;
    }
}
