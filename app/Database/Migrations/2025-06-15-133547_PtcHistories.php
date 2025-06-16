<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class PtcHistories extends Migration
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
            'user_id' => [
                'type'       => 'INT',
                'null'  => true
            ],
            'ads_id' => [
                'type'       => 'INT',
                'null'  => true
            ],
            'amount' => [
                'type'       => 'DECIMAL',
                'constraint'     => '20,8',
                'default' => '0.00000000',
            ],
            'claim_time' => [
                'type'          => 'BIGINT',
                'unsigned'      => true,
                'null'          => false
            ],
            'created_at DATETIME DEFAULT CURRENT_TIMESTAMP',
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ptc_histories');
    }

    public function down()
    {
        $this->forge->dropTable('ptc_histories');
    }
}
