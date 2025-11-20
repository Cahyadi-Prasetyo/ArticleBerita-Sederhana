<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddUpdatedAtToArticles extends Migration
{
    public function up()
    {
        // Add updated_at column to articles table
        $fields = [
            'updated_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
                'after'   => 'created_at'
            ],
        ];
        
        $this->forge->addColumn('articles', $fields);
        
        echo "Added 'updated_at' column to articles table.\n";
    }

    public function down()
    {
        // Remove updated_at column
        $this->forge->dropColumn('articles', 'updated_at');
        
        echo "Removed 'updated_at' column from articles table.\n";
    }
}