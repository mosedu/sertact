<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Sert */

$this->title = $model->sert_id;
$this->params['breadcrumbs'][] = ['label' => 'Serts', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sert-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->sert_id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->sert_id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'sert_id',
            'sert_name',
            'sert_active',
            'sert_template:ntext',
            'sert_created',
            'sert_updated',
        ],
    ]) ?>

</div>
