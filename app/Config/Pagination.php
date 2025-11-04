<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Pagination extends BaseConfig
{
    /**
     * --------------------------------------------------------------------------
     * Default Pagination Settings
     * --------------------------------------------------------------------------
     *
     * Default pagination settings for public interfaces
     */
    public array $default = [
        'per_page' => 10,
        'max_per_page' => 100,
        'page_query_var' => 'page',
        'show_page_numbers' => true,
        'show_first_last' => true,
        'page_range' => 3,
        'show_ellipsis' => true,
        'prev_text' => 'Previous',
        'next_text' => 'Next',
        'first_text' => 'First',
        'last_text' => 'Last'
    ];

    /**
     * --------------------------------------------------------------------------
     * Admin Pagination Settings
     * --------------------------------------------------------------------------
     *
     * Pagination settings for admin interfaces
     */
    public array $admin = [
        'per_page' => 15,
        'max_per_page' => 50,
        'page_query_var' => 'page',
        'show_page_numbers' => true,
        'show_first_last' => true,
        'page_range' => 5,
        'show_ellipsis' => true,
        'prev_text' => 'Previous',
        'next_text' => 'Next',
        'first_text' => 'First',
        'last_text' => 'Last'
    ];

    /**
     * --------------------------------------------------------------------------
     * URL Generation Settings
     * --------------------------------------------------------------------------
     *
     * Settings for SEO-friendly URL generation
     */
    public array $url_settings = [
        'preserve_query_params' => true,
        'use_clean_urls' => true,
        'base_url_auto_detect' => true
    ];

    /**
     * --------------------------------------------------------------------------
     * Performance Settings
     * --------------------------------------------------------------------------
     *
     * Settings for pagination performance optimization
     */
    public array $performance = [
        'enable_caching' => true,
        'cache_ttl' => 300, // 5 minutes in seconds
        'cache_prefix' => 'pagination_',
        'enable_query_optimization' => true,
        'max_cache_entries' => 1000,
        'clear_cache_on_crud' => true
    ];

    /**
     * --------------------------------------------------------------------------
     * Database Optimization Settings
     * --------------------------------------------------------------------------
     *
     * Settings for database query optimization
     */
    public array $database = [
        'use_indexes' => true,
        'optimize_count_queries' => true,
        'enable_query_caching' => true,
        'batch_size_limit' => 1000, // Maximum records per page
        'force_index_hints' => false // Use database-specific index hints
    ];
}