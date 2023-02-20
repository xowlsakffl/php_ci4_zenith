<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class BoardMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'bdx' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'board_title' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'board_description' => [
                'type' => 'TEXT',
            ],
            'created_at' => [
                'type'       => ' TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'null' => false,
            ],
            'updated_at' => [
                'type'       => ' TIMESTAMP DEFAULT CURRENT_TIMESTAMP',
                'null' => false,
            ],
            'deleted_at' => [
                'type'       => ' TIMESTAMP',
                'null' => true,
            ],
        ]);
        $this->forge->addPrimaryKey('bdx', true);
        $this->forge->createTable('boards');
    }

    public function down()
    {
        $this->forge->dropTable('boards');
    }
}
