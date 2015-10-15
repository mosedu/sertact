<?php

use yii\db\Schema;
use yii\db\Migration;
use yii\helpers\VarDumper;

class m151015_112827_add_font_dir extends Migration
{
    public function up()
    {
        $sAppDir = Yii::getAlias('@app');

        $sDestDir = Yii::getAlias('@app') . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'tecnickcom/tcpdf/fonts';
        $sDestDir = str_replace('/', DIRECTORY_SEPARATOR, $sDestDir);
        chmod($sDestDir, 0777);

/*
        $sDir = $this->getDirName();
        if( !is_dir($sDir) && !mkdir($sDir) ) {
            echo "Error create dir: {$sDir}\n";
            return false;
        }
        chmod($sDir, 0777);
        echo "\nCreate dir: {$sDir}\n\n";
*/
//        $fontList = $sDir . DIRECTORY_SEPARATOR . 'fonts.php';
//        $existFonts = file_exists($fontList) ? require($fontList) : [];

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
//                    $sFontFile = $sDir . DIRECTORY_SEPARATOR . $f;
//                    copy($sSrc . DIRECTORY_SEPARATOR . $f, $sFontFile);
                    $sFontName = \TCPDF_FONTS::addTTFfont($sFontFile, 'TrueTypeUnicode');
                    echo ($sFontName === false) ? "\nError on create font from {$sFontFile}\n" : "\nCreate font: {$sFontFile} from {$sFontFile}\n";
//                    $existFonts[$sFontName] = $f;
                }
            }
            closedir($hd);
        }
/*
        if( count($existFonts) > 0 ) {
            file_put_contents($fontList, "<?php\n\nreturn " . VarDumper::dumpAsString($existFonts) . ";\n");
            chmod($fontList, 0777);
        }
        else {
            if( file_exists($fontList) ) {
                unlink($fontList);
            }
        }
*/
    }

    public function down()
    {
        $sDir = $this->getDirName();
        if( is_dir($sDir) ) {
            $this->removeDir($sDir);
        }
        echo "\nRemove dir: {$sDir}\n\n";
        return true;
    }

    public function getDirName() {
        $sDir = dirname(dirname(__FILE__)) . Yii::$app->params['fontpath'];
        return $sDir;
    }

    public function removeDir($sDir) {
        if( is_dir($sDir) ) {
            if( $hd = opendir($sDir) ) {
                while( false !== ($fn = readdir($hd)) ) {
                    if( ($fn == '.') || ($fn == '..') ) {
                        continue;
                    }

                    $sName = $sDir . DIRECTORY_SEPARATOR . $fn;

                    if( is_dir($sName) ) {
                        $this->removeDir($sName);
                    }
                    else {
                        unlink($sName);
                    }
                }
                closedir($hd);
            }
            rmdir($sDir);
        }
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
