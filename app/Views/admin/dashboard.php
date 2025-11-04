<!DOCTYPE html>
<html lang="en">

<head>
    <?php ?>
    <?= view('admin/_partials/head') ?>
</head>

<body>
    <main class="main">
        <?php ?>
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Dashboard Overview</h1>

            <div style="display:flex; gap: 1em">
                <div class="card text-center" style="width: 100px;">
                    <h2>
                        <?php ?>
                        <?= esc($total_articles) ?>
                    </h2>
                    <p><a href="<?= site_url('admin/post') ?>">Artikel</a></p>
                </div>
                <div class="card text-center" style="width: 100px;">
                    <h2>
                        <?php ?>
                        <?= esc($total_feedbacks) ?>
                    </h2>
                    <p><a href="<?= site_url('admin/feedback') ?>">Feedback</a></p>
                </div>
            </div>


            <?= view('admin/_partials/footer') ?>
        </div>
    </main>
</body>

</html>