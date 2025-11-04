<!DOCTYPE html>
<html lang="en">

<head>
    <?php ?>
    <?= view('admin/_partials/head') ?>
</head>

<body>
    <main class="main">
        <?php
 ?>
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Article is Empty</h1>
            <p>No Article to show. Please create new article.</p>

            <div>
                <?php // site_url() masih berfungsi di CI4 
                ?>
                <a href="<?= site_url('admin/post/new') ?>" class="button button-primary">+ Create New Article</a>
            </div>

            <?php
     ?>
            <?= view('admin/_partials/footer') ?>
        </div>
    </main>
</body>

</html>