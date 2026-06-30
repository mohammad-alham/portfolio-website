<?php $pageTitle = 'Certificates'; include 'includes/header.php'; include 'includes/navbar.php';

$certificates = dbQuery("SELECT * FROM certificates ORDER BY issue_date DESC");

// Group by issuer
$grouped = [];
foreach ($certificates as $cert) {
    $issuer = $cert['issuer'];
    if (!isset($grouped[$issuer])) $grouped[$issuer] = [];
    $grouped[$issuer][] = $cert;
}
?>
<main id="main-content">
    <section class="page-header">
        <div class="bg-pattern"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="animate-on-scroll">
                <span class="section-tag justify-center">Credentials</span>
                <h1 class="section-title">Certifications & Licenses</h1>
                <p class="section-subtitle mx-auto">Industry-recognized certifications demonstrating my expertise and commitment to professional excellence.</p>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (empty($certificates)): ?>
                <div class="text-center py-20"><p class="text-gray-500">Certifications coming soon.</p></div>
            <?php else: ?>
                <?php
                $issuerIcons = [
                    'Cisco Systems' => ['fab fa-cisco', 'from-blue-900 to-blue-700'],
                    'MikroTik' => ['fas fa-network-wired', 'from-red-900 to-red-700'],
                    'Microsoft' => ['fab fa-microsoft', 'from-blue-800 to-blue-600'],
                    'CompTIA' => ['fas fa-network-wired', 'from-yellow-700 to-yellow-500'],
                    'Amazon Web Services' => ['fab fa-aws', 'from-orange-700 to-orange-500'],
                    'EC-Council' => ['fas fa-shield-halved', 'from-purple-900 to-purple-700'],
                    'Linux Professional Institute' => ['fab fa-linux', 'from-yellow-700 to-yellow-500'],
                    'AXELOS' => ['fas fa-project-diagram', 'from-green-800 to-green-600'],
                ];
                $issuerDefaultColor = 'from-blue-900 to-blue-700';
                $first = true;
                foreach ($grouped as $issuer => $certs):
                    $info = $issuerIcons[$issuer] ?? ['fas fa-certificate', 'from-blue-900 to-blue-700'];
                    $icon = $info[0];
                    $gradient = $info[1];
                ?>
                    <div class="mb-16 animate-on-scroll">
                        <h2 class="text-2xl font-bold mb-8 flex items-center gap-3">
                            <i class="<?= $icon ?> text-network-400"></i> <?= e($issuer) ?>
                        </h2>
                        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6">
                            <?php foreach ($certs as $cert): ?>
                                <div class="cert-card p-6 animate-on-scroll-scale">
                                    <div class="flex items-start gap-4">
                                        <div class="cert-badge flex-shrink-0 rounded-xl bg-gradient-to-br <?= $gradient ?> w-20 h-20 flex items-center justify-center">
                                            <i class="<?= $icon ?> text-3xl text-white"></i>
                                        </div>
                                        <div class="min-w-0">
                                            <h3 class="font-bold text-base"><?= e($cert['title']) ?></h3>
                                            <p class="text-xs text-network-400 font-mono mb-2"><?= e($issuer) ?></p>
                                            <p class="text-xs opacity-60">Issued: <?= $cert['issue_date'] ? date('M Y', strtotime($cert['issue_date'])) : 'N/A' ?></p>
                                            <?php if ($cert['certificate_file']): ?>
                                                <a href="<?= e(assetUrl($cert['certificate_file'])) ?>" class="inline-flex items-center gap-1 mt-3 text-sm text-network-400 hover:text-network-300" target="_blank">
                                                    <i class="fas fa-download"></i> Download
                                                </a>
                                            <?php else: ?>
                                                <button class="cert-download inline-flex items-center gap-1 mt-3 text-sm text-network-400 hover:text-network-300" data-cert="<?= e($cert['title']) ?>">
                                                    <i class="fas fa-download"></i> Download
                                                </button>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
