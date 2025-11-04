<?php

namespace Tests\Unit;

use CodeIgniter\Test\CIUnitTestCase;

class PaginationAccessibilityTest extends CIUnitTestCase
{
    public function testPaginationHasProperAriaLabels()
    {
        // Mock pagination data
        $pagination = [
            'current_page' => 2,
            'per_page' => 10,
            'total_records' => 150,
            'total_pages' => 15,
            'has_previous' => true,
            'has_next' => true,
            'previous_page' => 1,
            'next_page' => 3,
            'first_page' => 1,
            'last_page' => 15
        ];
        
        $base_url = '/articles';
        $query_params = [];
        
        // Generate pagination component
        $output = view('components/pagination', [
            'pagination' => $pagination,
            'base_url' => $base_url,
            'query_params' => $query_params
        ]);
        
        // Test for proper ARIA labels
        $this->assertStringContainsString('aria-label="Page navigation"', $output);
        $this->assertStringContainsString('role="navigation"', $output);
        $this->assertStringContainsString('role="list"', $output);
        $this->assertStringContainsString('role="listitem"', $output);
        $this->assertStringContainsString('aria-current="page"', $output);
        $this->assertStringContainsString('aria-disabled="true"', $output);
        $this->assertStringContainsString('aria-live="polite"', $output);
    }
    
    public function testPaginationHasKeyboardNavigation()
    {
        $pagination = [
            'current_page' => 2,
            'per_page' => 10,
            'total_records' => 150,
            'total_pages' => 15,
            'has_previous' => true,
            'has_next' => true,
            'previous_page' => 1,
            'next_page' => 3
        ];
        
        $output = view('components/pagination', [
            'pagination' => $pagination,
            'base_url' => '/articles',
            'query_params' => []
        ]);
        
        // Test for keyboard navigation attributes
        $this->assertStringContainsString('tabindex="0"', $output);
        $this->assertStringContainsString('tabindex="-1"', $output);
        $this->assertStringContainsString('data-page=', $output);
    }
    
    public function testAdminPaginationHasAccessibilityFeatures()
    {
        $pagination = [
            'current_page' => 1,
            'per_page' => 15,
            'total_records' => 100,
            'total_pages' => 7,
            'has_previous' => false,
            'has_next' => true,
            'next_page' => 2
        ];
        
        $output = view('components/admin_pagination', [
            'pagination' => $pagination,
            'base_url' => '/admin/posts',
            'query_params' => []
        ]);
        
        // Test for admin-specific accessibility features
        $this->assertStringContainsString('aria-label="Admin pagination"', $output);
        $this->assertStringContainsString('aria-label="Select number of entries to show per page"', $output);
        $this->assertStringContainsString('aria-label="Jump to page number"', $output);
        $this->assertStringContainsString('aria-describedby="page-jump-help"', $output);
        $this->assertStringContainsString('id="admin-pagination-status"', $output);
    }
    
    public function testScreenReaderContent()
    {
        $pagination = [
            'current_page' => 3,
            'per_page' => 10,
            'total_records' => 50,
            'total_pages' => 5,
            'has_previous' => true,
            'has_next' => true,
            'previous_page' => 2,
            'next_page' => 4
        ];
        
        $output = view('components/pagination', [
            'pagination' => $pagination,
            'base_url' => '/articles',
            'query_params' => []
        ]);
        
        // Test for screen reader only content
        $this->assertStringContainsString('class="sr-only"', $output);
        $this->assertStringContainsString('aria-hidden="true"', $output);
        $this->assertStringContainsString('Page 3 of 5. Use arrow keys to navigate', $output);
    }
    
    public function testDisabledStateAccessibility()
    {
        $pagination = [
            'current_page' => 1,
            'per_page' => 10,
            'total_records' => 5,
            'total_pages' => 1,
            'has_previous' => false,
            'has_next' => false
        ];
        
        $output = view('components/pagination', [
            'pagination' => $pagination,
            'base_url' => '/articles',
            'query_params' => []
        ]);
        
        // Should not render pagination navigation for single page
        // The component returns early if total_pages <= 1
        $this->assertStringNotContainsString('<nav', $output);
        $this->assertStringNotContainsString('class="pagination', $output);
    }
}