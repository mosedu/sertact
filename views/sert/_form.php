<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Sert */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sert-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sert_name')->textInput(['maxlength' => true]) ?>

    <?= '' // $form->field($model, 'sert_active')->textInput() ?>

    <?= '' // $form->field($model, 'sert_template')->textarea(['rows' => 6]) ?>

    <?= '' // $form->field($model, 'sert_created')->textInput() ?>

    <?= '' // $form->field($model, 'sert_updated')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
