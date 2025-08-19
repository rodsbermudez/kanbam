<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateProjectTypesTable extends Migration
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
            'name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'prompt' => [
                'type' => 'TEXT',
                'null' => false,
            ],
            'label_description' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => 'Descrição Detalhada do Projeto',
            ],
            'placeholder_description' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'label_items' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'default' => 'Itens/Páginas a Serem Criados (um por linha)',
            ],
            'placeholder_items' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'deleted_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('project_types');
    }

    public function down()
    {
        $this->forge->dropTable('project_types');
    }
}