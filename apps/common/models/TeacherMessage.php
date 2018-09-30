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
class TeacherMessage extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%teacher_message}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string', 'max' => 300],
            [['auctionId','auctionProductId','teacherId','created_at'], 'integer','max' => 13]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'content' => 'content',
            'auctionId' => 'auctionId',
            'auctionProductId' => 'auctionProductId',
            'teacherId' => 'teacherId',
            'created_at' => 'created_at'
        ];
    }
}
