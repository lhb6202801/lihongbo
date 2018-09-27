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
class Wx extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%wx}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wx_id'], 'string', 'max' => 128],
            [['sex','age'], 'integer','max' => 4],
            [['created_at'], 'integer','max' => 13],
            [['address','nickname','name','phone'], 'string', 'max' => 30],
            [['avatar'],'string', 'max' => 256]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'name',
            'wx_id' => 'openid',
            'sex' => 'sex',
            'age' => 'age',
            'created_at' => 'created_at',
            'address' => 'address',
            'nickname' => 'nickname',
            'phone' => 'phone',
            'avatar' => 'avatar'
        ];
    }
}
