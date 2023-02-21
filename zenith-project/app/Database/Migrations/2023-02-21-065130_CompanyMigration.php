<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CompanyMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'cdx' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'companyType' => [
                'type'       => 'VARCHAR',
                'constraint' => '30',
            ],
            'companyName' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'companyTel' => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
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
            ]
        ]);
        $this->forge->addKey('cdx', true);
        $this->forge->createTable('companies');
    }

    public function down()
    {
        $this->forge->dropTable('companies');
    }
}
