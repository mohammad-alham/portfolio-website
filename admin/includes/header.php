<?php
require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/functions.php';

// Require authentication for admin pages
if (basename($_SERVER['PHP_SELF']) !== 'login.php') {
    requireAuth();
}

$siteSettings = getSiteSettings();
$unreadCount = getUnreadMessageCount();
$currentUser = getCurrentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Panel | <?= e($siteSettings['site_name'] ?? 'Portfolio') ?></title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: { 50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc', 400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca', 800: '#3730a3', 900: '#312e81' },
                        network: { 50: '#ecfeff', 100: '#cffafe', 200: '#a5f3fc', 300: '#67e8f9', 400: '#22d3ee', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490', 800: '#155e75', 900: '#164e63' }
                    },
                    fontFamily: { sans: ['Inter', 'sans-serif'], mono: ['JetBrains Mono', 'monospace'] }
                }
            }
        }
    </script>
    <style>
        :root {
            --bg-dark: #0a0a0f; --bg-card: #12121a; --bg-card-2: #1a1a2e;
            --border: #2a2a3e; --text: #e2e8f0; --text-muted: #94a3b8;
            --primary: #6366f1; --network: #06b6d4;
        }
        * { margin:0; padding:0; box-sizing:border-box; }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg-dark);
            color: var(--text);
            overflow-x: hidden;
        }
        ::-webkit-scrollbar { width:6px; }
        ::-webkit-scrollbar-track { background:var(--bg-dark); }
        ::-webkit-scrollbar-thumb { background:var(--border); border-radius:3px; }

        .sidebar { width:260px; height:100vh; position:fixed; left:0; top:0; background:var(--bg-card); border-right:1px solid var(--border); overflow-y:auto; z-index:100; transition:transform 0.3s; }
        .main-content { margin-left:260px; min-height:100vh; }
        .nav-item { display:flex; align-items:center; gap:12px; padding:11px 20px; border-radius:10px; transition:all 0.2s; color:var(--text-muted); text-decoration:none; font-size:0.9rem; font-weight:500; margin:2px 12px; }
        .nav-item:hover, .nav-item.active { background:rgba(6,182,212,0.1); color:var(--network); }
        .nav-item.active { border-left:3px solid var(--network); border-radius:10px 0 0 10px; }
        .stat-card { background:var(--bg-card-2); border:1px solid var(--border); border-radius:12px; padding:20px; transition:all 0.3s; }
        .stat-card:hover { border-color:var(--network); transform:translateY(-2px); box-shadow:0 0 20px rgba(6,182,212,0.1); }

        .table-container { overflow-x:auto; }
        table { width:100%; border-collapse:collapse; }
        th { text-align:left; padding:12px 16px; font-size:0.8rem; text-transform:uppercase; letter-spacing:0.05em; color:var(--text-muted); border-bottom:1px solid var(--border); font-weight:600; }
        td { padding:12px 16px; border-bottom:1px solid rgba(42,42,62,0.5); font-size:0.9rem; }
        tr:hover td { background:rgba(6,182,212,0.03); }

        .badge { display:inline-flex; align-items:center; padding:3px 10px; border-radius:9999px; font-size:0.75rem; font-weight:500; }
        .badge-success { background:rgba(16,185,129,0.15); color:#10b981; }
        .badge-warning { background:rgba(245,158,11,0.15); color:#f59e0b; }
        .badge-danger { background:rgba(239,68,68,0.15); color:#ef4444; }

        .form-input { width:100%; padding:10px 14px; border-radius:8px; border:1px solid var(--border); background:var(--bg-card-2); color:var(--text); font-size:0.9rem; outline:none; transition:all 0.2s; font-family:'Inter',sans-serif; }
        .form-input:focus { border-color:var(--network); box-shadow:0 0 0 3px rgba(6,182,212,0.1); }
        select.form-input { appearance:auto; }
        textarea.form-input { resize:vertical; min-height:100px; }

        .btn { display:inline-flex; align-items:center; gap:6px; padding:9px 18px; border-radius:8px; font-size:0.85rem; font-weight:600; cursor:pointer; transition:all 0.2s; border:none; text-decoration:none; }
        .btn-primary { background:linear-gradient(135deg,var(--network),var(--primary)); color:#fff; }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(6,182,212,0.3); }
        .btn-danger { background:rgba(239,68,68,0.15); color:#ef4444; border:1px solid rgba(239,68,68,0.2); }
        .btn-danger:hover { background:#ef4444; color:#fff; }
        .btn-outline { background:transparent; color:var(--text-muted); border:1px solid var(--border); }
        .btn-outline:hover { border-color:var(--network); color:var(--network); }
        .btn-sm { padding:6px 12px; font-size:0.8rem; }

        .flash { padding:14px 18px; border-radius:10px; margin-bottom:20px; font-size:0.9rem; }
        .flash-success { background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); color:#10b981; }
        .flash-error { background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); color:#ef4444; }

        .mobile-header { display:none; }
        .sidebar-overlay { display:none; }

        @media (max-width:768px) {
            .sidebar { transform:translateX(-100%); }
            .sidebar.open { transform:translateX(0); }
            .sidebar-overlay.open { display:block; position:fixed; inset:0; background:rgba(0,0,0,0.5); z-index:99; }
            .main-content { margin-left:0; }
            .mobile-header { display:flex; }
        }
    </style>
</head>
<body>

<?php if (basename($_SERVER['PHP_SELF']) !== 'login.php'): ?>
    <!-- Mobile Header -->
    <div class="mobile-header items-center justify-between p-4 border-b border-gray-800 bg-[#12121a]">
        <button onclick="document.querySelector('.sidebar').classList.toggle('open');document.querySelector('.sidebar-overlay').classList.toggle('open')" class="text-2xl">
            <i class="fas fa-bars"></i>
        </button>
        <span class="font-bold text-sm">Admin Panel</span>
        <a href="logout.php" class="text-red-400"><i class="fas fa-sign-out-alt"></i></a>
    </div>
    <div class="sidebar-overlay" onclick="document.querySelector('.sidebar').classList.remove('open');this.classList.remove('open')"></div>

    <!-- Sidebar -->
    <aside class="sidebar">
        <div class="p-5 border-b border-gray-800">
            <a href="index.php" class="flex items-center gap-3">
                <span class="w-9 h-9 rounded-lg bg-gradient-to-br from-network-500 to-primary-600 flex items-center justify-center text-white text-sm">
                    <i class="fas fa-network-wired"></i>
                </span>
                <div>
                    <div class="font-bold text-sm">Admin Panel</div>
                    <div class="text-xs text-gray-500"><?= e($siteSettings['site_name'] ?? 'Portfolio') ?></div>
                </div>
            </a>
        </div>
        <div class="py-4">
            <div class="text-xs uppercase tracking-wider text-gray-600 px-6 mb-2 font-semibold">Main</div>
            <a href="index.php" class="nav-item <?= $currentPage === 'index.php' ? 'active' : '' ?>"><i class="fas fa-tachometer-alt w-5"></i> Dashboard</a>

            <div class="text-xs uppercase tracking-wider text-gray-600 px-6 mt-5 mb-2 font-semibold">Content</div>
            <a href="skills.php" class="nav-item <?= $currentPage === 'skills.php' ? 'active' : '' ?>"><i class="fas fa-cogs w-5"></i> Skills</a>
            <a href="services.php" class="nav-item <?= $currentPage === 'services.php' ? 'active' : '' ?>"><i class="fas fa-concierge-bell w-5"></i> Services</a>
            <a href="projects.php" class="nav-item <?= $currentPage === 'projects.php' ? 'active' : '' ?>"><i class="fas fa-project-diagram w-5"></i> Projects</a>
            <a href="certificates.php" class="nav-item <?= $currentPage === 'certificates.php' ? 'active' : '' ?>"><i class="fas fa-certificate w-5"></i> Certificates</a>

            <div class="text-xs uppercase tracking-wider text-gray-600 px-6 mt-5 mb-2 font-semibold">Professional</div>
            <a href="experience.php" class="nav-item <?= $currentPage === 'experience.php' ? 'active' : '' ?>"><i class="fas fa-briefcase w-5"></i> Experience</a>
            <a href="education.php" class="nav-item <?= $currentPage === 'education.php' ? 'active' : '' ?>"><i class="fas fa-graduation-cap w-5"></i> Education</a>
            <a href="testimonials.php" class="nav-item <?= $currentPage === 'testimonials.php' ? 'active' : '' ?>"><i class="fas fa-comment w-5"></i> Testimonials</a>

            <div class="text-xs uppercase tracking-wider text-gray-600 px-6 mt-5 mb-2 font-semibold">Inbox</div>
            <a href="messages.php" class="nav-item <?= $currentPage === 'messages.php' ? 'active' : '' ?>">
                <i class="fas fa-envelope w-5"></i> Messages
                <?php if ($unreadCount > 0): ?>
                    <span class="ml-auto bg-red-500 text-white text-xs rounded-full px-2 py-0.5"><?= $unreadCount ?></span>
                <?php endif; ?>
            </a>

            <div class="text-xs uppercase tracking-wider text-gray-600 px-6 mt-5 mb-2 font-semibold">Settings</div>
            <a href="profile.php" class="nav-item <?= $currentPage === 'profile.php' ? 'active' : '' ?>"><i class="fas fa-user-cog w-5"></i> Profile</a>
            <a href="../index.php" class="nav-item" target="_blank"><i class="fas fa-external-link-alt w-5"></i> View Site</a>
            <a href="logout.php" class="nav-item text-red-400"><i class="fas fa-sign-out-alt w-5"></i> Logout</a>
        </div>
    </aside>

    <!-- Main Content Wrapper -->
    <div class="main-content">
        <!-- Top Bar -->
        <div class="hidden md:flex items-center justify-between px-8 py-4 border-b border-gray-800 bg-[#12121a]">
            <div class="text-sm text-gray-500">
                Welcome, <span class="text-white font-medium"><?= e($currentUser['full_name'] ?? 'Admin') ?></span>
            </div>
            <div class="flex items-center gap-4">
                <a href="profile.php" class="text-sm text-gray-500 hover:text-network-400 transition-colors">
                    <i class="fas fa-user-cog mr-1"></i> Profile
                </a>
                <a href="../index.php" target="_blank" class="text-sm text-gray-500 hover:text-network-400 transition-colors">
                    <i class="fas fa-external-link-alt mr-1"></i> View Site
                </a>
                <a href="logout.php" class="text-sm text-red-400 hover:text-red-300 transition-colors">
                    <i class="fas fa-sign-out-alt mr-1"></i> Logout
                </a>
            </div>
        </div>
        <div class="p-6 md:p-8">
<?php endif; ?>
