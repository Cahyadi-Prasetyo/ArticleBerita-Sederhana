<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\LargeDatasetSeeder;
use App\Models\ArticleModel;
use App\Helpers\PaginationHelper;

/**
 * @internal
 */
final class PaginationPerformanceTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = LargeDatasetSeeder::class;
    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;

    private ArticleModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new ArticleModel();
    }

    public function testPaginationWithLargeDataset(): void
    {
        // Test pagination performance with large dataset
        $startTime = microtime(true);
        
        $result = $this->model->getPaginationData(1, 10);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Should complete within reasonable time (less than 1 second)
        $this->assertLessThan(1.0, $executionTime, 'Pagination query took too long');
        
        // Verify correct structure
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        $this->assertCount(10, $result['data']);
    }

    public function testCountQueryPerformance(): void
    {
        // Test count query performance
        $startTime = microtime(true);
        
        $count = $this->model->getTotalArticles();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Count query should be fast (less than 0.5 seconds)
        $this->assertLessThan(0.5, $executionTime, 'Count query took too long');
        $this->assertGreaterThan(0, $count);
    }

    public function testCachingPerformance(): void
    {
        // First call - should hit database
        $startTime1 = microtime(true);
        $count1 = $this->model->getTotalArticles([], true);
        $endTime1 = microtime(true);
        $time1 = $endTime1 - $startTime1;
        
        // Second call - should hit cache
        $startTime2 = microtime(true);
        $count2 = $this->model->getTotalArticles([], true);
        $endTime2 = microtime(true);
        $time2 = $endTime2 - $startTime2;
        
        // Results should be identical
        $this->assertEquals($count1, $count2);
        
        // Cached call should be significantly faster
        $this->assertLessThan($time1, $time2, 'Cached query should be faster');
    }

    public function testPaginationWithVariousPageSizes(): void
    {
        $pageSizes = [5, 10, 25, 50, 100];
        
        foreach ($pageSizes as $pageSize) {
            $startTime = microtime(true);
            
            $result = $this->model->getPaginationData(1, $pageSize);
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            // Should complete within reasonable time regardless of page size
            $this->assertLessThan(1.0, $executionTime, "Pagination with page size {$pageSize} took too long");
            
            // Verify correct page size (or less if not enough data)
            $this->assertLessThanOrEqual($pageSize, count($result['data']));
        }
    }

    public function testPaginationWithFilters(): void
    {
        $conditions = [
            ['draft' => 'false'],
            ['draft' => 'true'],
            ['draft' => ['true', 'false']]
        ];
        
        foreach ($conditions as $condition) {
            $startTime = microtime(true);
            
            $result = $this->model->getPaginationData(1, 10, $condition);
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            // Filtered queries should still be fast
            $this->assertLessThan(1.0, $executionTime, 'Filtered pagination took too long');
            
            // Verify structure
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('pagination', $result);
        }
    }

    public function testDeepPaginationPerformance(): void
    {
        // Test performance on later pages (where OFFSET is large)
        $totalRecords = $this->model->getTotalArticles();
        $perPage = 10;
        $lastPage = (int)ceil($totalRecords / $perPage);
        
        if ($lastPage > 10) {
            $testPage = $lastPage - 2; // Test a page near the end
            
            $startTime = microtime(true);
            
            $result = $this->model->getPaginationData($testPage, $perPage);
            
            $endTime = microtime(true);
            $executionTime = $endTime - $startTime;
            
            // Deep pagination should still be reasonably fast
            $this->assertLessThan(2.0, $executionTime, 'Deep pagination took too long');
            
            // Verify correct page
            $this->assertEquals($testPage, $result['pagination']['current_page']);
        }
    }

    public function testMemoryUsageWithLargePages(): void
    {
        $memoryBefore = memory_get_usage();
        
        // Get a large page of results
        $result = $this->model->getPaginationData(1, 100);
        
        $memoryAfter = memory_get_usage();
        $memoryUsed = $memoryAfter - $memoryBefore;
        
        // Memory usage should be reasonable (less than 10MB for 100 records)
        $this->assertLessThan(10 * 1024 * 1024, $memoryUsed, 'Memory usage too high for large page');
        
        // Verify we got the expected number of results
        $this->assertLessThanOrEqual(100, count($result['data']));
    }

    public function testConcurrentPaginationRequests(): void
    {
        // Simulate multiple concurrent pagination requests
        $results = [];
        $times = [];
        
        for ($i = 1; $i <= 5; $i++) {
            $startTime = microtime(true);
            
            $results[$i] = $this->model->getPaginationData($i, 10);
            
            $endTime = microtime(true);
            $times[$i] = $endTime - $startTime;
        }
        
        // All requests should complete in reasonable time
        foreach ($times as $page => $time) {
            $this->assertLessThan(1.0, $time, "Page {$page} took too long");
        }
        
        // Verify all results have correct structure
        foreach ($results as $page => $result) {
            $this->assertArrayHasKey('data', $result);
            $this->assertArrayHasKey('pagination', $result);
            $this->assertEquals($page, $result['pagination']['current_page']);
        }
    }

    public function testPaginationHelperPerformance(): void
    {
        // Test PaginationHelper performance with large pagination metadata
        $pagination = [
            'current_page' => 50,
            'per_page' => 10,
            'total_records' => 10000,
            'total_pages' => 1000,
            'has_previous' => true,
            'has_next' => true,
            'previous_page' => 49,
            'next_page' => 51,
            'first_page' => 1,
            'last_page' => 1000
        ];
        
        $startTime = microtime(true);
        
        $links = PaginationHelper::generateLinks($pagination, '/articles', ['category' => 'tech']);
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Link generation should be fast
        $this->assertLessThan(0.1, $executionTime, 'Link generation took too long');
        
        // Verify structure
        $this->assertArrayHasKey('pages', $links);
        $this->assertIsArray($links['pages']);
    }

    public function testCacheClearingPerformance(): void
    {
        // Populate cache first
        $this->model->getTotalArticles([], true);
        $this->model->getTotalArticles(['draft' => 'false'], true);
        
        $startTime = microtime(true);
        
        // Clear cache
        $this->model->clearPaginationCache();
        
        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;
        
        // Cache clearing should be fast
        $this->assertLessThan(0.1, $executionTime, 'Cache clearing took too long');
    }
}