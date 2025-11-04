<!DOCTYPE html>
<html lang="en">

<head>
    <?= view('admin/_partials/head') ?>

    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        #editor.invalid {
            border: 1px solid #dc3545;
            border-radius: 4px;
        }

        .invalid-feedback {
            color: #dc3545;
            font-size: 80%;
            margin-top: 5px;
        }
    </style>
</head>

<body>
    <main class="main">
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Edit Article</h1>

            <?php if (session()->getFlashdata('error')) : ?>
                <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    ❌ <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (isset($validation)) : ?>
                <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    ⚠️ Please check the following:
                    <?= $validation->listErrors() ?>
                </div>
            <?php endif; ?>

            <div class="card">
                <?= form_open('admin/post/edit/' . $article->id) ?>
                <?= csrf_field() ?>

                <div>
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title"
                        class="<?= isset($validation) && $validation->hasError('title') ? 'invalid' : '' ?>"
                        value="<?= old('title', $article->title) ?>"
                        placeholder="Enter article title" required
                        maxlength="128">

                    <?php if (isset($validation) && $validation->hasError('title')) : ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('title') ?>
                        </div>
                    <?php endif; ?>
                </div>

                <div>
                    <label for="content">Konten</label>

                    <input type="hidden" name="content" value="<?= esc(old('content', $article->content)) ?>">

                    <div id="editor" style="min-height: 160px;"
                        class="<?= isset($validation) && $validation->hasError('content') ? 'invalid' : '' ?>">
                        <?= old('content', $article->content) ?>
                    </div>

                    <?php if (isset($validation) && $validation->hasError('content')) : ?>
                        <div class="invalid-feedback">
                            <?= $validation->getError('content') ?>
                        </div>
                    <?php endif; ?>
                </div>
                <div>
                    <label for="draft">Status</label>
                    <select id="draft" name="draft">
                        <option value="true" <?= old('draft', $article->draft) === 'true' ? 'selected' : '' ?>>Draft</option>
                        <option value="false" <?= old('draft', $article->draft) === 'false' ? 'selected' : '' ?>>Published</option>
                    </select>
                </div>

                <div style="margin-top: 20px;">
                    <button type="submit" class="button button-primary">Update Article</button>
                    <a href="<?= site_url('admin/post') ?>" class="button">Cancel</a>
                </div>

                <?= form_close() ?>
            </div>

            <?= view('admin/_partials/footer') ?>
        </div>
    </main>

    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
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

        <?php if (session()->getFlashdata('success')): ?>
            Toast.fire({
                icon: 'success',
                title: '<?= esc(session()->getFlashdata('success'), 'js') ?>'
            });
        <?php endif ?>

        <?php if (session()->getFlashdata('error')): ?>
            Toast.fire({
                icon: 'error',
                title: '<?= esc(session()->getFlashdata('error'), 'js') ?>'
            });
        <?php endif ?>
    </script>

    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>

    <script>
        var quill = new Quill('#editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        header: [1, 2, 3, 4, 5, 6, false]
                    }],
                    [{
                        font: []
                    }],
                    ["bold", "italic"],
                    ["link", "blockquote", "code-block", "image"],
                    [{
                        list: "ordered"
                    }, {
                        list: "bullet"
                    }],
                    [{
                        script: "sub"
                    }, {
                        script: "super"
                    }],
                    [{
                        color: []
                    }, {
                        background: []
                    }],
                ]
            },
        });
        quill.on('text-change', function(delta, oldDelta, source) {
            document.querySelector("input[name='content']").value = quill.root.innerHTML;
        });
        document.querySelector("input[name='content']").value = quill.root.innerHTML;
    </script>
</body>

</html>