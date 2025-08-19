<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDueDateToTasks extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tasks', [
            'due_date' => [
                'type' => 'DATE',
                'null' => true,
                'after' => 'user_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tasks', 'due_date');
    }
}