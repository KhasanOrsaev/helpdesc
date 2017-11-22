<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "statuses".
 *
 * @property string $symbol
 * @property string $name
 * @property string $color
 */
class Statuses extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'statuses';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['symbol'], 'string', 'max' => 1],
            [['name'], 'string', 'max' => 10],
            [['color'], 'string', 'max' => 7],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'symbol' => 'Symbol',
            'name' => 'Name',
            'color' => 'Color',
        ];
    }
}
