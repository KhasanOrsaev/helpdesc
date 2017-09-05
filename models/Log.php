<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "logs".
 *
 * @property integer $id
 * @property string $text
 * @property string $time
 */
class Log extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'logs';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
            [['time'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'text' => 'Text',
            'time' => 'Time',
        ];
    }
}
