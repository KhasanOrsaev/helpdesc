<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "Users".
 *
 * @property integer $id
 * @property string $user_name
 * @property string $display_name
 * @property integer $dept_id
 * @property string $is_admin
 * @property string $is_chief
 * @property string $is_dept_chief
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'users';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['user_name', 'display_name'], 'required'],
            [['dept_id'], 'integer'],
            [['is_admin', 'is_chief', 'is_it', 'is_dept_chief','org','email'], 'string'],
            [['user_name', 'display_name'], 'string', 'max' => 100],
            [['dept_id'], 'exist', 'skipOnError' => true, 'targetClass' => Dept::className(), 'targetAttribute' => ['dept_id' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_name' => 'Логин',
            'display_name' => 'ФИО',
            'org' => 'Организация',
            'email' => 'Почта',
            'dept_id' => 'Dept ID',
            'is_admin' => 'Админ',
            'is_chief' => 'Is Chief',
            'is_dept_chief' => 'Is Dept Chief',
        ];
    }

    public static function findIdentity($name)
    {
        return static::findOne($name);
    }

    /**
     * Finds an identity by the given token.
     *
     * @param string $token the token to be looked for
     * @return IdentityInterface|null the identity object that matches the given token.
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['access_token' => $token]);
    }

    /**
     * @return int|string current user ID
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string current user auth key
     */
    public function getAuthKey()
    {
        return $this->id;
    }

    /**
     * @param string $authKey
     * @return boolean if auth key is valid for current user
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }
    public function getDept(){
        return $this->hasOne(Dept::className(),['id'=>'dept_id']);
    }
    public function getSubjects()
    {
        return $this->hasMany(Subject::className(), ['created_by' => 'id']);
    }

    public function getSubjects0()
    {
        return $this->hasMany(Subject::className(), ['taken_by' => 'id']);
    }

    public function getName(){
        return $this->display_name;
    }
}
