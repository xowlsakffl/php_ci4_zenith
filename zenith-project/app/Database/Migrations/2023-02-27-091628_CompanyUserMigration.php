<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CompanyUserMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'user_id' => [
                'type'       => 'INT',
                'constraint'     => 5,
                'unsigned' => true,
                'unique' => true,
                'auto_increment' => false,
            ],
            'company_id' => [
                'type'       => 'INT',
                'constraint'     => 5,
                'unsigned' => true
            ]
        ]);
        $this->forge->addKey('user_id', true, true);
        $this->forge->addForeignKey('company_id', 'companies', 'cdx', '', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', '', 'CASCADE');
        $this->forge->createTable('companies_users');
    }

    public function down()
    {
        $this->forge->dropTable('companies_users');
    }
}
