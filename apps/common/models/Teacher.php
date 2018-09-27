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
class Teacher extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%teacher}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['username','phone'], 'string', 'max' => 30],
            [['number'], 'string', 'max' => 80],
            [['wx_id','companyId'], 'integer','max' => 13]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'username',
            'number' => 'number',
            'wx_id' => 'wx_id',
            'phone' => 'phone',
            'companyID' => 'companyID'
        ];
    }
}
