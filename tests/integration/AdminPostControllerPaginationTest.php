<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use App\Models\ArticleModel;
use App\Controllers\Admin\Post as AdminPostController;

/**
 * Integration Tests for Admin Post Controller Pagination
 * 
 * Tests admin pagination functionality including CRUD state preservation,
 * search and filter integration, and admin-specific error handling.
 * 
 * Requirements: 2.1, 2.2, 2.3, 2.4
 * 
 * @internal
 */
final class AdminPostControllerPaginationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $seed = 'Tests\Support\Database\Seeds\ArticleSeeder';
    
    protected $controller;
    protected $request;
    protected $response;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load required helpers
        helper(['url', 'form']);
        
        // Create controller instance for direct testing
        $this->controller = new AdminPostController();
        $this->request = \Config\Services::request();
        $this->response = \Config\Services::response();
    }

    /**
     * Test admin pagination with 15 posts per page (admin-specific page size)
     * Requirements: 2.1, 2.2
     */
    public function testAdminPaginationWithCorrectPageSize(): void
    {
        // Create test articles to ensure we have data
        $articleModel = new ArticleModel();
        
        // Create 20 test articles to test pagination
        for ($i = 1; $i <= 20; $i++) {
            $article = [
                'id' => 'admin-test-' . $i . '-' . time(),
                'title' => "Admin Test Article $i",
                'content' => "Content for admin test article $i",
                'slug' => 'admin-test-' . $i . '-' . time(),
                'draft' => $i % 2 === 0 ? 'true' : 'false'
            ];
            $articleModel->insert($article);
        }
        
        // Test pagination data directly from model with admin conditions (include all articles)
        // For admin, we want to include both published and draft articles
        $adminConditions = ['draft' => ['true', 'false']]; // Include both draft and published
        $paginationData = $articleModel->getPaginationData(1, 15, $adminConditions);
        
        // Should have pagination metadata
        $this->assertArrayHasKey('data', $paginationData);
        $this->assertArrayHasKey('pagination', $paginationData);
        
        // Should have correct page size (15 for admin)
        $this->assertEquals(15, $paginationData['pagination']['per_page']);
        
        // Should have correct total pages calculation (all articles for admin)
        $expectedTotalPages = ceil(count($articleModel->findAll()) / 15);
        $this->assertEquals($expectedTotalPages, $paginationData['pagination']['total_pages']);
        
        // Test page 2 if we have enough data
        if ($paginationData['pagination']['total_pages'] > 1) {
            $page2Data = $articleModel->getPaginationData(2, 15, []);
            $this->assertEquals(2, $page2Data['pagination']['current_page']);
            $this->assertTrue($page2Data['pagination']['has_previous']);
        }
    }

    /**
     * Test pagination state preservation during CRUD operations
     * Requirements: 2.4, 2.5
     */
    public function testPaginationStatePreservationDuringCrud(): void
    {
        // Test the buildReturnUrl method directly
        $controller = new AdminPostController();
        
        // Mock request with pagination parameters
        $_GET['page'] = '2';
        $_GET['keyword'] = 'test';
        
        // Use reflection to access private method
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('buildReturnUrl');
        $method->setAccessible(true);
        
        $returnUrl = $method->invoke($controller);
        
        // Should contain pagination parameters
        $this->assertStringContainsString('page=2', $returnUrl);
        $this->assertStringContainsString('keyword=test', $returnUrl);
        $this->assertStringContainsString('admin/post', $returnUrl);
        
        // Test with no parameters
        unset($_GET['page']);
        unset($_GET['keyword']);
        
        $returnUrl = $method->invoke($controller);
        $this->assertEquals('admin/post', $returnUrl);
        
        // Test with only page parameter
        $_GET['page'] = '3';
        
        $returnUrl = $method->invoke($controller);
        $this->assertStringContainsString('page=3', $returnUrl);
        $this->assertStringNotContainsString('keyword', $returnUrl);
    }

    /**
     * Test search functionality with pagination
     * Requirements: 2.4
     */
    public function testSearchWithPagination(): void
    {
        // Create test articles with searchable content
        $articleModel = new ArticleModel();
        for ($i = 1; $i <= 20; $i++) {
            $testArticle = [
                'id' => 'search-test-' . $i . '-' . time(),
                'title' => "Searchable Article $i",
                'content' => 'This is searchable content for testing',
                'slug' => 'searchable-article-' . $i . '-' . time(),
                'draft' => 'false'
            ];
            $articleModel->insert($testArticle);
        }
        
        // Test search functionality directly
        $searchResults = $articleModel->search('Searchable');
        
        // Should find articles with searchable content
        $this->assertGreaterThan(0, count($searchResults));
        
        // Verify search results contain the keyword
        foreach ($searchResults as $article) {
            $this->assertStringContainsString('Searchable', $article->title);
        }
        
        // Test pagination with search results
        // Simulate search with pagination by building query manually
        $builder = $articleModel->builder();
        $builder->like('title', 'Searchable')
                ->orLike('content', 'Searchable');
        
        $totalSearchResults = $builder->countAllResults(false);
        $this->assertGreaterThan(0, $totalSearchResults);
        
        // Test pagination of search results
        $perPage = 15;
        $page1Results = $builder->limit($perPage, 0)->get()->getResult();
        $this->assertLessThanOrEqual($perPage, count($page1Results));
        
        if ($totalSearchResults > $perPage) {
            // Reset builder for page 2
            $builder = $articleModel->builder();
            $builder->like('title', 'Searchable')
                    ->orLike('content', 'Searchable');
            
            $page2Results = $builder->limit($perPage, $perPage)->get()->getResult();
            $this->assertGreaterThan(0, count($page2Results));
        }
    }

    /**
     * Test admin-specific error handling for pagination failures
     * Requirements: 5.5
     */
    public function testAdminSpecificErrorHandling(): void
    {
        $controller = new AdminPostController();
        
        // Test error message generation
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('getAdminErrorMessage');
        $method->setAccessible(true);
        
        // Test different error contexts
        $exception = new \Exception('Database connection failed');
        
        $paginationError = $method->invoke($controller, 'pagination', $exception);
        $this->assertStringContainsString('error occurred while loading', $paginationError);
        
        $searchError = $method->invoke($controller, 'search', $exception);
        $this->assertStringContainsString('Search functionality', $searchError);
        
        $invalidPageError = $method->invoke($controller, 'invalid_page', $exception);
        $this->assertStringContainsString('invalid', $invalidPageError);
        
        // Test error handling method
        $handleErrorMethod = $reflection->getMethod('handleAdminPaginationError');
        $handleErrorMethod->setAccessible(true);
        
        $errorData = $handleErrorMethod->invoke($controller, $exception, 'pagination');
        
        // Should return fallback data structure
        $this->assertArrayHasKey('articles', $errorData);
        $this->assertArrayHasKey('pagination', $errorData);
        $this->assertArrayHasKey('current_page', $errorData);
        $this->assertArrayHasKey('error_context', $errorData);
        
        // Pagination should be null in error state
        $this->assertNull($errorData['pagination']);
        $this->assertEquals(1, $errorData['current_page']);
        $this->assertEquals('pagination', $errorData['error_context']);
    }

    /**
     * Test pagination with both published and draft posts (admin view)
     * Requirements: 2.1, 2.2
     */
    public function testAdminPaginationWithAllPostTypes(): void
    {
        // Create mix of published and draft articles
        $articleModel = new ArticleModel();
        
        for ($i = 1; $i <= 10; $i++) {
            $publishedArticle = [
                'id' => 'published-' . $i . '-' . time(),
                'title' => "Published Article $i",
                'content' => 'Published content',
                'slug' => 'published-' . $i . '-' . time(),
                'draft' => 'false'
            ];
            $articleModel->insert($publishedArticle);
            
            $draftArticle = [
                'id' => 'draft-' . $i . '-' . time(),
                'title' => "Draft Article $i",
                'content' => 'Draft content',
                'slug' => 'draft-' . $i . '-' . time(),
                'draft' => 'true'
            ];
            $articleModel->insert($draftArticle);
        }
        
        // Test admin pagination includes all articles (include both draft and published)
        $adminConditions = ['draft' => ['true', 'false']]; // Include both draft and published
        $allArticles = $articleModel->getPaginationData(1, 15, $adminConditions);
        
        // Should include both published and draft articles
        $this->assertGreaterThanOrEqual(20, $allArticles['pagination']['total_records']);
        
        // Verify we have both types in the data
        $foundPublished = false;
        $foundDraft = false;
        
        foreach ($allArticles['data'] as $article) {
            if ($article->draft === 'false') {
                $foundPublished = true;
            }
            if ($article->draft === 'true') {
                $foundDraft = true;
            }
        }
        
        $this->assertTrue($foundPublished, 'Should include published articles');
        $this->assertTrue($foundDraft, 'Should include draft articles');
    }

    /**
     * Test admin pagination URL generation and parameter handling
     * Requirements: 2.3, 2.4
     */
    public function testAdminPaginationUrlHandling(): void
    {
        // Test buildReturnUrl method with various parameter combinations
        $controller = new AdminPostController();
        $reflection = new \ReflectionClass($controller);
        $method = $reflection->getMethod('buildReturnUrl');
        $method->setAccessible(true);
        
        // Test with page and keyword
        $_GET['page'] = '2';
        $_GET['keyword'] = 'test';
        $_GET['status'] = 'draft';
        
        $returnUrl = $method->invoke($controller);
        
        // Should maintain all parameters
        $this->assertStringContainsString('page=2', $returnUrl);
        $this->assertStringContainsString('keyword=test', $returnUrl);
        $this->assertStringContainsString('status=draft', $returnUrl);
        
        // Test with only keyword (no page)
        unset($_GET['page']);
        $returnUrl = $method->invoke($controller);
        
        $this->assertStringNotContainsString('page=', $returnUrl);
        $this->assertStringContainsString('keyword=test', $returnUrl);
        $this->assertStringContainsString('status=draft', $returnUrl);
        
        // Clean up
        unset($_GET['keyword']);
        unset($_GET['status']);
    }

    /**
     * Test admin pagination performance with large datasets
     * Requirements: 2.1, 2.2
     */
    public function testAdminPaginationPerformance(): void
    {
        // Create a larger dataset for performance testing
        $articleModel = new ArticleModel();
        
        for ($i = 1; $i <= 50; $i++) {
            $article = [
                'id' => 'perf-test-' . $i . '-' . time(),
                'title' => "Performance Test Article $i",
                'content' => 'Content for performance testing with some longer text to simulate real articles',
                'slug' => 'perf-test-' . $i . '-' . time(),
                'draft' => $i % 2 === 0 ? 'true' : 'false'
            ];
            $articleModel->insert($article);
        }
        
        // Test pagination performance directly with model
        $startTime = microtime(true);
        
        $paginationData = $articleModel->getPaginationData(2, 15, []);
        
        $endTime = microtime(true);
        $responseTime = $endTime - $startTime;
        
        // Should have correct pagination data
        $this->assertArrayHasKey('data', $paginationData);
        $this->assertArrayHasKey('pagination', $paginationData);
        $this->assertEquals(2, $paginationData['pagination']['current_page']);
        
        // Response should be reasonably fast (less than 1 second for model operations)
        $this->assertLessThan(1.0, $responseTime, 'Admin pagination model should respond within 1 second');
        
        // Test search performance
        $startTime = microtime(true);
        
        $searchResults = $articleModel->search('Performance');
        
        $endTime = microtime(true);
        $searchResponseTime = $endTime - $startTime;
        
        // Should find articles
        $this->assertGreaterThan(0, count($searchResults));
        
        // Search should also be reasonably fast
        $this->assertLessThan(1.0, $searchResponseTime, 'Admin search should respond within 1 second');
    }

    /**
     * Test admin pagination fallback behavior
     * Requirements: 5.5
     */
    public function testAdminPaginationFallbackBehavior(): void
    {
        // Test pagination edge cases with model directly
        $articleModel = new ArticleModel();
        
        // Test with invalid page numbers - model should handle gracefully
        $result1 = $articleModel->getPaginationData(-1, 15, []);
        $this->assertEquals(1, $result1['pagination']['current_page']); // Should default to page 1
        
        $result2 = $articleModel->getPaginationData(0, 15, []);
        $this->assertEquals(1, $result2['pagination']['current_page']); // Should default to page 1
        
        // Test with extremely high page number
        $result3 = $articleModel->getPaginationData(999999, 15, []);
        $totalPages = $result3['pagination']['total_pages'];
        if ($totalPages > 0) {
            $this->assertLessThanOrEqual($totalPages, $result3['pagination']['current_page']);
        } else {
            $this->assertEquals(1, $result3['pagination']['current_page']);
        }
        
        // Test with invalid per_page values
        $result4 = $articleModel->getPaginationData(1, -5, []);
        $this->assertGreaterThan(0, $result4['pagination']['per_page']); // Should use positive value
        
        $result5 = $articleModel->getPaginationData(1, 0, []);
        $this->assertGreaterThan(0, $result5['pagination']['per_page']); // Should use positive value
    }

    protected function tearDown(): void
    {
        // Clean up session
        session()->remove('admin_logged_in');
        parent::tearDown();
    }
}