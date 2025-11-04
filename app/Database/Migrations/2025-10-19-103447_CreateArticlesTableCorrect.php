<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateArticlesTableCorrect extends Migration
{
    public function up()
    {
        // Check if table already exists
        if ($this->db->tableExists('articles')) {
            echo "Table 'articles' already exists. Skipping creation.\n";
            return;
        }

        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 32,
                'null'       => false,
            ],
            'title' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => true,
            ],
            'slug' => [
                'type'       => 'VARCHAR',
                'constraint' => 128,
                'null'       => false,
            ],
            'content' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'draft' => [
                'type'       => 'ENUM',
                'constraint' => ['true', 'false'],
                'default'    => 'true',
                'null'       => false,
            ],
            'created_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('slug');
        $this->forge->createTable('articles');
    }

    public function down()
    {
        $this->forge->dropTable('articles');
    }
}
