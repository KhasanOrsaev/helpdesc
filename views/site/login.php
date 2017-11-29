<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use app\assets\AppAsset;

AppAsset::register($this);

$this->title = 'Авторизация';
?>
<style>
    body, html{
        padding: 0;
        margin: 0 auto;
        font-family: "Helvetica Neue", Helvetica, Arial, sans-serif;
        background: url('/images/background.jpg') no-repeat;
        background-size: cover;
    }
    .row{
        margin: 0;
    }
    .site-login{
        padding: 2% 3%;
        text-align: center;
        color: #2f999e;
    }
    .btn-success{
        color: #fff;
        background-color: #00a3aa;
        border-color: #00a3aa;
    }
</style>
<div class="border" style="background: url('/images/border.png'); min-height:20px"></div>
<div>
    <img src="/images/logo_nacpp.png" alt="" style="margin:2%; width:20%">
</div>
<div class="border" style="background: url('images/border.png'); min-height:20px; width:100%; position:absolute; bottom:0;"></div>
<div class="row">
    <div class="col-lg-4 col-lg-offset-4 site-login">
        <h1 style="margin: 10% 0"><?= Html::encode($this->title) ?></h1>

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

        <?= $form->field($model, 'password')->passwordInput() ?>

        <div class="form-group">
            <div class="col-lg-12">
                <?= Html::submitButton('Войти', ['class' => 'btn btn-success', 'name' => 'login-button']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
</div>


