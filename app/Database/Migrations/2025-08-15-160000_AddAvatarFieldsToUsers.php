<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAvatarFieldsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'initials' => [
                'type'       => 'VARCHAR',
                'constraint' => 5,
                'null'       => true,
                'after'      => 'name',
            ],
            'color' => [
                'type'       => 'VARCHAR',
                'constraint' => 7, // #RRGGBB
                'default'    => '#6c757d', // Cor cinza padrão
                'after'      => 'initials',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['initials', 'color']);
    }
}