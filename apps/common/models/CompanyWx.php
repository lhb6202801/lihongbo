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
 * 企业邀请微信表
 */
class CompanyWx extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%company_wx}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['created_at','wxId','companyId'], 'integer','max' => 13]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'companyId' => 'companyId',
            'wxId' => 'wxId',
            'created_at' => 'created_at'
        ];
    }
}
