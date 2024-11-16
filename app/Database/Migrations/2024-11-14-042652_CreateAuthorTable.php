<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAuthorTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                "type" => 'INT',
                "unsigned" => true,
                "auto_increment" => true,
            ],
            'username' => [
                "type" => "VARCHAR",
                "constraint" => 255,
                "null" => false,
            ],
            'email' => [
                "type" => 'VARCHAR',
                'constraint' => 255,
                "null" => false,
            ],
            'phone_no' => [
                "type" => 'VARCHAR',
                'constraint' => 255,
                "null" => true,
            ],
            'password' => [
                "type" => "VARCHAR",
                "constraint" => 255,
            ],
            'created_at datetime default current_timestamp'
        ]);
        $this->forge->addPrimaryKey('id');
        $this->forge->createTable('authors');
    }

    public function down()
    {
        $this->forge->dropTable('authors');
    }
}
