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
class InviterSeting extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%inviter_Seting}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['wxId','auctionId'], 'integer', 'max' => 13],
            [['biddingHow','inHow'], 'integer','max' => 10],
            [['product'], 'string', 'max' => 512]
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
            'product' => 'product',
            'biddingHow' => 'biddingHow',
            'inHow' => 'inHow'
        ];
    }
}
