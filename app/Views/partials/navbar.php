<nav class="navbar">
    <a href="<?= site_url() ?>">Home</a>
    <a href="<?= site_url('articles/') ?>">Article</a>
    <a href="<?= site_url('/about') ?>">About</a>
    <a href="<?= site_url('/search') ?>">Cari</a>
    <a href="<?= site_url('/contact') ?>">Contact</a>
    <?php if (session()->get('logged_in')): ?>
        <a href="<?= site_url('admin/dashboard') ?>" style="margin-left:auto">Dashboard</a>
        <a href="<?= site_url('logout') ?>">Logout</a>
    <?php else: ?>
        <a href="<?= site_url('login') ?>" style="margin-left:auto">Login</a>
    <?php endif; ?>
</nav>