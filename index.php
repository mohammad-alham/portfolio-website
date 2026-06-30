<?php $pageTitle = 'Home'; include 'includes/header.php'; include 'includes/navbar.php';

$skills  = dbQuery("SELECT * FROM skills ORDER BY display_order ASC LIMIT 8");
$stats   = [
    'experience'   => dbRow("SELECT COUNT(*) as c FROM experience")['c'] ?? 0,
    'projects'     => dbRow("SELECT COUNT(*) as c FROM projects")['c'] ?? 0,
    'certificates' => dbRow("SELECT COUNT(*) as c FROM certificates")['c'] ?? 0,
    'clients'      => dbRow("SELECT COUNT(*) as c FROM testimonials")['c'] ?? 0,
];
?>
<main id="main-content">
    <section class="hero-section">
        <div class="hero-bg"><div class="gradient-orb"></div><div class="gradient-orb"></div><div class="gradient-orb"></div></div>
        <div class="hero-grid"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 w-full">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="animate-on-scroll-left">
                    <div class="flex items-center gap-2 text-network-400 font-mono text-sm mb-6">
                        <span class="w-2 h-2 rounded-full bg-green-400 animate-pulse"></span>
                        <span>Available for opportunities</span>
                    </div>
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold leading-tight mb-6">
                        Network Engineer<br>
                        <span class="text-transparent bg-clip-text bg-gradient-to-r from-network-400 to-primary-400">& IT Specialist</span>
                    </h1>
                    <p class="typing-text text-lg sm:text-xl font-mono text-network-400 mb-6">&gt; sudo configure_network --secure --reliable</p>
                    <p class="text-base sm:text-lg opacity-70 mb-8 max-w-xl leading-relaxed">
                        <?= e($profile['headline'] ?? 'Designing, implementing, and securing enterprise-grade network infrastructures.') ?>
                    </p>
                    <div class="flex flex-wrap gap-4">
                        <a href="projects.php" class="btn-primary"><i class="fas fa-folder-open"></i> View Projects</a>
                        <a href="contact.php" class="btn-outline"><i class="fas fa-phone"></i> Contact Me</a>
                    </div>
                    <div class="flex items-center gap-6 mt-10 text-sm opacity-60">
                        <span class="flex items-center gap-2"><i class="fab fa-cisco text-network-400"></i> Cisco</span>
                        <span class="flex items-center gap-2"><i class="fas fa-shield-halved text-network-400"></i> Security</span>
                        <span class="flex items-center gap-2"><i class="fas fa-cloud text-network-400"></i> Cloud</span>
                    </div>
                </div>
                <div class="animate-on-scroll-right text-center lg:text-right">
                    <div class="hero-image-wrapper inline-block">
                        <div class="glow-ring"></div>
                        <div class="w-72 h-72 sm:w-80 sm:h-80 lg:w-96 lg:h-96 rounded-2xl bg-gradient-to-br from-network-800 to-primary-900 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($profile['profile_image'])): ?>
                                <img src="<?= e(assetUrl($profile['profile_image'])) ?>" alt="<?= e($profile['full_name'] ?? 'Network Engineer') ?>" class="w-full h-full object-cover opacity-80" onerror="this.parentElement.innerHTML='<i class=\'fas fa-user-tie text-8xl text-network-400/50\'></i>'">
                            <?php else: ?>
                                <img src="assets/images/profile-placeholder.jpg" alt="Network Engineer" class="w-full h-full object-cover opacity-80" onerror="this.parentElement.innerHTML='<i class=\'fas fa-user-tie text-8xl text-network-400/50\'></i>'">
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-16 border-y border-gray-800/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8">
                <div class="stat-item animate-on-scroll-scale delay-100">
                    <div class="stat-number" data-target="<?= $stats['experience'] ?>" data-suffix="+">0+</div>
                    <div class="text-sm opacity-60 mt-1">Years Experience</div>
                </div>
                <div class="stat-item animate-on-scroll-scale delay-200">
                    <div class="stat-number" data-target="<?= max($stats['projects'], 150) ?>" data-suffix="+">0+</div>
                    <div class="text-sm opacity-60 mt-1">Projects Completed</div>
                </div>
                <div class="stat-item animate-on-scroll-scale delay-300">
                    <div class="stat-number" data-target="<?= $stats['certificates'] ?>" data-suffix="+">0+</div>
                    <div class="text-sm opacity-60 mt-1">Certifications</div>
                </div>
                <div class="stat-item animate-on-scroll-scale delay-400">
                    <div class="stat-number" data-target="<?= $stats['clients'] ?>" data-suffix="+">0+</div>
                    <div class="text-sm opacity-60 mt-1">Happy Clients</div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-on-scroll">
                <span class="section-tag">Core Competencies</span>
                <h2 class="section-title">Featured Skills & Expertise</h2>
                <p class="section-subtitle mx-auto">Specialized in enterprise networking technologies and IT infrastructure management.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <?php foreach ($skills as $skill): ?>
                    <div class="card service-card animate-on-scroll">
                        <div class="icon-box mb-4"><?= $skill['icon'] ? '<i class="' . e($skill['icon']) . '"></i>' : '<i class="fas fa-code"></i>' ?></div>
                        <h3 class="text-lg font-bold mb-2"><?= e($skill['name']) ?></h3>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 rounded-full bg-gray-800"><div class="h-full rounded-full bg-gradient-to-r from-network-500 to-primary-500" style="width:<?= $skill['percentage'] ?>%"></div></div>
                            <span class="text-xs text-gray-500"><?= $skill['percentage'] ?>%</span>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="py-20 relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-network-900/20 to-primary-900/20"></div>
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <div class="animate-on-scroll">
                <h2 class="section-title">Let's Build Something Great Together</h2>
                <p class="section-subtitle mx-auto mb-8">Looking for a reliable Network Engineer for your next project? I'm just a message away.</p>
                <div class="flex flex-wrap justify-center gap-4">
                    <a href="contact.php" class="btn-primary text-base"><i class="fas fa-paper-plane"></i> Start a Conversation</a>
                    <a href="services.php" class="btn-outline text-base"><i class="fas fa-list"></i> View All Services</a>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
