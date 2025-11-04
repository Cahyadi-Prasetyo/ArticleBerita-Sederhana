<?php
// Helper functions for search highlighting
if (!function_exists('highlightSearchTerm')) {
    function highlightSearchTerm($text, $keyword)
    {
        if (empty($keyword)) return $text;

        $highlighted = preg_replace(
            '/(' . preg_quote($keyword, '/') . ')/i',
            '<mark class="search-highlight">$1</mark>',
            $text
        );

        return $highlighted;
    }
}

if (!function_exists('getSearchExcerpt')) {
    function getSearchExcerpt($content, $keyword, $length = 150)
    {
        if (empty($keyword)) return '';

        $content = strip_tags($content);
        $pos = stripos($content, $keyword);

        if ($pos === false) {
            return substr($content, 0, $length) . '...';
        }

        $start = max(0, $pos - 50);
        $excerpt = substr($content, $start, $length);

        if ($start > 0) {
            $excerpt = '...' . $excerpt;
        }

        if (strlen($content) > $start + $length) {
            $excerpt .= '...';
        }

        return highlightSearchTerm($excerpt, $keyword);
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?= view('admin/_partials/head') ?>
    <link rel="stylesheet" href="<?= base_url('assets/css/petanikode-pagination.css') ?>">
</head>

<body>
    <main class="main">
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Manage Posts</h1>

            <!-- <?php if (session()->getFlashdata('success')) : ?>
                <div style="color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    ✅ <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?> -->


            <div class="toolbar">
                <a href="<?= site_url('admin/post/new') ?>" class="button button-primary" role="button">+ Tulis Artikel</a>
                <div>
                    <?php // Gunakan form helper CI4 untuk membuat form GET 
                    ?>
                    <?= form_open(site_url('admin/post'), ['method' => 'get', 'style' => 'flex-direction: row; width:360px']) ?>

                    <?php // PERBAIKAN: Gunakan esc() dengan konteks 'attr' untuk keamanan 
                    ?>
                    <input type="search" name="keyword" placeholder="Cari artikel" value="<?= esc($keyword ?? '', 'attr') ?>">

                    <input type="submit" value="Cari" class="button" style="width: 32%;">

                    <?= form_close() ?>
                </div>
            </div>


            <?php if (isset($error)): ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <?= esc($error) ?>
                </div>
            <?php endif; ?>
            <?php if (!empty($articles)): ?>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th style="width: 15%;" class="text-center">Status</th>
                            <th style="width: 25%;" class="text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($articles as $article): ?>
                            <tr>
                                <td>
                                    <div class="post-title">
                                        <?php if (!empty($keyword)): ?>
                                            <?= highlightSearchTerm(esc($article->title), $keyword) ?>
                                        <?php else: ?>
                                            <?= esc($article->title) ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="post-meta text-gray">
                                        <small>
                                            <i class="fas fa-calendar-alt"></i>
                                            <?= date('M j, Y \a\t g:i A', strtotime($article->created_at)) ?>
                                        </small>
                                    </div>
                                    <?php if (!empty($keyword)): ?>
                                        <div class="post-excerpt">
                                            <small class="text-muted">
                                                <?= getSearchExcerpt($article->content, $keyword, 100) ?>
                                            </small>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <?php if ($article->draft === 'true'): ?>
                                    <td class="text-center">
                                        <span style="color: #f6c23e; font-weight: 500;">Draft</span>
                                    </td>
                                <?php else: ?>
                                    <td class="text-center">
                                        <span style="color: #1cc88a; font-weight: 500;">Published</span>
                                    </td>
                                <?php endif ?>
                                <td>
                                    <div class="action">
                                        <a href="<?= site_url('articles/' . $article->slug) ?>"
                                            class="button button-small"
                                            target="_blank"
                                            role="button"
                                            title="Preview post in new tab">
                                            <i class="fas fa-eye"></i> Preview
                                        </a>
                                        <a href="<?= site_url('admin/post/edit/' . $article->id) ?>"
                                            class="button button-small"
                                            role="button"
                                            title="Edit this post">
                                            <i class="fas fa-edit"></i> Edit
                                        </a>
                                        <a href="#"
                                            data-delete-url="<?= site_url('admin/post/delete/' . $article->id) ?>"
                                            class="button button-small button-danger"
                                            role="button"
                                            title="Delete this post"
                                            onclick="deleteConfirm(this)">
                                            <i class="fas fa-trash"></i> Delete
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach ?>
                    </tbody>
                </table>
            <?php elseif (!empty($keyword)): ?>
                <div class="no-results">
                    <div class="no-results-content">
                        <i class="fas fa-search" style="font-size: 3rem; color: #6c757d; margin-bottom: 1rem;"></i>
                        <h3>No articles found</h3>
                        <p>No articles match your search for "<strong><?= esc($keyword) ?></strong>"</p>
                        <div class="no-results-actions">
                            <a href="<?= site_url('admin/post') ?>" class="button">Show All Articles</a>
                            <a href="<?= site_url('admin/post/new') ?>" class="button button-primary">Create New Article</a>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <div class="pagination-wrapper">
                
                <?= $pager->links('default', 'petanikode_pagination') ?>
            </div>

            <?= view('admin/_partials/footer') ?>
        </div>
    </main>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Search functionality enhancements
        document.addEventListener('DOMContentLoaded', function() {
            const searchForm = document.getElementById('searchForm');
            const searchInput = document.getElementById('searchInput');

            if (searchForm && searchInput) {
                // Add loading state on form submit
                searchForm.addEventListener('submit', function(e) {
                    const keyword = searchInput.value.trim();

                    if (keyword.length === 0) {
                        e.preventDefault();
                        searchInput.focus();
                        return false;
                    }

                    // Add loading state
                    searchForm.classList.add('loading');
                });

                // Auto-focus search input if there's a keyword
                <?php if (!empty($keyword)): ?>
                    searchInput.focus();
                    // Move cursor to end
                    searchInput.setSelectionRange(searchInput.value.length, searchInput.value.length);
                <?php endif; ?>

                // Clear search on Escape key
                searchInput.addEventListener('keydown', function(e) {
                    if (e.key === 'Escape') {
                        if (searchInput.value) {
                            searchInput.value = '';
                        } else {
                            window.location.href = '<?= site_url('admin/post') ?>';
                        }
                    }
                });
            }
        });

        // Delete confirmation function
        function deleteConfirm(element) {
            event.preventDefault();
            Swal.fire({
                title: 'Delete Confirmation!',
                text: 'Are you sure to delete this article?',
                icon: 'warning',
                showCancelButton: true,
                cancelButtonText: 'No',
                confirmButtonText: 'Yes Delete',
                confirmButtonColor: '#dc3545',
                cancelButtonColor: '#6c757d',
                reverseButtons: false,
                customClass: {
                    confirmButton: 'btn btn-danger',
                    cancelButton: 'btn btn-secondary'
                }
            }).then(dialog => {
                if (dialog.isConfirmed) {
                    window.location.assign(element.dataset.deleteUrl);
                }
            });
        }
        // Toast configuration
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        // Show success toast
        <?php if (session()->getFlashdata('success')): ?>
            Toast.fire({
                icon: 'success',
                title: '<?= esc(session()->getFlashdata('success'), 'js') ?>'
            });
        <?php endif ?>

        // Show error toast
        <?php if (session()->getFlashdata('error')): ?>
            Toast.fire({
                icon: 'error',
                title: '<?= esc(session()->getFlashdata('error'), 'js') ?>'
            });
        <?php endif ?>
    </script>
</body>

</html>