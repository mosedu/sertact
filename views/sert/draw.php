<?php

use yii\helpers\Html;
use yii\jui\JuiAsset;

/* @var $this yii\web\View */
/* @var $model app\models\Sert */

$this->title = $model->isNewRecord ? 'Создание' : ('Изменение: ' . ' ' . $model->sert_name);
$this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->sert_id, 'url' => ['view', 'id' => $model->sert_id]];
$this->params['breadcrumbs'][] = $this->title;

JuiAsset::register($this);

// $model

?>

<div class="draw">
    <div id="paint-region"></div>
    <div id="tool-region">
        <div id="img-control">
            <a href="#" title="Select source" id="button-src" class="">Src</a>
        </div>
        <div id="text-control">
            <a href="#" title="Bold" id="button-bold" class="">B</a>
            <a href="#" title="Italic" id="button-italic" class="">I</a>
            <a href="#" title="Underline" id="button-underline" class="">U</a>
        </div>
        <div class="controls">
            <a href="#" title="Add img" id="button-addimg" class="btn btn-default">+ img</a>
            <a href="#" title="Add text" id="button-addtext" class="btn btn-default">+ text</a>
        </div>
    </div>
</div>
