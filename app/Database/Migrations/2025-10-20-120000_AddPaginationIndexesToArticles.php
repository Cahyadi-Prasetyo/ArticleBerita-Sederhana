<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPaginationIndexesToArticles extends Migration
{
    public function up()
    {
        // Add indexes to optimize pagination queries
        
        // Index for draft column (used in WHERE clauses for published articles)
        $this->forge->addKey('draft', false, false, 'idx_articles_draft');
        
        // Composite index for draft + created_at (optimizes ORDER BY with WHERE)
        $this->forge->addKey(['draft', 'created_at'], false, false, 'idx_articles_draft_created');
        
        // Index for created_at column (used in ORDER BY clauses)
        $this->forge->addKey('created_at', false, false, 'idx_articles_created_at');
        
        // Index for title column (used in search functionality)
        $this->forge->addKey('title', false, false, 'idx_articles_title');
        
        // Apply the indexes to the articles table
        $this->forge->processIndexes('articles');
    }

    public function down()
    {
        // Remove the indexes using raw SQL since CodeIgniter doesn't have a clean way
        $this->db->query('DROP INDEX IF EXISTS idx_articles_draft ON articles');
        $this->db->query('DROP INDEX IF EXISTS idx_articles_draft_created ON articles');
        $this->db->query('DROP INDEX IF EXISTS idx_articles_created_at ON articles');
        $this->db->query('DROP INDEX IF EXISTS idx_articles_title ON articles');
    }
}