<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Subject */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="subject-form">

    <? $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'comments')->textarea(['rows' => 6])->label('Комментарии') ?>

</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>
