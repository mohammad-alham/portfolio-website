<?php
$profile = getProfile() ?? [];
?>
<footer class="footer">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-10 mb-10">
            <div>
                <div class="flex items-center gap-3 mb-4">
                    <span class="w-10 h-10 rounded-lg bg-gradient-to-br from-network-500 to-primary-600 flex items-center justify-center text-white text-sm">
                        <i class="fas fa-network-wired"></i>
                    </span>
                    <span class="text-lg font-bold"><?= e($siteSettings['site_name'] ?? 'NetEngineer') ?></span>
                </div>
                <p class="text-sm leading-relaxed opacity-70">
                    <?= e($profile['headline'] ?? 'Professional Network Engineer & IT Infrastructure Specialist.') ?>
                </p>
            </div>

            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">Quick Links</h4>
                <ul class="space-y-3 text-sm">
                    <li><a href="index.php" class="opacity-70 hover:opacity-100">Home</a></li>
                    <li><a href="about.php" class="opacity-70 hover:opacity-100">About</a></li>
                    <li><a href="skills.php" class="opacity-70 hover:opacity-100">Skills</a></li>
                    <li><a href="services.php" class="opacity-70 hover:opacity-100">Services</a></li>
                    <li><a href="projects.php" class="opacity-70 hover:opacity-100">Projects</a></li>
                    <li><a href="contact.php" class="opacity-70 hover:opacity-100">Contact</a></li>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">Services</h4>
                <ul class="space-y-3 text-sm">
                    <?php
                    $footerServices = dbQuery("SELECT title FROM services ORDER BY display_order ASC LIMIT 5");
                    foreach ($footerServices as $fs): ?>
                        <li><a href="services.php" class="opacity-70 hover:opacity-100"><?= e($fs['title']) ?></a></li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <div>
                <h4 class="text-sm font-semibold uppercase tracking-wider mb-4">Connect</h4>
                <div class="flex gap-3 mb-4 flex-wrap">
                    <?php if (!empty($profile['linkedin'])): ?>
                        <a href="<?= e($profile['linkedin']) ?>" class="social-link" aria-label="LinkedIn" target="_blank"><i class="fab fa-linkedin-in"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['github'])): ?>
                        <a href="<?= e($profile['github']) ?>" class="social-link" aria-label="GitHub" target="_blank"><i class="fab fa-github"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['twitter'])): ?>
                        <a href="<?= e($profile['twitter']) ?>" class="social-link" aria-label="Twitter" target="_blank"><i class="fab fa-x-twitter"></i></a>
                    <?php endif; ?>
                    <?php if (!empty($profile['facebook'])): ?>
                        <a href="<?= e($profile['facebook']) ?>" class="social-link" aria-label="Facebook" target="_blank"><i class="fab fa-facebook-f"></i></a>
                    <?php endif; ?>
                    <a href="contact.php" class="social-link" aria-label="Email"><i class="fas fa-envelope"></i></a>
                </div>
                <p class="text-sm opacity-70">
                    <i class="fas fa-map-marker-alt mr-2 text-network-400"></i>
                    <?= e(!empty($profile['address']) ? $profile['address'] : 'Available for remote & onsite') ?>
                </p>
            </div>
        </div>

        <div class="border-t border-gray-800 pt-6 mt-6 flex flex-col sm:flex-row items-center justify-between gap-4">
            <p class="text-xs opacity-60">
                &copy; <?php echo date('Y'); ?> <?= e($siteSettings['site_name'] ?? 'Network Engineer Portfolio') ?>. All rights reserved.
            </p>
            <p class="text-xs opacity-60">
                Built with <i class="fas fa-heart text-red-500"></i> for network excellence
            </p>
        </div>
    </div>
</footer>

<button class="back-to-top" aria-label="Back to top">
    <i class="fas fa-arrow-up"></i>
</button>

<script src="assets/js/main.js"></script>
</body>
</html>
