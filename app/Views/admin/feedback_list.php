<!DOCTYPE html>
<html lang="en">

<head>
    <?= view('admin/_partials/head') ?>
</head>

<body>
    <main class="main">
        <?= view('admin/_partials/side_nav') ?>

        <div class="content">
            <h1>Manage Feedback</h1>

            <!-- <?php if (session()->getFlashdata('success')) : ?>
                <div style="color: #155724; background-color: #d4edda; border: 1px solid #c3e6cb; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    ✅ <?= session()->getFlashdata('success') ?>
                </div>
            <?php endif; ?> -->

            <?php if (session()->getFlashdata('error')) : ?>
                <div style="color: #721c24; background-color: #f8d7da; border: 1px solid #f5c6cb; padding: 12px; margin-bottom: 20px; border-radius: 4px;">
                    ❌ <?= session()->getFlashdata('error') ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($feedbacks)) : ?>
                <?php foreach ($feedbacks as $feedback): ?>
                    <div class="card">
                        <div class="card-header">
                            <div>
                                <b><?= esc($feedback->name) ?></b> 
                                <small class="text-gray"><?= esc($feedback->email) ?></small>
                            </div>
                            <div>
                                <small class="text-gray"><?= date('d M Y H:i', strtotime($feedback->created_at)) ?></small>
                            </div>
                        </div>
                        <p><?= esc($feedback->message) ?></p>
                        <a href="#"
                           data-delete-url="<?= base_url('admin/feedback/delete/' . $feedback->id) ?>"
                           class="button button-danger button-small"
                           role="button"
                           onclick="deleteConfirm(this)">Delete</a>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <div class="card">
                    <div class="text-center text-gray">
                        <p>No feedback messages found.</p>
                    </div>
                </div>
            <?php endif; ?>

            <?= view('admin/_partials/footer') ?>
        </div>
    </main>
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Delete confirmation function
        function deleteConfirm(element) { 
            event.preventDefault();
            Swal.fire({
                title: 'Delete Confirmation!',
                text: 'Are you sure to delete the item?',
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

        // Toast configuration - single declaration
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