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
            'type' => 'ENUM("default","lims","support") not null default "default"',  // тип
            'text' => 'varchar(100) not null',                                        // думаю ясно
            'created_at' => 'timestamp null default null',                                              // когда создали
            'updated_at' => 'timestamp null default null',                                              // когда обновили
            'finished_at' => 'timestamp',                                             // когда законсчиди
            'time_finish' => 'timestamp',                                             // срок выполнения в минутах
            'taken_at' => 'timestamp',                                                // время когда взялина исполнение
            'taken_by' => 'int(11)',                                                  // кто взял
            'senior' => 'boolean not null default false',                             // назначение исполнителя Саенко, true если тру
            'created_by' => 'int(11)',                                                // кто создал
            'phone' => 'int(11)',                                                     // телефон
            'from_dept' => 'int(11)',                                                 // откуда
            'level' => 'int(1)',                                                      // уровень срочности, для ит
            'comments' => 'text',                                                     // тоже ясно
            'description' => 'text',                                                  // и это
            'status' => 'varchar(1)',                                                 // и это
            'is_confirmed' => 'boolean',                                              // подтвержден ли начальником деп-та, true если тру
            'computer' => 'varchar(7)',                                               // компуктер
            'address' => 'varchar(20)',                                               // место работы
            'file' => 'varchar(255)',                                                 // файл(ы)
        ],'CHARACTER SET utf8');                                                      //

        $this->addForeignKey('FK_USERS_1','subjects','created_by','users','id');
        $this->addForeignKey('FK_USERS_2','subjects','taken_by','users','id');

        $sql = 'create trigger `BEFORE_UPDATE_1`
                before update on `subjects`
                for each row
                begin
                update subjects set updated_at = current_timestamp where id = new.id;
                end
        ';
        $this->execute($sql);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('subjects');
    }
}
