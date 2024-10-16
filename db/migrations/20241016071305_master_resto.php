<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class MasterResto extends AbstractMigration
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
        $table = $this->table('resto_masters', array('id' => 'id'));
        $table->addColumn('nama_resto', 'string', ['limit' => 64])
            ->addColumn('alamat', 'string', ['limit' => 64])
            ->addColumn('thumbnails','string', ['limit' => 255])
            ->addColumn('keterangan','string', ['limit' => 255])
            ->addColumn('roles', 'integer', ['limit' => 11])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
