<?php

namespace app\controllers;

use app\models\History;
use app\models\Log;
use app\models\Statuses;
use app\models\Subject;
use app\models\SubjectSearch;
use app\models\Task;
use app\models\User;
use app\models\Dept;
use app\models\LoginForm;
use mPDF;
use yii\data\Pagination;
use yii\helpers\ArrayHelper;
use yii\web\NotFoundHttpException;
use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\UploadedFile;

class SiteController extends Controller
{
    public $emails = ['OrsaevK.A@nacpp.ru'];
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => ['print','logout','index','create','update','getask', 'confirm', 'model']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['?'],
                        'actions' => ['login']
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => ['view', 'done', 'take', 'redirect', 'explain'],
                        'matchCallback' => function($role, $action){
                            return Yii::$app->user->identity->is_it?true:false;
                        }
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'actions' => ['delete', 'set-worker'],
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
                    'update' => ['post'],
                    'getask' => ['post']
                ],
            ],
            [
                'class' => TimestampBehavior::className(),
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['create_time'],
                    //ActiveRecord::EVENT_BEFORE_UPDATE => ['update_time'],
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
        $depts = ArrayHelper::map(Dept::find()->all(),'id', 'dept_name');
        $dataProvider = $searchModel->search(Yii::$app->request->post());
        if(Yii::$app->request->post('excel','')==1){
            $file = \Yii::createObject([
                'class' => 'codemix\excelexport\ExcelFile',
                'sheets' => [
                    'Отчет' => [
                        'class' => 'codemix\excelexport\ActiveExcelSheet',
                        'query' => $dataProvider,
                        'attributes' => [
                            'id',
                            'type',
                            'created_at',
                            'finished_at',
                            'description',
                            'createdBy.display_name',    // Related attribute
                            'takenBy.display_name',
                            'comments'
                        ],
                    ]
                ]
            ]);
            $file->send('text.xls');

        }
        $pages = new Pagination(['totalCount' => $dataProvider->count()]);
        $models = $dataProvider->offset($pages->offset)
            ->limit($pages->limit)
            ->all();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'model' => $models,
            'mod' => $model,
            'pages' => $pages,
            'depts' => $depts,
            'status' => ArrayHelper::map(Statuses::find()->all(),'symbol','name')
        ]);
    }

    public function actionCreate()
    {
        $model = new Subject();
        $history = new History();
        $log = new Log();
        $model->created_by = Yii::$app->user->id;
        $model->created_at = date('Y-m-d H:i:s');
        $model->status = 'C';
        $model->text = isset(Yii::$app->request->post()['typeName'])?Yii::$app->request->post()['typeName']:'';
        $request = Yii::$app->request->post();
        if ($model->load($request) && $model->save()){
            $user = User::find()->where(['dept_id'=>$model->from_dept, 'is_chief'=>1])->one(); // Начальник департамента
            if (Yii::$app->request->isPost) {
                $model->file = UploadedFile::getInstances($model, 'file');
                if(!empty($model->file) && $model->file !== 0) {
                    if (!$model->upload()) {
                        // file isn't uploaded successfully
                        $log->text = 'Ошибка при загрузки файла';
                        $log->time = date('Y-m-d H:i');
                        $log->save();
                    }
                }
            }
            Yii::$app->mailer->compose()
                ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                //->setTo($user->email)
                ->setCc(['OrsaevK.A@nacpp.ru', Yii::$app->user->identity->email])
                //->setCc(['supportlims@nacpp.ru','saenkok@nacpp.ru'])
                ->setSubject('Заявка на подтверждение №'.$model->id)
                ->setHtmlBody('Заявка на подтверждение №'.$model->id.' <a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>')
                ->send();
            /*switch ($model->type){
                case 'default':
                    $mail = Yii::$app->mailer->compose()
                        ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                        ->setTo('OrsaevK.A@nacpp.ru')
                        //->setCc(['supportlims@nacpp.ru','saenkok@nacpp.ru'])
                        ->setSubject('Новая заявка №'.$model->id)
                        ->setTextBody($model->text.' || '.$model->description)
                        ->setHtmlBody('<a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>');
                    if(Yii::$app->request->post('code',''!='')){
                        $pdf = new mPDF();
                        $pdf->WriteHTML(Yii::$app->request->post('code'));
                        $content = $pdf->Output('','S');
                        $mail->attachContent($content, ['fileName' => 'my-file.pdf', 'contentType' => 'application/pdf']);
                    }
                    $mail->send();
                    break;
                case 'lims':
                    Yii::$app->mailer->compose()
                        ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                        ->setTo('OrsaevK.A@nacpp.ru')
                        //->setCc(['supportlims@nacpp.ru','saenkok@nacpp.ru'])
                        ->setSubject('Новая заявка №'.$model->id)
                        ->setTextBody($model->text.' || '.$model->description)
                        ->setHtmlBody('<a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>')
                        ->send();
                    break;
                default:
            }*/;
            $history->subject_id = $model->id;
            $history->theme = 'Создана заявка пользователем '.Yii::$app->user->identity->user_name.' номер - '.$model->id;
            $history->description = 'Создана заявка пользователем '.Yii::$app->user->identity->user_name.' номер - '.$model->id;
            $log->text = 'Создана заявка пользователем '.Yii::$app->user->identity->user_name.' номер - '.$model->id;
            $history->save();
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
        $history = new History();
        $log = new Log();
        //$model->status = 'C';
        $model->text = isset(Yii::$app->request->post()['typeName'])?Yii::$app->request->post()['typeName']:'';
        $request = Yii::$app->request->post();
        if ($model->load($request)){
            $user = User::find()->where(['dept_id'=>$model->from_dept, 'is_chief'=>1])->one(); // Начальник департамента
            if (Yii::$app->request->isPost) {
                $model->file = UploadedFile::getInstances($model, 'file');
                if(!empty($model->file) && $model->file !== 0) {
                    if (!$model->upload()) {
                        // file isn't uploaded successfully
                        $log->text = 'Ошибка при загрузки файла';
                        $log->time = date('Y-m-d H:i');
                        $log->save();
                    }
                }
                //var_dump($model);die;
                $model->save();
            }
            Yii::$app->mailer->compose()
                ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                //->setTo($user->email)
                ->setCc(['OrsaevK.A@nacpp.ru', Yii::$app->user->identity->email])
                //->setCc(['supportlims@nacpp.ru','saenkok@nacpp.ru'])
                ->setSubject('Заявка на подтверждение №'.$model->id)
                ->setHtmlBody('Заявка на подтверждение №'.$model->id.' <a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>')
                ->send();
            /*switch ($model->type){
                case 'default':
                    $mail = Yii::$app->mailer->compose()
                        ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                        ->setTo('OrsaevK.A@nacpp.ru')
                        //->setCc(['supportlims@nacpp.ru','saenkok@nacpp.ru'])
                        ->setSubject('Новая заявка №'.$model->id)
                        ->setTextBody($model->text.' || '.$model->description)
                        ->setHtmlBody('<a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>');
                    if(Yii::$app->request->post('code',''!='')){
                        $pdf = new mPDF();
                        $pdf->WriteHTML(Yii::$app->request->post('code'));
                        $content = $pdf->Output('','S');
                        $mail->attachContent($content, ['fileName' => 'my-file.pdf', 'contentType' => 'application/pdf']);
                    }
                    $mail->send();
                    break;
                case 'lims':
                    Yii::$app->mailer->compose()
                        ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                        ->setTo('OrsaevK.A@nacpp.ru')
                        //->setCc(['supportlims@nacpp.ru','saenkok@nacpp.ru'])
                        ->setSubject('Новая заявка №'.$model->id)
                        ->setTextBody($model->text.' || '.$model->description)
                        ->setHtmlBody('<a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>')
                        ->send();
                    break;
                default:
            }*/;
            $history->subject_id = $model->id;
            $history->theme = 'Отредактирована заявка пользователем '.Yii::$app->user->identity->user_name.' номер - '.$model->id;
            $history->description = 'Отредактирована заявка пользователем '.Yii::$app->user->identity->user_name.' номер - '.$model->id;
            $log->text = 'Отредактирована заявка пользователем '.Yii::$app->user->identity->user_name.' номер - '.$model->id;
            $history->save();
            $log->save();
            $this->redirect('/');
        } else {
            $log->text = 'Ошибка при редактировании '.$model->getErrors();
            $log->time = date('Y-m-d H:i');
            $log->save();
            $this->redirect('/');
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
        $model->status = 'R';
        $model->update();
        return $this->redirect(['/']);
    }

    public function actionView($id)
    {
        $it_users = User::find()->where(['is_it'=>1])->all();
        return $this->render('view', [
            'model' => $this->findModel($id),
            'users' => ArrayHelper::map($it_users,'id','display_name')
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
        if($model->save()) {
            $arr = $this->getEmails($model);
            Yii::$app->mailer->compose()
                ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                ->setTo($arr)
                ->setCc(array_merge($this->emails,[Yii::$app->user->identity->email]))
                ->setSubject('Заявка выполнена №'.$model->id)
                ->setHtmlBody('Заявка №'.$model->id.' выполнена <a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>')
                ->send();
            $log = new History();
            $log->theme = 'Заявка номер - ' . $id . ' выполнена ';
            $log->subject_id = $id;
            $log->save();
            $this->redirect('/');
        } else echo "error!";
    }

    public function actionTake($id){
        $model = Subject::findOne($id);
        $model->status = 'A';
        $model->taken_by = Yii::$app->user->id;
        $model->taken_at = date('Y-m-d H:i');
        $time = explode(':',Yii::$app->request->post('time'));
        $model->time_finish = date('Y-m-d H:i', strtotime("+$time[0] hours + $time[1] minutes"));

        if($model->save()) {
            $arr = $this->getEmails($model);
            Yii::$app->mailer->compose()
                ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                ->setTo($arr)
                ->setCc(array_merge($this->emails,[Yii::$app->user->identity->email]))
                ->setSubject('Заявка взята на выполнение №'.$model->id)
                ->setHtmlBody('Заявка №'.$model->id.' взята на выполнение сотрудником IT отдела '.Yii::$app->user->identity->display_name.' <a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>')
                ->send();
            $log = new History();
            $log->theme = 'Заявка номер - ' . $id . ' взята пользователем ' . Yii::$app->user->identity->user_name;
            $log->subject_id = $id;
            $log->save();
            $this->redirect('/');
        }

    }

    public function actionExplain($id){
        $model = Subject::findOne($id);
        $model->status = 'W';
        if($model->save()) {
            $arr = $this->getEmails($model);
            Yii::$app->mailer->compose()
                ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                ->setTo($arr)
                ->setCc(array_merge($this->emails,[Yii::$app->user->identity->email]))
                ->setSubject('Заявка отправлена на уточнение №'.$model->id)
                ->setHtmlBody('Заявка №'.$model->id.' отправлена на уточнение сотрудником IT отдела '.Yii::$app->user->identity->display_name.' <a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>')
                ->send();
            $history = new History();
            $log = new Log();
            $log->text = 'Заявка номер - ' . $id . ' отправлена на уточнение ' . Yii::$app->user->identity->user_name;
            $history->theme = 'Заявка номер - ' . $id . ' отправлена на уточнение ' . Yii::$app->user->identity->user_name.'. Комментарий:'.$model->comments;
            $history->subject_id = $id;
            $history->save();
            $log->save();
            $this->redirect('/');
        }

    }

    public function actionSetWorker($id) {
        $model = Subject::findOne($id);
        $request = Yii::$app->request->post();
        $model->status = 'A';
        $model->taken_at = date('Y-m-d H:i');
        $model->senior = 1;
        $time = explode(':',Yii::$app->request->post('time'));
        $model->time_finish = date('Y-m-d H:i', strtotime("+$time[0] hours + $time[1] minutes"));
        if($model->load($request) && $model->save()) {
            $user = User::findOne($model->taken_by);
            if($model->getOldAttribute('taken_by')){
                $text = 'Заявка №'.$model->id.' обновлена, выполняется сотрудником IT отдела '.$user->display_name.' <a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>';
            } else {
                $text = 'Заявка №'.$model->id.' взята на выполнение сотрудником IT отдела '.$user->display_name.' <a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>';
            }
            $arr = $this->getEmails($model);
            Yii::$app->mailer->compose()
                ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                ->setTo($arr)
                ->setCc($this->emails)
                ->setSubject('Заявка выполняется №'.$model->id)
                ->setHtmlBody($text)
                ->send();
            $log = new History();
            $log->theme = 'Заявка номер - ' . $id . ' назначен сотрудник ' . $user->display_name;
            $log->subject_id = $id;
            $log->description = $model->comments;
            $log->save();
            $this->redirect('/');
        }
    }

    public function actionRedirect($id) {
        $model = Subject::findOne($id);
        $request = Yii::$app->request->post();
        $model->status = 'A';
        $time = explode(':',Yii::$app->request->post('time'));
        $model->time_finish = date('Y-m-d H:i', strtotime("+$time[0] hours + $time[1] minutes"));
        if($model->load($request) && $model->save()) {
            $user = User::findOne($model->taken_by);
            $text = 'Заявка №'.$model->id.' обновлена, выполняется сотрудником IT отдела '.$user->display_name.' <a href="192.168.0.2:84/site/view?id='.$model->id.'"> <b>Ссылка</b></a>';
            $arr = $this->getEmails($model);
            Yii::$app->mailer->compose()
                ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                ->setTo($arr)
                ->setCc($this->emails)
                ->setSubject('Заявка выполняется №'.$model->id)
                ->setHtmlBody($text)
                ->send();
            $log = new History();
            $log->theme = 'Заявка номер - ' . $id . ' переадресована сотруднику ' . $user->display_name;
            $log->subject_id = $id;
            $log->description = $model->comments;
            $log->save();
            $this->redirect('/');
        }
    }

    public function actionPrint($id){
        $model = Task::findOne($id);
        $format = 'A4-'.$model->orientation;
        $mgt = (isset($model->header))?45:15;
        $pdf = new mPDF($mode = '', $format, $default_font_size = 10, $default_font = '', $mgl = 15, $mgr = 15, $mgt, $mgb = 16, $mgh = 9, $mgf = 9);
        $pdf->setHTMLHeader($model->header);
        $pdf->setHTMLFooter($model->footer);
        $pdf->WriteHTML(Yii::$app->request->post('code'));
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

    public function actionModel($id){
        return $this->findModel($id);
    }

    protected function getEmails($model){
        $users = User::find()
            ->select(['email'])
            ->where(['dept_id' => $model->from_dept, 'is_chief'=>1])
            ->orWhere(['id'=>$model->created_by])
            ->orWhere(['id'=>$model->taken_by])
            ->asArray()->all();
        $arr = [];
        array_map(function($a) use(&$arr) { $arr[] = $a['email'];}, $users);
        return $arr;
    }

    public function actionGetask(){
        $task = Task::findOne(Yii::$app->request->post('id'));
        return $task['header'].$task['code'].$task['footer'];
    }

    public function actionConfirm($id) {
        $subject = Subject::findOne($id);
        $subject->status = 'D';
        if($subject->save()){
            $user = User::findOne($subject->created_by);
            Yii::$app->mailer->compose()
                ->setFrom(['portal@nacpp.ru'=>'HELPDESK'])
                ->setTo($user->email)
                ->setCc(['OrsaevK.A@nacpp.ru', Yii::$app->user->identity->email])
                //->setCc(['supportlims@nacpp.ru','saenkok@nacpp.ru'])
                ->setSubject('Заявка подтверждена №'.$subject->id)
                ->setHtmlBody('Заявка подтверждена №'.$subject->id.' <a href="192.168.0.2:84/site/view?id='.$subject->id.'"> <b>Ссылка</b></a>')
                ->send();
            return $this->redirect('/view/'.$subject->id);
        } else {
            return $this->redirect(Yii::$app->request->referrer);
        }
    }
}