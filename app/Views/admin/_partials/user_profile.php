<?php
/**
 * User Profile Component
 * 
 * Displays the current logged-in user's profile information including
 * avatar, name, and email in the admin interface.
 * 
 * Requirements: 1.1, 1.2, 1.4, 2.1, 2.2
 */

// Get current user data from session
$session = session();
$sessionName = $session->get('name');
$userEmail = $session->get('email') ?? '';
$userId = $session->get('user_id') ?? 0;

// Use default "User" only if we have some session data but no name
$userName = $sessionName ?? ($userEmail ? 'User' : '');

// Ensure we have valid user data
// Hide component if no session data at all (no name and no email)
if (empty($sessionName) && empty($userEmail)) {
    // If no user data in session, don't display the component
    return;
}
?>

<div class="user-profile" tabindex="0" role="button" aria-label="User profile: <?= esc($userName) ?>" data-user-id="<?= $userId ?>" data-user-avatar="<?= esc($userAvatar ?? '') ?>">
    <div class="user-avatar">
        <?php 
        // Get fresh user data from database instead of session
        $db = \Config\Database::connect();
        $userQuery = $db->query("SELECT avatar FROM users WHERE id = ?", [$userId]);
        $dbUser = $userQuery->getRow();
        
        // Always use database avatar first, then fallback to session
        $userAvatar = null;
        if ($dbUser && !empty($dbUser->avatar)) {
            $userAvatar = $dbUser->avatar;
            error_log('Sidebar Avatar Debug - Using database avatar: ' . $userAvatar);
            
            // Update session with fresh database data
            if ($dbUser->avatar !== $session->get('avatar')) {
                $session->set('avatar', $dbUser->avatar);
                error_log('Sidebar Avatar Debug - Updated session avatar to: ' . $dbUser->avatar);
            }
        } else {
            $userAvatar = $session->get('avatar');
            error_log('Sidebar Avatar Debug - Using session avatar: ' . ($userAvatar ?? 'NULL'));
        }
        
        // Debug logging
        error_log('Sidebar Avatar Debug - User ID: ' . $userId);
        error_log('Sidebar Avatar Debug - DB Avatar: ' . ($dbUser ? $dbUser->avatar : 'NULL'));
        error_log('Sidebar Avatar Debug - Session Avatar: ' . ($session->get('avatar') ?? 'NULL'));
        error_log('Sidebar Avatar Debug - Final Avatar: ' . ($userAvatar ?? 'NULL'));
        
        // Create user object with fresh data
        $currentUser = (object) [
            'name' => $session->get('name') ?? 'User',
            'email' => $session->get('email') ?? '',
            'avatar' => $userAvatar
        ];
        
        // Generate URLs
        if (!empty($userAvatar)) {
            $avatarPath = FCPATH . 'uploads/avatars/' . $userAvatar;
            if (file_exists($avatarPath)) {
                $avatarUrl = base_url('uploads/avatars/' . $userAvatar) . '?v=' . filemtime($avatarPath);
            } else {
                $avatarUrl = generate_default_avatar(get_user_initials($userName));
            }
        } else {
            $avatarUrl = generate_default_avatar(get_user_initials($userName));
        }
        
        $fallbackUrl = generate_default_avatar(get_user_initials($userName));
        
        error_log('Sidebar Avatar Debug - Final URL: ' . $avatarUrl);
        ?>
        <img src="<?= esc($avatarUrl) ?>" 
             alt="<?= esc($userName) ?> Avatar" 
             class="avatar-img avatar-medium"
             id="sidebar-avatar"
             onerror="this.src='<?= esc($fallbackUrl) ?>'; this.classList.add('error');"
             loading="lazy">
    </div>
    <div class="user-info">
        <div class="user-name" title="<?= esc($userName) ?>">
            <?= esc($userName) ?>
        </div>
        <?php if (!empty($userEmail)): ?>
        <div class="user-email" title="<?= esc($userEmail) ?>">
            <?= esc($userEmail) ?>
        </div>
        <?php endif; ?>
    </div>
</div>