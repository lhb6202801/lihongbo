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
class Auction extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%auction}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name'], 'string', 'max' => 80],
            [['begindate','noticeId','companyId','notesId','teacherId'], 'integer','max' => 13],
            [['biddersTicket','watchTicket','state'], 'safe']
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
            'begindate' => 'begindate',
            'noticeId' => 'noticeId',
            'companyId' => 'companyId',
            'created_at' => 'created_at',
            'notesId' => 'notesId',
            'teacherId' => 'teacherId',
            'biddersTicket' => 'biddersTicket',
            'watchTicket' => 'watchTicket'
        ];
    }
}
