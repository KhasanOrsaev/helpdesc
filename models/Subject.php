<?php

namespace app\models;

use Yii;
/**
 * This is the model class for table "subjects".
 *
 * @property integer $id
 * @property string $type
 * @property string $text
 * @property string $created_at
 * @property string $updated_at
 * @property string $finished_at
 * @property string $taken_at
 * @property integer $taken_by
 * @property integer $created_by
 * @property integer $level
 * @property string $comments
 * @property string $status
 */
class Subject extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'subjects';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['type', 'text', 'comments','description' ], 'string'],
            [['description'], 'required'],
            [['created_at', 'updated_at', 'finished_at', 'taken_at'], 'safe'],
            [['taken_by', 'created_by', 'level'], 'integer'],
            [['status'], 'string', 'max' => 1],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['taken_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['taken_by' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type' => 'Тип заявки',
            'description' => 'Описание',
            'text' => 'Текст',
            'created_at' => 'Создана',
            'updated_at' => 'Обновлено',
            'time_finish' => 'Срок выполнения',
            'finished_at' => 'Выполнено',
            'taken_at' => 'Взято на исполнение',
            'taken_by' => 'Исполнитель',
            'created_by' => 'Автор заявки',
            'level' => 'Уровень срочности',
            'comments' => 'Комментарии',
            'status' => 'Статус',
        ];
    }

    public function getCreatedBy()
    {
        return $this->hasOne(User::className(), ['id' => 'created_by']);
    }

    public function getTakenBy()
    {
        return $this->hasOne(User::className(), ['id' => 'taken_by']);
    }
}
