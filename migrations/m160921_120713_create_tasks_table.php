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
<<<<<<< HEAD
            'name' => 'varchar(100) not null',
            'document' => 'varchar(100)',
            'code' => 'text',
            'header' => 'text',
            'footer' => 'text',
            'orientation' =>'enum("L","P") default "P"',
            'department' =>'enum("nacpp","csm","iki") default "nacpp"'
=======
            'name' => 'varchar(30) not null',
            'document' => 'varchar(100)',
            'code' => 'text'
>>>>>>> 59e1fa3ca5a28fddd8f3d6763c995033754131c3
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
