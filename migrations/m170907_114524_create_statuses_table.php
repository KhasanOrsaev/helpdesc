<?php

use yii\db\Migration;

/**
 * Handles the creation for table `statuses`.
 */
class m170907_114524_create_statuses_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('statuses', [
            'symbol'    => 'varchar(1)',
            'name'      => 'varchar(10)',
            'color'     => 'varchar(7)',

        ],'CHARACTER SET utf8');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('statuses');
    }
}
