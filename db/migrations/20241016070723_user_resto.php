<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class UserResto extends AbstractMigration
{
    /**
     * Change Method.
     *
     * Write your reversible migrations using this method.
     *
     * More information on writing migrations is available here:
     * https://book.cakephp.org/phinx/0/en/migrations.html#the-change-method
     *
     * Remember to call "create()" or "update()" and NOT "save()" when working
     * with the Table class.
     */
    public function change(): void
    {
        $table = $this->table('user_restos', array('id' => 'id'));
        $table->addColumn('users_id', 'integer', ['null' => false])
              ->addColumn('resto_masters_id', 'integer', ['limit' => 45, 'null' => false])
              ->addColumn('created_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addColumn('updated_at', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('resto_masters_id', 'resto_masters', 'id', [
                  'constraint' => 'fk_resto_masters_id',
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->addForeignKey('users_id', 'users', 'id', [
                'constraint' => 'fk_users_id',
                'delete' => 'CASCADE',
                'update' => 'NO_ACTION',
              ])
              ->create();
    }
}
