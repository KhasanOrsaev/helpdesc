<?php

namespace app\controllers;

use app\models\Log;
use app\models\Subject;
use app\models\SubjectSearch;
use app\models\User;
use mPDF;
use yii\web\NotFoundHttpException;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;

class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => ['print','logout','index','create','update',]
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                        'actions' => ['login']
                    ],
                    [
                        'allow' => true,
                        'actions' => ['view', 'done', 'take'],
                        'matchCallback' => function($role, $action){
                            return Yii::$app->user->identity->is_it?true:false;
                        }
                    ],
                    [
                        'allow' => true,
                        'actions' => ['delete'],
                        'matchCallback' => function($role, $action){
                            if(Yii::$app->user->identity->is_admin==1 or Yii::$app->user->identity->is_chief==1)
                                return true;
                            return false;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'print' => ['POST','get'],
                    'logout' => ['post'],
                    'create' => ['post'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $model = new Subject();
        $searchModel = new SubjectSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'model' => $dataProvider,
            'mod' => $model
        ]);
    }

    public function actionCreate()
    {
        $model = new Subject();
        $log = new Log();
        $model->created_by = Yii::$app->user->id;
        $model->created_at = date('Y-m-d H:i:s');
        $model->status = 'D';
        $model->text = isset(Yii::$app->request->post()['typeName'])?Yii::$app->request->post()['typeName']:'';
        /**
         * Сохраняем департамент, если прежде был пуст
         */
        if(isset(Yii::$app->request->post()['Subject']['dept_id'])){
            $deptid = Yii::$app->request->post()['Subject']['dept_id'];
            $users = User::findOne(Yii::$app->user->id);
            $users->dept_id = $deptid;
            $users->update();
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()){
            switch ($model->type){
                case 'default':

                    $mail = Yii::$app->mailer->compose()
                        ->setFrom(['portal@nacpp.ru'=>'HELPDESC'])
                        ->setTo('it@nacpp.ru')
                        ->setCc('saenkok@nacpp.ru')
                        ->setSubject('Новая заявка №'.$model->id)
                        ->setTextBody($model->text.' || '.$model->description)
                        ->setHtmlBody('<a href="192.168.0.2:84/view?id="'.$model->id.'> <b>Ссылка</b></a>');
                    if(isset(Yii::$app->request->post()['code'])){
                        $pdf = new mPDF();
                        $pdf->WriteHTML(Yii::$app->request->post()['code']);
                        $mail->attach($pdf->Output());
                    }
                        $mail->send();
                    break;
                case 'lims':
                    Yii::$app->mailer->compose()
                        ->setFrom(['portal@nacpp.ru'=>'HELPDESC'])
                        ->setTo('it@nacpp.ru')
                        ->setCc(['supportlims@nacpp.ru','saenkok@nacpp.ru'])
                        ->setSubject('Новая заявка №'.$model->id)
                        ->setTextBody($model->text.' || '.$model->description)
                        ->setHtmlBody('<a href="localhost:84/view?id='.$model->id.'"> <b>Ссылка</b></a>')
                        ->send();
                    break;
            }
;
            $log->text = 'Создана заявка пользователем '.Yii::$app->user->identity->user_name.' номер - '.$model->id;
            $log->time = date('Y-m-d H:i');
            $log->save();
            $this->redirect('/');
        } else {
            $log->text = 'Ошибка при создании '.$model->getErrors();
            $log->time = date('Y-m-d H:i');
            $log->save();
            $this->redirect('/');
        }

    }

    public function actionUpdate($id)
    {
        $model = $this->findModel($id);
        $log = new Log();
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            $log->text = 'Изменена заявка пользователем '.Yii::$app->user->identity->user_name.' номер - '.$model->id;
            $log->time = date('Y-m-d H:i');
            $log->save();
            return $this->redirect('/');
        } else {
            $log->text = 'Ошибка при создании '.$model->getErrors();
            $log->time = date('Y-m-d H:i');
            $log->save();
            return $this->redirect('/');
        }
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }
        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->status = 'C';
        $model->update();
        return $this->redirect(['/']);
    }

    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }
    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    public function actionDone($id)
    {
        $model = $this->findModel($id);
        $model->status = 'T';
        $model->finished_at = date('Y-m-d H:i');
        if ($model->save()) {
            $log = new Log();
            $log->text = 'Заявка номер - '.$id.' выполнена';
            $log->time = date('Y-m-d H:i');
            $log->save();
            $this->redirect('/');
        }
    }

    public function actionTake($id){
        $model = Subject::findOne($id);
        $model->status = 'L';
        $model->taken_by = Yii::$app->user->id;
        $model->taken_at = date('Y-m-d H:i');
        $model->save();
        $log = new Log();
        $log->text = 'Заявка номер - '.$id.' взята пользователем '.Yii::$app->user->identity->user_name;
        $log->time = date('Y-m-d H:i');
        $log->save();
        $this->redirect('/');

    }

    public function actionPrint(){
        $pdf = new mPDF();
        $pdf->WriteHTML(Yii::$app->request->post()['code']);
        $pdf->Output();
    }

    protected function findModel($id)
    {
        if (($model = Subject::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
