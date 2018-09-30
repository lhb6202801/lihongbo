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
class OfferRecord extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%offer_record}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['biddersNumber'], 'string', 'max' => 30],
            [['state'], 'integer','max' => 4],
            [['offer'], 'safe'],
            [['offerDate','wxId','auctionProductId'], 'integer', 'max' => 13]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'biddersNumber' => 'biddersNumber',
            'state' => 'state',
            'created_at' => 'created_at',
            'offer' => 'offer',
            'auctionProductId' => 'auctionProductId',
            'wxId' => 'wxId',
            'offerDate' => 'offerDate'
        ];
    }
}
