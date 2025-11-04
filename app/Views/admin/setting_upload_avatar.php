<!DOCTYPE html>
<html lang="en">

<head>
    <?= view('admin/_partials/head') ?>
</head>

<body>
    <main class="main">
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Upload Avatar</h1>

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

            <!-- Current Avatar -->
            <div class="card">
                <div class="card-header">
                    <b>Current Avatar</b>
                </div>
                <div class="card-body">
                    <?php
                    if ($user->avatar && file_exists(FCPATH . 'uploads/avatars/' . $user->avatar)) {
                        $avatar = base_url('uploads/avatars/' . $user->avatar);
                    } else {
                        $initials = get_user_initials($user->name);
                        $avatar = generate_default_avatar($initials, 100);
                    }
                    ?>
                    <img src="<?= $avatar ?>" alt="<?= esc($user->name) ?>" height="100" width="100" style="border: 1px solid #ddd;">
                </div>
            </div>

            <!-- Upload Avatar Form -->
            <div class="card">
                <div class="card-header">
                    <b>Upload New Avatar</b>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('admin/setting/uploadAvatar') ?>" enctype="multipart/form-data">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="avatar">Choose Avatar Image</label>
                            <input type="file" 
                                   id="avatar" 
                                   name="avatar" 
                                   accept="image/jpeg,image/jpg,image/png,image/gif"
                                   required>
                            <div class="form-help">
                                <small>Supported formats: JPG, PNG, GIF. Maximum size: 2MB.</small>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div id="preview-container" style="display: none;">
                                <label>Preview:</label>
                                <img id="avatar-preview" src="" alt="Preview" height="100" width="100" style="border: 2px solid #ddd;">
                            </div>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="button button-primary">Upload Avatar</button>
                            <a href="<?= base_url('admin/setting') ?>" class="button button-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <?= view('admin/_partials/footer') ?>
        </div>
    </main>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
    </style>
    <script>
        // Refresh sidebar avatar when page loads
        document.addEventListener('DOMContentLoaded', function() {
            // Small delay to ensure sidebar is loaded
            setTimeout(function() {
                if (typeof refreshSidebarAvatar === 'function') {
                    console.log('Refreshing sidebar avatar on page load');
                    refreshSidebarAvatar();
                }
            }, 500);
        });
        
        // Avatar preview functionality
        document.getElementById('avatar').addEventListener('change', function(e) {
            const file = e.target.files[0];
            const previewContainer = document.getElementById('preview-container');
            const previewImg = document.getElementById('avatar-preview');
            
            if (file) {
                // Validate file type
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Please select a valid image file (JPG, PNG, or GIF).');
                    this.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }
                
                // Validate file size (2MB max)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB.');
                    this.value = '';
                    previewContainer.style.display = 'none';
                    return;
                }
                
                // Show preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    previewContainer.style.display = 'block';
                };
                reader.readAsDataURL(file);
            } else {
                previewContainer.style.display = 'none';
            }
        });
        
        // Form submission with AJAX for better UX
        document.querySelector('form').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            const formData = new FormData(this);
            
            // Show loading state
            submitBtn.textContent = 'Uploading...';
            submitBtn.disabled = true;
            
            // Submit via AJAX
            fetch(this.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                console.log('Upload response:', data);
                if (data.success) {
                    console.log('Upload successful with URL:', data.avatar_url);
                    
                    // Update sidebar avatar directly using DOM manipulation
                    const sidebarAvatar = document.getElementById('sidebar-avatar');
                    if (sidebarAvatar) {
                        console.log('Found sidebar avatar element, updating src');
                        sidebarAvatar.src = data.avatar_url;
                        sidebarAvatar.classList.remove('error', 'default-avatar');
                        
                        // Force reload the image to ensure it displays
                        sidebarAvatar.onload = function() {
                            console.log('Sidebar avatar image loaded successfully');
                        };
                        sidebarAvatar.onerror = function() {
                            console.log('Sidebar avatar image failed to load');
                        };
                    } else {
                        console.log('Sidebar avatar element not found');
                    }

                    // Dispatch global event so listeners can update consistently
                    try {
                        document.dispatchEvent(new CustomEvent('avatarUpdated', { detail: { avatarUrl: data.avatar_url } }));
                        console.log('Dispatched avatarUpdated event');
                    } catch (e) {
                        console.log('Failed to dispatch avatarUpdated event', e);
                    }

                    // Fallback: ask sidebar to refresh from server
                    if (typeof refreshSidebarAvatar === 'function') {
                        refreshSidebarAvatar();
                    }
                    
                    // Show success message
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: data.message,
                        timer: 2000,
                        showConfirmButton: false
                    });
                    
                    // Redirect to settings page
                    setTimeout(() => {
                        window.location.href = '<?= base_url('admin/setting') ?>';
                    }, 1500);
                } else {
                    // Show error message
                    Swal.fire({
                        icon: 'error',
                        title: 'Upload Failed',
                        text: data.message
                    });
                    
                    // Reset button
                    submitBtn.textContent = originalText;
                    submitBtn.disabled = false;
                }
            })
            .catch(error => {
                console.error('Upload error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Upload Failed',
                    text: 'An error occurred while uploading the avatar.'
                });
                
                // Reset button
                submitBtn.textContent = originalText;
                submitBtn.disabled = false;
            });
        });
    </script>
</body>

</html>