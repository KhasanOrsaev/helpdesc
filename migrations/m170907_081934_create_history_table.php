<?php

use yii\db\Migration;

/**
 * Handles the creation for table `history`.
 */
class m170907_081934_create_history_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('history', [
            'subject_id'    => 'int(11)',
            'logdate'       => 'timestamp',
            'theme'         => 'varchar(100)',
            'description'   => 'text'
        ],'CHARACTER SET utf8');

        $this->addForeignKey('FK_SUBJECT_ID','history','subject_id','subjects','id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('history');
    }
}
