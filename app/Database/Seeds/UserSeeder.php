<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name'       => 'Administrator',
                'email'      => 'admin@myprojek.com',
                'username'   => 'admin',
                'password'   => password_hash('admin123', PASSWORD_DEFAULT),
                'avatar'     => null,
                'created_at' => date('Y-m-d H:i:s'),
                'last_login' => null,
            ],
            [
                'name'       => 'Demo User',
                'email'      => 'demo@myprojek.com',
                'username'   => 'demo',
                'password'   => password_hash('demo123', PASSWORD_DEFAULT),
                'avatar'     => null,
                'created_at' => date('Y-m-d H:i:s'),
                'last_login' => null,
            ]
        ];

        // Insert data
        $this->db->table('users')->insertBatch($data);
    }
}
