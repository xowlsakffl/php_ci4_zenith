<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CompanyIdxMigration extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'parent_cdx' => [
                'type'       => 'INT',
                'constraint'     => 5,
                'unsigned' => true,
            ],
            'cdx' => [
                'type'       => 'INT',
                'constraint'     => 5,
                'unsigned' => true,
                'unique' => true,
            ]
        ]);
        $this->forge->createTable('companies_idx');
    }

    public function down()
    {
        $this->forge->dropTable('companies_idx');
    }
}
