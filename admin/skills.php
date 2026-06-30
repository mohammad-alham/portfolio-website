<?php
$pageTitle = 'Manage Skills';

// ─── Process actions BEFORE output ────────────────────────────
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrfToken();
    $name       = sanitize($_POST['name'] ?? '');
    $category   = sanitize($_POST['category'] ?? '');
    $percentage = (int)($_POST['percentage'] ?? 0);
    $icon       = sanitize($_POST['icon'] ?? '');
    $order      = (int)($_POST['display_order'] ?? 0);
    if (empty($name)) $errors[] = 'Skill name is required.';
    if (empty($category)) $errors[] = 'Category is required.';
    if ($percentage < 0 || $percentage > 100) $errors[] = 'Percentage must be 0-100.';
    if (empty($errors)) {
        if ($action === 'create') {
            dbInsert("INSERT INTO skills (name, category, percentage, icon, display_order) VALUES (?,?,?,?,?)", [$name, $category, $percentage, $icon, $order]);
            setFlash('success', 'Skill created successfully.');
        } elseif ($action === 'edit' && $id > 0) {
            dbExecute("UPDATE skills SET name=?, category=?, percentage=?, icon=?, display_order=? WHERE id=?", [$name, $category, $percentage, $icon, $order, $id]);
            setFlash('success', 'Skill updated successfully.');
        }
        header('Location: skills.php'); exit;
    }
}

if (isset($_GET['delete']) && $id > 0) {
    checkCsrfToken();
    dbExecute("DELETE FROM skills WHERE id=?", [$id]);
    setFlash('success', 'Skill deleted successfully.');
    header('Location: skills.php'); exit;
}

// ─── Include header (output starts) ───────────────────────────
require_once __DIR__ . '/includes/header.php';

$flash = getFlash();
$skills = dbQuery("SELECT * FROM skills ORDER BY display_order ASC");
$editSkill = ($id > 0) ? dbRow("SELECT * FROM skills WHERE id=?", [$id]) : null;
$categories = ['Networking', 'Security', 'Server', 'Cloud'];
?>

<?php if ($flash): ?><div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div><?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-2xl font-bold">Skills</h1><p class="text-gray-500 text-sm mt-1">Manage your technical skills and expertise</p></div>
    <button onclick="document.getElementById('formModal').classList.remove('hidden')" class="btn btn-primary"><i class="fas fa-plus"></i> Add Skill</button>
</div>

<div class="stat-card p-0 overflow-hidden">
    <div class="table-container">
        <table>
            <thead><tr><th>Name</th><th>Category</th><th>Progress</th><th>Icon</th><th>Order</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
                <?php if (empty($skills)): ?>
                    <tr><td colspan="6" class="text-center text-gray-500 py-10">No skills found.</td></tr>
                <?php else: ?>
                    <?php foreach ($skills as $skill): ?>
                        <tr>
                            <td class="font-medium"><?= e($skill['name']) ?></td>
                            <td><span class="badge" style="background:rgba(6,182,212,0.1);color:#06b6d4;"><?= e($skill['category']) ?></span></td>
                            <td>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 h-1.5 rounded-full bg-gray-800"><div class="h-full rounded-full bg-gradient-to-r from-network-500 to-primary-500" style="width:<?= $skill['percentage'] ?>%"></div></div>
                                    <span class="text-xs text-gray-500 w-8 text-right"><?= $skill['percentage'] ?>%</span>
                                </div>
                            </td>
                            <td class="text-lg"><?= $skill['icon'] ? '<i class="' . e($skill['icon']) . '"></i>' : '-' ?></td>
                            <td class="text-gray-500"><?= $skill['display_order'] ?></td>
                            <td class="text-right">
                                <a href="?action=edit&id=<?= $skill['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-edit"></i></a>
                                <a href="?delete=1&id=<?= $skill['id'] ?>&csrf_token=<?= getCsrfToken() ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<div id="formModal" class="fixed inset-0 z-50 flex items-center justify-center p-4 <?= ($action === 'edit' && $editSkill) || !empty($errors) ? '' : 'hidden' ?>" style="background:rgba(0,0,0,0.7);backdrop-filter:blur(4px);">
    <div class="bg-[#12121a] border border-gray-800 rounded-2xl p-6 w-full max-w-lg max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold"><?= $editSkill ? 'Edit Skill' : 'Add New Skill' ?></h2>
            <button onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','skills.php')" class="text-gray-500 hover:text-white text-xl">&times;</button>
        </div>
        <?php if (!empty($errors)): ?><div class="flash flash-error mb-4"><?php foreach ($errors as $err): ?><?= e($err) ?><br><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="?action=<?= $editSkill ? 'edit&id=' . $editSkill['id'] : 'create' ?>">
            <?= csrfField() ?>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-sm font-medium block mb-1.5">Skill Name <span class="text-red-400">*</span></label>
                    <input type="text" name="name" class="form-input" value="<?= e($editSkill['name'] ?? $_POST['name'] ?? '') ?>" required>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-sm font-medium block mb-1.5">Category <span class="text-red-400">*</span></label>
                    <select name="category" class="form-input" required>
                        <option value="">Select...</option>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat ?>" <?= (($editSkill['category'] ?? $_POST['category'] ?? '') === $cat) ? 'selected' : '' ?>><?= $cat ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div><label class="text-sm font-medium block mb-1.5">Percentage <span class="text-red-400">*</span></label><input type="number" name="percentage" class="form-input" min="0" max="100" value="<?= e($editSkill['percentage'] ?? $_POST['percentage'] ?? '85') ?>" required></div>
                <div><label class="text-sm font-medium block mb-1.5">Display Order</label><input type="number" name="display_order" class="form-input" min="0" value="<?= e($editSkill['display_order'] ?? $_POST['display_order'] ?? '0') ?>"></div>
                <div class="col-span-2"><label class="text-sm font-medium block mb-1.5">Icon Class</label><input type="text" name="icon" class="form-input" placeholder="fas fa-network-wired" value="<?= e($editSkill['icon'] ?? $_POST['icon'] ?? '') ?>"></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','skills.php')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-<?= $editSkill ? 'save' : 'plus' ?>"></i> <?= $editSkill ? 'Update Skill' : 'Create Skill' ?></button>
            </div>
        </form>
    </div>
</div>

<?php if ($action === 'edit' && $editSkill): ?><script>document.getElementById('formModal').classList.remove('hidden');</script><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
