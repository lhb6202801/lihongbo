<?php

namespace common\models;



/**
 * This is the model class for table "{{%config_api_error}}".
 *
 * @property integer $id
 * @property string $code
 * @property string $message
 * @property string $version
 * @property string $description
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%menu}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'parent', 'icon', 'label','code'], 'string', 'max' => 128],
            [['order'], 'integer', 'max' => 11],
            [['data'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Code',
            'parent' => 'Message',
            'icon' => '版本号',
            'lable' => '描述',
            'code' => '描述',
            'order' => '描述',
            'data' => '描述',
        ];
    }
}
