<?php

use yii\helpers\Html;
use yii\jui\JuiAsset;
use yii\widgets\ActiveForm;
use yii\web\View;
use yii\helpers\Json;

/* @var $this yii\web\View */
/* @var $model app\models\Sert */
/* @var $this yii\web\View */
/* @var $form yii\widgets\ActiveForm */

$this->title = $model->isNewRecord ? 'Создание' : ('Изменение: ' . ' ' . $model->sert_name);
$this->params['breadcrumbs'][] = ['label' => 'Сертификаты', 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->sert_id, 'url' => ['view', 'id' => $model->sert_id]];
$this->params['breadcrumbs'][] = $this->title;

JuiAsset::register($this);
$fons = [
    'Arial' => 'Arial',
    'Times' => 'Times',
    'Lobster' => 'Lobster',
    'Courier' => 'Courier',
];
$aFontList = [];
foreach($fons As $k=>$v) {
    $aFontList[] = '<option value="'.$k.'">'.$v.'</option>';
}

// <link href='https://fonts.googleapis.com/css?family=Lobster' rel='stylesheet' type='text/css'>

?>

<div class="sert-form">

    <?php
        $form = ActiveForm::begin([
            'id' => 'sert-editor',
            'enableAjaxValidation' => true,
            'enableClientValidation' => false,
            'validateOnSubmit' => true,
            'validateOnChange' => false,
            'validateOnBlur' => false,
            'validateOnType' => false,
        ]);
    ?>

    <div class="draw">
        <div id="tool-region" style="float: right; width: 300px; margin-left: 10px; background-color: #ffffff; min-height: 100%;">
            <div id="img-control">
                <a href="#" title="Select source" id="button-src" class="">Src</a>
            </div>
            <div class="text-control">
                <div class="col-md-5">
                    Шрифт
                </div>
                <div class="col-md-7">
                    <select class="form-control" data-field="fontfamily"><?= implode('', $aFontList) ?></select>
                </div>

                <div class="col-md-5">
                    Размер
                </div>
                <div class="col-md-7">
                    <select class="form-control" data-field="fontsize"><?= array_reduce(range(3, 50), function($res, $item){ return $res . '<option value="'.$item.'">'.$item.'</option>'; }, "") ?></select>
                </div>

                <div class="col-md-5">
                    Вид
                </div>
                <div class="col-md-7">
                    <a href="#" title="Bold" class="button-bold btn btn-default" data-field="bold">B</a>
                    <a href="#" title="Italic" class="button-italic btn btn-default" data-field="italic">I</a>
                    <!-- input type="checkbox" id="cb-italic" data-field="italic"> <label for="cb-italic">Italic</label -->
                    <a href="#" title="Underline" class="button-underline btn btn-default" data-field="underline">U</a>
                </div>

                <div class="col-md-5">
                    Выравнивание
                </div>
                <div class="col-md-7">
                    <a href="#" title="Left" class="button-align-left btn btn-default" data-field="align" data-value="left">L</a>
                    <a href="#" title="Center" class="button-align-center btn btn-default" data-field="align" data-value="center">C</a>
                    <a href="#" title="Right" class="button-align-right btn btn-default" data-field="align" data-value="right">R</a>
                    <!-- select data-field="align"><option value="left">Left</option><option value="right">Right</option><option value="center">Center</option></select -->
                </div>

                <!-- input type="radio" name="group2" value="left" data-field="align"> Left
                <input type="radio" name="group2" value="center" data-field="align"> Center
                <input type="radio" name="group2" value="right"  data-field="align"> Rigth -->

                <div class="col-md-5">
                    Текст
                </div>
                <div class="col-md-7">
                    <input class="form-control" type="text" data-field="text" />
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="controls">
                <div class="col-md-12">
                    <?= $form->field($model, 'sert_name')->textInput(['maxlength' => 255]) ?>
                    <?= $form->field($model, 'sert_template', ['template' => "{input}"])->hiddenInput() ?>
                </div>
                <div class="col-md-12">
                    <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
                    <div class="clearfix"></div>
                </div>

                <div class="col-md-12">
                    <a href="#" title="Add img" id="button-addimg" class="btn btn-default">+ img</a>
                    <a href="#" title="Add text" id="button-addtext" class="btn btn-default">+ text</a>
                    <div class="clearfix"></div>
                </div>

            </div>
        </div>
        <div id="paint-region"></div>
        <div class="clearfix"></div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<?php

$sTemplate = Html::getInputId($model, 'sert_template');
if( $model->sert_template === null ) {
    $sJson = '[{type: "page", width: 210, height: 297},{type: "text", text: "some text", bold: true, align: "center", left: 50, top: 100, width: 100}]';
}
else {
    $sJson = $model->sert_template;
}
$sJs = <<<EOT
    var pPaint = jQuery("#paint-region"),
        pTool = jQuery("#tool-region"),
        pImgControl = jQuery("#img-control"),
        pTextControl = jQuery("#text-control"),

        scale = 2 // точек/мм
        ;

    pImgControl.hide();
    pTextControl.hide();

    console.log('sJson = {$sJson}');

    jQuery("#button-addtext")
        .on(
            "click",
            function(event){
                event.preventDefault();
                pPaint.drawpage('append', {type: "text", text: "New text", fontsize: 20, left: 0, top: 0});
                pPaint.drawpage('redraw');
                return false;
            }
        );

    pPaint.drawpage(
        {panel: pTool, scale: 1},
        {$sJson}
    );
    console.log(pPaint.drawpage('regions'));

    var oForm = jQuery('#{$form->options['id']}'),
        oTemplate = jQuery("#{$sTemplate}");

    oForm
        .on('beforeSubmit', function(e) {
             console.log("beforeSubmit()");
        })
        .on('beforeValidate', function (event, messages) {
             console.log("beforeValidate()", event);
        })
        .on('afterValidate', function (event, messages) {
             console.log("afterValidate()", event);
        })
        .on('submit', function (event) {
            var sText = JSON.stringify(pPaint.drawpage('regions'));
            oTemplate.val(sText);
            console.log("submit(): " + sText);
        });
EOT;

$this->registerJs($sJs, View::POS_READY, 'submit_sert_form');
