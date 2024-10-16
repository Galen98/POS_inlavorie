<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class LoginHistory extends AbstractMigration
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
        $table = $this->table('login_historys', array('id' => 'id'));
        $table->addColumn('karyawan_masters_id', 'integer', ['null' => false])
              ->addColumn('login_ip', 'string', ['limit' => 45, 'null' => false])
              ->addColumn('login_time', 'datetime', ['default' => 'CURRENT_TIMESTAMP'])
              ->addForeignKey('karyawan_masters_id', 'karyawan_masters', 'id', [
                  'constraint' => 'fk_karyawan_masters_id',
                  'delete' => 'CASCADE',
                  'update' => 'NO_ACTION',
              ])
              ->create();
    }
}
