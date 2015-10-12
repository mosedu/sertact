<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SertSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Сертификаты';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="sert-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Создать', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
//            ['class' => 'yii\grid\SerialColumn'],

//            'sert_id',
            'sert_name',
//            'sert_active',
//            'sert_template:ntext',
//            'sert_created',
            'sert_updated',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
