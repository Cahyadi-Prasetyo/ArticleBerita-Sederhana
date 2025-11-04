<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Read Article</title>
    <?= view('partials/head') ?>
</head>

<body>
    <?= view('partials/navbar') ?>
    <article class="article">
        <h1 class="post-title"><?= $article->title ? esc($article->title) : "No Title" ?></h1>
        <div class="post-meta">
            Published at <?= $article->created_at ?>
        </div>
        <div class="post-body">
            <?php ?>
            <?= $article->content ?>
        </div>
    </article>
    <?= view('partials/footer') ?>
</body>

</html>