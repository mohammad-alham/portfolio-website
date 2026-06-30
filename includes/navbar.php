<?php
$profile = getProfile() ?? [];
?>
<nav class="navbar" role="navigation" aria-label="Main navigation">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between">
            <a href="index.php" class="flex items-center gap-3 text-xl font-bold hover:text-network-400 transition-colors">
                <span class="w-10 h-10 rounded-lg bg-gradient-to-br from-network-500 to-primary-600 flex items-center justify-center text-white text-sm font-bold">
                    <i class="fas fa-network-wired"></i>
                </span>
                <span class="hidden sm:block"><?= e($siteSettings['site_name'] ?? 'NetEngineer') ?></span>
            </a>

            <div class="hidden lg:flex items-center gap-8">
                <a href="index.php" class="nav-link text-sm font-medium">Home</a>
                <a href="about.php" class="nav-link text-sm font-medium">About</a>
                <a href="skills.php" class="nav-link text-sm font-medium">Skills</a>
                <a href="services.php" class="nav-link text-sm font-medium">Services</a>
                <a href="projects.php" class="nav-link text-sm font-medium">Projects</a>
                <a href="certificates.php" class="nav-link text-sm font-medium">Certificates</a>
                <a href="contact.php" class="nav-link text-sm font-medium">Contact</a>
            </div>

            <div class="flex items-center gap-3">
                <button class="theme-toggle" aria-label="Toggle theme">
                    <i class="fas fa-sun"></i>
                </button>
                <a href="contact.php" class="hidden sm:inline-flex btn-primary text-sm !py-2 !px-5">
                    <i class="fas fa-envelope"></i> Hire Me
                </a>
                <button class="lg:hidden mobile-menu-btn" aria-label="Toggle menu">
                    <span></span><span></span><span></span>
                </button>
            </div>
        </div>
    </div>
</nav>

<div class="mobile-overlay"></div>
<div class="mobile-menu">
    <div class="flex flex-col gap-6">
        <a href="index.php" class="nav-link text-lg font-medium flex items-center gap-3"><i class="fas fa-home w-6 text-network-400"></i> Home</a>
        <a href="about.php" class="nav-link text-lg font-medium flex items-center gap-3"><i class="fas fa-user w-6 text-network-400"></i> About</a>
        <a href="skills.php" class="nav-link text-lg font-medium flex items-center gap-3"><i class="fas fa-cogs w-6 text-network-400"></i> Skills</a>
        <a href="services.php" class="nav-link text-lg font-medium flex items-center gap-3"><i class="fas fa-concierge-bell w-6 text-network-400"></i> Services</a>
        <a href="projects.php" class="nav-link text-lg font-medium flex items-center gap-3"><i class="fas fa-project-diagram w-6 text-network-400"></i> Projects</a>
        <a href="certificates.php" class="nav-link text-lg font-medium flex items-center gap-3"><i class="fas fa-certificate w-6 text-network-400"></i> Certificates</a>
        <a href="contact.php" class="nav-link text-lg font-medium flex items-center gap-3"><i class="fas fa-phone w-6 text-network-400"></i> Contact</a>
        <hr class="border-gray-700 my-4">
        <a href="contact.php" class="btn-primary text-center"><i class="fas fa-envelope"></i> Hire Me</a>
    </div>
</div>
