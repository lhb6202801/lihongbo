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
class AuctionProduct extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auction_product}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 80],
            [['beginValue','stepValue','state',], 'safe'],
            [['freeOffer','timeLimitOffer','auctionId','companyId'], 'integer','max' => 13],
            [['introduce'],'string', 'max' => 8000]
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
            'beginValue' => 'beginValue',
            'stepValue' => 'stepValue',
            'freeOffer' => 'freeOffer',
            'timeLimitOffer' => 'timeLimitOffer',
            'introduce' => 'introduce',
            'auctionId' => 'auctionId',
            'companyId' => 'companyId'
        ];
    }
}
