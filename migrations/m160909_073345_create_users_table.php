<?php

use yii\db\Migration;
/**
 * Handles the creation for table `users`.
 */
class m160909_073345_create_users_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('users', [
            'id' => $this->primaryKey(),
            'user_name' => 'varchar(100) not null',
            'display_name' => 'varchar(100) not null',
            'dept_id' => 'int(11)',
            'email' => 'varchar(50)',
            'is_admin' => 'enum("0","1") default "0"',
            'org' => 'enum("nacpp","iki","csm") default "nacpp"',
            'is_chief' => 'enum("0","1") default "0"',
            'is_it' => 'enum("0","1") default "0"',
            'is_dept_chief' => 'enum("0","1") default "0"',
        ], 'CHARACTER SET utf8');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('users');
    }
}
