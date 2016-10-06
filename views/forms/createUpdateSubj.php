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
            'class' => 'form'
            ],
        'action' => 'site/create',
        'method' => 'post',
    ]); ?>
    <?
        if(!isset(Yii::$app->user->identity->dept_id)){
            $depts = Dept::find()->all();
            $items = \yii\helpers\ArrayHelper::map($depts,'id','dept_name');
            echo "<label for='dept_id'>Ваш департамент</label> <select name='dept_id' required class='form-control'><option value='' selected disabled></option>";
            foreach($items as $key=>$val)
                echo "<option value='$key'>$val</option>";
            echo "</select>";
        }
    ?>
    <?= $form->field($model, 'type')->dropDownList([ 'default' => 'Общая', 'lims' => 'ЛИМС', ], ['prompt' => '', 'onchange'=>'changeType(this)', 'required'=>'required'])->label('Тип заявки') ?>
    <?= $form->field($model, 'description')->textarea(['rows' => 6])->label('Описание') ?>
    <div id="code"></div>
</div>
</div>
<div class="modal-footer">
    <button type="button" class="btn btn-default" data-dismiss="modal">Закрыть</button>
    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Обновить', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    <?= Html::button('Печать',['onClick'=>'pageConvert()', 'class'=>'btn btn-warning']) ?>
    <?php ActiveForm::end(); ?>
</div>
<script>
    function changeType(a){
        if($(a).val()=='default'){
            var b = "<?
               $tasks = Task::find()->all();
            $items = \yii\helpers\ArrayHelper::map($tasks,'name','name');
            $str = "<label for='typeName'>Шаблоны</label>";
            $str.= "<select name='typeName' class='form-control' onchange='changeTemplate(this)'>";
            $str.="<option value='' selected disabled>Выберите шаблон</option>";
            foreach($items as $val){
                $str.="<option value='$val'>$val</option>";
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
        switch ($(a).val()){
            <? foreach($tasks as $val){ ?>
                case '<?=$val->name?>':c = ''+<?=($val->code)?"'$val->code'":'""'?>; $('#code').empty(); $('#code').append(c);
                break;
            <? }
            ?>
        }
    }
    function pageConvert(){
        var text = $('#code').html();
        var i=0;
        regex = new RegExp('<input \([^>]*?\)id="'+ 1 + '"([^>]*?)>','g')
        b=text.match(regex);
        i=1;
        while ((res = text.match(new RegExp('<input \([^>]*?\)id="'+ i + '"([^>]*?)>','g')))) {
            text = text.replace(new RegExp('<input \([^>]*?\)id="'+ i + '"([^>]*?)>','g'),' '+$('input#'+i).val()+' ');
            i++;
        }
        form  = $('#code').parent('form');
        form.after("<form action='site/print' method='post' target='_blank'><input type='hidden' name='_csrf' value='<?=Yii::$app->request->getCsrfToken()?>' /><input type='text' name='code' value='"+text+"'></form>");
        $("form[action='site/print']").submit();
        //alert(a);

    }
</script>
