<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Sert */

$this->title = $model->isNewRecord ? 'Создание' : ('Изменение: ' . ' ' . $model->sert_name);
$this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->sert_id, 'url' => ['view', 'id' => $model->sert_id]];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sert-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
