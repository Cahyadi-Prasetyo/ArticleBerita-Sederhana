<?php

namespace App\Libraries;

/**
 * PaginationHelper Library
 * 
 * Provides reusable pagination functionality including URL generation
 * and page range calculations for both public and admin interfaces.
 * 
 * This class implements the requirements for SEO-friendly URLs,
 * page range calculation, and metadata generation.
 */
class PaginationHelper
{
    /**
     * Generate pagination links with proper URL structure
     *
     * @param array $pagination Pagination metadata
     * @param string $baseUrl Base URL for pagination links
     * @param array $queryParams Additional query parameters to preserve
     * @return array Array of pagination links
     */
    public function generateLinks(array $pagination, string $baseUrl, array $queryParams = []): array
    {
        $links = [];
        
        // First page link
        if (($pagination['show_first_last'] ?? true) && ($pagination['has_previous'] ?? false)) {
            $links['first'] = [
                'url' => $this->buildUrl($baseUrl, 1, $queryParams),
                'text' => $pagination['first_text'] ?? 'First',
                'active' => false,
                'disabled' => false
            ];
        }
        
        // Previous page link
        if ($pagination['has_previous'] ?? false) {
            $links['previous'] = [
                'url' => $this->buildUrl($baseUrl, $pagination['previous_page'] ?? 1, $queryParams),
                'text' => $pagination['prev_text'] ?? 'Previous',
                'active' => false,
                'disabled' => false
            ];
        } else {
            $links['previous'] = [
                'url' => '#',
                'text' => $pagination['prev_text'] ?? 'Previous',
                'active' => false,
                'disabled' => true
            ];
        }
        
        // Page number links
        if ($pagination['show_page_numbers'] ?? true) {
            $pageRange = $this->getPageRange(
                $pagination['current_page'] ?? 1, 
                $pagination['total_pages'] ?? 1, 
                $pagination['page_range'] ?? 3
            );
            
            $links['pages'] = [];
            
            // Show ellipsis before if needed
            if ($pageRange['show_start_ellipsis']) {
                $links['pages'][] = [
                    'url' => '#',
                    'text' => '...',
                    'active' => false,
                    'disabled' => true,
                    'ellipsis' => true
                ];
            }
            
            // Generate page number links
            for ($i = $pageRange['start']; $i <= $pageRange['end']; $i++) {
                $links['pages'][] = [
                    'url' => $this->buildUrl($baseUrl, $i, $queryParams),
                    'text' => (string)$i,
                    'active' => ($i == ($pagination['current_page'] ?? 1)),
                    'disabled' => false,
                    'page_number' => $i
                ];
            }
            
            // Show ellipsis after if needed
            if ($pageRange['show_end_ellipsis']) {
                $links['pages'][] = [
                    'url' => '#',
                    'text' => '...',
                    'active' => false,
                    'disabled' => true,
                    'ellipsis' => true
                ];
            }
        }
        
        // Next page link
        if ($pagination['has_next'] ?? false) {
            $links['next'] = [
                'url' => $this->buildUrl($baseUrl, $pagination['next_page'] ?? 1, $queryParams),
                'text' => $pagination['next_text'] ?? 'Next',
                'active' => false,
                'disabled' => false
            ];
        } else {
            $links['next'] = [
                'url' => '#',
                'text' => $pagination['next_text'] ?? 'Next',
                'active' => false,
                'disabled' => true
            ];
        }
        
        // Last page link
        if (($pagination['show_first_last'] ?? true) && ($pagination['has_next'] ?? false)) {
            $links['last'] = [
                'url' => $this->buildUrl($baseUrl, $pagination['last_page'] ?? 1, $queryParams),
                'text' => $pagination['last_text'] ?? 'Last',
                'active' => false,
                'disabled' => false
            ];
        }
        
        return $links;
    }
    
    /**
     * Calculate page range for pagination display
     *
     * @param int $currentPage Current active page
     * @param int $totalPages Total number of pages
     * @param int $range Number of pages to show around current page
     * @return array Page range information
     */
    public function getPageRange(int $currentPage, int $totalPages, int $range = 3): array
    {
        // Ensure minimum values
        $currentPage = max(1, $currentPage);
        $totalPages = max(1, $totalPages);
        $range = max(1, $range);
        
        // Calculate start and end pages
        $start = max(1, $currentPage - $range);
        $end = min($totalPages, $currentPage + $range);
        
        // Adjust range if we're near the beginning or end
        if ($end - $start < ($range * 2)) {
            if ($start == 1) {
                $end = min($totalPages, $start + ($range * 2));
            } elseif ($end == $totalPages) {
                $start = max(1, $end - ($range * 2));
            }
        }
        
        return [
            'start' => $start,
            'end' => $end,
            'show_start_ellipsis' => $start > 1,
            'show_end_ellipsis' => $end < $totalPages,
            'total_visible' => $end - $start + 1
        ];
    }
    
    /**
     * Build SEO-friendly URL with page parameter
     *
     * @param string $baseUrl Base URL
     * @param int $page Page number
     * @param array $queryParams Additional query parameters
     * @return string Complete URL
     */
    public function buildUrl(string $baseUrl, int $page, array $queryParams = []): string
    {
        // Remove trailing slash from base URL
        $baseUrl = rtrim($baseUrl, '/');
        
        // Add page parameter to query params
        if ($page > 1) {
            $queryParams['page'] = $page;
        } else {
            // Remove page parameter for first page (cleaner URLs)
            unset($queryParams['page']);
        }
        
        // Build query string
        if (!empty($queryParams)) {
            $queryString = http_build_query($queryParams);
            return $baseUrl . '?' . $queryString;
        }
        
        return $baseUrl;
    }
    
    /**
     * Generate pagination metadata
     *
     * @param int $currentPage Current page number
     * @param int $perPage Items per page
     * @param int $totalRecords Total number of records
     * @param array $config Pagination configuration
     * @return array Pagination metadata
     */
    public function generateMetadata(int $currentPage, int $perPage, int $totalRecords, array $config = []): array
    {
        // Ensure minimum values
        $currentPage = max(1, $currentPage);
        $perPage = max(1, $perPage);
        $totalRecords = max(0, $totalRecords);
        
        // Calculate total pages
        $totalPages = $totalRecords > 0 ? (int)ceil($totalRecords / $perPage) : 1;
        
        // Ensure current page doesn't exceed total pages
        $currentPage = min($currentPage, $totalPages);
        
        // Calculate offset for database queries
        $offset = ($currentPage - 1) * $perPage;
        
        // Calculate record range for current page
        $startRecord = $totalRecords > 0 ? $offset + 1 : 0;
        $endRecord = min($offset + $perPage, $totalRecords);
        
        // Merge with default configuration
        $defaultConfig = [
            'show_page_numbers' => true,
            'show_first_last' => true,
            'page_range' => 3,
            'prev_text' => 'Previous',
            'next_text' => 'Next',
            'first_text' => 'First',
            'last_text' => 'Last'
        ];
        
        $config = array_merge($defaultConfig, $config);
        
        return array_merge($config, [
            'current_page' => $currentPage,
            'per_page' => $perPage,
            'total_records' => $totalRecords,
            'total_pages' => $totalPages,
            'has_previous' => $currentPage > 1,
            'has_next' => $currentPage < $totalPages,
            'previous_page' => max(1, $currentPage - 1),
            'next_page' => min($totalPages, $currentPage + 1),
            'first_page' => 1,
            'last_page' => $totalPages,
            'offset' => $offset,
            'start_record' => $startRecord,
            'end_record' => $endRecord
        ]);
    }
    
    /**
     * Validate and sanitize page parameter
     *
     * @param mixed $page Page parameter from request
     * @param int $totalPages Total number of pages
     * @return int Valid page number
     */
    public function validatePage($page, int $totalPages = 1): int
    {
        // Convert to integer
        $page = (int)$page;
        
        // Ensure page is within valid range
        if ($page < 1) {
            return 1;
        }
        
        if ($page > $totalPages && $totalPages > 0) {
            return $totalPages;
        }
        
        return $page;
    }
    
    /**
     * Get pagination configuration for specified context
     *
     * @param string $context Configuration context ('default' or 'admin')
     * @return array Configuration array
     */
    public function getConfig(string $context = 'default'): array
    {
        $config = config('Pagination');
        
        if ($context === 'admin' && isset($config->admin)) {
            return $config->admin;
        }
        
        return $config->default ?? [];
    }
}