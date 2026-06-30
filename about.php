<?php $pageTitle = 'About'; include 'includes/header.php'; include 'includes/navbar.php';

$experience   = dbQuery("SELECT * FROM experience ORDER BY start_date DESC");
$education    = dbQuery("SELECT * FROM education ORDER BY start_year DESC");
$certificates = dbQuery("SELECT * FROM certificates ORDER BY issue_date DESC LIMIT 4");
?>
<main id="main-content">
    <section class="page-header">
        <div class="bg-pattern"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="animate-on-scroll">
                <span class="section-tag justify-center">About Me</span>
                <h1 class="section-title">Know Who I Am</h1>
                <p class="section-subtitle mx-auto">A passionate Network Engineer dedicated to building secure, scalable, and reliable network infrastructures.</p>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12 items-center">
                <div class="animate-on-scroll-left">
                    <div class="relative inline-block">
                        <div class="w-80 h-80 rounded-2xl bg-gradient-to-br from-network-800 to-primary-900 flex items-center justify-center overflow-hidden">
                            <?php if (!empty($profile['profile_image'])): ?>
                                <img src="<?= e(assetUrl($profile['profile_image'])) ?>" alt="<?= e($profile['full_name'] ?? 'About Me') ?>" class="w-full h-full object-cover opacity-80" onerror="this.parentElement.innerHTML='<i class=\'fas fa-user-tie text-8xl text-network-400/50\'></i>'">
                            <?php else: ?>
                                <img src="assets/images/profile-placeholder.jpg" alt="About Me" class="w-full h-full object-cover opacity-80" onerror="this.parentElement.innerHTML='<i class=\'fas fa-user-tie text-8xl text-network-400/50\'></i>'">
                            <?php endif; ?>
                        </div>
                        <div class="absolute -bottom-4 -right-4 w-24 h-24 rounded-xl bg-network-500/20 border border-network-500/30 flex items-center justify-center backdrop-blur">
                            <div class="text-center">
                                <div class="text-2xl font-bold text-network-400"><?= count($experience) ?>+</div>
                                <div class="text-xs opacity-60">Exp.</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="animate-on-scroll-right">
                    <h2 class="text-2xl sm:text-3xl font-bold mb-4">
                        A Dedicated Network Engineer Based in <span class="text-network-400">[Your City]</span>
                    </h2>
                    <p class="opacity-70 mb-4 leading-relaxed"><?= nl2br(e($profile['about_me'] ?? 'I am a certified Network Engineer and IT Infrastructure Specialist with hands-on experience in designing, implementing, and managing complex network environments.')) ?></p>
                    <div class="flex flex-wrap gap-4">
                        <a href="contact.php" class="btn-primary"><i class="fas fa-paper-plane"></i> Let's Talk</a>
                        <?php if (!empty($profile['cv_file'])): ?>
                            <a href="<?= e(assetUrl($profile['cv_file'])) ?>" class="btn-outline" target="_blank"><i class="fas fa-download"></i> Download CV</a>
                        <?php else: ?>
                            <a href="#" class="btn-outline" onclick="alert('CV file not uploaded yet. Please check back later.');return false;"><i class="fas fa-download"></i> Download CV</a>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-on-scroll">
                <span class="section-tag">Career Path</span>
                <h2 class="section-title">Work Experience</h2>
                <p class="section-subtitle mx-auto">A journey through my professional career in network engineering.</p>
            </div>
            <div class="timeline">
                <?php $i = 1; foreach ($experience as $exp): ?>
                    <div class="timeline-item animate-on-scroll-left delay-<?= ($i * 100) ?>">
                        <span class="timeline-date"><?= formatDate($exp['start_date'], 'M Y') ?> - <?= formatDate($exp['end_date'], 'M Y') ?></span>
                        <h3 class="text-xl font-bold"><?= e($exp['job_title']) ?></h3>
                        <p class="text-network-400 font-medium text-sm mb-2"><?= e($exp['company_name']) ?></p>
                        <p class="text-sm opacity-70 leading-relaxed"><?= nl2br(e($exp['description'] ?? '')) ?></p>
                    </div>
                <?php $i++; endforeach; ?>
            </div>
        </div>
    </section>

    <?php if (!empty($education)): ?>
    <section class="py-20 bg-gray-800/20">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-on-scroll">
                <span class="section-tag">Education</span>
                <h2 class="section-title">Academic Background</h2>
            </div>
            <div class="space-y-6">
                <?php foreach ($education as $edu): ?>
                    <div class="card animate-on-scroll-left flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-network-500/10 flex items-center justify-center text-network-400 flex-shrink-0"><i class="fas fa-graduation-cap text-xl"></i></div>
                        <div>
                            <h3 class="font-bold text-lg"><?= e($edu['degree']) ?> in <?= e($edu['field_of_study'] ?? '') ?></h3>
                            <p class="text-network-400 text-sm"><?= e($edu['institution']) ?></p>
                            <p class="text-xs opacity-60 mt-1"><?= e($edu['start_year']) ?> - <?= e($edu['end_year'] ?? 'Present') ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

    <?php if (!empty($certificates)): ?>
    <section class="py-20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-on-scroll">
                <span class="section-tag">Credentials</span>
                <h2 class="section-title">Professional Certifications</h2>
                <p class="section-subtitle mx-auto">Industry-recognized certifications that validate my expertise.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($certificates as $cert): ?>
                    <div class="card text-center animate-on-scroll-scale">
                        <div class="text-4xl mb-4 text-network-400"><i class="fas fa-certificate"></i></div>
                        <h3 class="font-bold text-sm mb-1"><?= e($cert['title']) ?></h3>
                        <p class="text-xs opacity-60"><?= e($cert['issuer']) ?></p>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-10 animate-on-scroll">
                <a href="certificates.php" class="btn-outline"><i class="fas fa-certificate"></i> View All Certifications</a>
            </div>
        </div>
    </section>
    <?php endif; ?>
</main>
<?php include 'includes/footer.php'; ?>
