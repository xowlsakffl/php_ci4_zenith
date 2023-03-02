<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CompanyUserMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 5,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'company_id' => [
                'type'       => 'INT',
                'constraint'     => 5,
                'unsigned' => true
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint'     => 5,
                'unsigned' => true,
                'unique' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('company_id', 'companies', 'cdx', '', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->createTable('companies_users');
    }

    public function down()
    {
        $this->forge->dropTable('companies_users');
    }
}
