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
class AuctionInviter extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auction_inviter}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['auctionId','wxId','companyId'], 'integer','max' => 13],
            [['type'], 'integer','max' => 4],
            [['auctionNumber'], 'string','max' => 30]
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
            'auctionId' => 'auctionId',
            'type' => 'type',
            'auctionNumber' => 'auctionNumber'
        ];
    }
}
