<?php
$pageTitle = 'Contact Messages';

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$action = $_GET['action'] ?? 'list';
$id     = (int)($_GET['id'] ?? 0);

if (isset($_GET['read']) && $id > 0) {
    checkCsrfToken();
    dbExecute("UPDATE contact_messages SET is_read=1 WHERE id=?", [$id]);
    header('Location: messages.php'); exit;
}
if (isset($_GET['unread']) && $id > 0) {
    checkCsrfToken();
    dbExecute("UPDATE contact_messages SET is_read=0 WHERE id=?", [$id]);
    header('Location: messages.php'); exit;
}
if (isset($_GET['delete']) && $id > 0) {
    checkCsrfToken();
    dbExecute("DELETE FROM contact_messages WHERE id=?", [$id]);
    setFlash('success', 'Message deleted successfully.');
    header('Location: messages.php'); exit;
}

require_once __DIR__ . '/includes/header.php';

$flash = getFlash();
$items = dbQuery("SELECT * FROM contact_messages ORDER BY created_at DESC");
$viewMessage = ($id > 0 && $action === 'view') ? dbRow("SELECT * FROM contact_messages WHERE id=?", [$id]) : null;

if ($viewMessage && !$viewMessage['is_read']) {
    dbExecute("UPDATE contact_messages SET is_read=1 WHERE id=?", [$id]);
    $viewMessage['is_read'] = 1;
}
?>

<?php if ($flash): ?><div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div><?php endif; ?>

<div class="flex items-center justify-between mb-6">
    <div><h1 class="text-2xl font-bold"><?= $viewMessage ? 'Message Details' : 'Contact Messages' ?></h1>
        <p class="text-gray-500 text-sm mt-1"><?= $viewMessage ? 'Viewing message from ' . e($viewMessage['name']) : 'Messages from your contact form' ?></p></div>
    <?php if ($viewMessage): ?>
        <a href="messages.php" class="btn btn-outline"><i class="fas fa-arrow-left"></i> Back to Messages</a>
    <?php endif; ?>
</div>

<?php if ($viewMessage): ?>
    <div class="stat-card p-6 max-w-3xl">
        <div class="flex items-start justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold"><?= e($viewMessage['subject'] ?? '(No Subject)') ?></h2>
                <p class="text-sm text-gray-500 mt-1">From <strong><?= e($viewMessage['name']) ?></strong> &lt;<?= e($viewMessage['email']) ?>&gt;</p>
                <p class="text-xs text-gray-600 mt-1"><i class="far fa-clock mr-1"></i> <?= date('F j, Y \a\t g:i A', strtotime($viewMessage['created_at'])) ?></p>
            </div>
            <div class="flex gap-2">
                <a href="?unread=1&id=<?= $viewMessage['id'] ?>&csrf_token=<?= getCsrfToken() ?>" class="btn btn-sm btn-outline" title="Mark as unread"><i class="fas fa-envelope"></i></a>
                <a href="?delete=1&id=<?= $viewMessage['id'] ?>&csrf_token=<?= getCsrfToken() ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
            </div>
        </div>
        <div class="border-t border-gray-800 pt-6">
            <p class="text-gray-300 leading-relaxed whitespace-pre-wrap"><?= e($viewMessage['message']) ?></p>
        </div>
        <div class="mt-8 pt-6 border-t border-gray-800">
            <a href="mailto:<?= e($viewMessage['email']) ?>?subject=Re: <?= e($viewMessage['subject'] ?? 'Your Inquiry') ?>" class="btn btn-primary"><i class="fas fa-reply"></i> Reply via Email</a>
        </div>
    </div>
<?php else: ?>
    <div class="stat-card p-0 overflow-hidden">
        <div class="table-container">
            <table>
                <thead><tr><th>Name</th><th>Email</th><th>Subject</th><th>Date</th><th>Status</th><th class="text-right">Actions</th></tr></thead>
                <tbody>
                    <?php if (empty($items)): ?><tr><td colspan="6" class="text-center text-gray-500 py-10">No messages yet.</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $msg): ?>
                            <tr class="<?= !$msg['is_read'] ? 'font-medium' : '' ?>">
                                <td><?= e($msg['name']) ?></td>
                                <td class="text-gray-400 text-sm"><?= e($msg['email']) ?></td>
                                <td class="max-w-xs truncate"><?= e($msg['subject'] ?? '(No subject)') ?></td>
                                <td class="text-gray-400 text-sm whitespace-nowrap"><?= date('M j, Y', strtotime($msg['created_at'])) ?></td>
                                <td><?= $msg['is_read'] ? '<span class="badge badge-success">Read</span>' : '<span class="badge badge-warning">New</span>' ?></td>
                                <td class="text-right whitespace-nowrap">
                                    <a href="?action=view&id=<?= $msg['id'] ?>" class="btn btn-sm btn-outline"><i class="fas fa-eye"></i></a>
                                    <?php if (!$msg['is_read']): ?>
                                        <a href="?read=1&id=<?= $msg['id'] ?>&csrf_token=<?= getCsrfToken() ?>" class="btn btn-sm btn-outline"><i class="fas fa-check"></i></a>
                                    <?php endif; ?>
                                    <a href="?delete=1&id=<?= $msg['id'] ?>&csrf_token=<?= getCsrfToken() ?>" class="btn btn-sm btn-danger confirm-delete"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
