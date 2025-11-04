<!DOCTYPE html>
<html>

<head>
    <title>Contact</title>
    <?= view('partials/head') ?>
</head>

<body>
    <?= view('partials/navbar') ?>

    <?php if (session()->getFlashdata('success')) : ?>
        <div style="color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
            ✅ <?= session()->getFlashdata('success') ?>
        </div>
    <?php endif; ?>

    <?php if (session()->getFlashdata('error')) : ?>
        <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
            ❌ <?= session()->getFlashdata('error') ?>
        </div>
    <?php endif; ?>

    <div class="container">
        <h1>Contact Us</h1>
        <p>Hubungi kami melalui form berikut</p>

        <?= form_open('contact', ['style' => 'max-width: 600px;']) ?>

        <?= csrf_field() ?>

        <div>
            <label for="name">Name*</label>
            <input type="text" name="name" class="<?= isset($validation) && $validation->hasError('name') ? 'invalid' : '' ?>" placeholder="your name" value="<?= old('name') ?>" required
                maxlength="32" />
            <?php if (isset($validation) && $validation->hasError('name')) : ?>
                <div class="invalid-feedback"><?= $validation->getError('name') ?></div>
            <?php endif; ?>
        </div>
        <div>
            <label for="email">Email*</label>
            <input type="email" name="email" class="<?= isset($validation) && $validation->hasError('email') ? 'invalid' : '' ?>" placeholder="your email address" value="<?= old('email') ?>" required maxlength="32" />
            <?php if (isset($validation) && $validation->hasError('email')) : ?>
                <div class="invalid-feedback"><?= $validation->getError('email') ?></div>
            <?php endif; ?>
        </div>
        <div>
            <label for="message">Message*</label><br>
            <textarea name="message" cols="30" class="<?= isset($validation) && $validation->hasError('message') ? 'invalid' : '' ?>" rows="5" placeholder="write your message" required
                maxlength="32"><?= old('message') ?></textarea>
            <?php if (isset($validation) && $validation->hasError('message')) : ?>
                <div class="invalid-feedback"><?= $validation->getError('message') ?></div>
            <?php endif; ?>
        </div>

        <div style="display: flex; gap: 1rem">
            <input type="submit" class="button button-primary" value="Kirim">
            <input type="reset" class="button" value="Reset">
        </div>
        <?= form_close() ?>
    </div>
    <?= view('partials/footer'); ?>
</body>

</html>