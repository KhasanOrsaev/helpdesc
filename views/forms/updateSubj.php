<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Subject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subject-form">

    <? $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'options' => [
            'validateOnSubmit' => true,
            'class' => 'form'
        ],
        'action' => \yii\helpers\Url::to(['/site/update']).'?id='.$model->id,
    ]); ?>

    <?= $form->field($model, 'type')->dropDownList([ 'default' => 'Общая', 'lims' => 'ЛИМС', ], ['prompt' => ''])->label('Тип заявки') ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6])->label('Описание') ?>


</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>
