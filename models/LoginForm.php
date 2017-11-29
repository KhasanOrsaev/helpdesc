<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\helpers\Url;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class LoginForm extends Model
{
    public $username;
    public $password;
    public $rememberMe = true;

    private $_user = false;

    private $config = [
        'account_suffix'        => '@int',
        'domain_controllers'    => ['192.168.0.99'],
        'base_dn'               => 'dc=int,dc=nacph,dc=ru',
        'admin_username'        => 'Portal',
        'admin_password'        => 'QWEasd234',
    ];


    public function attributeLabels()
    {
        return [
            'username' => 'Логин',
            'password' => 'Пароль',
        ];
    }
    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // username and password are both required
            [['username', 'password'], 'required', 'message' => 'Заполните поле.'],
            // rememberMe must be a boolean value
            //['rememberMe', 'boolean'],
            // password is validated by validatePassword()
            //['password', 'validatePassword'],
        ];
    }

    /**
     * Validates the password.
     * This method serves as the inline validation for password.
     *
     * @param string $attribute the attribute currently being validated
     * @param array $params the additional name-value pairs given in the rule
     */

    public function validatePassword($attribute, $params)
    {
        if (!$this->hasErrors()) {
            $user = $this->getUser();

            if (!$user || !$user->validatePassword($this->password)) {
                $this->addError($attribute, 'Incorrect username or password.');
            }
        }
    }

    /**
     * Logs in a user using the provided username and password.
     * @return boolean whether the user is logged in successfully
     */
    public function login()
    {
        //var_dump($this->validate());die;
        if ($this->validate()) {
            if($this->getUser())
                return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600*24*30 : 0);
            else Yii::$app->response->redirect(Url::to(['/']));
        }
        return false;
    }

    /**
     * Finds user by [[username]]
     *
     * @return User|null
     */
    public function getUser()
    {
        $a = $this->checkAdldap($this->username,$this->password);
        if($a)
        {
            if(!$user = User::find()->where(['user_name'=>$this->username])->one()) {
                $user = new User();
                $user->user_name = $this->username;
                $user->display_name = $a['displayname'][0];
                $user->email = $a['mail'][0];
                if(strpos($a['distinguishedname'][0],'OU=НАКФФ ЦСМ')){
                    $user->org = 'csm';
                }
                elseif(strpos($a['distinguishedname'][0],'OU=НАКФФ ИКИ ФЭ')){
                    $user->org = 'iki';
                }
                elseif(strpos($a['distinguishedname'][0],'OU=IT')){
                    $user->org = 'nacpp';
                    $user->is_it = '1';
                }
                else $user->org = 'nacpp';
                $user->save();
                //var_dump($user->getErrors());die;
            }
            return User::findIdentity($user->id);
        }

        return $this->_user;
    }

    /**
     * @param $username
     * @param $password
     * @return bool
     * @throws \Adldap\Exceptions\AdldapException
     *
     * Checking with ldap
     */
    public function checkAdldap($username, $password)
    {
        try {
            $ad = ldap_connect('192.168.0.99') or die('error with LDAP connect,' . ldap_error($ad));
            $attributes = ['displayName', 'mail', 'distinguishedName'];
            ldap_set_option($ad, LDAP_OPT_PROTOCOL_VERSION, 3);
            ldap_set_option($ad, LDAP_OPT_REFERRALS, 0);
            $r = ldap_bind($ad, $username.$this->config['account_suffix'], $password) or die('error with LDAP bind,' . ldap_error($ad));
            $sr = ldap_search($ad, $this->config['base_dn'], 'sAMAccountName=' . $username, $attributes);
            $res = ldap_get_entries($ad, $sr);
        } catch (\Exception $e){
            echo $e;
        }
        if(isset($res))
        return $res[0]; else return false;
    }
}
