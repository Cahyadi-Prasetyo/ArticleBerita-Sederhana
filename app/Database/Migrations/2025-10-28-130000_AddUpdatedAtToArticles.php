<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToArticles extends Migration
{
    public function up()
    {
        // Check if column already exists
        if ($this->db->fieldExists('updated_at', 'articles')) {
            echo "Column 'updated_at' already exists in 'articles' table. Skipping.\n";
            return;
        }

        // Add updated_at column to articles table
        $this->forge->addColumn('articles', [
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'created_at'
            ]
        ]);

        echo "Added 'updated_at' column to 'articles' table successfully.\n";
    }

    public function down()
    {
        // Remove updated_at column
        if ($this->db->fieldExists('updated_at', 'articles')) {
            $this->forge->dropColumn('articles', 'updated_at');
            echo "Removed 'updated_at' column from 'articles' table.\n";
        }
    }
}