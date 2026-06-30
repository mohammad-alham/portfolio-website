<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';

// Get statistics
$stats = [];
$stats['skills']       = dbRow("SELECT COUNT(*) as count FROM skills")['count'] ?? 0;
$stats['services']     = dbRow("SELECT COUNT(*) as count FROM services")['count'] ?? 0;
$stats['projects']     = dbRow("SELECT COUNT(*) as count FROM projects")['count'] ?? 0;
$stats['certificates'] = dbRow("SELECT COUNT(*) as count FROM certificates")['count'] ?? 0;
$stats['experience']   = dbRow("SELECT COUNT(*) as count FROM experience")['count'] ?? 0;
$stats['education']    = dbRow("SELECT COUNT(*) as count FROM education")['count'] ?? 0;
$stats['testimonials'] = dbRow("SELECT COUNT(*) as count FROM testimonials")['count'] ?? 0;
$stats['messages']     = dbRow("SELECT COUNT(*) as count FROM contact_messages")['count'] ?? 0;
$stats['unread']       = $unreadCount;

// Recent messages
$recentMessages = dbQuery("SELECT * FROM contact_messages ORDER BY created_at DESC LIMIT 5");

// Featured projects
$featuredProjects = dbQuery("SELECT * FROM projects WHERE featured = 1 ORDER BY created_at DESC LIMIT 5");

$flash = getFlash();
?>

<?php if ($flash): ?>
    <div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div>
<?php endif; ?>

<div class="flex items-center justify-between mb-8">
    <div>
        <h1 class="text-2xl font-bold">Dashboard</h1>
        <p class="text-gray-500 text-sm mt-1">Welcome back, <?= e($currentUser['full_name'] ?? 'Admin') ?></p>
    </div>
    <div class="text-sm text-gray-500">
        <i class="far fa-calendar mr-1"></i> <?= date('l, F j, Y') ?>
    </div>
</div>

<!-- Stats Cards -->
<div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-8">
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <span class="text-3xl text-network-400"><i class="fas fa-cogs"></i></span>
            <span class="badge badge-success"><?= $stats['skills'] ?></span>
        </div>
        <div class="text-2xl font-bold"><?= $stats['skills'] ?></div>
        <div class="text-xs text-gray-500">Skills</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <span class="text-3xl text-primary-400"><i class="fas fa-concierge-bell"></i></span>
            <span class="badge badge-success"><?= $stats['services'] ?></span>
        </div>
        <div class="text-2xl font-bold"><?= $stats['services'] ?></div>
        <div class="text-xs text-gray-500">Services</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <span class="text-3xl text-network-400"><i class="fas fa-project-diagram"></i></span>
            <span class="badge badge-success"><?= $stats['projects'] ?></span>
        </div>
        <div class="text-2xl font-bold"><?= $stats['projects'] ?></div>
        <div class="text-xs text-gray-500">Projects</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <span class="text-3xl text-primary-400"><i class="fas fa-certificate"></i></span>
            <span class="badge badge-success"><?= $stats['certificates'] ?></span>
        </div>
        <div class="text-2xl font-bold"><?= $stats['certificates'] ?></div>
        <div class="text-xs text-gray-500">Certificates</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <span class="text-3xl text-network-400"><i class="fas fa-briefcase"></i></span>
            <span class="badge badge-success"><?= $stats['experience'] ?></span>
        </div>
        <div class="text-2xl font-bold"><?= $stats['experience'] ?></div>
        <div class="text-xs text-gray-500">Experience</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <span class="text-3xl text-primary-400"><i class="fas fa-graduation-cap"></i></span>
            <span class="badge badge-success"><?= $stats['education'] ?></span>
        </div>
        <div class="text-2xl font-bold"><?= $stats['education'] ?></div>
        <div class="text-xs text-gray-500">Education</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <span class="text-3xl text-network-400"><i class="fas fa-comment"></i></span>
            <span class="badge badge-success"><?= $stats['testimonials'] ?></span>
        </div>
        <div class="text-2xl font-bold"><?= $stats['testimonials'] ?></div>
        <div class="text-xs text-gray-500">Testimonials</div>
    </div>
    <div class="stat-card">
        <div class="flex items-center justify-between mb-2">
            <span class="text-3xl text-yellow-400"><i class="fas fa-envelope"></i></span>
            <span class="badge <?= $stats['unread'] > 0 ? 'badge-warning' : 'badge-success' ?>">
                <?= $stats['unread'] ?> unread
            </span>
        </div>
        <div class="text-2xl font-bold"><?= $stats['messages'] ?></div>
        <div class="text-xs text-gray-500">Messages</div>
    </div>
</div>

<div class="grid lg:grid-cols-2 gap-6">
    <!-- Recent Messages -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
            <h2 class="font-bold">Recent Messages</h2>
            <a href="messages.php" class="text-sm text-network-400 hover:text-network-300">View All <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
        </div>
        <div class="p-4">
            <?php if (empty($recentMessages)): ?>
                <p class="text-gray-500 text-sm text-center py-6">No messages yet.</p>
            <?php else: ?>
                <?php foreach ($recentMessages as $msg): ?>
                    <div class="flex items-start gap-3 p-3 rounded-lg hover:bg-gray-800/50 transition-colors">
                        <div class="w-9 h-9 rounded-full bg-network-500/10 flex items-center justify-center text-network-400 flex-shrink-0">
                            <?= strtoupper(substr(e($msg['name']), 0, 1)) ?>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <span class="font-medium text-sm"><?= e($msg['name'] ?? '') ?></span>
                                <span class="text-xs text-gray-500"><?= date('M j, g:i A', strtotime($msg['created_at'] ?? '')) ?></span>
                            </div>
                            <p class="text-xs text-gray-500 mt-0.5 truncate"><?= e($msg['subject'] ?? '(No subject)') ?></p>
                            <p class="text-xs text-gray-600 mt-0.5 truncate"><?= e(truncate($msg['message'] ?? '', 80)) ?></p>
                        </div>
                        <?php if (!$msg['is_read']): ?>
                            <span class="w-2 h-2 rounded-full bg-network-400 mt-2 flex-shrink-0"></span>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <!-- Featured Projects -->
    <div class="stat-card p-0 overflow-hidden">
        <div class="flex items-center justify-between px-6 py-4 border-b border-gray-800">
            <h2 class="font-bold">Featured Projects</h2>
            <a href="projects.php" class="text-sm text-network-400 hover:text-network-300">Manage <i class="fas fa-arrow-right ml-1 text-xs"></i></a>
        </div>
        <div class="p-4">
            <?php if (empty($featuredProjects)): ?>
                <p class="text-gray-500 text-sm text-center py-6">No featured projects yet.</p>
            <?php else: ?>
                <?php foreach ($featuredProjects as $proj): ?>
                    <div class="flex items-center gap-3 p-3 rounded-lg hover:bg-gray-800/50 transition-colors">
                        <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-network-800 to-primary-900 flex items-center justify-center text-network-400 flex-shrink-0">
                            <i class="fas fa-network-wired"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <span class="font-medium text-sm"><?= e($proj['title'] ?? '') ?></span>
                            <p class="text-xs text-gray-500 truncate mt-0.5"><?= e(truncate($proj['description'] ?? '', 80)) ?></p>
                        </div>
                        <a href="projects.php?action=edit&id=<?= $proj['id'] ?>" class="text-xs text-network-400 hover:text-network-300">
                            <i class="fas fa-edit"></i>
                        </a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
