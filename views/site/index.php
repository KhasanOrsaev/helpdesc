<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;

$this->title = 'HELPDESK';
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Заявки в IT отдел </h1>
        <div class="body-content">
            <div class="row">
                <div class="col-lg-10 col-lg-offset-1">
                    <button class="btn btn-success pull-left" data-toggle="modal" data-target="#new">Новая заявка</button>
                </div>
                <div class="col-lg-12">
                    <?= GridView::widget([
                        'dataProvider' => $model,
                        'filterModel' => $searchModel,
                        'columns' => [
                            [
                                'attribute' => 'id',
                                'filter' => false
                            ],
                            [
                                'attribute' => 'type',
                                'filter' => ['default'=>'ОБЩИЕ','lims'=>'ЛИМС'],
                                'value' => function($data){
                                    return $data['type']=='default'?'Общие':'ЛИМС';
                                }
                            ],
                            [
                                'attribute' => 'description',
                                'format' => 'ntext',
                                'value' => function($data){
                                    if(isset($data['description']))
                                        return $data['description'];
                                    return $data['text'];
                                }
                            ],
                            [
                                'attribute' => 'created_at',
                                'format' => ['date','HH:mm dd.MM.Y'],
                                'options' => ['width' => '200']
                            ],
                            [
                                'attribute' => 'finished_at',
                                'options' => ['width' => '200'],
                                'value' => function($data){
                                    if($data['updated_at']=='0000-00-00 00:00:00')
                                        return 'Нет';
                                    else return date('H:i d.m.Y',strtotime($data['updated_at']));
                                }
                            ],
                            'createdBy.name:text:Автор',
                                        /**
                                         * ['attribute' => 'createdBy.name',
                                         *  'format' => 'text',
                                         *  'label' => 'Автор']
                                         */
                            'takenBy.name:text:Исполнитель',
                            // 'updated_at',
                            // 'taken_at',
                            // 'taken_by',
                            // 'created_by',
                            // 'level',
                            // 'comments:ntext',
                            [
                                'attribute' => 'status',
                                'format' => 'text',
                                'filter' => false,
                                'contentOptions' => ['style'=>"color: green"],
                                'value' => function($data){
                                    switch($data['status']){
                                        case 'D': return 'Зарегистрирован';
                                        case 'L': return 'В работе';
                                        case 'T': return 'Выполнен';
                                        case 'C': return 'Отменен';
                                    }
                                }
                            ],

                            ['class' => 'yii\grid\ActionColumn',
                                'contentOptions' => ['style' => 'width:5%;'],
                                'header'=>'',
                                'template' => '{view} {delete}',
                                'buttons' => [

                                    //view button
                                    'view' => function ($url, $model) {
                                        return Yii::$app->user->identity->is_it ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, [
                                            'title' => Yii::t('app', 'View'),
                                        ]) : '';
                                    },
                                    'delete' => function ($url, $model) {
                                        if(Yii::$app->user->id==$model->created_by && !isset($model->taken_by))
                                            return Html::a('<span class="glyphicon glyphicon-remove"></span>',$url,[]);
                                    }
                                    ],
                            ],
                        ],
                    ]); ?>
                </div>
            </div>

        </div>
    </div>
</div>
<div class="modal fade" id="new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Новая заявка</h4>
            </div>
            <div class="modal-body">
                <?= $this->render('/forms/createUpdateSubj', [
                    'model' => $mod,
                ]) ?>

        </div>
    </div>
</div>
