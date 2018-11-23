<?php

use Migrations\AbstractMigration;

class AddCreatedByToLogs extends AbstractMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * http://docs.phinx.org/en/latest/migrations.html#the-change-method
     *
     * @return void
     */
    public function change()
    {
        $table = $this->table('logs');
        $table->addColumn('created_by', 'string', [
            'default' => null,
            'limit'   => 50,
            'null'    => false,
        ]);
        $table->addColumn('name', 'string', [
            'default' => null,
            'limit'   => 255,
            'null'    => true,
        ]);
        $table->update();
    }
}
