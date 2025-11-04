<meta charset="UTF-8">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= isset($title) ? $title : 'Admin Panel' ?> - MyProjek</title>
<link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
<script>
// Sidebar Avatar Management - Embedded
function updateSidebarAvatar(avatarUrl = null) {
    console.log('updateSidebarAvatar called with:', avatarUrl);
    const sidebarAvatar = document.getElementById('sidebar-avatar');
    if (!sidebarAvatar) {
        console.log('Sidebar avatar element not found');
        return;
    }
    
    if (avatarUrl) {
        console.log('Setting sidebar avatar to:', avatarUrl);
        sidebarAvatar.src = avatarUrl;
        sidebarAvatar.classList.remove('error');
        sidebarAvatar.classList.remove('default-avatar');
    } else {
        const userName = sidebarAvatar.alt.replace(' Avatar', '');
        const initials = getUserInitials(userName);
        const defaultAvatarUrl = generateDefaultAvatar(initials, 48);
        console.log('Setting sidebar avatar to default:', defaultAvatarUrl);
        sidebarAvatar.src = defaultAvatarUrl;
        sidebarAvatar.classList.add('default-avatar');
    }
}

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

function generateDefaultAvatar(initials, size = 48) {
    const background = '007bff';
    const color = 'ffffff';
    return `https://ui-avatars.com/api/?name=${encodeURIComponent(initials)}&size=${size}&background=${background}&color=${color}&bold=true&rounded=false`;
}

function refreshSidebarAvatar() {
    fetch('<?= base_url('auth/check') ?>', {
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
            updateSidebarAvatar(null);
        }
    })
    .catch(error => {
        console.log('Could not refresh sidebar avatar:', error);
        updateSidebarAvatar(null);
    });
}

document.addEventListener('avatarUpdated', function(event) {
    console.log('Avatar updated event received:', event.detail);
    updateSidebarAvatar(event.detail.avatarUrl);
});

document.addEventListener('avatarRemoved', function() {
    console.log('Avatar removed event received');
    updateSidebarAvatar(null);
});

document.addEventListener('DOMContentLoaded', function() {
    setTimeout(() => {
        const sidebarAvatar = document.getElementById('sidebar-avatar');
        if (sidebarAvatar && sidebarAvatar.classList.contains('error')) {
            updateSidebarAvatar(null);
        }
    }, 100);
});
</script>
