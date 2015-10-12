<?php

use yii\db\Schema;
use yii\db\Migration;

class m151012_075725_add_sert_table extends Migration
{
    public function up()
    {
        $tableOptionsMyISAM = 'CHARACTER SET utf8 COLLATE utf8_general_ci ENGINE=MyISAM';

        $this->createTable('{{%sert}}', [
            'sert_id' => Schema::TYPE_PK,
            'sert_name' => Schema::TYPE_STRING . ' Not Null Comment \'Название\'',
            'sert_active' => Schema::TYPE_SMALLINT . ' Not Null Default 1 Comment \'Активен\'',
            'sert_template' => Schema::TYPE_TEXT . ' Comment \'Шаблон\'',
            'sert_created' => Schema::TYPE_DATETIME . ' Not Null Comment \'Создан\'',
            'sert_updated' => Schema::TYPE_DATETIME . ' Not Null Comment \'Изменен\'',
        ], $tableOptionsMyISAM);

        $this->createIndex('idx_sert_name', '{{%sert}}', 'sert_name');

        $this->refreshCache();
    }

    public function down()
    {
        $this->dropTable('{{%sert}}');
        $this->refreshCache();
    }

    public function refreshCache()
    {
        Yii::$app->db->schema->refresh();
        Yii::$app->db->schema->getTableSchemas();
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
