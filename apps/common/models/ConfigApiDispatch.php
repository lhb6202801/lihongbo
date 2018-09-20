<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%config_api_dispatch}}".
 *
 * @property integer $id
 * @property string $dispatch
 * @property string $class
 * @property string $version
 * @property integer $allowed
 * @property integer $token
 * @property string $description
 * @property string $request_eg
 * @property string $response_eg
 * @property string $detail
 */
class ConfigApiDispatch extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%config_api_dispatch}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['allowed', 'token'], 'integer'],
            [['dispatch', 'class', 'type', 'version', 'description'], 'string', 'max' => 255],
            [['request_eg', 'response_eg', 'response_error_eg', 'detail'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'dispatch' => Yii::t('app', '方法名'),
            'class' => Yii::t('app', '类'),
            'version' => Yii::t('app', '版本'),
            'allowed' => Yii::t('app', '是否允许'),
            'token' => Yii::t('app', '需要token'),
            'description' => Yii::t('app', '描述'),
            'request_eg' => Yii::t('app', '请求示例'),
            'response_eg' => Yii::t('app', '响应示例'),
            'response_error_eg' => Yii::t('app', '响应错误示例'),
            'detail' => Yii::t('app', '详述'),
            'type' => Yii::t('app', '分类')
        ];
    }

}
