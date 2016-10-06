<?php

use yii\db\Migration;

class m160914_125423_insert_dept_table extends Migration
{
    public function up()
    {
        $this->insert('depts',['dept_name'=>'IT']);
    }

    public function down()
    {
        echo "m160914_125423_insert_dept_table cannot be reverted.\n";

        return false;
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
