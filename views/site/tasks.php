<?

use yii\bootstrap\Modal;
?>

<div class="row">
    <div class="col-lg-12" style="text-align: center">
        <div class="pagination">
            <?
            if($pages->totalCount > $pages->defaultPageSize){
                echo "<ul>";
                echo "<li><button class='btn btn-lnk' onclick='previous_page()'";
                echo Yii::$app->request->get('page')==1 ? 'disabled' : '';
                echo "><i class='glyphicon glyphicon-chevron-left'></i></button></li>";
                foreach($indexes as $i){
                    echo "<li><button class='btn btn-lnk ";
                    if(Yii::$app->request->get('page') == $i)
                        echo 'active';
                    echo "' onclick='link(this)'>$i</button></li>";
                }
                echo "<li><button class='btn btn-lnk' onclick='next_page()'";
                echo Yii::$app->request->get('page')==ceil($pages->totalCount/$pages->defaultPageSize) ? 'disabled' : '';
                echo "><i class='glyphicon glyphicon-chevron-right'></i></button></li>";
                echo "</ul>";
            }
            ?>
        </div>
    </div>
    <div class="col-lg-12">
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
                                    'data-target' => '#edit',
                                    'style' => 'margin-right: 10%; font-size: 1.5em;'
                                ],
                                'id' => 'edit',
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
        <div class="pagination">
            <?
            if($pages->totalCount > $pages->defaultPageSize){
                echo "<ul>";
                echo "<li><button class='btn btn-lnk' onclick='previous_page()'";
                echo Yii::$app->request->get('page')==1 ? 'disabled' : '';
                echo "><i class='glyphicon glyphicon-chevron-left'></i></button></li>";
                foreach($indexes as $i){
                    echo "<li><button class='btn btn-lnk ";
                    if(Yii::$app->request->get('page') == $i)
                        echo 'active';
                    echo "' onclick='link(this)'>$i</button></li>";
                }
                echo "<li><button class='btn btn-lnk' onclick='next_page()'";
                echo Yii::$app->request->get('page')==ceil($pages->totalCount/$pages->defaultPageSize) ? 'disabled' : '';
                echo "><i class='glyphicon glyphicon-chevron-right'></i></button></li>";
                echo "</ul>";
            }
            ?>
        </div>
    </div>
</div>