<!DOCTYPE html>
<html lang="en">

<head>
    <?php // PERBAIKAN: Gunakan helper view() 
    ?>
    <?= view('admin/_partials/head') ?>
</head>

<body>
    <main class="main">
        <?php // PERBAIKAN: Gunakan helper view() 
        ?>
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Feedback is Empty</h1>

            <p>No Feedback to show</p>

            <?php 
            ?>
            <?= view('admin/_partials/footer') ?>
        </div>
    </main>
</body>

</html>