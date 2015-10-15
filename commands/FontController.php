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
/*
Открываем https://www.google.com/fonts/
Переходим на страницу шрифта https://www.google.com/fonts#UsePlace:use/Collection:Ubuntu+Mono
Там справа качаем коллекцию
Переходим по линку, который дан на странице:
<link href='https://fonts.googleapis.com/css?family=Ubuntu+Mono' rel='stylesheet' type='text/css'>

Там берем css:
@font-face {
  font-family: 'Ubuntu Mono';
  font-style: normal;
  font-weight: 400;
  src: local('Ubuntu Mono'), local('UbuntuMono-Regular'), url(https://fonts.gstatic.com/s/ubuntumono/v6/ViZhet7Ak-LRXZMXzuAfkbWJ8El2VFcUWHOh_Oq6BA8.woff2) format('woff2');
}
@font-face {
  font-family: 'Ubuntu Mono';
  font-style: normal;
  font-weight: 700;
  src: local('Ubuntu Mono Bold'), local('UbuntuMono-Bold'), url(https://fonts.gstatic.com/s/ubuntumono/v6/ceqTZGKHipo8pJj4molytrLZkzWHE0Rai0z0KZwCYHE.woff2) format('woff2');
}
@font-face {
  font-family: 'Ubuntu Mono';
  font-style: italic;
  font-weight: 400;
  src: local('Ubuntu Mono Italic'), local('UbuntuMono-Italic'), url(https://fonts.gstatic.com/s/ubuntumono/v6/KAKuHXAHZOeECOWAHsRKA-S36XEWCHkcB1lVV5U7Zv8.woff2) format('woff2');
}
@font-face {
  font-family: 'Ubuntu Mono';
  font-style: italic;
  font-weight: 700;
  src: local('Ubuntu Mono Bold Italic'), local('UbuntuMono-BoldItalic'), url(https://fonts.gstatic.com/s/ubuntumono/v6/n_d8tv_JOIiYyMXR4eaV9cO5-Pn8cGVFpxXkUbgnQ_0.woff2) format('woff2');
}
Качаем по этим адресам woff и даем им имена
Кладем их в нашу папку css, пишем туда исправленные правила для css

Из скачанного zip файлы кладем в папку asset и натравливаем на них yii font/create

Добавляем в файл draw.php в список название семейства и в вывод в pdf - соотвествие этого семейства названию файла шрифта у tcpdf

 */
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
