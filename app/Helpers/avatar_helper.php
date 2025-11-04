<?php

/**
 * Avatar Helper Functions
 * 
 * Provides functions for handling user avatars including custom avatars,
 * default avatar generation, and user initials extraction.
 */

if (!function_exists('get_user_avatar')) {
    /**
     * Get user avatar URL with fallback to default avatar
     * 
     * @param object|null $user User object with name, email, and avatar properties
     * @param string $size Avatar size: 'small' (32px), 'medium' (48px), 'large' (64px)
     * @return string Avatar URL
     */
    function get_user_avatar($user = null, $size = 'medium')
    {
        $sizes = [
            'small' => 32,
            'medium' => 48,
            'large' => 64
        ];
        
        $pixelSize = $sizes[$size] ?? 48;
        
        // If no user provided, get from session
        if (!$user) {
            $session = session();
            $user = (object) [
                'name' => $session->get('name') ?? 'User',
                'email' => $session->get('email') ?? '',
                'avatar' => $session->get('avatar') ?? null
            ];
        }
        
        // Check if user has custom avatar and file exists
        if (!empty($user->avatar)) {
            $fcPath = defined('FCPATH') ? FCPATH : (defined('ROOTPATH') ? ROOTPATH . 'public/' : './public/');
            $avatarPath = $fcPath . 'uploads/avatars/' . $user->avatar;
            if (file_exists($avatarPath)) {
                // Use base_url if available, otherwise construct URL
                if (function_exists('base_url')) {
                    return base_url('uploads/avatars/' . $user->avatar);
                } else {
                    return '/uploads/avatars/' . $user->avatar;
                }
            }
        }
        
        // Generate default avatar using initials
        $initials = get_user_initials($user->name);
        return generate_default_avatar($initials, $pixelSize);
    }
}

if (!function_exists('get_user_initials')) {
    /**
     * Extract user initials from full name
     * 
     * @param string $name Full name
     * @return string User initials (max 2 characters)
     */
    function get_user_initials($name)
    {
        if (empty($name)) {
            return 'U';
        }
        
        $words = explode(' ', trim($name));
        $initials = '';
        
        foreach ($words as $word) {
            $word = trim($word);
            if (!empty($word)) {
                $initials .= strtoupper($word[0]);
                if (strlen($initials) >= 2) {
                    break;
                }
            }
        }
        
        return $initials ?: 'U';
    }
}

if (!function_exists('generate_default_avatar')) {
    /**
     * Generate default avatar URL using initials
     * 
     * @param string $initials User initials
     * @param int $size Avatar size in pixels
     * @return string Default avatar URL
     */
    function generate_default_avatar($initials, $size = 48)
    {
        // Sanitize initials for URL
        $initials = urlencode($initials);
        
        // Use UI Avatars service for generating default avatars
        $background = '007bff'; // Bootstrap primary color
        $color = 'ffffff';      // White text
        
        // Build avatar URL with error handling
        $avatarUrl = "https://ui-avatars.com/api/?name={$initials}&size={$size}&background={$background}&color={$color}&bold=true&rounded=false";
        
        return $avatarUrl;
    }
}

if (!function_exists('validate_avatar_file')) {
    /**
     * Validate if avatar file exists and is accessible
     * 
     * @param string $filename Avatar filename
     * @return bool True if file exists and is valid
     */
    function validate_avatar_file($filename)
    {
        if (empty($filename)) {
            return false;
        }
        
        $fcPath = defined('FCPATH') ? FCPATH : (defined('ROOTPATH') ? ROOTPATH . 'public/' : './public/');
        $avatarPath = $fcPath . 'uploads/avatars/' . $filename;
        
        // Check if file exists
        if (!file_exists($avatarPath)) {
            return false;
        }
        
        // Check if file is readable
        if (!is_readable($avatarPath)) {
            return false;
        }
        
        // Basic file type validation
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        return in_array($extension, $allowedExtensions);
    }
}

if (!function_exists('get_avatar_sizes')) {
    /**
     * Get available avatar sizes
     * 
     * @return array Available sizes with pixel values
     */
    function get_avatar_sizes()
    {
        return [
            'small' => 32,
            'medium' => 48,
            'large' => 64
        ];
    }
}