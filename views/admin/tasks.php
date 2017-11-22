<?
use yii\widgets\ActiveForm;
use yii\helpers\Html;
?>
<style>
    table td,table th  {
        text-align: center;
    }
</style>
<h1>Предопределенные задачи</h1>
<button class="btn btn-success" data-toggle="modal" data-target="#add">Добавить</button>
<table class="table table-striped">
    <tr>
        <th width="10%">id</th>
        <th width="40%">Название</th>
        <th width="30%">Департамент</th>
        <th width="10%"></th>
        <th width="10%"></th>
    </tr>
    <?
    foreach($tasks as $val){
?>
        <tr>
            <? $form = ActiveForm::begin(['action'=>'update']) ?>
            <td width="10%"><?=$val->id?></td>
            <td width="60%"><?=$val->name?></td>
            <td width="60%"><?=$val->department?></td>
            <td width="15%"><?=Html::button('Изменить',[
                    'class'=>'btn btn-primary',
                    'data-toggle' => 'modal',
                    'data-target' => '#edit',
                    'data-id' => $val->id
                ])?></td>
            <td width="15%"><?=Html::a('Удалить','delete?id='.$val->id,[
                    'class'=>'btn btn-danger'
                ])?></td>
            <? ActiveForm::end()?>
        </tr>
    <?
    }
    ?>
</table>
<div class="modal" id="edit" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Добавить</h4>
            </div>
            <div class="modal-body">
                <? $form=ActiveForm::begin(['action'=>'update']); ?>
                <?=$form->field($model,'name')->textInput()?>
                <?=$form->field($model,'department')->dropDownList(['nacpp'=>"НАКФФ",'csm'=>"МК",'iki'=>'ИКИ'])?>
                <?=$form->field($model,'id')->hiddenInput()->label('')?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                <? ActiveForm::end(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div class="modal" id="add" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document" style="width: 90%;">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">Добавить</h4>
            </div>
            <div class="modal-body">
                <? $form=ActiveForm::begin(['action'=>'create']); ?>
                <?=$form->field($model,'name')->textInput()?>
                <?=$form->field($model,'department')->dropDownList(['nacpp'=>"НАКФФ",'csm'=>"МК",'iki'=>'ИКИ'], ['id'=>'dep'])?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                <?= Html::submitButton('Сохранить', ['class' => 'btn btn-primary']) ?>
                <? ActiveForm::end(); ?>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script>
    $(function(){
        $('#edit').on('show.bs.modal', function(event){
            var button = $(event.relatedTarget);
            var id = button.data('id');
            var modal = $(this);
            $.ajax({
                type:"GET",
                url:"show?id="+id,
                dataType:"json",
                success:function(data){
                    modal.find('form').attr('action', 'update');
                    $('#edit #task-name').val(data.name);
                    modal.find('#task-id').val(id);
                    modal.find('#dep').val(data.department);
                }
            });
        });
    });
</script>