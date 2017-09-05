<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "depts".
 *
 * @property integer $id
 * @property string $dept_name
 */
class dept extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'depts';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['dept_name'], 'required'],
            [['dept_name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'dept_name' => 'Dept Name',
        ];
    }
    public function getUsers()
    {
        return $this->hasMany(User::className(), ['dept_id' => 'id']);
    }
}
