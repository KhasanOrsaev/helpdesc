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
            'is_admin' => 'boolean default false',
            'org' => 'enum("nacpp","iki","csm") default "nacpp"',
            'is_chief' => 'boolean default false',
            'is_it' => 'boolean default false',
            'is_dept_chief' => 'boolean default false',
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
