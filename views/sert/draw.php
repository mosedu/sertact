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
                <?= $form->field($model, 'sert_name')->textInput(['maxlength' => 255]) ?>
                <?= $form->field($model, 'sert_template', ['template' => "{input}"])->hiddenInput() ?>
                <?= Html::submitButton($model->isNewRecord ? 'Создать' : 'Сохранить', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
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
