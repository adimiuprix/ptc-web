<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PtcOptions extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 100,
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'timer' => [
                'type'          => 'INT',
                'unsigned'      => true,
                'constraint'    => 100,
                'null'          => false
            ],
            'price' => [
                'type'       => 'DECIMAL',
                'constraint'     => '20,8',
                'default' => '0.00000000',
            ],
            'reward' => [
                'type'       => 'DECIMAL',
                'constraint'     => '20,8',
                'default' => '0.00000000',
            ],
            'min_view' => [
                'type'          => 'INT',
                'unsigned'      => true,
                'constraint'    => 100,
                'null'          => false
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ptc_options');
    }

    public function down()
    {
        $this->forge->dropTable('ptc_options');
    }
}
