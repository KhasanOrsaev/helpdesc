<?php

use yii\db\Migration;

class m170907_114723_insert_statuses_table extends Migration
{
    public function up()
    {
        $this->insert('statuses',['symbol'=>'A', 'color' => '#0000CD', 'name' => 'В работе']);
        $this->insert('statuses',['symbol'=>'T', 'color' => '#008000', 'name' => 'Выполнена']);
        $this->insert('statuses',['symbol'=>'D', 'color' => '#DC143C', 'name' => 'Создана']);
        $this->insert('statuses',['symbol'=>'C', 'color' => '#FFD700', 'name' => 'На подтверждении']);
        $this->insert('statuses',['symbol'=>'R', 'color' => '#808080', 'name' => 'Удалена']);
        $this->insert('statuses',['symbol'=>'W', 'color' => '#808000', 'name' => 'В ожидании']);
    }

    public function down()
    {
        echo "m170907_114723_insert_statuses_table cannot be reverted.\n";

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
