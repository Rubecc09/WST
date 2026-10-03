<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class AdminSeeder extends Seeder
{
    public function run()
    {
        $table = $this->db->table('user_accounts');
        if ($table->where('username', 'admin')->countAllResults() === 0) {
            $table->insert([
                'username' => 'admin',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'display_name' => 'PowerFlow Administrator',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }
}
