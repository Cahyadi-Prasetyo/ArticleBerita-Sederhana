<!DOCTYPE html>
<html lang="en">

<head>
    <?= view('admin/_partials/head') ?>
</head>

<body>
    <main class="main">
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Edit Profile</h1>

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

            <!-- Profile Edit Form -->
            <div class="card">
                <div class="card-header">
                    <b>Profile Information</b>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('admin/setting/updateProfile') ?>">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="<?= old('name', $user->name) ?>" 
                                   placeholder="Enter your full name"
                                   class="<?= isset($validation) && $validation->hasError('name') ? 'invalid' : '' ?>"
                                   required>
                            <?php if (isset($validation) && $validation->hasError('name')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" 
                                   id="email" 
                                   name="email" 
                                   value="<?= old('email', $user->email) ?>" 
                                   placeholder="Enter your email address"
                                   class="<?= isset($validation) && $validation->hasError('email') ? 'invalid' : '' ?>"
                                   required>
                            <?php if (isset($validation) && $validation->hasError('email')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="button button-primary">Update Profile</button>
                            <a href="<?= base_url('admin/setting') ?>" class="button button-secondary">Cancel</a>
                        </div>
                    </form>
                </div>
            </div>

            <?= view('admin/_partials/footer') ?>
        </div>
    </main>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Enhanced input validation feedback
        document.querySelectorAll('input[required]').forEach(input => {
            input.addEventListener('blur', function() {
                if (this.value.trim() === '') {
                    this.style.borderColor = '#e74c3c';
                } else {
                    this.style.borderColor = '';
                }
            });
            
            input.addEventListener('input', function() {
                if (this.style.borderColor === 'rgb(231, 76, 60)') {
                    this.style.borderColor = '';
                }
            });
        });
        
        // Email validation
        document.getElementById('email').addEventListener('input', function() {
            const email = this.value;
            const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            
            if (email && !emailRegex.test(email)) {
                this.setCustomValidity('Please enter a valid email address');
                this.style.borderColor = '#e74c3c';
            } else {
                this.setCustomValidity('');
                this.style.borderColor = '';
            }
        });
        
        // Form submission with loading state (normal form submission)
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Updating...';
            submitBtn.disabled = true;
            
            // Trigger profile update event for sidebar (global system)
            const nameValue = document.getElementById('name').value;
            const emailValue = document.getElementById('email').value;
            
            window.dispatchEvent(new CustomEvent('profileUpdated', {
                detail: { 
                    name: nameValue,
                    email: emailValue
                }
            }));
            
            // Let the form submit normally (will redirect to settings page with flash message)
        });
    </script>
</body>

</html>