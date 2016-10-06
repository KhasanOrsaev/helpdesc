<?php

use yii\db\Migration;

/**
 * Handles the creation for table `subjects`.
 */
class m160909_083746_create_subjects_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('subjects', [
            'id' => $this->primaryKey(),
            'type' => 'ENUM("default","lims") not null default "default"',
            'text' => 'varchar(100) not null',
            'created_at' => 'timestamp',
            'updated_at' => 'timestamp',
            'finished_at' => 'timestamp',
            'time_finish' => 'timestamp',
            'taken_at' => 'timestamp',
            'taken_by' => 'int(11)',
            'created_by' => 'int(11)',
            'level' => 'int(1)',
            'comments' => 'text',
            'description' => 'text',
            'status' => 'varchar(1)'
        ],'CHARACTER SET utf8');

        $this->addForeignKey('FK_USERS_1','subjects','created_by','users','id');
        $this->addForeignKey('FK_USERS_2','subjects','taken_by','users','id');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('subjects');
    }
}
