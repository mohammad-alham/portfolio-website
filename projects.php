<?php $pageTitle = 'Projects'; include 'includes/header.php'; include 'includes/navbar.php';

$projects = dbQuery("SELECT * FROM projects ORDER BY created_at DESC");
$categories = [];
foreach ($projects as $p) {
    $techs = array_map('trim', explode(',', $p['technology_stack'] ?? ''));
    foreach ($techs as $t) {
        $cat = 'general';
        $lower = strtolower($t);
        if (strpos($lower, 'cisco') !== false || strpos($lower, 'ospf') !== false || strpos($lower, 'mpls') !== false || strpos($lower, 'qos') !== false || strpos($lower, 'wifi') !== false || strpos($lower, 'meraki') !== false) $cat = 'networking';
        elseif (strpos($lower, 'security') !== false || strpos($lower, 'fortinet') !== false || strpos($lower, 'snort') !== false || strpos($lower, 'pci') !== false || strpos($lower, 'ids') !== false || strpos($lower, 'ips') !== false) $cat = 'security';
        elseif (strpos($lower, 'vmware') !== false || strpos($lower, 'san') !== false || strpos($lower, 'vcenter') !== false || strpos($lower, 'cat6') !== false || strpos($lower, 'tier') !== false || strpos($lower, 'fiber') !== false) $cat = 'infrastructure';
        elseif (strpos($lower, 'aws') !== false || strpos($lower, 'vpc') !== false || strpos($lower, 'cloud') !== false || strpos($lower, 'direct connect') !== false) $cat = 'cloud';
        $categories[$cat] = true;
    }
}
$categories = array_keys($categories);
if (empty($categories)) $categories = ['networking', 'security', 'infrastructure', 'cloud'];
?>
<main id="main-content">
    <section class="page-header">
        <div class="bg-pattern"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="animate-on-scroll">
                <span class="section-tag justify-center">Portfolio</span>
                <h1 class="section-title">Featured Projects</h1>
                <p class="section-subtitle mx-auto">A showcase of network infrastructure projects I have designed and implemented.</p>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap justify-center gap-3 mb-12 animate-on-scroll">
                <button class="filter-btn active" data-filter="all">All Projects</button>
                <?php foreach ($categories as $cat): ?>
                    <button class="filter-btn" data-filter="<?= e($cat) ?>"><?= ucfirst(e($cat)) ?></button>
                <?php endforeach; ?>
            </div>

            <?php if (empty($projects)): ?>
                <div class="text-center py-20"><p class="text-gray-500">No projects to display yet. Check back soon!</p></div>
            <?php else: ?>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($projects as $proj):
                        $cat = 'general';
                        $lower = strtolower($proj['technology_stack'] ?? '');
                        if (strpos($lower, 'cisco') !== false || strpos($lower, 'ospf') !== false || strpos($lower, 'mpls') !== false) $cat = 'networking';
                        elseif (strpos($lower, 'fortinet') !== false || strpos($lower, 'snort') !== false || strpos($lower, 'pci') !== false) $cat = 'security';
                        elseif (strpos($lower, 'vmware') !== false || strpos($lower, 'san') !== false || strpos($lower, 'tier') !== false) $cat = 'infrastructure';
                        elseif (strpos($lower, 'aws') !== false || strpos($lower, 'vpc') !== false) $cat = 'cloud';
                        $catIcons = ['networking' => 'fab fa-cisco', 'security' => 'fas fa-shield-halved', 'infrastructure' => 'fas fa-database', 'cloud' => 'fab fa-aws', 'general' => 'fas fa-code'];
                        $catColors = ['networking' => 'network-400', 'security' => 'green-400', 'infrastructure' => 'blue-400', 'cloud' => 'orange-400', 'general' => 'primary-400'];
                    ?>
                        <div class="project-item animate-on-scroll delay-100" data-category="<?= $cat ?>" data-project="<?= $proj['id'] ?>" data-details="<?= e('<p>' . nl2br(e($proj['description'])) . '</p><div class=\"mt-4\"><h4 class=\"font-bold mb-2\">Technologies Used:</h4><p>' . e($proj['technology_stack'] ?? 'N/A') . '</p></div>') ?>">
                            <div class="project-card">
                                <?php if (!empty($proj['thumbnail'])): ?>
                                    <img src="<?= e(assetUrl($proj['thumbnail'])) ?>" alt="<?= e($proj['title']) ?>" class="project-image" onerror="this.style.display='none';this.parentElement.innerHTML='<div class=\'w-full h-48 bg-gradient-to-br from-network-900 to-primary-900 flex items-center justify-center\'><i class=\'fas fa-network-wired text-5xl text-network-400/50\'></i></div>'">
                                <?php else: ?>
                                    <div class="w-full h-48 bg-gradient-to-br from-network-900 to-primary-900 flex items-center justify-center"><i class="fas fa-network-wired text-5xl text-network-400/50"></i></div>
                                <?php endif; ?>
                                <div class="p-6">
                                    <span class="text-xs font-mono text-network-400 uppercase tracking-wider"><?= ucfirst($cat) ?></span>
                                    <h3 class="project-title text-lg font-bold mt-2 mb-2"><?= e($proj['title']) ?></h3>
                                    <p class="project-description text-sm opacity-70"><?= e(truncate($proj['description'], 100)) ?></p>
                                    <div class="flex items-center justify-between mt-4">
                                        <div class="flex -space-x-2">
                                            <span class="w-8 h-8 rounded-full bg-<?= $catColors[$cat] ?? 'network-400' ?>/20 border-2 border-gray-900 flex items-center justify-center"><i class="<?= $catIcons[$cat] ?? 'fas fa-code' ?> text-<?= $catColors[$cat] ?? 'network-400' ?> text-xs"></i></span>
                                        </div>
                                        <button class="text-sm text-network-400 hover:text-network-300 font-medium" data-modal-trigger="<?= $proj['id'] ?>">
                                            View Details <i class="fas fa-arrow-right ml-1 text-xs"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <div class="project-modal" role="dialog" aria-modal="true" aria-label="Project details">
        <div class="modal-backdrop"></div>
        <div class="modal-content">
            <button class="modal-close absolute top-4 right-4 w-10 h-10 rounded-full bg-gray-800/80 flex items-center justify-center hover:bg-gray-700 transition-colors" aria-label="Close modal"><i class="fas fa-times"></i></button>
            <div class="modal-body"></div>
        </div>
    </div>
</main>
<?php include 'includes/footer.php'; ?>
