<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAgencyIdToClientAccess extends Migration
{
    public function up()
    {
        $this->forge->addColumn('client_access', [
            'agency_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);

        $this->forge->addForeignKey('agency_id', 'agencies', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropForeignKey('client_access', 'client_access_agency_id_foreign');
        $this->forge->dropColumn('client_access', 'agency_id');
    }
}