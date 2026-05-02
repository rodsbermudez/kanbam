<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAgencyIdToClients extends Migration
{
    public function up()
    {
        $this->forge->addColumn('clients', [
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
        $this->forge->dropForeignKey('clients', 'clients_agency_id_foreign');
        $this->forge->dropColumn('clients', 'agency_id');
    }
}