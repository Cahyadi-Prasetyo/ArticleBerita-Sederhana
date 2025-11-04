<?php

namespace Tests\Support\Database\Seeds;

use CodeIgniter\Database\Seeder;

class LargeDatasetSeeder extends Seeder
{
    public function run()
    {
        // Create a large dataset for performance testing
        $batchSize = 100;
        $totalRecords = 1000;
        $batches = ceil($totalRecords / $batchSize);
        
        for ($batch = 0; $batch < $batches; $batch++) {
            $data = [];
            $startIndex = $batch * $batchSize;
            $endIndex = min($startIndex + $batchSize, $totalRecords);
            
            for ($i = $startIndex; $i < $endIndex; $i++) {
                $articleNumber = $i + 1;
                $isDraft = ($i % 10 === 0) ? 'true' : 'false'; // 10% draft articles
                
                $data[] = [
                    'id' => 'article_' . str_pad($articleNumber, 6, '0', STR_PAD_LEFT),
                    'title' => "Performance Test Article {$articleNumber}",
                    'slug' => "performance-test-article-{$articleNumber}",
                    'content' => "This is the content for performance test article number {$articleNumber}. " .
                               "It contains enough text to simulate real article content for testing purposes. " .
                               "Lorem ipsum dolor sit amet, consectetur adipiscing elit. Sed do eiusmod tempor " .
                               "incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis " .
                               "nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.",
                    'draft' => $isDraft,
                    'created_at' => date('Y-m-d H:i:s', strtotime("-{$i} hours"))
                ];
            }
            
            // Insert batch
            $this->db->table('articles')->insertBatch($data);
        }
        
        echo "Inserted {$totalRecords} articles for performance testing.\n";
    }
}