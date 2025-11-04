<!DOCTYPE html>
<html lang="en">

<head>
    <?= view('admin/_partials/head') ?>
</head>

<body>
    <main class="main">
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Settings</h1>

            <!-- Success/Error Messages -->
            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-danger">
                    <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <!-- Avatar Section -->
            <div class="card">
                <div class="card-header">
                    <b>Avatar</b>
                    <div style="display: flex; gap: 1em">
                        <a href="<?= site_url('admin/setting/remove_avatar') ?>" style="color: #dc3545; text-decoration: none;">Remove Avatar</a>
                        <a href="<?= site_url('admin/setting/upload_avatar') ?>" style="text-decoration: none;" >Change Avatar</a>
                    </div>
                </div>
                <div class="card-body">
                    <?php
                    if ($user->avatar && file_exists(FCPATH . 'uploads/avatars/' . $user->avatar)) {
                        $avatarPath = FCPATH . 'uploads/avatars/' . $user->avatar;
                        $avatar = base_url('uploads/avatars/' . $user->avatar) . '?v=' . filemtime($avatarPath);
                    } else {
                        $initials = get_user_initials($user->name);
                        $avatar = generate_default_avatar($initials, 80);
                    }
                    ?>
                    <img src="<?= $avatar ?>" alt="<?= esc($user->name) ?>" height="80" width="80" style="border: 1px solid #ddd;">
                </div>
            </div>

            <!-- Profile Settings Section -->
            <div class="card">
                <div class="card-header">
                    <b>Profile Settings</b>
                    <a href="<?= site_url('admin/setting/edit_profile') ?>" style="text-decoration: none;">Edit Profile</a>
                </div>
                <div class="card-body">
                    Name: <span class="text-gray"><?= esc($user->name) ?></span><br>
                    Email: <span class="text-gray"><?= esc($user->email) ?></span>
                </div>
            </div>

            <!-- Security & Password Section -->
            <div class="card">
                <div class="card-header">
                    <b>Security & Password</b>
                    <a href="<?= site_url('admin/setting/edit_password') ?>"style="text-decoration: none;">Edit Password</a>
                </div>
                <div class="card-body">
                    Your Password: <span class="text-gray">******</span><br>
                    Last Changed: <span class="text-gray"><?= $user->password_updated_at ? date('d-m-Y H:i', strtotime($user->password_updated_at)) : 'Never' ?></span>
                </div>
            </div>

            <?= view('admin/_partials/footer') ?>
        </div>
    </main>

    <!-- SweetAlert for flash messages -->
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Delete confirmation function
        function deleteConfirm(element) {
            event.preventDefault();
            Swal.fire({
                title: 'Delete Confirmation!',
                text: 'Are you sure to delete this article?',
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: 'Yes Delete',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: false,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then(dialog => {
                if (dialog.isConfirmed) {
                    window.location.assign(element.dataset.deleteUrl);
                }
            });
        }

        // Toast configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer);
                toast.addEventListener('mouseleave', Swal.resumeTimer);
            }
        });

        // Show success toast
        <?php if (session()->getFlashdata('success')): ?>
        Toast.fire({
            icon: 'success',
            title: '<?= esc(session()->getFlashdata('success'), 'js') ?>'
        });
        <?php endif; ?>

        // Show error toast
        <?php if (session()->getFlashdata('error')): ?>
        Toast.fire({
            icon: 'error',
            title: '<?= esc(session()->getFlashdata('error'), 'js') ?>'
        });
        <?php endif; ?>

        // Show info messages
        <?php if (session()->getFlashdata('message')): ?>
        Toast.fire({
            icon: 'info',
            title: '<?= esc(session()->getFlashdata('message'), 'js') ?>'
        });
        <?php endif; ?>

        // Note: Sidebar avatar functions are now handled by global sidebar-avatar.js

        // Helper function for user initials
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

        // Handle page-specific functionality
        document.addEventListener('DOMContentLoaded', function() {
            // Handle remove avatar with confirmation
            const removeAvatarLink = document.querySelector('a[href*="remove_avatar"]');
            if (removeAvatarLink) {
                removeAvatarLink.addEventListener('click', function(e) {
                    e.preventDefault();
                    
                    Swal.fire({
                        title: 'Remove Avatar?',
                        text: 'Are you sure you want to remove your current avatar?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#dc3545',
                        cancelButtonColor: '#6c757d',
                        confirmButtonText: 'Yes, remove it',
                        cancelButtonText: 'Cancel',
                        customClass: {
                            confirmButton: 'btn btn-danger',
                            cancelButton: 'btn btn-secondary'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Show loading
                            Swal.fire({
                                title: 'Removing Avatar...',
                                allowOutsideClick: false,
                                didOpen: () => {
                                    Swal.showLoading();
                                }
                            });
                            
                            // Use AJAX to remove avatar and update sidebar immediately
                            fetch(this.href, {
                                method: 'GET',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            })
                            .then(response => response.json())
                            .then(data => {
                                console.log('Remove avatar response:', data);
                                
                                if (data.success) {
                                    // Update sidebar avatar directly to default
                                    const sidebarAvatar = document.getElementById('sidebar-avatar');
                                    if (sidebarAvatar) {
                                        console.log('Found sidebar avatar element, setting to default');
                                        const userName = sidebarAvatar.alt.replace(' Avatar', '');
                                        const initials = getUserInitials(userName);
                                        const defaultUrl = `https://ui-avatars.com/api/?name=${encodeURIComponent(initials)}&size=48&background=007bff&color=ffffff&bold=true&rounded=false`;
                                        sidebarAvatar.src = defaultUrl;
                                        sidebarAvatar.classList.add('default-avatar');
                                    }

                                    // Dispatch global removal event so listeners can react
                                    try {
                                        document.dispatchEvent(new CustomEvent('avatarRemoved'));
                                        console.log('Dispatched avatarRemoved event');
                                    } catch (e) {
                                        console.log('Failed to dispatch avatarRemoved event', e);
                                    }

                                    // Fallback: ask sidebar to refresh from server
                                    if (typeof refreshSidebarAvatar === 'function') {
                                        refreshSidebarAvatar();
                                    }
                                }
                                
                                // Show success message
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Avatar Removed',
                                    text: 'Your avatar has been removed successfully.',
                                    timer: 2000,
                                    showConfirmButton: false
                                });
                                
                                // Reload page to update the settings page avatar
                                setTimeout(() => {
                                    window.location.reload();
                                }, 1500);
                            })
                            .catch(error => {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Error',
                                    text: 'Failed to remove avatar. Please try again.'
                                });
                            });
                        }
                    });
                });
            }
        });
    </script>

    <style>
        .animated-toast {
            animation: slideInRight 0.3s ease-out;
        }
        
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        
        .swal2-timer-progress-bar {
            background: rgba(0, 0, 0, 0.2);
        }
    </style>
</body>

</html>