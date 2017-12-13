<?php

/* @var $this yii\web\View */
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\bootstrap\Modal;
$this->title = 'HELPDESK';
?>
<div class="site-index">
<style>
    table td,th{
        text-align: center;
    }
    table p{
        font-size: 1.3em;
    }
    .modal-lg{
        width: 90%;
    }
</style>
    <h3><i class="glyphicon glyphicon-cog"></i> <a href="/">Заявки в IT отдел</a></h3>
    <hr>
    <div class="body-content">
        <div class="row">
            <div class="col-lg-10">
                <button class="btn btn-success pull-left" data-toggle="modal" data-target="#new">Новая заявка</button>
                <?

                Modal::begin([
                    'header' => '<h3>Фильтр</h3>',
                    'toggleButton' => [
                        'label' => 'Фильтр',
                        'class' => 'btn btn-default',
                        'style' => 'margin-left:1%'
                    ],
                ]);

                $form = ActiveForm::begin([
                    'options' =>
                        [
                            //'class' => 'form-inline',
                            'style' => 'padding:2%; background:whitesmoke;border-radius:5px',
                        ],
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'template' => "{label}\n{beginWrapper}\n{input}\n{hint}\n{error}\n{endWrapper}",
                        'horizontalCssClasses' => [
                            'label' => 'col-sm-4',
                            'offset' => 'col-sm-offset-4',
                            'wrapper' => 'col-sm-8',
                            'error' => '',
                            'hint' => '',
                        ],
                    ],
                ])?>
                <?=$form->field($searchModel,'createdBy.name')->textInput()->label('Автор записи')?>
                <?=$form->field($searchModel,'takenBy.name')->textInput()->label('Исполнитель')?>
                <?=$form->field($searchModel,'type')->dropDownList([
                    'lims' => 'ЛИМС',
                    'default' => 'Общие'
                ])?>
                <?=$form->field($searchModel,'description')->textInput()?>
                <?=$form->field($searchModel,'created_at')->widget(\yii\jui\DatePicker::className(),[
                    'language' => 'ru',
                    'dateFormat' => 'yyyy-MM-dd',

                    'options' => [
                        'class' => 'form-control'
                    ]
                ]) ?>
                <?=$form->field($searchModel,'finished_at')->widget(\yii\jui\DatePicker::className(),[
                    'language' => 'ru',
                    'dateFormat' => 'yyyy-MM-dd',

                    'options' => [
                        'class' => 'form-control'
                    ]
                ]) ?>
                <?=$form->field($searchModel,'statuses')->dropDownList($status, ['prompt' => ''])->label('Статус')?>
                <?=Html::Button('Поиск',['class'=>'btn btn-default', 'onclick'=>'search(this)'])?>
                <?=Html::Button('Excel',['class'=>'btn btn-warning', 'onclick'=>'excel(this)'])?>
                <? ActiveForm::end();

                Modal::end();

                ?>
            </div>
            <div class="col-lg-12" style="text-align: center">
                <?
                echo \yii\widgets\LinkPager::widget([
                    'pagination' => $pages,
                ]);
                ?>
            </div>
            <div class="col-lg-12" style="margin: 2% 0;">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <td class="text-center"><b>#</b></td>
                            <td class="text-center"><b>Тип Заявки</b></td>
                            <td class="text-center"><b>Описание</b></td>
                            <td class="text-center"><b>Создана</b></td>
                            <td class="text-center"><b>Выполнено</b></td>
                            <td class="text-center"><b>Автор</b></td>
                            <td class="text-center"><b>Исполнитель</b></td>
                            <td class="text-center"><b>Статус</b></td>
                            <td class="text-center"><b></td>
                        </tr>
                    </thead>
                    <? foreach($model as $val){?>
                    <tr>
                        <td class="text-center"><?=$val['id'] ?></td>
                        <td class="text-center">
                            <?
                                switch ($val->type){
                                    case 'default': echo "Общие"; break;
                                    case 'lims': echo "ЛИМС"; break;
                                    case 'support': echo "Поддержка"; break;
                                    default: echo "Общие";
                                }
                            ?>
                        </td>
                        <td class="text-center"><?
                            echo "<a href='/view/".$val['id']."'>";
                            echo $val->tasks ? $val->tasks->name.' - ' : '';
                            echo $val->description."</a>" ?>
                        </td>
                        <td class="text-center"><?=date('H:i d.m.Y', strtotime($val->created_at)) ?></td>
                        <td class="text-center"><?=$val->finished_at!='0000-00-00 00:00:00' ? date('H:i d.m.Y', strtotime($val->finished_at)) : ''?></td>
                        <td class="text-center"><?=$val->createdBy->display_name ?></td>
                        <td class="text-center"><?=isset($val->takenBy->user_name) ? $val->takenBy->display_name : '' ?></td>
                        <td class="text-center"><p style="background: <?=$val->statuses->color ?>; color: white;"><?=$val->statuses->name ?></p></td>
                        <td class="text-center">
                            <a href="/view/<?=$val['id']?>">
                                <i class="glyphicon glyphicon-eye-open" style="margin-right: 10%; font-size: 1.5em;"></i>
                            </a>
                            <? if($val->status == 'D' or $val->status == 'W'){
                                Modal::begin([
                                    'header' => '<h3>Редактировать</h3>',
                                    'toggleButton' => [
                                        'tag' => 'i',
                                        'class' => 'glyphicon glyphicon-pencil',
                                        'label' => '',
                                        'style' => 'margin-right: 10%; font-size: 1.5em;'
                                    ],
                                    'size' => 'modal-lg',
                                ]);

                                echo $this->render('/forms/createUpdateSubj', [
                                    'model' => $val,
                                    'depts' => $depts
                                ]);

                                Modal::end();
                             }
                            if(Yii::$app->user->identity->is_admin){
                                ?>
                            <a href="/delete/<?=$val['id']?>">
                                <i class="glyphicon glyphicon-trash" style="font-size: 1.5em;"></i>
                            </a>
                            <? } ?>
                        </td>
                    </tr>
                    <? } ?>
                </table>
            </div>
            <div class="col-lg-12" style="text-align: center">
                <?
                echo \yii\widgets\LinkPager::widget([
                    'pagination' => $pages,
                ]);
                ?>
            </div>
        </div>

    </div>
</div>
<div class="modal fade" id="new" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h3 class="modal-title" id="myModalLabel">Новая заявка</h3>
            </div>
            <div class="modal-body">
                <?= $this->render('/forms/createUpdateSubj', [
                    'model' => $mod,
                    'depts' => $depts
                ]) ?>

        </div>
    </div>
</div>
    <script>
        function excel(e){
            form = $(e).parent();
            form.append('<input name="excel" type="hidden" value="1" />');
            form.attr('target','_blank');
            form.submit();
        }
        function search(e){
            form = $(e).parent();
            form.removeAttr('target');
            $(form).find('input[name="excel"]').remove();
            form.submit();
        }
    </script>
