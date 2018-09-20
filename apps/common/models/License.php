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
class License extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%license}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['license', 'note'], 'string', 'max' => 80],
            [['companyId'], 'integer', 'max' => 13]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'license' => '营业执照',
            'companyId' => '公司ID',
            'note' => '说明'
        ];
    }
}
