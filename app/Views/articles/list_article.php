<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>List of Article</title>

    <?= view('partials/head') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/petanikode-pagination.css') ?>">
</head>

<body>
    <?= view('partials/navbar') ?>

    <div class="container">
        <header class="article-list-header">
            <h1>List Artikel</h1>


        </header>

        <main class="">
            

            <?php if (!empty($articles)): ?>
                <!-- Simple bullet list of article titles -->
                <ul>
                    <?php foreach ($articles as $article): ?>
                        <li>
                            <a href="<?= site_url('articles/' . $article->slug) ?>"
                               title="Baca: <?= esc($article->title ?: 'Artikel Tanpa Judul') ?>">
                                <?= esc($article->title ?: 'Artikel Tanpa Judul') ?>
                            </a>
                        </li>
                    <?php endforeach ?>
                </ul>

                <!-- Custom Pagination -->
                <div class="pagination-wrapper">
                   
                    <?= $pager->links('default', 'petanikode_pagination') ?>
                </div>
            <?php else: ?>
                <div class="no-results">
                    <div class="no-results-content">
                        <i class="fas fa-file-alt" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
                        <h3>Belum ada artikel</h3>
                        <p>Konten akan segera hadir. Silakan kembali lagi nanti.</p>
                    </div>
                </div>
            <?php endif; ?>
        </main>


    </div>

    <?= view('partials/footer') ?>
</body>

</html>