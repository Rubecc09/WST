<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class MoveCustomersToCustomerAccounts extends Migration
{
    public function up()
    {
        $fields = $this->db->getFieldNames('customer_accounts');
        if (! in_array('username', $fields, true)) {
            $this->forge->addColumn('customer_accounts', [
                'username' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'account_number'],
            ]);
        }
        if (! in_array('password', $fields, true)) {
            $this->forge->addColumn('customer_accounts', [
                'password' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true, 'after' => 'username'],
            ]);
        }

        $indexes = $this->db->getIndexData('customer_accounts');
        if (! isset($indexes['unique_customer_username'])) {
            $this->db->query('ALTER TABLE customer_accounts ADD UNIQUE KEY unique_customer_username (username)');
        }

        $this->forge->dropTable('users', true);
    }

    public function down()
    {
        $indexes = $this->db->getIndexData('customer_accounts');
        if (isset($indexes['unique_customer_username'])) {
            $this->db->query('ALTER TABLE customer_accounts DROP INDEX unique_customer_username');
        }
        $this->forge->dropColumn('customer_accounts', ['username', 'password']);
    }
}
