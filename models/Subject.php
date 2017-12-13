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
            [['type', 'text', 'comments','description','computer','address'], 'string'],
            [['is_confirmed','senior' ], 'boolean'],
            [['description'], 'required'],
            [['created_at', 'updated_at', 'finished_at', 'taken_at'], 'safe'],
            [['taken_by', 'created_by', 'from_dept', 'level', 'phone'], 'integer'],
            [['status'], 'exist', 'skipOnError' => true, 'targetClass' => Statuses::className(), 'targetAttribute' => ['status' => 'symbol']],
            [['created_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['created_by' => 'id']],
            [['taken_by'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['taken_by' => 'id']],
            [['file'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, doc, xml, txt, xlsx, png, jpg', 'maxFiles' => 4],
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
            'time_finish' => 'Срок выполнения (мин)',
            'finished_at' => 'Выполнено',
            'taken_at' => 'Взято на исполнение',
            'taken_by' => 'Исполнитель',
            'created_by' => 'Автор заявки',
            'computer' => 'Компьютер',
            'address' => 'Местоположение',
            'level' => 'Уровень срочности',
            'comments' => 'Комментарии',
            'phone' => 'Телефон',
            'status' => 'Статус',
            'from_dept' => 'Ваш департамент',
            'file' => 'Приложение'
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

    public function getStatuses() {
        return $this->hasOne(Statuses::className(),['symbol' => 'status']);
    }

    public function getTasks() {
        return $this->hasOne(Task::className(),['id' => 'text']);
    }

    public function getDept() {
        return $this->hasOne(Dept::className(),['id' => 'from_dept']);
    }

    public function getHistory() {
        return $this->hasMany(History::className(),['subject_id' => 'id']);
    }
    
    public function upload()
    {
        $name = '';
        if ($this->validate()) {
            foreach ($this->file as $file) {
                $file->saveAs('uploads/' . $file->baseName . '.' . $file->extension);
                $name.=$file->baseName . '.' . $file->extension.';';
            }
            $this->file = mb_substr($name,0,-1);
            return true;
        } else {
            return false;
        }
    }
    public function fileName(){
        if(isset($this->file)){
            $text = '<i class="glyphicon glyphicon-paperclip"></i>';
            foreach(explode(';',$this->file) as $file){
                $text.= "<a href='/uploads/$file'>$file</a>,";
            }
            return mb_substr($text,0,-1);
        }
    }
}
