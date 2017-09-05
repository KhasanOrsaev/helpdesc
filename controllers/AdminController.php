<?php

namespace app\controllers;

use yii\web\Response;
use app\models\Task;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use Yii;
use yii\web\UploadedFile;

class AdminController extends \yii\web\Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => ['index','tasks','create', 'upload','update','show','delete'],
                        'matchCallback' => function($role, $action){
                            return Yii::$app->user->identity->is_admin?true:false;
                        }
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                    'create' => ['post'],
                    'update' => ['post'],
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
    public function actionIndex()
    {
        return $this->render('index');
    }
    public function actionTasks()
    {
        $tasks = Task::find()->orderBy(['name'=>SORT_ASC])->all();
        $model = new Task();
        $model->orientation = 'P';
        return $this->render('tasks',['model'=>$model, 'tasks'=>$tasks]);
    }
    public function actionCreate(){
        $model = new Task();
        if($model->load(Yii::$app->request->post())){
            $model->code = preg_replace( "/\r|\n/", "", $model->code );
            $model->header = preg_replace( "/\r|\n/", "", $model->header );
            $model->footer = preg_replace( "/\r|\n/", "", $model->footer );
            if(!$model->save()){
                exit;
            }
        }
        $this->redirect('/admin/tasks');
    }
    public function actionDelete($id){
        Task::deleteAll('id='.$id);
        $this->redirect('/admin/tasks');
    }
    public function actionUpload($name){
        $path = Yii::$app->getBasePath()."/upload/".$name;

        if(file_exists($path)){
            Yii::$app->response->sendFile($path);
        }
    }
    public function actionUpdate(){

        $model = Task::findOne(Yii::$app->request->post()['Task']['id']);
        //file_put_contents('text.txt',preg_replace( "/\r|\n/", "", $code ));
        if($model->load(Yii::$app->request->post())){
            $model->code = preg_replace( "/\r|\n/", "", $model->code);
            if(!$model->save()){
                echo "error when saving";
            }
        }
        $this->redirect('/admin/tasks');
    }
    public function actionShow($id){
        //Yii::$app->response->format = Response::FORMAT_JSON;
        $item = Task::findOne($id)->toArray();
        return json_encode($item, JSON_UNESCAPED_UNICODE);
    }

}
