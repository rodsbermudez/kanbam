<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class FixClientAccessForeignKeys extends Migration
{
    public function up()
    {
        // Drop existing foreign keys
        $this->forge->dropForeignKey('client_access', 'client_access_client_id_foreign');
        
        // Modify client_id to allow NULL
        $this->forge->modifyColumn('client_access', [
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => true,
            ],
        ]);
        
        // Re-add foreign key allowing NULL
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'SET NULL');
    }

    public function down()
    {
        $this->forge->dropForeignKey('client_access', 'client_access_client_id_foreign');
        
        $this->forge->modifyColumn('client_access', [
            'client_id' => [
                'type'       => 'INT',
                'constraint' => 11,
                'unsigned'   => true,
                'null'       => false,
            ],
        ]);
        
        $this->forge->addForeignKey('client_id', 'clients', 'id', 'CASCADE', 'CASCADE');
    }
}