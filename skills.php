<?php $pageTitle = 'Skills'; include 'includes/header.php'; include 'includes/navbar.php';

$skillsByCategory = getSkillsByCategory();
$categories = array_keys($skillsByCategory);
?>
<main id="main-content">
    <section class="page-header">
        <div class="bg-pattern"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="animate-on-scroll">
                <span class="section-tag justify-center">Expertise</span>
                <h1 class="section-title">Technical Skills</h1>
                <p class="section-subtitle mx-auto">Comprehensive technical skills acquired through years of hands-on experience and continuous learning.</p>
            </div>
        </div>
    </section>

    <?php foreach ($categories as $catIndex => $category): ?>
        <section class="py-16 <?= $catIndex % 2 === 1 ? 'bg-gray-800/20' : '' ?>">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-12 animate-on-scroll">
                    <span class="text-4xl text-network-400 mb-4 block">
                        <?php
                        $icons = ['Networking' => 'fas fa-network-wired', 'Security' => 'fas fa-shield-halved', 'Server' => 'fas fa-server', 'Cloud' => 'fas fa-cloud'];
                        echo '<i class="' . e($icons[$category] ?? 'fas fa-code') . '"></i>';
                        ?>
                    </span>
                    <h2 class="text-2xl font-bold"><?= e($category) ?></h2>
                </div>
                <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                    <?php foreach ($skillsByCategory[$category] as $skill): ?>
                        <div class="skill-item animate-on-scroll-left">
                            <div class="skill-info"><span><?= e($skill['name']) ?></span><span><?= $skill['percentage'] ?>%</span></div>
                            <div class="skill-bar"><div class="skill-progress" style="width:<?= $skill['percentage'] ?>%"></div></div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    <?php endforeach; ?>

    <!-- Tools & Technologies -->
    <section class="py-16 bg-gray-800/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12 animate-on-scroll">
                <span class="text-4xl text-network-400 mb-4 block"><i class="fas fa-tools"></i></span>
                <h2 class="text-2xl font-bold">Tools & Technologies</h2>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-4">
                <?php
                $tools = [
                    ['fas fa-terminal', 'Putty'], ['fas fa-chart-line', 'PRTG'], ['fas fa-eye', 'Wireshark'],
                    ['fas fa-code', 'GNS3'], ['fas fa-cloud', 'SolarWinds'], ['fas fa-draw-polygon', 'LucidChart'],
                    ['fas fa-tasks', 'Ansible'], ['fas fa-git-alt', 'Git'], ['fab fa-docker', 'Docker'],
                    ['fas fa-database', 'MySQL'], ['fas fa-kubernetes', 'K8s'], ['fas fa-napster', 'Nagios'],
                ];
                foreach ($tools as $i => $tool): ?>
                    <div class="card text-center p-6 animate-on-scroll-scale delay-<?= (($i % 6) + 1) * 100 ?>">
                        <div class="text-2xl text-network-400 mb-2"><i class="<?= $tool[0] ?>"></i></div>
                        <div class="text-sm font-medium"><?= $tool[1] ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
