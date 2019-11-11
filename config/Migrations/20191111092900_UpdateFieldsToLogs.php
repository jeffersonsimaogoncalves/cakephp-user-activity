<?php

use Migrations\AbstractMigration;

class UpdateFieldsToLogs extends AbstractMigration
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
        $table->changeColumn('table_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->changeColumn('database_name', 'string', [
            'default' => null,
            'limit' => 255,
            'null' => false,
        ]);
        $table->update();
    }
}
