<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Subject */

$this->title = $model->id;

?>
<div class="subject-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <div class="alert" style="background: <?=$model->statuses->color?>; color: white; opacity: .5"><b><?=$model->statuses->name?></b></div>
    <p>
            <? if(isset($model->taken_by) and $model->taken_by==Yii::$app->user->id && $model->status=='A')
                echo Html::a('Выполнено', ['done', 'id' => $model->id], ['class' => 'btn btn-success']);
            if(!isset($model->taken_by) && $model->status=='D' && Yii::$app->user->identity->is_it) {
                echo Html::button('Взять',[
                    'class' => 'btn btn-warning',
                    'data-toggle' => 'modal',
                    'data-target' => '#take'
                ]);
                echo Html::a('Отправить на уточнение', ['explain','id' => $model->id], [
                    'class' => 'btn btn-default'
                ]);
            }
            if(Yii::$app->user->identity->is_dept_chief==1 && Yii::$app->user->identity->dept_id==$model->from_dept && $model->status=='C'){
                echo Html::a('Подтвердить', ['confirm', 'id' => $model->id], [
                    'class' => 'btn btn-warning',
                    'data' => [
                        'confirm' => 'Подвердить заявку?',
                        'method' => 'post',
                    ],
                ]); }
            if(Yii::$app->user->id==$model->taken_by && $model->status=='A' && $model->senior!=1){
                echo Html::button('Перенаправить/продлить',[
                    'class' => 'btn btn-warning',
                    'style' => 'margin:0 1%',
                    'data-toggle' => 'modal',
                    'data-target' => '#redirect'
                ]);
            }
            if(Yii::$app->user->identity->is_admin==1 ){
                echo Html::a('Удалить', ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => 'Уверены что хотите удалить?',
                        'method' => 'post',
                    ],
                ]); }

            if($model->created_by == Yii::$app->user->id && $model->status=='W') {
                Modal::begin([
                    'header' => '<h2>Редактировать</h2>',
                    'toggleButton' => ['label' => 'Редактировать', 'class' => 'btn btn-default'],
                ]);
                echo $this->render('/forms/createUpdateSubj', [
                    'model' => $model,
                    'depts' => $depts
                ]);

                Modal::end();
            }

            if(Yii::$app->user->identity->is_admin==1 && $model->status=='D' && $model->status=='A'){
                Modal::begin([
                    'header' => '<h3>Исполнитель</h3>',
                    'toggleButton' => [
                        'tag' => 'button',
                        'class' => 'btn btn-primary',
                        'label' => 'Назначить исполнителя',
                    ]
                ]);

                $form = ActiveForm::begin([
                    'action' => '/set-worker/'.$model->id
                ]);
                echo $form->field($model,'taken_by')->dropDownList($users);
                echo $form->field($model,'comments')->textInput();?>
                <input type="time" name="time" class="form-control" style="margin: 2% 0" required>
        <?
                echo Html::submitButton('Сохранить', ['class' => 'btn btn-success']);
                ActiveForm::end();
                Modal::end();
                }
            ?>
        </p>

    <?= DetailView::widget([
        'model' => $model,
        'template' => '<tr><th {captionOptions} width="100">{label}</th><td {contentOptions} width="200">{value}</td></tr>',
        'attributes' => [
                'id',
            [
                'label' => 'Тип',
                'value' =>  $model->type=='default'?'Общие':'ЛИМС'
            ],
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
                'value' => ($model->updated_at=='0000-00-00 00:00:00')?'-':date('H:i d.m.Y',strtotime($model->updated_at))
            ],
            [
                'label'=> 'Закончено',
                'value' => ($model->finished_at=='0000-00-00 00:00:00')?'-':date('H:i d.m.Y',strtotime($model->finished_at))
            ],
            [
                'label'=> 'Взято на выполнение (время)',
                'value' => ($model->taken_at=='0000-00-00 00:00:00')?'-':date('H:i d.m.Y',strtotime($model->taken_at))
            ],
            [
                'label' => 'Исполнитель',
                'value' => $model->taken_by?$model->takenBy->name:'-'
            ],
            [
                'label' => 'От кого',
                'value' => $model->createdBy->name
            ],
            [
                'label' => 'Департамент',
                'value' => $model->dept->dept_name
            ],
            [
                'attribute' => 'level',
                'visible' => Yii::$app->user->identity->is_it
            ],
            'comments:ntext',
            [
                'label' => 'Статус',
                'value' => $model->statuses->name
            ],
            [
                'attribute' => 'file',
                'value' => $model->fileName(),
                'format' => 'raw'
            ]
        ],
    ]) ?>
</div>
<hr>
<h3>История</h3>
<?
foreach($model->history as $history){
    echo "<p style='font-size: 1.1em; background: #eceec7; padding: 1%; border-radius: 5px;'><i>$history->logdate</i><br>$history->description</p><hr>";
}
?>
<?
Modal::begin([
    'header' => '<h3>Назначить исполнителя</h3>',
    'id' => 'take'
]);

$form = ActiveForm::begin([
    'action' => '/take/'.$model->id
]) ?>
<label for="time"> Требуемое время на выполнение</label>
<input type="time" name="time" class="form-control" style="margin: 2% 0" required>
<?
echo Html::submitButton('Ok', ['class' => 'btn btn-primary']);
ActiveForm::end();
Modal::end();

Modal::begin([
    'header' => '<h3>Переназначить/продлить</h3>',
    'id' => 'redirect'
]);

$form = ActiveForm::begin([
    'action' => '/redirect/'.$model->id
])

?>
<label for="time"> Требуемое время на выполнение</label>
<input type="time" name="time" class="form-control" style="margin: 2% 0" required>
<?
echo $form->field($model,'taken_by')->dropDownList($users);
echo $form->field($model,'comments')->textInput(['required'=>true]);
echo Html::submitButton('Ok', ['class' => 'btn btn-primary']);
ActiveForm::end();
Modal::end();
?>
