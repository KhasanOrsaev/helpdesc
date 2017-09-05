<?php

use yii\db\Migration;

/**
 * Handles the creation for table `logs`.
 */
class m160909_083807_create_logs_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('logs', [
            'id' => $this->primaryKey(),
            'text' => 'text',
            'time' => 'timestamp'
        ],'CHARACTER SET utf8');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('logs');
    }
}
