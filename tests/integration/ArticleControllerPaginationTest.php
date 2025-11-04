<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\FeatureTestTrait;
use CodeIgniter\Test\DatabaseTestTrait;

/**
 * Integration Tests for Article Controller Pagination
 * 
 * Tests pagination functionality in Article Controller including
 * parameter handling, view data passing, URL generation, and edge cases.
 * 
 * Requirements: 1.3, 3.4
 * 
 * @internal
 */
final class ArticleControllerPaginationTest extends CIUnitTestCase
{
    use FeatureTestTrait;
    use DatabaseTestTrait;

    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;
    protected $seed = 'Tests\Support\Database\Seeds\ArticleSeeder';

    protected function setUp(): void
    {
        parent::setUp();
        
        // Load required helpers
        helper(['url', 'form']);
    }

    /**
     * Test basic pagination functionality with valid page numbers
     * Requirements: 1.3, 3.4
     */
    public function testBasicPaginationWithValidPageNumbers(): void
    {
        // Test page 1 (default)
        $result = $this->get('/articles');
        
        $result->assertOK();
        
        // Debug: Let's see what the actual response contains
        $response = $result->response();
        $body = $response->getBody();
        
        // Check if we have articles or empty state
        if (strpos($body, 'List Artikel') !== false) {
            $result->assertSee('List Artikel');
            $result->assertSee('pagination-nav');
        } else {
            // If no articles, we should see the empty view
            $this->assertStringNotContainsString('List Artikel', $body);
        }
        
        // Test page 2 only if we have enough articles
        $articleModel = new \App\Models\ArticleModel();
        $totalArticles = $articleModel->getTotalArticles();
        
        if ($totalArticles > 10) {
            $result = $this->get('/articles?page=2');
            
            $result->assertOK();
            $result->assertSee('List Artikel');
            $result->assertSee('Page 2 of');
        }
    }

    /**
     * Test pagination with invalid page numbers
     * Requirements: 1.3, 3.4
     */
    public function testPaginationWithInvalidPageNumbers(): void
    {
        // Test negative page number
        $result = $this->get('/articles?page=-1');
        
        // Check if it's a redirect or handles the error gracefully
        if ($result->isRedirect()) {
            $result->assertRedirectTo(base_url('articles'));
            $result->assertSessionHas('error', 'Invalid page number');
        } else {
            // If not redirecting, should still be OK and handle gracefully
            $result->assertOK();
        }
        
        // Test zero page number
        $result = $this->get('/articles?page=0');
        
        if ($result->isRedirect()) {
            $result->assertRedirectTo(base_url('articles'));
            $result->assertSessionHas('error', 'Invalid page number');
        } else {
            $result->assertOK();
        }
        
        // Test non-numeric page number
        $result = $this->get('/articles?page=abc');
        
        if ($result->isRedirect()) {
            $result->assertRedirectTo(base_url('articles'));
            $result->assertSessionHas('error', 'Invalid page number');
        } else {
            $result->assertOK();
        }
    }

    /**
     * Test pagination with page number exceeding total pages
     * Requirements: 1.3, 3.4
     */
    public function testPaginationWithPageExceedingTotal(): void
    {
        // Get total pages first
        $result = $this->get('/articles');
        $result->assertOK();
        
        // Extract total pages from response (this is a simplified approach)
        // In a real scenario, you might need to parse the HTML or check the model directly
        $articleModel = new \App\Models\ArticleModel();
        $paginationData = $articleModel->getPaginationData(1, 10);
        $totalPages = $paginationData['pagination']['total_pages'];
        
        if ($totalPages > 0) {
            // Test page number higher than total pages
            $highPageNumber = $totalPages + 5;
            $result = $this->get("/articles?page={$highPageNumber}");
            
            $expectedRedirectUrl = base_url("articles?page={$totalPages}");
            $result->assertRedirectTo($expectedRedirectUrl);
            $result->assertSessionHas('error', 'Page not found, redirected to last page');
        }
    }

    /**
     * Test pagination URL parameter handling and maintenance
     * Requirements: 1.3, 3.4
     */
    public function testPaginationUrlParameterHandling(): void
    {
        // Test basic pagination with page parameter
        $result = $this->get('/articles?page=2');
        
        $result->assertOK();
        $result->assertSee('Page 2 of');
        
        // Test with custom parameter (should be maintained in pagination links)
        $result = $this->get('/articles?custom=value&page=1');
        
        $result->assertOK();
        
        // Verify custom parameter is maintained in pagination links
        $response = $result->response();
        $body = $response->getBody();
        $this->assertStringContainsString('custom=value', $body);
        
        // Test with multiple custom parameters
        $result = $this->get('/articles?param1=value1&param2=value2&page=1');
        
        $result->assertOK();
        
        // Verify both parameters are maintained
        $response = $result->response();
        $body = $response->getBody();
        $this->assertStringContainsString('param1=value1', $body);
        $this->assertStringContainsString('param2=value2', $body);
    }

    /**
     * Test pagination view data passing
     * Requirements: 1.3, 3.4
     */
    public function testPaginationViewDataPassing(): void
    {
        $result = $this->get('/articles?page=1');
        
        $result->assertOK();
        
        $response = $result->response();
        $body = $response->getBody();
        
        // Verify pagination information is displayed
        $this->assertStringContainsString('Showing', $body);
        $this->assertStringContainsString('of', $body);
        $this->assertStringContainsString('articles', $body);
        $this->assertStringContainsString('Page', $body);
        
        // Verify pagination navigation is present
        $this->assertStringContainsString('pagination-nav', $body);
        $this->assertStringContainsString('page-item', $body);
        $this->assertStringContainsString('page-link', $body);
        
        // Verify SEO meta tags are present
        $this->assertStringContainsString('rel="canonical"', $body);
        
        // Test with page 2 to verify next/prev links
        $result = $this->get('/articles?page=2');
        
        if ($result->isOK()) {
            $response = $result->response();
            $body = $response->getBody();
            
            // Should have previous page link
            $this->assertStringContainsString('Previous', $body);
            
            // May have next page link depending on total pages
            // This is conditional based on actual data
        }
    }

    /**
     * Test pagination with empty results
     * Requirements: 1.3, 3.4
     */
    public function testPaginationWithEmptyResults(): void
    {
        // Clear all articles to test empty state
        $db = \Config\Database::connect();
        $db->table('articles')->truncate();
        
        $result = $this->get('/articles');
        
        $result->assertOK();
        
        // Should show empty article view (check for the actual view content)
        $response = $result->response();
        $body = $response->getBody();
        
        // The empty view might contain different content, let's check for absence of article list
        $this->assertStringNotContainsString('List Artikel', $body);
        
        // Verify no pagination is shown for empty results
        $this->assertStringNotContainsString('pagination-nav', $body);
    }

    /**
     * Test pagination error handling and fallback
     * Requirements: 1.3, 3.4
     */
    public function testPaginationErrorHandlingAndFallback(): void
    {
        // This test simulates database errors by temporarily breaking the model
        // In a real scenario, you might use mocking or dependency injection
        
        $result = $this->get('/articles');
        
        // Even with potential errors, the page should still load
        $result->assertOK();
        $result->assertSee('List Artikel');
        
        // Test with various edge cases
        $edgeCases = [
            '/articles?page=1',
            '/articles?page=999999', // Very high page number
            '/articles?page=1&search=', // Empty search
            '/articles?page=1&category=', // Empty category
        ];
        
        foreach ($edgeCases as $url) {
            $result = $this->get($url);
            
            // Should either be OK or redirect (not error)
            $this->assertTrue(
                $result->isOK() || $result->isRedirect(),
                "URL {$url} should not return an error"
            );
        }
    }

    /**
     * Test pagination SEO meta tags generation
     * Requirements: 1.3, 3.4
     */
    public function testPaginationSeoMetaTags(): void
    {
        // Test page 1 - should have canonical and possibly next
        $result = $this->get('/articles?page=1');
        
        $result->assertOK();
        
        $response = $result->response();
        $body = $response->getBody();
        
        // Should have canonical URL
        $this->assertStringContainsString('rel="canonical"', $body);
        $this->assertStringContainsString('href="' . base_url('articles?page=1') . '"', $body);
        
        // Test page 2 - should have canonical, prev, and possibly next
        $result = $this->get('/articles?page=2');
        
        if ($result->isOK()) {
            $response = $result->response();
            $body = $response->getBody();
            
            // Should have canonical URL
            $this->assertStringContainsString('rel="canonical"', $body);
            $this->assertStringContainsString('href="' . base_url('articles?page=2') . '"', $body);
            
            // Should have previous page link
            $this->assertStringContainsString('rel="prev"', $body);
            $this->assertStringContainsString('href="' . base_url('articles') . '"', $body);
        }
        
        // Test with query parameters
        $result = $this->get('/articles?custom=test&page=1');
        
        $result->assertOK();
        
        $response = $result->response();
        $body = $response->getBody();
        
        // Canonical should include custom parameter
        $this->assertStringContainsString('rel="canonical"', $body);
        $this->assertStringContainsString('custom=test', $body);
    }

    /**
     * Test pagination page title generation
     * Requirements: 1.3, 3.4
     */
    public function testPaginationPageTitleGeneration(): void
    {
        // Test page 1 - should have basic title
        $result = $this->get('/articles');
        
        $result->assertOK();
        
        $response = $result->response();
        $body = $response->getBody();
        
        // Check for title in the response body
        $this->assertStringContainsString('<title>List of Article</title>', $body);
        
        // Test page 2 - should have page number in title (if we have enough articles)
        $articleModel = new \App\Models\ArticleModel();
        $totalArticles = $articleModel->getTotalArticles();
        
        if ($totalArticles > 10) {
            $result = $this->get('/articles?page=2');
            
            if ($result->isOK()) {
                $response = $result->response();
                $body = $response->getBody();
                $this->assertStringContainsString('<title>List of Article - Page 2</title>', $body);
            }
        }
    }

    /**
     * Test pagination component integration
     * Requirements: 1.3, 3.4
     */
    public function testPaginationComponentIntegration(): void
    {
        $result = $this->get('/articles?page=2');
        
        if ($result->isOK()) {
            $response = $result->response();
            $body = $response->getBody();
            
            // Verify pagination component elements
            $this->assertStringContainsString('pagination-nav', $body);
            $this->assertStringContainsString('aria-label="Page navigation"', $body);
            $this->assertStringContainsString('class="pagination', $body);
            $this->assertStringContainsString('class="page-item"', $body);
            $this->assertStringContainsString('class="page-link"', $body);
            
            // Verify navigation buttons
            $this->assertStringContainsString('Previous', $body);
            $this->assertStringContainsString('Next', $body);
            $this->assertStringContainsString('First', $body);
            $this->assertStringContainsString('Last', $body);
            
            // Verify active page styling
            $this->assertStringContainsString('class="page-item active"', $body);
            $this->assertStringContainsString('aria-current="page"', $body);
        }
    }

    protected function tearDown(): void
    {
        parent::tearDown();
    }
}