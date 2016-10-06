<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Subject */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Subjects', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="subject-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
            <? if(isset($model->taken_by) and $model->taken_by==Yii::$app->user->id)
                echo Html::a('Выполнено', ['done', 'id' => $model->id], ['class' => 'btn btn-success']) ?>
            <? if(!isset($model->taken_by))
                echo Html::a('Взять', ['take', 'id' => $model->id], ['class' => 'btn btn-warning']) ?>
            <? if(Yii::$app->user->identity->is_admin==1){
             echo Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
            'data' => [
                        'confirm' => 'Уверены что хотите удалить?',
                'method' => 'post',
            ],
        ]); }
            ?>
        </p>

    <?= DetailView::widget([
            'model' => $model,
        'attributes' => [
                'id',
            [
                'label' => 'Тип',
                'value' =>  $model->type=='default'?'Общие':'ЛИМС'
            ],
            'text:ntext',
            'description',
            [
                'label'=> 'Дата регистрации',
                'value' => date('H:i d.m.Y',strtotime($model->created_at))
            ],
            [
                'label'=> 'Срок выполнения',
                'value' => ($model->time_finish=='0000-00-00 00:00:00')?'бессрочно':date('H:i d.m.Y',strtotime($model->time_finish))
            ],
            [
                'label'=> 'Обновлено',
                'value' => ($model->updated_at=='0000-00-00 00:00:00')?'ыххх':date('H:i d.m.Y',strtotime($model->updated_at))
            ],
            [
                'label'=> 'Закончено',
                'value' => ($model->finished_at=='0000-00-00 00:00:00')?'ыххх':date('H:i d.m.Y',strtotime($model->finished_at))
            ],
            [
                'label'=> 'Взято на выполнение',
                'value' => ($model->taken_at=='0000-00-00 00:00:00')?'ыххх':date('H:i d.m.Y',strtotime($model->taken_at))
            ],
            [
                'label' => 'Исполнитель',
                'value' => $model->taken_by?$model->takenBy->name:'Астраханцев'
            ],
            [
                'label' => 'От кого',
                'value' => $model->createdBy->name
            ],
            'level',
            'comments:ntext',
            'status',
        ],
    ]) ?>

</div>
