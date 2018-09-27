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
class Notes extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%notes}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title'], 'string', 'max' => 80],
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
            'title' => 'title',
            'content' => 'content'
        ];
    }
}
