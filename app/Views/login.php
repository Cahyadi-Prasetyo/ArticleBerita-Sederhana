<!DOCTYPE html>
<html lang="en">

<head>
    <?= view('partials/head') ?>
</head>

<body>
    <?= view('partials/navbar') ?>

    <div class="container">
        <h1>Login</h1>
        <p>Masuk ke Dashboard</p>

        <?php if (session()->getFlashdata('message_login_error')): ?>
            <div class="invalid-feedback">
                <?= session()->getFlashdata('message_login_error') ?>
            </div>
        <?php endif ?>

        <?php if (session()->getFlashdata('error')): ?>
            <div class="invalid-feedback">
                <?= session()->getFlashdata('error') ?>
            </div>
        <?php endif ?>

        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert-success">
                <?= session()->getFlashdata('success') ?>
            </div>
        <?php endif ?>

        <?= form_open('login', ['style' => 'max-width: 600px;']) ?>
        <?= csrf_field() ?>

        <div>
            <label for="username">Email/Username*</label>
            <input type="text" 
                   name="username" 
                   id="username"
                   class="<?= isset($validation) && $validation->hasError('username') ? 'invalid' : '' ?>" 
                   placeholder="Your username or email" 
                   value="<?= old('username') ?>" 
                   required />
            <?php if (isset($validation) && $validation->hasError('username')): ?>
                <div class="invalid-feedback">
                    <?= $validation->getError('username') ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <label for="password">Password*</label>
            <input type="password" 
                   name="password" 
                   id="password"
                   class="<?= isset($validation) && $validation->hasError('password') ? 'invalid' : '' ?>" 
                   placeholder="Enter your password" 
                   required />
            <?php if (isset($validation) && $validation->hasError('password')): ?>
                <div class="invalid-feedback">
                    <?= $validation->getError('password') ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <input type="submit" class="button button-primary" value="Login">
        </div>

        <?= form_close() ?>
    </div>

    <?= view('partials/footer') ?>
</body>

</html>