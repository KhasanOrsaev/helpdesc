<?php

use yii\db\Migration;

/**
 * Handles the creation for table `depts`.
 */
class m160909_083758_create_depts_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('depts', [
            'id' => 'pk',
            'dept_name' => 'varchar(100) not null',
        ],'CHARACTER SET utf8');
        $this->addForeignKey('FK_USERS_3','users','dept_id','depts','id');

    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('depts');
    }
}
