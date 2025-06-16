<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PtcAds extends Migration
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
            'owner' => [
                'type'          => 'INT',
                'unsigned'      => true,
                'constraint'    => 100,
                'null'          => false
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint'     => 255,
                'null'  => true
            ],
            'description' => [
                'type'       => 'VARCHAR',
                'constraint'     => 255,
                'null'  => true
            ],
            'reward' => [
                'type'       => 'DECIMAL',
                'constraint'     => '20,8',
                'default' => '0.00000000',
            ],
            'timer' => [
                'type'       => 'INT',
                'constraint'     => '255',
                'default' => '0',
                'null'  => false
            ],
            'url' => [
                'type'       => 'VARCHAR',
                'constraint'     => 255,
                'null'  => true
            ],
            'total_view' => [
                'type'       => 'INT',
                'constraint'     => 255,
                'null'  => false
            ],
            'views' => [
                'type'       => 'INT',
                'constraint'     => 255,
                'null'  => false
            ],
            'status' => [
                'type'       => 'ENUM("active", "inactive")',
                'default'        => 'active',
            ],
            'option_id' => [
                'type'       => 'INT',
                'constraint'     => 255,
                'null'  => false
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ptc_ads');
    }

    public function down()
    {
        $this->forge->dropTable('ptc_ads');
    }
}
