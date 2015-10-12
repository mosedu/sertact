<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SertSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sert-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'sert_id') ?>

    <?= $form->field($model, 'sert_name') ?>

    <?= $form->field($model, 'sert_active') ?>

    <?= $form->field($model, 'sert_template') ?>

    <?= $form->field($model, 'sert_created') ?>

    <?php // echo $form->field($model, 'sert_updated') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
