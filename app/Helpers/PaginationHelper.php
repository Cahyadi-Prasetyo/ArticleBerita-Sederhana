<?php

namespace App\Helpers;

/**
 * Pagination Helper Class
 * 
 * Provides utility methods for pagination URL generation and page range calculation
 */
class PaginationHelper
{
    /**
     * Generate pagination links with SEO-friendly URLs
     * 
     * @param array $pagination Pagination metadata
     * @param string $baseUrl Base URL for pagination
     * @param array $queryParams Existing query parameters to maintain
     * @return array Array of pagination link data
     */
    public static function generateLinks($pagination, $baseUrl, $queryParams = [])
    {
        $links = [];
        $currentPage = $pagination['current_page'];
        $totalPages = $pagination['total_pages'];
        
        // First page link
        if ($currentPage > 1) {
            $links['first'] = [
                'url' => self::buildUrl($baseUrl, 1, $queryParams),
                'label' => 'First',
                'active' => false
            ];
        }
        
        // Previous page link
        if ($pagination['has_previous']) {
            $links['previous'] = [
                'url' => self::buildUrl($baseUrl, $pagination['previous_page'], $queryParams),
                'label' => 'Previous',
                'active' => false
            ];
        }
        
        // Page number links
        $pageRange = self::getPageRange($currentPage, $totalPages);
        $links['pages'] = [];
        
        foreach ($pageRange as $page) {
            if ($page === '...') {
                $links['pages'][] = [
                    'url' => null,
                    'label' => '...',
                    'active' => false,
                    'disabled' => true
                ];
            } else {
                $links['pages'][] = [
                    'url' => self::buildUrl($baseUrl, $page, $queryParams),
                    'label' => (string)$page,
                    'active' => $page == $currentPage
                ];
            }
        }
        
        // Next page link
        if ($pagination['has_next']) {
            $links['next'] = [
                'url' => self::buildUrl($baseUrl, $pagination['next_page'], $queryParams),
                'label' => 'Next',
                'active' => false
            ];
        }
        
        // Last page link
        if ($currentPage < $totalPages) {
            $links['last'] = [
                'url' => self::buildUrl($baseUrl, $totalPages, $queryParams),
                'label' => 'Last',
                'active' => false
            ];
        }
        
        return $links;
    }
    
    /**
     * Calculate page range to display with ellipsis handling
     * 
     * @param int $currentPage Current active page
     * @param int $totalPages Total number of pages
     * @param int $range Number of pages to show around current page
     * @return array Array of page numbers and ellipsis
     */
    public static function getPageRange($currentPage, $totalPages, $range = 3)
    {
        if ($totalPages <= 7) {
            // Show all pages if total is 7 or less
            return range(1, $totalPages);
        }
        
        $pages = [];
        
        // Always show first page
        $pages[] = 1;
        
        // Calculate start and end of middle range
        $start = max(2, $currentPage - $range);
        $end = min($totalPages - 1, $currentPage + $range);
        
        // Add ellipsis after first page if needed
        if ($start > 2) {
            $pages[] = '...';
        }
        
        // Add middle range pages
        for ($i = $start; $i <= $end; $i++) {
            if ($i != 1 && $i != $totalPages) {
                $pages[] = $i;
            }
        }
        
        // Add ellipsis before last page if needed
        if ($end < $totalPages - 1) {
            $pages[] = '...';
        }
        
        // Always show last page (if different from first)
        if ($totalPages > 1) {
            $pages[] = $totalPages;
        }
        
        return $pages;
    }
    
    /**
     * Build SEO-friendly URL with query parameters
     * 
     * @param string $baseUrl Base URL
     * @param int $page Page number
     * @param array $queryParams Additional query parameters
     * @return string Complete URL
     */
    public static function buildUrl($baseUrl, $page, $queryParams = [])
    {
        // Remove page parameter from query params to avoid duplication
        $params = $queryParams;
        unset($params['page']);
        
        // For page 1, don't include page parameter for cleaner URLs
        if ($page > 1) {
            $params['page'] = $page;
        }
        
        // Build clean URL
        if (!empty($params)) {
            return $baseUrl . '?' . http_build_query($params);
        }
        
        return $baseUrl;
    }
    
    /**
     * Generate canonical URL for SEO
     * 
     * @param string $baseUrl Base URL
     * @param int $currentPage Current page number
     * @param array $queryParams Query parameters
     * @return string Canonical URL
     */
    public static function getCanonicalUrl($baseUrl, $currentPage, $queryParams = [])
    {
        // For canonical URL, we want to include the page parameter even for page 1
        // to be explicit about pagination state
        $params = $queryParams;
        $params['page'] = $currentPage;
        
        return $baseUrl . '?' . http_build_query($params);
    }
    
    /**
     * Generate meta tags for pagination SEO
     * 
     * @param array $pagination Pagination metadata
     * @param string $baseUrl Base URL
     * @param array $queryParams Query parameters
     * @return array Array of meta tag data
     */
    public static function getMetaTags($pagination, $baseUrl, $queryParams = [])
    {
        $metaTags = [];
        
        // Canonical URL
        $metaTags['canonical'] = self::getCanonicalUrl($baseUrl, $pagination['current_page'], $queryParams);
        
        // Previous page rel link
        if ($pagination['has_previous']) {
            $metaTags['prev'] = self::buildUrl($baseUrl, $pagination['previous_page'], $queryParams);
        }
        
        // Next page rel link
        if ($pagination['has_next']) {
            $metaTags['next'] = self::buildUrl($baseUrl, $pagination['next_page'], $queryParams);
        }
        
        return $metaTags;
    }
}