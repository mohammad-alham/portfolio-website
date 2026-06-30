<?php
$pageTitle = 'Manage Experience';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrfToken();
    $company     = sanitize($_POST['company_name'] ?? '');
    $job_title   = sanitize($_POST['job_title'] ?? '');
    $start_date  = sanitize($_POST['start_date'] ?? '');
    $end_date    = sanitize($_POST['end_date'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    if (empty($company)) $errors[] = 'Company name is required.';
    if (empty($job_title)) $errors[] = 'Job title is required.';
    if (empty($start_date)) $errors[] = 'Start date is required.';
    if (empty($errors)) {
        if ($action === 'create') {
            dbInsert("INSERT INTO experience (company_name, job_title, start_date, end_date, description) VALUES (?,?,?,?,?)",
                [$company, $job_title, $start_date, $end_date ?: null, $description]);
            setFlash('success', 'Experience added successfully.');
        } elseif ($action === 'edit' && $id > 0) {
            dbExecute("UPDATE experience SET company_name=?, job_title=?, start_date=?, end_date=?, description=? WHERE id=?",
                [$company, $job_title, $start_date, $end_date ?: null, $description, $id]);
            setFlash('success', 'Experience updated successfully.');
        }
        header('Location: experience.php'); exit;
    }
}

if (isset($_GET['delete']) && $id > 0) {
    checkCsrfToken();
    dbExecute("DELETE FROM experience WHERE id=?", [$id]);
    setFlash('success', 'Experience deleted successfully.');
    header('Location: experience.php'); exit;
}

require_once __DIR__ . '/includes/header.php';

$flash = getFlash();
$items = dbQuery("SELECT * FROM experience ORDER BY start_date DESC");
$editItem = ($id > 0) ? dbRow("SELECT * FROM experience WHERE id=?", [$id]) : null;
?>

<?php if ($flash): ?><div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div><?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-2xl font-bold">Experience</h1><p class="text-gray-500 text-sm mt-1">Manage your work history</p></div>
    <button onclick="document.getElementById('formModal').classList.remove('hidden')" class="btn btn-primary"><i class="fas fa-plus"></i> Add Experience</button>
</div>

<div class="stat-card p-0 overflow-hidden">
    <div class="table-container">
        <table>
            <thead><tr><th>Company</th><th>Job Title</th><th>Period</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
                <?php if (empty($items)): ?><tr><td colspan="4" class="text-center text-gray-500 py-10">No experience entries found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="font-medium"><?= e($item['company_name']) ?></td>
                            <td><?= e($item['job_title']) ?></td>
                            <td class="text-gray-400 text-sm"><?= formatDate($item['start_date'], 'M Y') ?> - <?= formatDate($item['end_date'], 'M Y') ?></td>
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
            <h2 class="text-lg font-bold"><?= $editItem ? 'Edit Experience' : 'Add Experience' ?></h2>
            <button onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','experience.php')" class="text-gray-500 hover:text-white text-xl">&times;</button>
        </div>
        <?php if (!empty($errors)): ?><div class="flash flash-error mb-4"><?php foreach ($errors as $err): ?><?= e($err) ?><br><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="?action=<?= $editItem ? 'edit&id=' . $editItem['id'] : 'create' ?>">
            <?= csrfField() ?>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-2 sm:col-span-1"><label class="text-sm font-medium block mb-1.5">Company Name <span class="text-red-400">*</span></label><input type="text" name="company_name" class="form-input" value="<?= e($editItem['company_name'] ?? $_POST['company_name'] ?? '') ?>" required></div>
                <div class="col-span-2 sm:col-span-1"><label class="text-sm font-medium block mb-1.5">Job Title <span class="text-red-400">*</span></label><input type="text" name="job_title" class="form-input" value="<?= e($editItem['job_title'] ?? $_POST['job_title'] ?? '') ?>" required></div>
                <div><label class="text-sm font-medium block mb-1.5">Start Date <span class="text-red-400">*</span></label><input type="date" name="start_date" class="form-input" value="<?= e($editItem['start_date'] ?? $_POST['start_date'] ?? '') ?>" required></div>
                <div><label class="text-sm font-medium block mb-1.5">End Date</label><input type="date" name="end_date" class="form-input" value="<?= e($editItem['end_date'] ?? $_POST['end_date'] ?? '') ?>"></div>
                <div class="col-span-2"><label class="text-sm font-medium block mb-1.5">Description</label><textarea name="description" class="form-input" rows="4"><?= e($editItem['description'] ?? $_POST['description'] ?? '') ?></textarea></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','experience.php')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-<?= $editItem ? 'save' : 'plus' ?>"></i> <?= $editItem ? 'Update Experience' : 'Add Experience' ?></button>
            </div>
        </form>
    </div>
</div>

<?php if ($action === 'edit' && $editItem): ?><script>document.getElementById('formModal').classList.remove('hidden');</script><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
