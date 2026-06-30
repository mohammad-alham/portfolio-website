<?php
$pageTitle = 'Manage Education';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrfToken();
    $institution = sanitize($_POST['institution'] ?? '');
    $degree      = sanitize($_POST['degree'] ?? '');
    $field       = sanitize($_POST['field_of_study'] ?? '');
    $start_year  = (int)($_POST['start_year'] ?? 0);
    $end_year    = !empty($_POST['end_year']) ? (int)$_POST['end_year'] : null;
    if (empty($institution)) $errors[] = 'Institution is required.';
    if (empty($degree)) $errors[] = 'Degree is required.';
    if ($start_year < 1900 || $start_year > 2100) $errors[] = 'Invalid start year.';
    if (empty($errors)) {
        if ($action === 'create') {
            dbInsert("INSERT INTO education (institution, degree, field_of_study, start_year, end_year) VALUES (?,?,?,?,?)", [$institution, $degree, $field, $start_year, $end_year]);
            setFlash('success', 'Education added successfully.');
        } elseif ($action === 'edit' && $id > 0) {
            dbExecute("UPDATE education SET institution=?, degree=?, field_of_study=?, start_year=?, end_year=? WHERE id=?", [$institution, $degree, $field, $start_year, $end_year, $id]);
            setFlash('success', 'Education updated successfully.');
        }
        header('Location: education.php'); exit;
    }
}

if (isset($_GET['delete']) && $id > 0) {
    checkCsrfToken();
    dbExecute("DELETE FROM education WHERE id=?", [$id]);
    setFlash('success', 'Education deleted successfully.');
    header('Location: education.php'); exit;
}

require_once __DIR__ . '/includes/header.php';

$flash = getFlash();
$items = dbQuery("SELECT * FROM education ORDER BY start_year DESC");
$editItem = ($id > 0) ? dbRow("SELECT * FROM education WHERE id=?", [$id]) : null;
?>

<?php if ($flash): ?><div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div><?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-2xl font-bold">Education</h1><p class="text-gray-500 text-sm mt-1">Manage your educational background</p></div>
    <button onclick="document.getElementById('formModal').classList.remove('hidden')" class="btn btn-primary"><i class="fas fa-plus"></i> Add Education</button>
</div>

<div class="stat-card p-0 overflow-hidden">
    <div class="table-container">
        <table>
            <thead><tr><th>Institution</th><th>Degree</th><th>Field</th><th>Years</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
                <?php if (empty($items)): ?><tr><td colspan="5" class="text-center text-gray-500 py-10">No education entries found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="font-medium"><?= e($item['institution']) ?></td>
                            <td><?= e($item['degree']) ?></td>
                            <td class="text-gray-400"><?= e($item['field_of_study'] ?? '-') ?></td>
                            <td class="text-gray-400 text-sm"><?= e($item['start_year']) ?> - <?= e($item['end_year'] ?? 'Present') ?></td>
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
            <h2 class="text-lg font-bold"><?= $editItem ? 'Edit Education' : 'Add Education' ?></h2>
            <button onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','education.php')" class="text-gray-500 hover:text-white text-xl">&times;</button>
        </div>
        <?php if (!empty($errors)): ?><div class="flash flash-error mb-4"><?php foreach ($errors as $err): ?><?= e($err) ?><br><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="?action=<?= $editItem ? 'edit&id=' . $editItem['id'] : 'create' ?>">
            <?= csrfField() ?>
            <div class="mb-4"><label class="text-sm font-medium block mb-1.5">Institution <span class="text-red-400">*</span></label><input type="text" name="institution" class="form-input" value="<?= e($editItem['institution'] ?? $_POST['institution'] ?? '') ?>" required></div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="text-sm font-medium block mb-1.5">Degree <span class="text-red-400">*</span></label><input type="text" name="degree" class="form-input" value="<?= e($editItem['degree'] ?? $_POST['degree'] ?? '') ?>" required></div>
                <div><label class="text-sm font-medium block mb-1.5">Field of Study</label><input type="text" name="field_of_study" class="form-input" value="<?= e($editItem['field_of_study'] ?? $_POST['field_of_study'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Start Year <span class="text-red-400">*</span></label><input type="number" name="start_year" class="form-input" min="1900" max="2100" value="<?= e($editItem['start_year'] ?? $_POST['start_year'] ?? '2010') ?>" required></div>
                <div><label class="text-sm font-medium block mb-1.5">End Year</label><input type="number" name="end_year" class="form-input" min="1900" max="2100" value="<?= e($editItem['end_year'] ?? $_POST['end_year'] ?? '') ?>"></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','education.php')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-<?= $editItem ? 'save' : 'plus' ?>"></i> <?= $editItem ? 'Update Education' : 'Add Education' ?></button>
            </div>
        </form>
    </div>
</div>

<?php if ($action === 'edit' && $editItem): ?><script>document.getElementById('formModal').classList.remove('hidden');</script><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
