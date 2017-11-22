<?
use yii\widgets\ActiveForm;
use yii\helpers\Html;
use yii\bootstrap\Modal;
?>
<div class="row">
    <div class="col-lg-12">
        <?Modal::begin([
        'header' => '<h3>Добавить/редактировать пользователя</h3>',
        'toggleButton' => [
        'tag' => 'button',
        'class' => 'btn btn-lg btn-info',
        'label' => 'Новый пользователь',
        ]
        ]);

        $form = ActiveForm::begin([
            'action' => '/admin/user-modify'
        ]);
        echo $form->field($model,'user_name')->textInput([
            'id' => 'username'
        ]);
        echo $form->field($model,'display_name')->textInput([
            'id' => 'displayname'
        ]);
        echo $form->field($model,'email')->textInput([
            'id' => 'email'
        ]);
        echo $form->field($model,'dept_id')->dropDownList($depts, [
            'id' => 'deptid'
        ]);
        echo $form->field($model,'org')->dropDownList(['csm'=>'MK', 'nacpp'=>'НАКФФ', 'iki'=>'ИКИ'], [
            'id' => 'org',
            'options' => ['nacpp'=> ['selected'=>'selected']]
        ]);
        echo $form->field($model,'is_chief')->checkbox([
            'value' => 1
        ]);
        echo $form->field($model,'is_dept_chief')->checkbox();
        //echo $form->field($model,'is_chief')->checkbox();
        echo Html::submitButton('Сохранить');
        ActiveForm::end();
        Modal::end();
        ?>
    </div>
    <div class="col-lg-12">
        <? $form = ActiveForm::begin([
            'options' =>
                [
                    'class' => 'form-inline',
                    'style' => 'padding:2%; background:whitesmoke;'
                ]
        ])?>
        <?=$form->field($search,'display_name')->textInput()?>
        <?=$form->field($search,'dept_id')->dropDownList($depts, ['prompt' => ''])?>
        <?=Html::submitButton('Поиск',['class'=>'btn btn-default'])?>
        <? ActiveForm::end() ?>
    </div>
    <div class="col-lg-12" style="text-align: center">
        <?
        echo \yii\widgets\LinkPager::widget([
            'pagination' => $pages,
        ]);
        ?>
    </div>
    <div class="col-lg-12">
        <table class="table table-bordered">
            <tr>
                <th>#</th>
                <th>Имя</th>
                <th>Пользователь</th>
                <th>Департамент</th>
                <th>Почта</th>
                <th></th>
            </tr>
            <?
            foreach($users as $val){
                ?>
            <tr>
                <td><?=$val['id']?></td>
                <td><?=$val['display_name']?></td>
                <td><?=$val['user_name']?></td>
                <td><?=isset($val->dept->dept_name) ? $val->dept->dept_name : ''?></td>
                <td><?=$val['email']?></td>
                <td><i class="glyphicon glyphicon-eye-open" style="margin-right: 10%; font-size: 1.5em;" data-toggle="modal" data-target="#w0" data-id="<?=$val->id?>"></i>
                    <a href="/admin/user-delete/<?=$val->id?>"><i class="glyphicon glyphicon-trash" style="margin-right: 10%; font-size: 1.5em;"></i></a>
                </td>
            </tr>
            <?
            }
            ?>
        </table>
    </div>
</div>
<script>
    $(function(){
        $('#w0').on('show.bs.modal', function (event) {
            var modal = $(this);
            var button = $(event.relatedTarget) // Button that triggered the modal
            var id = button.data('id') // Extract info from data-* attribute
            if(id) {
                $.ajax({
                    method: 'post',
                    url: '/admin/get-user',
                    data: {'id': id},
                    success: function (data) {
                        var person = $.parseJSON(data);
                        modal.find('#username').val(person['user_name']);
                        modal.find('#displayname').val(person['display_name']);
                        modal.find('#email').val(person['email']);
                        modal.find('#deptid').val(person['dept_id']);
                        modal.find('#org').val(person['org']);
                        if(person['is_dept_chief']){
                            modal.find('#user-is_dept_chief').prop('checked','checked');
                        }
                        if(person['is_chief']){
                            modal.find('#user-is_chief').prop('checked','checked');
                        }
                        modal.find('form').append("<input type='hidden' id='user_id' name='id' value='" + id + "'>");
                    }
                });
                $('#w0').on('hidden.bs.modal', function () {
                    $(this)
                        .find("input,textarea,select")
                        .val('')
                        .end()
                        .find("input[type=checkbox], input[type=radio]")
                        .prop("checked", "")
                        .end()
                        .find("#user_id")
                        .remove()
                        .end();
                });
            }
        })
    });
</script>