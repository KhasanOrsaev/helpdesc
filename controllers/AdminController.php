<?php

namespace app\controllers;

use app\models\User;
use app\models\UserSearch;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\Response;
use app\models\Task;
use app\models\Dept;
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
                        'actions' => ['users','index','tasks','create', 'upload','update','show','delete','get-user','user-modify', 'user-delete'],
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
                    'get-user' => ['post'],
                    'user-modify' => ['post']
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
        return $this->render('tasks',['model'=>$model, 'tasks'=>$tasks]);
    }
    public function actionCreate(){
        $model = new Task();
        if($model->load(Yii::$app->request->post())){
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
        if($model->load(Yii::$app->request->post())){
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

    public function actionUsers() {
        $search = new UserSearch();
        $model = new User();
        $request = Yii::$app->request->post();
        $users = $search->search($request);
        $pages = new Pagination(['totalCount' => $users->count()]);
        $users = $users->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('users',[
            'users' => $users,
            'pages' => $pages,
            'search' => $search,
            'model' => $model,
            'depts' => ArrayHelper::map(Dept::find()->all(),'id','dept_name')
        ]);
    }
    public function actionUserModify(){
        $request = Yii::$app->request->post();
        if(isset($request['id'])){
            $user = User::findOne($request['id']);
        } else {
            $user = new User();
        }
        if($user->load($request) && $user->save()){
            return $this->redirect('/admin/users');
        } else {
            return $this->redirect('/admin/users/?msg=error');
        }
    }

    public function actionGetUser() {
        $id = Yii::$app->request->post('id');
        $user = User::findOne($id)->toArray();
        return json_encode($user);
    }

    public function actionUserDelete($id) {
        User::findOne($id)->delete();
        return $this->redirect('/admin/users');
    }

}
