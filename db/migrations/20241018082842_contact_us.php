<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class ContactUs extends AbstractMigration
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
        $table = $this->table('contact_us', array('id' => 'id'));
        $table->addColumn('name', 'string', ['limit' => 64])
            ->addColumn('email', 'string', ['limit' => 64])
            ->addColumn('message','string', ['limit' => 255])
            ->addColumn('created_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->addColumn('updated_at', 'timestamp', ['default' => 'CURRENT_TIMESTAMP'])
            ->create();
    }
}
