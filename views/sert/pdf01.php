<?php
/**
 * Created by PhpStorm.
 * User: KozminVA
 * Date: 15.10.2015
 * Time: 10:58
 */

/* @var $this yii\web\View */
/* @var $model app\models\Sert */

// use TCPDF;

$sFontDir = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'tecnickcom/tcpdf/fonts';
$sFontDir = str_replace('/', DIRECTORY_SEPARATOR, $sFontDir);

/*
Unicode russian:

arial
dejavusans
dejavusanscondensed
dejavusansextralight
dejavusansmono
dejavuserif
dejavuserifcondensed
freemono
freesans
freeserif
msungstdlight
stsongstdlight
*/
$aFontReplace = [
    'courier' => 'cour',
//    'courier' => 'freemono',
    'times' => 'freeserif',
];

$aData = json_decode($model->sert_template, true);

$page = array_reduce($aData, function($res, $item){ return ($item['type'] == 'page') ? $item : $res; }, null);

if( $page === null ) {
    throw new \yii\base\InvalidValueException("Not found page data for print");
}

$page_format = array(
    'MediaBox' => array ('llx' => 0, 'lly' => 0, 'urx' => $page['width'], 'ury' => $page['height']),
    'CropBox' => array ('llx' => 0, 'lly' => 0, 'urx' => $page['width'], 'ury' => $page['height']),
    'BleedBox' => array ('llx' => 0, 'lly' => 0, 'urx' => $page['width'], 'ury' => $page['height']),
    'TrimBox' => array ('llx' => 0, 'lly' => 0, 'urx' => $page['width'], 'ury' => $page['height']),
    'ArtBox' => array ('llx' => 0, 'lly' => 0, 'urx' => $page['width'], 'ury' => $page['height']),
//    'Dur' => 3,
/*
    'trans' => array(
        'D' => 1.5,
        'S' => 'Split',
        'Dm' => 'V',
        'M' => 'O'
    ),
*/
    'Rotate' => 0,
    'PZ' => 1,
);

$aAlign = [
    'left' => 'L',
    'right' => 'R',
    'center' => 'C',
    'justify' => 'J',
];

$sFile = Yii::getAlias('@app') . Yii::$app->params['pdfpath'] . DIRECTORY_SEPARATOR . 'test.pdf';

$sPath = $sFile;
if( isset($_SERVER) && isset($_SERVER['DOCUMENT_ROOT']) ) {
    $sroot = rtrim(str_replace('/', DIRECTORY_SEPARATOR, $_SERVER['DOCUMENT_ROOT']), DIRECTORY_SEPARATOR);
    $sPath = str_replace($sroot, '', $sPath);
}

// $pdf = new TCPDF('P', 'mm', 'A4', true, 'UTF-8');
$pdf = new TCPDF('P', 'mm', $page_format, true, 'UTF-8');

$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Victor Kozmin');
$pdf->SetTitle('Print by ' . $model->sert_name);
$pdf->SetSubject(date('d.m.Y H:i'));
$pdf->SetKeywords($model->sert_name);

// remove default header/footer
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);

$pdf->setFontSubsetting(true);

// add a page
$pdf->AddPage('P', $page_format, false, false);
$pdf->SetXY(0, 0, true);

/*
$aFin = ['b', 'i', 'bi', 'bd'];
$nCou = 0;
if( $hd = opendir($sFontDir) ) {
    while( false !== ($f = readdir($hd)) ) {
        if( ($f == '.') || ($f == '..') ) {
            continue;
        }
        $aParts = explode('.', $f);
        $nParts = count($aParts);
        if( strtolower($aParts[$nParts - 1]) == 'php' ) {
            Yii::info('Font: ' . $f);
            $bExists = false;
            if( $nParts > 1 ) {
                // MS fonts bold has 'bd' on file name end
                $s1 = substr($aParts[$nParts - 2], 0, 4);
                if( ($s1 == 'cid0') || ($s1 == 'uni2') ) { //
                    continue;
                }
                foreach($aFin As $v) {
                    $n = strlen($v);
                    if( (strtolower(substr($aParts[$nParts - 2], -1 * $n)) == $v) ) {
                        $aParts[$nParts - 2] = substr($aParts[$nParts - 2], 0, -1 * $n);
                        $sNew = $sFontDir . DIRECTORY_SEPARATOR . implode('.', $aParts);
                        Yii::info('Font: ' . $f . ' -> ' . $sNew . ' ' . (file_exists($sNew) ? '' : 'not') . ' exist');
                        if( file_exists($sNew) ) {
                            $bExists = true;
                            break;
                        }
                    }
                }
            }
            if( $bExists ) {
                continue;
            }
            Yii::info('Font: print = ' . substr($f, 0, -4) . ' nCou = ' . $nCou . ' [' . (($nCou % 3) * 60) . ', ' . (intval($nCou / 3) * 10) . ']');
            if( ($nCou % 4) == 0 ) {
                if( $nCou > 0 ) {
                    $pdf->Output($sFile, 'F');
                    $sFile = Yii::getAlias('@app') . Yii::$app->params['pdfpath'] . DIRECTORY_SEPARATOR . 'test'.$nCou.'.pdf';
                    unset($pdf);
                }

                $pdf = new TCPDF('P', 'mm', $page_format, true, 'UTF-8');

                $pdf->SetCreator(PDF_CREATOR);
                $pdf->SetAuthor('Victor Kozmin');
                $pdf->SetTitle('Print by ' . $model->sert_name);
                $pdf->SetSubject(date('d.m.Y H:i'));
                $pdf->SetKeywords($model->sert_name);

// remove default header/footer
                $pdf->setPrintHeader(false);
                $pdf->setPrintFooter(false);

                $pdf->setFontSubsetting(true);

// add a page
                $pdf->AddPage('P', $page_format, false, false);
                $pdf->SetXY(0, 0, true);
            }

            $pdf->SetFont(
                substr($f, 0, -4),
                '', // style
                12 // size in pt
            );

            $pdf->SetXY(0, ($nCou % 4) * 20, true);

            $pdf->Cell(
                120,
                20,
                substr($f, 0, -4) . ' : Аа-Лл-Тт-Яя',
                0,
                0,
                $aAlign['left']
            );

            $nCou++;
        }
    }
    closedir($hd);
}
*/

foreach($aData As $block) {
    if( $block['type'] == "text" ) {
        // get font ***************************************************************************************
        $sFont = strtolower($block['fontfamily']);
        if( isset($aFontReplace[$sFont]) ) {
            $sFont = $aFontReplace[$sFont];
        }
        if( !file_exists($sFontDir . DIRECTORY_SEPARATOR . $sFont . '.php') ) {
            Yii::warning('Font does not exist: '.$sFont.' ['.$sFontDir.']');
            $sFont = 'freeserif';
        }

        Yii::warning('-------------- Font : ' . $block['fontfamily'] . ' -> ' . $sFont);
        $pdf->SetFont(
            $sFont,
            ($block['bold'] ? 'B' : '') . ($block['italic'] ? 'I' : '') . ($block['underline'] ? 'U' : ''), // style
            round(72 * $block['fontsize'] / 25.4) // size in pt
        );

        // get position ***************************************************************************************
        $pdf->SetXY($block['left'], $block['top'], true);

        // print text ***************************************************************************************

//        $pdf->Cell(
//            $block['width'],
//            $block['height'] !== null ? $block['height'] : 0,
//            $block['text'],
//            0, // border
//            0, // position after print
//            isset($aAlign[$block['align']]) ? $aAlign[$block['align']] : $aAlign['left']
//        );
        $pdf->MultiCell(
            $block['width'],
            $block['height'] !== null ? $block['height'] : 0,
            $block['text'],
            0, // border
            isset($aAlign[$block['align']]) ? $aAlign[$block['align']] : $aAlign['left']
        );
    }
}

$pdf->Output($sFile, 'F');

if( file_exists($sFile) ) {
    echo str_replace(DIRECTORY_SEPARATOR, '/', $sPath);
    echo "<br />\n";
    echo $_SERVER['DOCUMENT_ROOT'];
}
else {
    echo "error create file";
}

