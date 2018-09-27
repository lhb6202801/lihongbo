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
 * 公告
 */
class Notice extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notice}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['noticeName'], 'string', 'max' => 80],
            [['content'], 'string','max' => 8000]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'noticeName' => 'noticeName',
            'content' => 'content'
        ];
    }
}
