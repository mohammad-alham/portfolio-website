<?php
$pageTitle = 'Manage Projects';

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
    $tech_stack  = sanitize($_POST['technology_stack'] ?? '');
    $project_url = sanitize($_POST['project_url'] ?? '');
    $github_url  = sanitize($_POST['github_url'] ?? '');
    $featured    = isset($_POST['featured']) ? 1 : 0;
    $slug        = slugify($title);
    if (empty($title)) $errors[] = 'Title is required.';
    if (empty($description)) $errors[] = 'Description is required.';
    $thumbnail = null;
    if (!empty($_FILES['thumbnail']['name'])) {
        $thumbnail = uploadFile($_FILES['thumbnail'], __DIR__ . '/../assets/uploads/projects');
    }
    if (empty($errors)) {
        if ($action === 'create') {
            $pid = dbInsert("INSERT INTO projects (title, slug, description, technology_stack, project_url, github_url, featured) VALUES (?,?,?,?,?,?,?)",
                [$title, $slug, $description, $tech_stack, $project_url, $github_url, $featured]);
            if ($thumbnail) dbExecute("UPDATE projects SET thumbnail=? WHERE id=?", [$thumbnail, $pid]);
            setFlash('success', 'Project created successfully.');
        } elseif ($action === 'edit' && $id > 0) {
            $sql = "UPDATE projects SET title=?, slug=?, description=?, technology_stack=?, project_url=?, github_url=?, featured=?";
            $params = [$title, $slug, $description, $tech_stack, $project_url, $github_url, $featured];
            if ($thumbnail) { $sql .= ", thumbnail=?"; $params[] = $thumbnail; }
            $sql .= " WHERE id=?"; $params[] = $id;
            dbExecute($sql, $params);
            setFlash('success', 'Project updated successfully.');
        }
        header('Location: projects.php'); exit;
    }
}

if (isset($_GET['delete']) && $id > 0) {
    checkCsrfToken();
    $project = dbRow("SELECT * FROM projects WHERE id=?", [$id]);
    if ($project && $project['thumbnail'] && file_exists($project['thumbnail'])) unlink($project['thumbnail']);
    dbExecute("DELETE FROM projects WHERE id=?", [$id]);
    setFlash('success', 'Project deleted successfully.');
    header('Location: projects.php'); exit;
}

require_once __DIR__ . '/includes/header.php';

$flash = getFlash();
$items = dbQuery("SELECT * FROM projects ORDER BY created_at DESC");
$editItem = ($id > 0) ? dbRow("SELECT * FROM projects WHERE id=?", [$id]) : null;
?>

<?php if ($flash): ?><div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div><?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-2xl font-bold">Projects</h1><p class="text-gray-500 text-sm mt-1">Manage your project portfolio</p></div>
    <button onclick="document.getElementById('formModal').classList.remove('hidden')" class="btn btn-primary"><i class="fas fa-plus"></i> Add Project</button>
</div>

<div class="stat-card p-0 overflow-hidden">
    <div class="table-container">
        <table>
            <thead><tr><th>Title</th><th>Tech Stack</th><th>Featured</th><th>Created</th><th class="text-right">Actions</th></tr></thead>
            <tbody>
                <?php if (empty($items)): ?><tr><td colspan="5" class="text-center text-gray-500 py-10">No projects found.</td></tr>
                <?php else: ?>
                    <?php foreach ($items as $item): ?>
                        <tr>
                            <td class="font-medium"><?= e($item['title']) ?></td>
                            <td class="text-gray-400 text-xs max-w-xs truncate"><?= e($item['technology_stack'] ?? '-') ?></td>
                            <td><?= $item['featured'] ? '<span class="badge badge-success">Featured</span>' : '<span class="badge badge-danger">No</span>' ?></td>
                            <td class="text-gray-500 text-sm"><?= date('M j, Y', strtotime($item['created_at'])) ?></td>
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
    <div class="bg-[#12121a] border border-gray-800 rounded-2xl p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-lg font-bold"><?= $editItem ? 'Edit Project' : 'Add New Project' ?></h2>
            <button onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','projects.php')" class="text-gray-500 hover:text-white text-xl">&times;</button>
        </div>
        <?php if (!empty($errors)): ?><div class="flash flash-error mb-4"><?php foreach ($errors as $err): ?><?= e($err) ?><br><?php endforeach; ?></div><?php endif; ?>
        <form method="POST" action="?action=<?= $editItem ? 'edit&id=' . $editItem['id'] : 'create' ?>" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-2"><label class="text-sm font-medium block mb-1.5">Title <span class="text-red-400">*</span></label><input type="text" name="title" class="form-input" value="<?= e($editItem['title'] ?? $_POST['title'] ?? '') ?>" required></div>
                <div class="col-span-2"><label class="text-sm font-medium block mb-1.5">Description <span class="text-red-400">*</span></label><textarea name="description" class="form-input" rows="5" required><?= e($editItem['description'] ?? $_POST['description'] ?? '') ?></textarea></div>
                <div class="col-span-2"><label class="text-sm font-medium block mb-1.5">Technology Stack</label><input type="text" name="technology_stack" class="form-input" placeholder="Cisco, OSPF, MPLS, QoS" value="<?= e($editItem['technology_stack'] ?? $_POST['technology_stack'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Project URL</label><input type="url" name="project_url" class="form-input" value="<?= e($editItem['project_url'] ?? $_POST['project_url'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">GitHub URL</label><input type="url" name="github_url" class="form-input" value="<?= e($editItem['github_url'] ?? $_POST['github_url'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Thumbnail Image</label><input type="file" name="thumbnail" class="form-input text-sm" accept="image/*"></div>
                <div class="flex items-center"><label class="flex items-center gap-3 cursor-pointer"><input type="checkbox" name="featured" value="1" <?= (isset($editItem['featured']) && $editItem['featured']) ? 'checked' : '' ?> class="w-4 h-4 rounded border-gray-600 bg-gray-800 text-network-500"><span class="text-sm font-medium">Featured Project</span></label></div>
            </div>
            <div class="flex justify-end gap-3 mt-6">
                <button type="button" onclick="document.getElementById('formModal').classList.add('hidden');window.history.replaceState({},'','projects.php')" class="btn btn-outline">Cancel</button>
                <button type="submit" class="btn btn-primary"><i class="fas fa-<?= $editItem ? 'save' : 'plus' ?>"></i> <?= $editItem ? 'Update Project' : 'Create Project' ?></button>
            </div>
        </form>
    </div>
</div>

<?php if ($action === 'edit' && $editItem): ?><script>document.getElementById('formModal').classList.remove('hidden');</script><?php endif; ?>
<?php require_once __DIR__ . '/includes/footer.php'; ?>
