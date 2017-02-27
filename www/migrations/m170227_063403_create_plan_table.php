<?php

use yii\db\Schema;
use yii\db\Migration;

/**
 * Handles the creation of table `plan`.
 */
class m170227_063403_create_plan_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $tableOptions = null;
        if ($this->db->driverName === 'mysql') {
            $tableOptions = 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB';
        }

        $this->createTable('{{%plan%}}', [
            'id'    => Schema::TYPE_PK,
            'name'  => Schema::TYPE_TEXT.' NOT NULL COMMENT "名称" ',
            'ctime' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT "创建时间"',
            'utime' => Schema::TYPE_TIMESTAMP . ' NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT "更新时间"',
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%plan}}');
    }
}
