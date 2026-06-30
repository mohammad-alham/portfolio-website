<?php
$pageTitle = 'Manage Services';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrfToken();
    $title       = sanitize($_POST['title'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $icon        = sanitize($_POST['icon'] ?? '');
    $order       = (int)($_POST['display_order'] ?? 0);
    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    if (empty($errors)) {
        if ($action === 'create') {
            dbInsert("INSERT INTO services (title, description, icon, display_order) VALUES (?,?,?,?)", [$title, $description, $icon, $order]);
            setFlash('success', 'Service created successfully.');
        } elseif ($action === 'edit' && $id > 0) {
            dbExecute("UPDATE services SET title=?, description=?, icon=?, display_order=? WHERE id=?", [$title, $description, $icon, $order, $id]);
            setFlash('success', 'Service updated successfully.');
        }
        header('Location: services.php'); exit;
    }
}

if (isset($_GET['delete']) && $id > 0) {
    checkCsrfToken();
    dbExecute("DELETE FROM services WHERE id=?", [$id]);
    setFlash('success', 'Service deleted successfully.');
    header('Location: services.php'); exit;
}

require_once __DIR__ . '/includes/header.php';

$flash = getFlash();
$items = dbQuery("SELECT * FROM services ORDER BY display_order ASC");
$editItem = ($id > 0) ? dbRow("SELECT * FROM services WHERE id=?", [$id]) : null;
?>

<?php if ($flash): ?><div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div><?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-2xl font-bold">Services</h1><p class="text-gray-500 text-sm mt-1">Manage your professional services</p></div>
    <button onclick="document.getElementById('formModal').classList.remove('hidden')" class="btn btn-primary"><i class="fas fa-plus"></i> Add Service</button>
</div>

<div class="stat-card p-0 overflow-hidden">
    <div class="table-container">
        <table>
            <thead><tr><th>Title</th><th>Description</th><th>Icon</th><th>Order</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
                <?php if (empty($items)): ?><tr><td colspan="5" class="text-center text-gray-500 py-10">No services found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="font-medium"><?= e($item['title']) ?></td>
                            <td class="text-gray-400 max-w-xs truncate"><?= e(truncate($item['description'], 80)) ?></td>
                            <td class="text-lg"><?= $item['icon'] ? '<i class="' . e($item['icon']) . '"></i>' : '-' ?></td>
                            <td class="text-gray-500"><?= $item['display_order'] ?></td>
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
            <h2 class="text-lg font-bold"><?= $editItem ? 'Edit Service' : 'Add New Service' ?></h2>
            <button onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','services.php')" class="text-gray-500 hover:text-white text-xl">&times;</button>
        </div>
        <?php if (!empty($errors)): ?><div class="flash flash-error mb-4"><?php foreach ($errors as $err): ?><?= e($err) ?><br><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="?action=<?= $editItem ? 'edit&id=' . $editItem['id'] : 'create' ?>">
            <?= csrfField() ?>
            <div class="mb-4"><label class="text-sm font-medium block mb-1.5">Title <span class="text-red-400">*</span></label><input type="text" name="title" class="form-input" value="<?= e($editItem['title'] ?? $_POST['title'] ?? '') ?>" required></div>
            <div class="mb-4"><label class="text-sm font-medium block mb-1.5">Description <span class="text-red-400">*</span></label><textarea name="description" class="form-input" rows="4" required><?= e($editItem['description'] ?? $_POST['description'] ?? '') ?></textarea></div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="text-sm font-medium block mb-1.5">Icon Class</label><input type="text" name="icon" class="form-input" placeholder="fas fa-sitemap" value="<?= e($editItem['icon'] ?? $_POST['icon'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Display Order</label><input type="number" name="display_order" class="form-input" min="0" value="<?= e($editItem['display_order'] ?? $_POST['display_order'] ?? '0') ?>"></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','services.php')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-<?= $editItem ? 'save' : 'plus' ?>"></i> <?= $editItem ? 'Update Service' : 'Create Service' ?></button>
            </div>
        </form>
    </div>
</div>

<?php if ($action === 'edit' && $editItem): ?><script>document.getElementById('formModal').classList.remove('hidden');</script><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
