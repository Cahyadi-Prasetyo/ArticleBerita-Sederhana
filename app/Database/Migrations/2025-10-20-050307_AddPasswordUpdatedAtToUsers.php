<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPasswordUpdatedAtToUsers extends Migration
{
    public function up()
    {
        $this->db->query('ALTER TABLE users ADD password_updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP AFTER last_login');
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'password_updated_at');
    }
}
