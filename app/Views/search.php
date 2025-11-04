<!DOCTYPE html>
<html lang="en">

<head>
    <?php // PERBAIKAN: Gunakan helper view() 
    ?>
    <?= view('partials/head') ?>
</head>

<body>
    <?php
    ?>
    <?= view('partials/navbar') ?>

    <div class="container">
        <h1>Cari Artikel</h1>
        <p>Tuliskan kata kunci artikel yang ingin kamu cari</p>

        <?php // form_open_get() adalah helper CI4 untuk membuat form GET 
        ?>
        <?= form_open(site_url('search'), ['method' => 'get', 'style' => 'flex-direction: row; align-items:center']) ?>
        <div>
            <?php // PERBAIKAN: Gunakan esc() untuk keamanan 
            ?>
            <input type="search" name="keyword" style="width: 360px;" placeholder="Keyword.." value="<?= esc($keyword, 'attr') ?>" required maxlength="32" />
        </div>

        <div>
            <input type="submit" class="button button-primary" value="Cari">
        </div>
        <?= form_close() ?>

        <?php if ($searchResults) : ?>
            <div class="search-result">
                <hr>
                <?php foreach ($searchResults as $article) : ?>
                    <h2>
                        <?php 
                        ?>
                        <a href="<?= site_url('articles/' . $article->slug) ?>"><?= esc($article->title) ?></a>
                    </h2>
                    <?php 
                    ?>
                    <p><?= strip_tags(substr($article->content, 0, 200)) ?>...</p>
                <?php endforeach ?>
            </div>
        <?php else : ?>
            <?php if ($keyword) : ?>
                <div style="height: 400px;">
                    <h1>Tidak ada yang ditemukan</h1>
                    <p>Coba dengan kata kunci yang lain</p>
                </div>
            <?php endif ?>
        <?php endif ?>
    </div>

    <?php
    ?>
    <?= view('partials/footer') ?>
</body>

</html>