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
 * 须知
 */
class AuctionImage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auction_image}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['images'], 'string', 'max' => 80],
            [['auctionId'], 'integer','max' => 13]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'images' => 'images',
            'auction' => 'auction'
        ];
    }
}
