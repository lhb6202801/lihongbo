<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "{{%config_api_version}}".
 *
 * @property integer $id
 * @property string $version
 * @property string $description
 * @property integer $allowed
 */
class ConfigApiVersion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%config_api_version}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['allowed', 'autoExpireDays'], 'integer'],
            [['version', 'description', 'tokenClass'], 'string', 'max' => 255]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'version' => '版本号',
            'description' => '描述',
            'allowed' => '是否允许',
            'tokenClass' => 'token模型类',
            'autoExpireDays' => '自动失效天数'
        ];
    }

    /**
     * @inheritdoc
     * @return \common\models\query\ConfigApiVersionQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \common\models\query\ConfigApiVersionQuery(get_called_class());
    }
}
