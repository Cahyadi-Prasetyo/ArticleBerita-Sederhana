<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - MyProjek</title>
    <link rel="stylesheet" href="<?= base_url('assets/css/admin.css') ?>">
    <style>
        .auth-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            padding: 2rem 0;
        }
        
        .auth-card {
            background: white;
            padding: 2rem;
            border-radius: 10px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 450px;
        }
        
        .auth-header {
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .auth-header h1 {
            color: #333;
            margin-bottom: 0.5rem;
        }
        
        .auth-header p {
            color: #666;
            margin: 0;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #333;
            font-weight: 500;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: border-color 0.3s;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        
        .btn-auth {
            width: 100%;
            padding: 0.75rem;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            cursor: pointer;
            transition: transform 0.2s;
        }
        
        .btn-auth:hover {
            transform: translateY(-2px);
        }
        
        .auth-links {
            text-align: center;
            margin-top: 1.5rem;
        }
        
        .auth-links a {
            color: #667eea;
            text-decoration: none;
        }
        
        .auth-links a:hover {
            text-decoration: underline;
        }
        
        .alert {
            padding: 0.75rem;
            margin-bottom: 1rem;
            border-radius: 5px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .invalid-feedback {
            color: #dc3545;
            font-size: 0.875rem;
            margin-top: 0.25rem;
        }
        
        .form-group input.invalid {
            border-color: #dc3545;
        }
        
        .back-to-site {
            text-align: center;
            margin-bottom: 1rem;
        }
        
        .back-to-site a {
            color: #667eea;
            text-decoration: none;
            font-size: 0.9rem;
        }
        
        .back-to-site a:hover {
            text-decoration: underline;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        <div class="auth-card">
            <div class="back-to-site">
                <a href="<?= base_url('/') ?>">← Back to Site</a>
            </div>
            
            <div class="auth-header">
                <h1>Register</h1>
                <p>Create your MyProjek account</p>
            </div>

            <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    ✅ <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?>

            <?php if (session()->getFlashdata('error')): ?>
                <div class="alert alert-error">
                    ❌ <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (isset($validation)): ?>
                <div class="alert alert-error">
                    ⚠️ Please check the following:
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <?= form_open('register') ?>
            <?= csrf_field() ?>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input type="text" 
                       id="name" 
                       name="name" 
                       value="<?= old('name') ?>" 
                       class="<?= isset($validation) && $validation->hasError('name') ? 'invalid' : '' ?>"
                       placeholder="Enter your full name" 
                       required>
                <?php if (isset($validation) && $validation->hasError('name')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" 
                       id="email" 
                       name="email" 
                       value="<?= old('email') ?>" 
                       class="<?= isset($validation) && $validation->hasError('email') ? 'invalid' : '' ?>"
                       placeholder="Enter your email address" 
                       required>
                <?php if (isset($validation) && $validation->hasError('email')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" 
                       id="username" 
                       name="username" 
                       value="<?= old('username') ?>" 
                       class="<?= isset($validation) && $validation->hasError('username') ? 'invalid' : '' ?>"
                       placeholder="Choose a username" 
                       required>
                <?php if (isset($validation) && $validation->hasError('username')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('username') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" 
                       id="password" 
                       name="password" 
                       class="<?= isset($validation) && $validation->hasError('password') ? 'invalid' : '' ?>"
                       placeholder="Enter your password (min. 6 characters)" 
                       required>
                <?php if (isset($validation) && $validation->hasError('password')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('password') ?></div>
                <?php endif; ?>
            </div>

            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" 
                       id="confirm_password" 
                       name="confirm_password" 
                       class="<?= isset($validation) && $validation->hasError('confirm_password') ? 'invalid' : '' ?>"
                       placeholder="Confirm your password" 
                       required>
                <?php if (isset($validation) && $validation->hasError('confirm_password')): ?>
                    <div class="invalid-feedback"><?= $validation->getError('confirm_password') ?></div>
                <?php endif; ?>
            </div>

            <button type="submit" class="btn-auth">Register</button>

            <?= form_close() ?>

            <div class="auth-links">
                <p>Already have an account? <a href="<?= base_url('login') ?>">Login here</a></p>
                <p><a href="<?= base_url('/') ?>">Home</a> | <a href="<?= base_url('about') ?>">About</a> | <a href="<?= base_url('contact') ?>">Contact</a></p>
            </div>
        </div>
    </div>
</body>

</html>