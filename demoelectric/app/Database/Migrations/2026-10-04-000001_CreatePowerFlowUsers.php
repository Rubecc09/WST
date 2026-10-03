<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePowerFlowUsers extends Migration
{
    public function up()
    {
        if (! $this->db->tableExists('customer_accounts')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'auto_increment' => true],
                'account_number' => ['type' => 'VARCHAR', 'constraint' => 50],
                'username' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
                'customer_name' => ['type' => 'VARCHAR', 'constraint' => 150],
                'address' => ['type' => 'TEXT'],
                'phone' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
                'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
                'meter_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
                'connection_type' => ['type' => 'ENUM', 'constraint' => ['residential', 'commercial', 'industrial'], 'default' => 'residential'],
                'status' => ['type' => 'ENUM', 'constraint' => ['active', 'inactive', 'suspended'], 'default' => 'active'],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('account_number');
            $this->forge->addUniqueKey('username');
            $this->forge->addKey('status');
            $this->forge->addKey('connection_type');
            $this->forge->addKey('customer_name');
            $this->forge->createTable('customer_accounts');
        }

        if (! $this->db->tableExists('user_accounts')) {
            $this->forge->addField([
                'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
                'username' => ['type' => 'VARCHAR', 'constraint' => 100],
                'password' => ['type' => 'VARCHAR', 'constraint' => 255],
                'display_name' => ['type' => 'VARCHAR', 'constraint' => 150],
                'created_at' => ['type' => 'DATETIME', 'null' => true],
                'updated_at' => ['type' => 'DATETIME', 'null' => true],
            ]);
            $this->forge->addKey('id', true);
            $this->forge->addUniqueKey('username');
            $this->forge->createTable('user_accounts');
        }

    }

    public function down()
    {
        $this->forge->dropTable('user_accounts', true);
    }
}
