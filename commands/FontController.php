<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\commands;

use yii;
use yii\console\Controller;

/**
 * This command echoes the first argument that you have entered.
 *
 * This command is provided as an example for you to learn how to create console commands.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class FontController extends Controller
{
    /**
     * This command
     */
    public function actionIndex()
    {
        echo 'font/create' . "\n";
    }

    /**
     * This command
     */
    public function actionCreate()
    {
        $sAppDir = Yii::getAlias('@app');

        $sDestDir = $sAppDir . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'tecnickcom/tcpdf/fonts';
        $sDestDir = str_replace('/', DIRECTORY_SEPARATOR, $sDestDir);
        chmod($sDestDir, 0777);

        $sSrc = $sAppDir . DIRECTORY_SEPARATOR . 'assets';
        if( $hd = opendir($sSrc) ) {
            while( false !== ($f = readdir($hd)) ) {
                if( ($f == '.') || ($f == '..') ) {
                    continue;
                }
                $aParts = explode('.', $f);
                $nParts = count($aParts);
                if( strtolower($aParts[$nParts - 1]) == 'ttf' ) {
                    $sFontFile = $sSrc . DIRECTORY_SEPARATOR . $f;
                    if( $nParts > 1 ) {
                        // MS fonts bold has 'bd' on file name end
                        if( strtolower(substr($aParts[$nParts - 2], -2)) == 'bd' ) {
                            $aParts[$nParts - 2] = substr($aParts[$nParts - 2], 0, -2) . 'b';
                            $fNew = $sSrc . DIRECTORY_SEPARATOR . implode('.', $aParts);
                            copy($sFontFile, $fNew);
                            $sFontFile = $fNew;
                        }
                    }

                    echo "\nDo {$sFontFile}\n";
                    $sFontName = \TCPDF_FONTS::addTTFfont($sFontFile, 'TrueTypeUnicode');
                    echo ($sFontName === false) ? "\nError on create font from {$sFontFile}\n" : "\nCreate font: {$sFontName} from {$sFontFile}\n";
                }
            }
            closedir($hd);
        }

    }
}
