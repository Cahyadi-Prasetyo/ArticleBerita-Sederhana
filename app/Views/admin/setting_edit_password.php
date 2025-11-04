<!DOCTYPE html>
<html lang="en">

<head>
    <?= view('admin/_partials/head') ?>
</head>

<body>
    <main class="main">
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Change Password</h1>

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

            <!-- Password Change Form -->
            <div class="card">
                <div class="card-header">
                    <b>Change Password</b>
                </div>
                <div class="card-body">
                    <form method="post" action="<?= base_url('admin/setting/updatePassword') ?>">
                        <?= csrf_field() ?>
                        
                        <div class="form-group">
                            <label for="current_password">Current Password</label>
                            <input type="password" 
                                   id="current_password" 
                                   name="current_password" 
                                   placeholder="Enter your current password"
                                   class="<?= isset($validation) && $validation->hasError('current_password') ? 'invalid' : '' ?>"
                                   required>
                            <?php if (isset($validation) && $validation->hasError('current_password')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('current_password') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="new_password">New Password</label>
                            <input type="password" 
                                   id="new_password" 
                                   name="new_password" 
                                   placeholder="Enter new password"
                                   class="<?= isset($validation) && $validation->hasError('new_password') ? 'invalid' : '' ?>"
                                   required>
                            <div class="password-requirements">
                                Password must be at least 8 characters long and contain a mix of letters, numbers, and special characters.
                            </div>
                            <?php if (isset($validation) && $validation->hasError('new_password')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('new_password') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-group">
                            <label for="confirm_password">Confirm New Password</label>
                            <input type="password" 
                                   id="confirm_password" 
                                   name="confirm_password" 
                                   placeholder="Confirm new password"
                                   class="<?= isset($validation) && $validation->hasError('confirm_password') ? 'invalid' : '' ?>"
                                   required>
                            <?php if (isset($validation) && $validation->hasError('confirm_password')): ?>
                                <div class="invalid-feedback"><?= $validation->getError('confirm_password') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="form-actions">
                            <button type="submit" class="button button-primary">Change Password</button>
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
        // Real-time password validation
        document.getElementById('new_password').addEventListener('input', function() {
            const password = this.value;
            const confirmPassword = document.getElementById('confirm_password');
            const requirements = document.querySelector('.password-requirements');
            
            // Update requirements display based on password strength
            if (password.length > 0) {
                let validationMessage = '';
                const checks = {
                    length: password.length >= 8,
                    lowercase: /[a-z]/.test(password),
                    uppercase: /[A-Z]/.test(password),
                    number: /\d/.test(password),
                    special: /[@$!%*?&]/.test(password)
                };
                
                const failedChecks = [];
                if (!checks.length) failedChecks.push('at least 8 characters');
                if (!checks.lowercase) failedChecks.push('one lowercase letter');
                if (!checks.uppercase) failedChecks.push('one uppercase letter');
                if (!checks.number) failedChecks.push('one number');
                if (!checks.special) failedChecks.push('one special character');
                
                if (failedChecks.length > 0) {
                    validationMessage = `Password needs: ${failedChecks.join(', ')}.`;
                    requirements.style.color = '#e74c3c';
                } else {
                    validationMessage = 'Password meets all requirements ✓';
                    requirements.style.color = '#27ae60';
                }
                
                requirements.textContent = validationMessage;
            } else {
                requirements.textContent = 'Password must be at least 8 characters long and contain a mix of letters, numbers, and special characters.';
                requirements.style.color = '#666';
            }
            
            // Reset confirm password validation when new password changes
            if (confirmPassword.value) {
                validatePasswordConfirmation();
            }
        });
        
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', validatePasswordConfirmation);
        
        function validatePasswordConfirmation() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            
            if (confirmPassword && newPassword !== confirmPassword) {
                document.getElementById('confirm_password').setCustomValidity('Passwords do not match');
                document.getElementById('confirm_password').style.borderColor = '#e74c3c';
            } else {
                document.getElementById('confirm_password').setCustomValidity('');
                document.getElementById('confirm_password').style.borderColor = '';
            }
        }
        
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
        
        // Form submission with loading state (normal form submission)
        document.querySelector('form').addEventListener('submit', function(e) {
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.textContent;
            
            // Show loading state
            submitBtn.textContent = 'Changing Password...';
            submitBtn.disabled = true;
            
            // Let the form submit normally (will redirect to settings page with flash message)
        });
    </script>
</body>

</html>