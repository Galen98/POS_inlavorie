<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class KaryawanMaster extends AbstractMigration
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
        $table = $this->table('karyawan_masters', array('id' => 'id'));
        $table->addColumn('username', 'string', ['limit' => 64])
            ->addColumn('users_id', 'integer', ['limit' => 45, 'null' => false])
            ->addColumn('name', 'string', ['limit' => 64])
            ->addColumn('password','string', ['limit' => 255])
            ->addColumn('roles', 'integer', ['limit' => 11])
            ->addColumn('status', 'boolean')
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addIndex('username', ['unique' => true])
            ->create();
    }
}
