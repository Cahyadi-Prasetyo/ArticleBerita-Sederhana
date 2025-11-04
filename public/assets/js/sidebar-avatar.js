/**
 * Sidebar Avatar Management
 * Handles avatar updates across the admin interface
 */

// Function to update sidebar avatar
function updateSidebarAvatar(avatarUrl = null) {
    console.log('updateSidebarAvatar called with:', avatarUrl);
    const sidebarAvatar = document.getElementById('sidebar-avatar');
    if (!sidebarAvatar) {
        console.log('Sidebar avatar element not found');
        return;
    }
    
    if (avatarUrl) {
        // Update with new avatar
        console.log('Setting sidebar avatar to:', avatarUrl);
        sidebarAvatar.src = avatarUrl;
        sidebarAvatar.classList.remove('error');
        sidebarAvatar.classList.remove('default-avatar');
    } else {
        // Generate default avatar using user initials
        const userName = sidebarAvatar.alt.replace(' Avatar', '');
        const initials = getUserInitials(userName);
        const defaultAvatarUrl = generateDefaultAvatar(initials, 48);
        console.log('Setting sidebar avatar to default:', defaultAvatarUrl);
        sidebarAvatar.src = defaultAvatarUrl;
        sidebarAvatar.classList.add('default-avatar');
    }
}

// Function to force refresh sidebar avatar using data attributes
function forceRefreshSidebarAvatar() {
    console.log('Force refreshing sidebar avatar...');
    
    // Get user data from data attributes
    const userProfile = document.querySelector('.user-profile[data-user-id]');
    if (userProfile) {
        const userId = userProfile.getAttribute('data-user-id');
        const userAvatar = userProfile.getAttribute('data-user-avatar');
        
        console.log('Found user profile element');
        console.log('User ID:', userId);
        console.log('User Avatar attribute:', "'" + userAvatar + "'");
        console.log('Avatar length:', userAvatar ? userAvatar.length : 0);
        console.log('Avatar trimmed:', userAvatar ? "'" + userAvatar.trim() + "'" : 'null');
        console.log('Is avatar empty?', !userAvatar || userAvatar.trim() === '');
        
        if (userAvatar && userAvatar.trim() !== '' && userAvatar !== 'null') {
            // User has uploaded avatar, construct full URL
            let avatarUrl;
            if (userAvatar.startsWith('http')) {
                avatarUrl = userAvatar;
            } else {
                // Construct full URL - simple and reliable approach
                const baseUrl = window.location.origin + '/my-project';
                avatarUrl = baseUrl + '/uploads/avatars/' + userAvatar + '?v=' + Date.now();
            }
            console.log('Constructed avatar URL:', avatarUrl);
            updateSidebarAvatar(avatarUrl);
        } else {
            console.log('No uploaded avatar found, reasons:');
            console.log('- userAvatar is falsy:', !userAvatar);
            console.log('- userAvatar is empty after trim:', userAvatar && userAvatar.trim() === '');
            console.log('- userAvatar is "null" string:', userAvatar === 'null');
            updateSidebarAvatar(null);
        }
    } else {
        console.log('No user profile data found');
        // Fallback: check settings page for avatar
        const settingsAvatar = document.querySelector('.card img[alt*="Avatar"]');
        if (settingsAvatar && !settingsAvatar.src.includes('ui-avatars.com')) {
            console.log('Found avatar in settings page:', settingsAvatar.src);
            updateSidebarAvatar(settingsAvatar.src);
        } else {
            updateSidebarAvatar(null);
        }
    }
}

// Function to get user initials
function getUserInitials(name) {
    if (!name || name === 'Avatar') return 'U';
    
    const words = name.trim().split(' ');
    let initials = '';
    
    for (const word of words) {
        if (word.trim()) {
            initials += word.trim()[0].toUpperCase();
            if (initials.length >= 2) break;
        }
    }
    
    return initials || 'U';
}

// Function to generate default avatar URL
function generateDefaultAvatar(initials, size = 48) {
    const background = '007bff';
    const color = 'ffffff';
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(initials)}&size=${size}&background=${background}&color=${color}&bold=true&rounded=false`;
}

// Function to refresh sidebar avatar from server
function refreshSidebarAvatar() {
    // Make AJAX request to get current user avatar
    fetch('/auth/check', {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.logged_in && data.avatar_url) {
            updateSidebarAvatar(data.avatar_url);
        } else {
            updateSidebarAvatar(null); // Use default avatar
        }
    })
    .catch(error => {
        console.log('Could not refresh sidebar avatar:', error);
        // Fallback to default avatar
        updateSidebarAvatar(null);
    });
}

// Listen for avatar update events
document.addEventListener('avatarUpdated', function(event) {
    console.log('Avatar updated event received:', event.detail);
    updateSidebarAvatar(event.detail.avatarUrl);
});

document.addEventListener('avatarRemoved', function() {
    console.log('Avatar removed event received');
    updateSidebarAvatar(null);
});

// Auto-refresh sidebar avatar when page loads
document.addEventListener('DOMContentLoaded', function() {
    console.log('DOM loaded, checking sidebar avatar...');
    
    // Small delay to ensure DOM is fully loaded
    setTimeout(() => {
        const sidebarAvatar = document.getElementById('sidebar-avatar');
        if (sidebarAvatar) {
            console.log('Sidebar avatar found, current src:', sidebarAvatar.src);
            
            // Always force refresh from server to get latest avatar
            forceRefreshSidebarAvatar();
            
            // Also check if current image failed to load
            if (sidebarAvatar.classList.contains('error')) {
                console.log('Avatar has error class, using default');
                updateSidebarAvatar(null);
            }
        } else {
            console.log('Sidebar avatar element not found');
        }
    }, 500);
});