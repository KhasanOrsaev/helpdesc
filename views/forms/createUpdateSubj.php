<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Dept;
use app\models\Task;

/* @var $this yii\web\View */
/* @var $model app\models\Subject */
/* @var $form yii\widgets\ActiveForm */
?>
<style type="text/css">
    #code {
        margin: 1%;
        padding: 1%;
        background: rgba(189, 168, 168, 0.25);
    }
</style>
<div class="subject-form">

    <? $form = ActiveForm::begin([
        'enableClientValidation' => true,
        'options' => [
            'validateOnSubmit' => true,
            'class' => 'form',
            'enctype' => 'multipart/form-data'
            ],
        'action' => $model->isNewRecord ? '/create' : '/update/'.$model->id,
        'method' => 'post',
    ]); ?>
    <?= $form->field($model, 'from_dept')->dropDownList($depts,['prompt'=>'', 'required' => 'required'])?>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'computer')->textInput()?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'phone')->textInput()?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'address')->textInput()?>
        </div>
        <div class="col-md-12" style="text-align:left; font-size: 1.2em;">
            <?= $form->field($model, 'file[]')->fileInput(['multiple' => true])?>
            <?=$model->fileName()?>
        </div>
    </div>
    <?= $form->field($model, 'type')->dropDownList([ 'default' => 'Общая', 'lims' => 'ЛИМС', ], ['prompt' => '', 'onchange'=>'changeType(this)', 'required'=>'required'])->label('Тип заявки') ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6])->label('Описание') ?>
    <!--div id="code"></div-->
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    <?php ActiveForm::end(); ?>
</div>
<script>
    function changeType(a){
        if($(a).val()=='default'){
            var b = "<?
               $tasks = Task::find()->all();
            $items = \yii\helpers\ArrayHelper::map($tasks,'id','name');
            $str = "<label for='typeName'>Шаблоны</label>";
            $str.= "<select name='typeName' class='form-control' id='template' <!--onchange='changeTemplate(this)->'>";
            $str.="<option value='' selected disabled>Выберите шаблон</option>";
            foreach($items as $key=>$val){
                $str.="<option value='$key'>$val</option>";
            }
            $str.='</select>';
            echo $str;
            ?>";
           $(a).after(b);
        } else {
            $("select[name='typeName']").remove();
            $("label[for='typeName']").remove();
        }
    }
    function changeTemplate(a){
        $.ajax({
            method: "POST",
            data: {id:$(a).val()},
            url: "/getask",
            dataType: "html",
            success: function(data){
                $('#code').empty(); $('#code').append(data);
            }

        });
    }
</script>
