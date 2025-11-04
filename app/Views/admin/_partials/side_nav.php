<div class="side-nav">
    <div class="brand">
        <h3>MyProjek Admin</h3>
    </div>
    
    <?php
    // Include user profile component if user is logged in
    if (session()->get('logged_in')) {
        echo view('admin/_partials/user_profile');
    }
    ?>
    
    <nav>
        <a href="<?= base_url('admin/dashboard') ?>">📊 Dashboard</a>
        <a href="<?= base_url('admin/post') ?>">📝 Posts</a>
        <a href="<?= base_url('admin/feedback') ?>">💬 Feedback</a>
        <a href="<?= base_url('admin/setting') ?>">⚙️ Settings</a>
        <a href="<?= base_url('logout') ?>">🚪 Logout</a>
    </nav>
</div>