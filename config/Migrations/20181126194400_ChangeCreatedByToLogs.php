<?php

use Migrations\AbstractMigration;

class ChangeCreatedByToLogs extends AbstractMigration
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
        $table->changeColumn('created_by', 'string', [
            'default' => null,
            'limit'   => 50,
            'null'    => true,
        ]);
        $table->update();
    }
}
