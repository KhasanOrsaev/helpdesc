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
            <div class="col-lg-12">
                <button class="btn btn-success pull-left" data-toggle="modal" data-target="#newTask">Новая заявка</button>

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
                    ''          => '',
                    'lims'      => 'ЛИМС',
                    'default'   => 'Общие'
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
                <?//=$form->field($searchModel,'statuses')->dropDownList($status, ['prompt' => ''])->label('Статус')?>
                <?=Html::Button('Поиск',['class'=>'btn btn-default', 'onclick'=>'search(this)'])?>
                <?=Html::Button('Excel',['class'=>'btn btn-warning', 'onclick'=>'excel(this)'])?>
                <?=Html::Button('Закрыть',['class'=>'btn btn-danger', 'data-dismiss'=>'modal'])?>
                <? ActiveForm::end();

                Modal::end();
                ?>
                <hr>
                <!-- Nav tabs -->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="active"> <a   data-target="#new"     mode="/D" role="tab" onclick = 'getTable(this, 1)'>Новые</a></li>
                    <li>
                        <a   data-target="#doing"   mode="/A" role="tab" onclick = 'getTable(this, 1)'>
                            В работе <?=(Yii::$app->user->identity->is_it && !Yii::$app->user->identity->is_admin && !Yii::$app->user->identity->is_chief) ? ' ('.Yii::$app->user->identity->display_name.')' : ''?>
                        </a>
                    </li>
                    <li>                <a   data-target="#waiting" mode="/W" role="tab" onclick = 'getTable(this, 1)'>На уточнении</a></li>
                    <li>
                        <a   data-target="#ready"   mode="/T" role="tab" onclick = 'getTable(this, 1)'>
                            Выполнено<?=(Yii::$app->user->identity->is_it && !Yii::$app->user->identity->is_admin && !Yii::$app->user->identity->is_chief) ? ' ('.Yii::$app->user->identity->display_name.')' : ''?>
                        </a>
                    </li>
                    <li>                <a   data-target="#all"     mode="" role="tab" onclick = 'getTable(this, 1)'>Все</a></li>
                </ul>

                <div class="tab-content">
                    <div class="tab-pane active" id="new"></div>
                    <div class="tab-pane" id="doing"></div>
                    <div class="tab-pane" id="waiting"></div>
                    <div class="tab-pane" id="ready"></div>
                    <div class="tab-pane" id="all"></div>

                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="newTask" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
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
        $(function(){
            $.get('/subject-type/D' + '?' + '<?=http_build_query(Yii::$app->request->post())?>', function(data){
                $('#new').html(data);
            });
        });

        function getTable(e, page){
            loadurl = '/subject-type' + $(e).attr('mode') + '?page=' + page + '&<?=http_build_query(Yii::$app->request->post())?>';
            targ = $(e).attr('data-target');
            $.get(loadurl, function(data) {
                $(targ).html(data);
            });
            $(e).tab('show');
        }

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

        function next_page(){
            button = $(".pagination .btn.active");
            tab = $("ul.nav li.active a");
            getTable(tab, parseInt(button.text())+1);
        }

        function previous_page(){
            button = $(".pagination .btn.active");
            tab = $("ul.nav li.active a");
            getTable(tab, parseInt(button.text())-1);
        }

        function link(e){
            tab = $("ul.nav li.active a");
            getTable(tab, $(e).text());
        }
    </script>
