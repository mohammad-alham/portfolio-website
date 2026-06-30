<?php
$pageTitle = 'Manage Certificates';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    checkCsrfToken();
    $title       = sanitize($_POST['title'] ?? '');
    $issuer      = sanitize($_POST['issuer'] ?? '');
    $issue_date  = sanitize($_POST['issue_date'] ?? '');
    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($issuer)) $errors[] = 'Issuer is required.';
    $certFile = null;
    $certImage = null;
    if (!empty($_FILES['certificate_file']['name'])) $certFile = uploadFile($_FILES['certificate_file'], __DIR__ . '/../assets/uploads/certificates', ['pdf', 'jpg', 'jpeg', 'png']);
    if (!empty($_FILES['certificate_image']['name'])) $certImage = uploadFile($_FILES['certificate_image'], __DIR__ . '/../assets/uploads/certificates');
    if (empty($errors)) {
        if ($action === 'create') {
            dbInsert("INSERT INTO certificates (title, issuer, issue_date, certificate_file, certificate_image) VALUES (?,?,?,?,?)", [$title, $issuer, $issue_date ?: null, $certFile, $certImage]);
            setFlash('success', 'Certificate created successfully.');
        } elseif ($action === 'edit' && $id > 0) {
            $sql = "UPDATE certificates SET title=?, issuer=?, issue_date=?"; $params = [$title, $issuer, $issue_date ?: null];
            if ($certFile) { $sql .= ", certificate_file=?"; $params[] = $certFile; }
            if ($certImage) { $sql .= ", certificate_image=?"; $params[] = $certImage; }
            $sql .= " WHERE id=?"; $params[] = $id;
            dbExecute($sql, $params);
            setFlash('success', 'Certificate updated successfully.');
        }
        header('Location: certificates.php'); exit;
    }
}

if (isset($_GET['delete']) && $id > 0) {
    checkCsrfToken();
    $item = dbRow("SELECT * FROM certificates WHERE id=?", [$id]);
    if ($item) {
        if ($item['certificate_file'] && file_exists($item['certificate_file'])) unlink($item['certificate_file']);
        if ($item['certificate_image'] && file_exists($item['certificate_image'])) unlink($item['certificate_image']);
    }
    dbExecute("DELETE FROM certificates WHERE id=?", [$id]);
    setFlash('success', 'Certificate deleted successfully.');
    header('Location: certificates.php'); exit;
}

require_once __DIR__ . '/includes/header.php';

$flash = getFlash();
$items = dbQuery("SELECT * FROM certificates ORDER BY issue_date DESC");
$editItem = ($id > 0) ? dbRow("SELECT * FROM certificates WHERE id=?", [$id]) : null;
?>

<?php if ($flash): ?><div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div><?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-2xl font-bold">Certificates</h1><p class="text-gray-500 text-sm mt-1">Manage your certifications</p></div>
    <button onclick="document.getElementById('formModal').classList.remove('hidden')" class="btn btn-primary"><i class="fas fa-plus"></i> Add Certificate</button>
</div>

<div class="stat-card p-0 overflow-hidden">
    <div class="table-container">
        <table>
            <thead><tr><th>Title</th><th>Issuer</th><th>Issue Date</th><th>File</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
                <?php if (empty($items)): ?><tr><td colspan="5" class="text-center text-gray-500 py-10">No certificates found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="font-medium"><?= e($item['title']) ?></td>
                            <td><?= e($item['issuer']) ?></td>
                            <td class="text-gray-500 text-sm"><?= $item['issue_date'] ? date('M Y', strtotime($item['issue_date'])) : '-' ?></td>
                            <td><?= $item['certificate_file'] ? '<span class="badge badge-success"><i class="fas fa-file mr-1"></i>File</span>' : '-' ?></td>
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
            <h2 class="text-lg font-bold"><?= $editItem ? 'Edit Certificate' : 'Add New Certificate' ?></h2>
            <button onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','certificates.php')" class="text-gray-500 hover:text-white text-xl">&times;</button>
        </div>
        <?php if (!empty($errors)): ?><div class="flash flash-error mb-4"><?php foreach ($errors as $err): ?><?= e($err) ?><br><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="?action=<?= $editItem ? 'edit&id=' . $editItem['id'] : 'create' ?>" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="mb-4"><label class="text-sm font-medium block mb-1.5">Title <span class="text-red-400">*</span></label><input type="text" name="title" class="form-input" value="<?= e($editItem['title'] ?? $_POST['title'] ?? '') ?>" required></div>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div><label class="text-sm font-medium block mb-1.5">Issuer <span class="text-red-400">*</span></label><input type="text" name="issuer" class="form-input" value="<?= e($editItem['issuer'] ?? $_POST['issuer'] ?? '') ?>" required></div>
                <div><label class="text-sm font-medium block mb-1.5">Issue Date</label><input type="date" name="issue_date" class="form-input" value="<?= e($editItem['issue_date'] ?? $_POST['issue_date'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">File (PDF)</label><input type="file" name="certificate_file" class="form-input text-sm" accept=".pdf,.jpg,.jpeg,.png"></div>
                <div><label class="text-sm font-medium block mb-1.5">Image</label><input type="file" name="certificate_image" class="form-input text-sm" accept="image/*"></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','certificates.php')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-<?= $editItem ? 'save' : 'plus' ?>"></i> <?= $editItem ? 'Update Certificate' : 'Create Certificate' ?></button>
            </div>
        </form>
    </div>
</div>

<?php if ($action === 'edit' && $editItem): ?><script>document.getElementById('formModal').classList.remove('hidden');</script><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
