<?php $pageTitle = 'Services'; include 'includes/header.php'; include 'includes/navbar.php';

$services = dbQuery("SELECT * FROM services ORDER BY display_order ASC");
$serviceCount = count($services);
// Service features for each
$features = [
    ['LAN/WAN Design', 'Data Center Architecture', 'Redundancy & Failover', 'Scalability Assessment'],
    ['Performance Analysis', 'Packet Capture & Analysis', 'Root Cause Investigation', 'Network Optimization'],
    ['Windows Server Admin', 'Linux Server Management', 'Active Directory & DNS', 'Backup & Recovery'],
    ['Vulnerability Assessment', 'Firewall Optimization', 'Access Control Review', 'Compliance Checks'],
    ['Infrastructure Assessment', 'Technology Roadmap', 'Vendor Management', 'Cost Optimization'],
    ['New Site Setup', 'Infrastructure Migration', 'Cloud Migration', 'Hardware Procurement'],
];
?>
<main id="main-content">
    <section class="page-header">
        <div class="bg-pattern"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="animate-on-scroll">
                <span class="section-tag justify-center">What I Do</span>
                <h1 class="section-title">Professional Services</h1>
                <p class="section-subtitle mx-auto">End-to-end network and IT infrastructure services delivered with precision and professionalism.</p>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <?php if (empty($services)): ?>
                <div class="text-center py-20"><p class="text-gray-500">Services coming soon.</p></div>
            <?php else: ?>
                <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-8">
                    <?php foreach ($services as $idx => $service): ?>
                        <div class="card service-card animate-on-scroll delay-<?= (($idx % 3) + 1) * 100 ?>">
                            <div class="icon-box mb-5"><?= $service['icon'] ? '<i class="' . e($service['icon']) . '"></i>' : '<i class="fas fa-concierge-bell"></i>' ?></div>
                            <h3 class="text-xl font-bold mb-3"><?= e($service['title']) ?></h3>
                            <p class="text-sm opacity-70 leading-relaxed mb-4"><?= e($service['description']) ?></p>
                            <ul class="space-y-2 text-sm opacity-70">
                                <?php $idx2 = $idx < count($features) ? $features[$idx] : []; ?>
                                <?php foreach ($idx2 as $feat): ?>
                                    <li><i class="fas fa-check text-green-400 mr-2"></i><?= e($feat) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <section class="py-20 bg-gray-800/20">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16 animate-on-scroll">
                <span class="section-tag">My Approach</span>
                <h2 class="section-title">How I Work</h2>
                <p class="section-subtitle mx-auto">A structured methodology ensuring quality, reliability, and client satisfaction.</p>
            </div>
            <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center animate-on-scroll-scale delay-100">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-network-500/10 border border-network-500/30 flex items-center justify-center text-2xl font-bold text-network-400">01</div>
                    <h3 class="font-bold mb-2">Discovery</h3>
                    <p class="text-sm opacity-70">Understanding your requirements, challenges, and goals through in-depth consultation.</p>
                </div>
                <div class="text-center animate-on-scroll-scale delay-200">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-network-500/10 border border-network-500/30 flex items-center justify-center text-2xl font-bold text-network-400">02</div>
                    <h3 class="font-bold mb-2">Planning</h3>
                    <p class="text-sm opacity-70">Designing a comprehensive solution with detailed architecture and implementation roadmap.</p>
                </div>
                <div class="text-center animate-on-scroll-scale delay-300">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-network-500/10 border border-network-500/30 flex items-center justify-center text-2xl font-bold text-network-400">03</div>
                    <h3 class="font-bold mb-2">Implementation</h3>
                    <p class="text-sm opacity-70">Professional deployment with minimal disruption and thorough testing at every stage.</p>
                </div>
                <div class="text-center animate-on-scroll-scale delay-400">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-full bg-network-500/10 border border-network-500/30 flex items-center justify-center text-2xl font-bold text-network-400">04</div>
                    <h3 class="font-bold mb-2">Support</h3>
                    <p class="text-sm opacity-70">Ongoing monitoring, maintenance, and support to ensure optimal performance and reliability.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 text-center">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 animate-on-scroll">
            <h2 class="section-title">Need a Reliable Network Engineer?</h2>
            <p class="section-subtitle mx-auto mb-8">Let's discuss how I can help strengthen your network infrastructure.</p>
            <a href="contact.php" class="btn-primary text-base"><i class="fas fa-calendar"></i> Schedule a Consultation</a>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
