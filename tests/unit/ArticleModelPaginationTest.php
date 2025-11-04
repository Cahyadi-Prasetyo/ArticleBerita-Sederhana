<?php

use CodeIgniter\Test\CIUnitTestCase;
use CodeIgniter\Test\DatabaseTestTrait;
use Tests\Support\Database\Seeds\ArticleSeeder;
use App\Models\ArticleModel;

/**
 * @internal
 */
final class ArticleModelPaginationTest extends CIUnitTestCase
{
    use DatabaseTestTrait;

    protected $seed = ArticleSeeder::class;
    protected $migrate = true;
    protected $migrateOnce = false;
    protected $refresh = true;

    private ArticleModel $model;

    protected function setUp(): void
    {
        parent::setUp();
        $this->model = new ArticleModel();
    }

    // Tests for getPaginatedArticles() method
    public function testGetPaginatedArticlesFirstPage(): void
    {
        $result = $this->model->getPaginatedArticles(1, 5);
        
        $this->assertCount(5, $result);
        // Should return most recent articles first (ordered by created_at DESC)
        $this->assertEquals('Twelfth Published Article', $result[0]->title);
        $this->assertEquals('Eighth Published Article', $result[4]->title);
    }

    public function testGetPaginatedArticlesSecondPage(): void
    {
        $result = $this->model->getPaginatedArticles(2, 5);
        
        $this->assertCount(5, $result);
        // Should return next 5 articles
        $this->assertEquals('Seventh Published Article', $result[0]->title);
        $this->assertEquals('Third Published Article', $result[4]->title);
    }

    public function testGetPaginatedArticlesThirdPage(): void
    {
        $result = $this->model->getPaginatedArticles(3, 5);
        
        $this->assertCount(2, $result); // Only 2 remaining published articles
        $this->assertEquals('Second Published Article', $result[0]->title);
        $this->assertEquals('First Published Article', $result[1]->title);
    }

    public function testGetPaginatedArticlesWithDifferentPageSizes(): void
    {
        // Test with page size 3
        $result = $this->model->getPaginatedArticles(1, 3);
        $this->assertCount(3, $result);
        
        // Test with page size 10
        $result = $this->model->getPaginatedArticles(1, 10);
        $this->assertCount(10, $result);
        
        // Test with page size larger than total records
        $result = $this->model->getPaginatedArticles(1, 20);
        $this->assertCount(12, $result); // Only 12 published articles exist
    }

    public function testGetPaginatedArticlesWithConditions(): void
    {
        // Test with draft condition to include draft articles
        $result = $this->model->getPaginatedArticles(1, 5, ['draft' => 'true']);
        $this->assertCount(2, $result); // Only 2 draft articles exist
        
        // Test with specific title condition (using LIKE)
        $result = $this->model->getPaginatedArticles(1, 5, ['title' => 'First Published Article']);
        $this->assertCount(1, $result);
        $this->assertEquals('First Published Article', $result[0]->title);
    }

    public function testGetPaginatedArticlesEdgeCases(): void
    {
        // Test with page 0 (should default to page 1)
        $result = $this->model->getPaginatedArticles(0, 5);
        $this->assertCount(5, $result);
        
        // Test with negative page (should default to page 1)
        $result = $this->model->getPaginatedArticles(-1, 5);
        $this->assertCount(5, $result);
        
        // Test with page size 0 (should default to 1)
        $result = $this->model->getPaginatedArticles(1, 0);
        $this->assertCount(1, $result);
        
        // Test with negative page size (should default to 1)
        $result = $this->model->getPaginatedArticles(1, -5);
        $this->assertCount(1, $result);
    }

    public function testGetPaginatedArticlesExcludesDraftsByDefault(): void
    {
        $result = $this->model->getPaginatedArticles(1, 20);
        
        // Should only return published articles (12 total)
        $this->assertCount(12, $result);
        
        // Verify no draft articles are included
        foreach ($result as $article) {
            $this->assertEquals('false', $article->draft);
        }
    }

    // Tests for getTotalArticles() method
    public function testGetTotalArticlesDefault(): void
    {
        $total = $this->model->getTotalArticles();
        
        // Should return only published articles count (12)
        $this->assertEquals(12, $total);
    }

    public function testGetTotalArticlesWithConditions(): void
    {
        // Test with draft condition
        $total = $this->model->getTotalArticles(['draft' => 'true']);
        $this->assertEquals(2, $total); // 2 draft articles
        
        // Test with published condition (explicit)
        $total = $this->model->getTotalArticles(['draft' => 'false']);
        $this->assertEquals(12, $total); // 12 published articles
        
        // Test with specific title condition
        $total = $this->model->getTotalArticles(['title' => 'First Published Article']);
        $this->assertEquals(1, $total);
    }

    public function testGetTotalArticlesWithArrayConditions(): void
    {
        // Test with array condition (whereIn)
        $total = $this->model->getTotalArticles(['draft' => ['true', 'false']]);
        $this->assertEquals(14, $total); // All articles (12 published + 2 draft)
    }

    // Tests for getPaginationData() method
    public function testGetPaginationDataStructure(): void
    {
        $result = $this->model->getPaginationData(1, 5);
        
        // Verify structure
        $this->assertArrayHasKey('data', $result);
        $this->assertArrayHasKey('pagination', $result);
        
        // Verify data
        $this->assertCount(5, $result['data']);
        
        // Verify pagination metadata
        $pagination = $result['pagination'];
        $this->assertArrayHasKey('current_page', $pagination);
        $this->assertArrayHasKey('per_page', $pagination);
        $this->assertArrayHasKey('total_records', $pagination);
        $this->assertArrayHasKey('total_pages', $pagination);
        $this->assertArrayHasKey('has_previous', $pagination);
        $this->assertArrayHasKey('has_next', $pagination);
        $this->assertArrayHasKey('previous_page', $pagination);
        $this->assertArrayHasKey('next_page', $pagination);
        $this->assertArrayHasKey('first_page', $pagination);
        $this->assertArrayHasKey('last_page', $pagination);
    }

    public function testGetPaginationDataFirstPage(): void
    {
        $result = $this->model->getPaginationData(1, 5);
        $pagination = $result['pagination'];
        
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(5, $pagination['per_page']);
        $this->assertEquals(12, $pagination['total_records']);
        $this->assertEquals(3, $pagination['total_pages']); // ceil(12/5) = 3
        $this->assertFalse($pagination['has_previous']);
        $this->assertTrue($pagination['has_next']);
        $this->assertNull($pagination['previous_page']);
        $this->assertEquals(2, $pagination['next_page']);
        $this->assertEquals(1, $pagination['first_page']);
        $this->assertEquals(3, $pagination['last_page']);
    }

    public function testGetPaginationDataMiddlePage(): void
    {
        $result = $this->model->getPaginationData(2, 5);
        $pagination = $result['pagination'];
        
        $this->assertEquals(2, $pagination['current_page']);
        $this->assertTrue($pagination['has_previous']);
        $this->assertTrue($pagination['has_next']);
        $this->assertEquals(1, $pagination['previous_page']);
        $this->assertEquals(3, $pagination['next_page']);
    }

    public function testGetPaginationDataLastPage(): void
    {
        $result = $this->model->getPaginationData(3, 5);
        $pagination = $result['pagination'];
        
        $this->assertEquals(3, $pagination['current_page']);
        $this->assertTrue($pagination['has_previous']);
        $this->assertFalse($pagination['has_next']);
        $this->assertEquals(2, $pagination['previous_page']);
        $this->assertNull($pagination['next_page']);
    }

    public function testGetPaginationDataSinglePage(): void
    {
        // Test with page size larger than total records
        $result = $this->model->getPaginationData(1, 20);
        $pagination = $result['pagination'];
        
        $this->assertEquals(1, $pagination['current_page']);
        $this->assertEquals(1, $pagination['total_pages']);
        $this->assertFalse($pagination['has_previous']);
        $this->assertFalse($pagination['has_next']);
        $this->assertNull($pagination['previous_page']);
        $this->assertNull($pagination['next_page']);
    }

    public function testGetPaginationDataPageExceedsTotalPages(): void
    {
        // Request page 10 when only 3 pages exist
        $result = $this->model->getPaginationData(10, 5);
        $pagination = $result['pagination'];
        
        // Should be clamped to last page (3)
        $this->assertEquals(3, $pagination['current_page']);
        $this->assertEquals(3, $pagination['total_pages']);
        $this->assertTrue($pagination['has_previous']);
        $this->assertFalse($pagination['has_next']);
    }

    public function testGetPaginationDataWithConditions(): void
    {
        // Test with draft articles
        $result = $this->model->getPaginationData(1, 5, ['draft' => 'true']);
        $pagination = $result['pagination'];
        
        $this->assertEquals(2, $pagination['total_records']); // 2 draft articles
        $this->assertEquals(1, $pagination['total_pages']); // ceil(2/5) = 1
        $this->assertCount(2, $result['data']);
    }

    public function testGetPaginationDataEmptyResults(): void
    {
        // Test with condition that returns no results
        $result = $this->model->getPaginationData(1, 5, ['title' => 'Non-existent Article']);
        $pagination = $result['pagination'];
        
        $this->assertEquals(0, $pagination['total_records']);
        $this->assertEquals(0, $pagination['total_pages']);
        $this->assertEmpty($result['data']);
        $this->assertFalse($pagination['has_previous']);
        $this->assertFalse($pagination['has_next']);
    }

    // Tests for LIMIT/OFFSET calculations
    public function testLimitOffsetCalculations(): void
    {
        // Page 1, per_page 5: OFFSET 0, LIMIT 5
        $result = $this->model->getPaginatedArticles(1, 5);
        $this->assertCount(5, $result);
        
        // Page 2, per_page 5: OFFSET 5, LIMIT 5
        $result = $this->model->getPaginatedArticles(2, 5);
        $this->assertCount(5, $result);
        
        // Page 3, per_page 5: OFFSET 10, LIMIT 5 (but only 2 records available)
        $result = $this->model->getPaginatedArticles(3, 5);
        $this->assertCount(2, $result);
        
        // Page 4, per_page 5: OFFSET 15, LIMIT 5 (no records available)
        $result = $this->model->getPaginatedArticles(4, 5);
        $this->assertCount(0, $result);
    }

    public function testConsistentOrderingAcrossPages(): void
    {
        // Get all articles in pages
        $page1 = $this->model->getPaginatedArticles(1, 5);
        $page2 = $this->model->getPaginatedArticles(2, 5);
        $page3 = $this->model->getPaginatedArticles(3, 5);
        
        // Combine all results
        $allPaginated = array_merge($page1, $page2, $page3);
        
        // Get all articles at once
        $allAtOnce = $this->model->getPaginatedArticles(1, 20);
        
        // Should have same order and count
        $this->assertCount(12, $allPaginated);
        $this->assertCount(12, $allAtOnce);
        
        // Verify same ordering
        for ($i = 0; $i < 12; $i++) {
            $this->assertEquals($allAtOnce[$i]->id, $allPaginated[$i]->id);
        }
    }
}