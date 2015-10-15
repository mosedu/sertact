<?php

use yii\db\Schema;
use yii\db\Migration;

class m151015_080919_add_result_dir extends Migration
{
    public function up()
    {
        $sDir = $this->getDirName();
        if( !is_dir($sDir) && !mkdir($sDir) ) {
            echo "Error create dir: {$sDir}\n";
            return false;
        }
        chmod($sDir, 0777);
        echo "\nCreate dir: {$sDir}\n\n";
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
        $sDir = dirname(dirname(__FILE__)) . Yii::$app->params['pdfpath'];
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
