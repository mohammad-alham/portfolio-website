<?php
$pageTitle = 'Manage Testimonials';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrfToken();
    $client_name     = sanitize($_POST['client_name'] ?? '');
    $client_position = sanitize($_POST['client_position'] ?? '');
    $company_name    = sanitize($_POST['company_name'] ?? '');
    $message         = sanitize($_POST['message'] ?? '');
    if (empty($client_name)) $errors[] = 'Client name is required.';
    if (empty($message)) $errors[] = 'Message is required.';
    $clientImage = null;
    if (!empty($_FILES['client_image']['name'])) $clientImage = uploadFile($_FILES['client_image'], __DIR__ . '/../assets/uploads/testimonials');
    if (empty($errors)) {
        if ($action === 'create') {
            dbInsert("INSERT INTO testimonials (client_name, client_position, company_name, message, client_image) VALUES (?,?,?,?,?)", [$client_name, $client_position, $company_name, $message, $clientImage]);
            setFlash('success', 'Testimonial created successfully.');
        } elseif ($action === 'edit' && $id > 0) {
            $sql = "UPDATE testimonials SET client_name=?, client_position=?, company_name=?, message=?";
            $params = [$client_name, $client_position, $company_name, $message];
            if ($clientImage) { $sql .= ", client_image=?"; $params[] = $clientImage; }
            $sql .= " WHERE id=?"; $params[] = $id;
            dbExecute($sql, $params);
            setFlash('success', 'Testimonial updated successfully.');
        }
        header('Location: testimonials.php'); exit;
    }
}

if (isset($_GET['delete']) && $id > 0) {
    checkCsrfToken();
    $item = dbRow("SELECT * FROM testimonials WHERE id=?", [$id]);
    if ($item && $item['client_image'] && file_exists($item['client_image'])) unlink($item['client_image']);
    dbExecute("DELETE FROM testimonials WHERE id=?", [$id]);
    setFlash('success', 'Testimonial deleted successfully.');
    header('Location: testimonials.php'); exit;
}

require_once __DIR__ . '/includes/header.php';

$flash = getFlash();
$items = dbQuery("SELECT * FROM testimonials ORDER BY created_at DESC");
$editItem = ($id > 0) ? dbRow("SELECT * FROM testimonials WHERE id=?", [$id]) : null;
?>

<?php if ($flash): ?><div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div><?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-2xl font-bold">Testimonials</h1><p class="text-gray-500 text-sm mt-1">Manage client testimonials</p></div>
    <button onclick="document.getElementById('formModal').classList.remove('hidden')" class="btn btn-primary"><i class="fas fa-plus"></i> Add Testimonial</button>
</div>

<div class="stat-card p-0 overflow-hidden">
    <div class="table-container">
        <table>
            <thead><tr><th>Client</th><th>Position</th><th>Company</th><th>Message</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
                <?php if (empty($items)): ?><tr><td colspan="5" class="text-center text-gray-500 py-10">No testimonials found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="font-medium"><?= e($item['client_name']) ?></td>
                            <td class="text-gray-400 text-sm"><?= e($item['client_position'] ?? '-') ?></td>
                            <td class="text-gray-400 text-sm"><?= e($item['company_name'] ?? '-') ?></td>
                            <td class="text-gray-400 text-xs max-w-xs truncate"><?= e(truncate($item['message'], 60)) ?></td>
                            <td class="text-right">
                                <a href="?action=edit&id=<?= $item['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
                                <a href="?delete=1&id=<?= $item['id'] ?>&csrf_token=<?= getCsrfToken() ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="formModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 <?= ($action === 'edit' && $editItem) || !empty($errors) ? '' : 'hidden' ?>" style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);">
    <div class="bg-[#12121a] border border-gray-800 rounded-2xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold"><?= $editItem ? 'Edit Testimonial' : 'Add Testimonial' ?></h2>
            <button onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','testimonials.php')" class="text-gray-500 hover:text-white text-xl">&times;</button>
        </div>
        <?php if (!empty($errors)): ?><div class="flash flash-error mb-4"><?php foreach ($errors as $err): ?><?= e($err) ?><br><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="?action=<?= $editItem ? 'edit&id=' . $editItem['id'] : 'create' ?>" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="text-sm font-medium block mb-1.5">Client Name <span class="text-red-400">*</span></label><input type="text" name="client_name" class="form-input" value="<?= e($editItem['client_name'] ?? $_POST['client_name'] ?? '') ?>" required></div>
                <div><label class="text-sm font-medium block mb-1.5">Client Image</label><input type="file" name="client_image" class="form-input text-sm" accept="image/*"></div>
                <div><label class="text-sm font-medium block mb-1.5">Position</label><input type="text" name="client_position" class="form-input" value="<?= e($editItem['client_position'] ?? $_POST['client_position'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Company</label><input type="text" name="company_name" class="form-input" value="<?= e($editItem['company_name'] ?? $_POST['company_name'] ?? '') ?>"></div>
                <div class="col-span-2"><label class="text-sm font-medium block mb-1.5">Message <span class="text-red-400">*</span></label><textarea name="message" class="form-input" rows="4" required><?= e($editItem['message'] ?? $_POST['message'] ?? '') ?></textarea></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','testimonials.php')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-<?= $editItem ? 'save' : 'plus' ?>"></i> <?= $editItem ? 'Update Testimonial' : 'Add Testimonial' ?></button>
            </div>
        </form>
    </div>
</div>

<?php if ($action === 'edit' && $editItem): ?><script>document.getElementById('formModal').classList.remove('hidden');</script><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
