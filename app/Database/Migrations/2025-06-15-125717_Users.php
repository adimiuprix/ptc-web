<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class Users extends Migration
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
            'username' => [
                'type'       => 'VARCHAR',
                'constraint'     => 255,
                'null'  => true
            ],
            'password' => [
                'type'       => 'VARCHAR',
                'constraint'     => 255,
                'null'  => true
            ],

            'balance' => [
                'type'       => 'DECIMAL',
                'constraint'     => '20,8',
                'default' => '0.00000000',
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
