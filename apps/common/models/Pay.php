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
class Pay extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%pay}}';
    }
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wxId','auctionId','payDate'], 'integer', 'max' => 13],
            [['state'], 'integer','max' => 4],
            [['payValue'], 'safe']
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'wxId' => 'wxId',
            'auctionId' => 'auctionId',
            'payDate' => 'payDate',
            'state' => 'state',
            'payValue' => 'payValue'
        ];
    }
}
