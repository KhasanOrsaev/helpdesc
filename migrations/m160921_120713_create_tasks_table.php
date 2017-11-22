<?php

use yii\db\Migration;

/**
 * Handles the creation for table `tasks`.
 */
class m160921_120713_create_tasks_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('tasks', [
            'id' => $this->primaryKey(),
            'name' => 'varchar(100) not null',
            'department' =>'enum("nacpp","csm","iki") default "nacpp"'
        ], 'CHARACTER SET utf8');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('tasks');
    }
}
