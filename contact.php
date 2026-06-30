<?php $pageTitle = 'Contact'; include 'includes/header.php'; include 'includes/navbar.php';

$success = false;
$errors  = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name    = sanitize($_POST['name'] ?? '');
    $email   = sanitize($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if (empty($name) || strlen($name) < 2) $errors[] = 'Please enter your full name.';
    if (!isValidEmail($email)) $errors[] = 'Please enter a valid email address.';
    if (empty($subject) || strlen($subject) < 3) $errors[] = 'Please enter a subject.';
    if (empty($message) || strlen($message) < 10) $errors[] = 'Please enter a message (min. 10 characters).';

    if (empty($errors)) {
        dbInsert("INSERT INTO contact_messages (name, email, subject, message) VALUES (?,?,?,?)",
            [$name, $email, $subject, $message]);
        $success = true;
    }
}

$profile = getProfile() ?? [];
?>
<main id="main-content">
    <section class="page-header">
        <div class="bg-pattern"></div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="animate-on-scroll">
                <span class="section-tag justify-center">Get In Touch</span>
                <h1 class="section-title">Let's Connect</h1>
                <p class="section-subtitle mx-auto">Have a project in mind? Let's discuss how I can help with your network infrastructure needs.</p>
            </div>
        </div>
    </section>

    <section class="py-16">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid lg:grid-cols-2 gap-12">
                <div class="animate-on-scroll-left">
                    <div class="card p-8">
                        <h2 class="text-2xl font-bold mb-2">Send a Message</h2>
                        <p class="text-sm opacity-60 mb-6">Fill out the form below and I'll get back to you within 24 hours.</p>

                        <?php if ($success): ?>
                            <div class="p-4 rounded-lg bg-green-500/10 border border-green-500/20 text-green-400 mb-6">
                                <i class="fas fa-check-circle mr-2"></i> Thank you! Your message has been sent successfully. I will get back to you soon.
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($errors)): ?>
                            <div class="p-4 rounded-lg bg-red-500/10 border border-red-500/20 text-red-400 mb-6">
                                <i class="fas fa-exclamation-circle mr-2"></i>
                                <?php foreach ($errors as $err): ?><?= e($err) ?><br><?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <form id="contactForm" method="POST" action="" novalidate>
                            <div class="grid sm:grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label for="name" class="text-sm font-medium mb-1 block">Full Name <span class="text-red-400">*</span></label>
                                    <input type="text" id="name" name="name" class="form-input" placeholder="John Doe" required minlength="2" value="<?= e($_POST['name'] ?? '') ?>">
                                    <span class="error-text">Please enter your name (min. 2 characters)</span>
                                </div>
                                <div>
                                    <label for="email" class="text-sm font-medium mb-1 block">Email Address <span class="text-red-400">*</span></label>
                                    <input type="email" id="email" name="email" class="form-input" placeholder="john@example.com" required value="<?= e($_POST['email'] ?? '') ?>">
                                    <span class="error-text">Please enter a valid email address</span>
                                </div>
                            </div>
                            <div class="mb-4">
                                <label for="subject" class="text-sm font-medium mb-1 block">Subject <span class="text-red-400">*</span></label>
                                <input type="text" id="subject" name="subject" class="form-input" placeholder="Network Design Project" required minlength="3" value="<?= e($_POST['subject'] ?? '') ?>">
                                <span class="error-text">Please enter a subject (min. 3 characters)</span>
                            </div>
                            <div class="mb-6">
                                <label for="message" class="text-sm font-medium mb-1 block">Message <span class="text-red-400">*</span></label>
                                <textarea id="message" name="message" rows="6" class="form-input resize-none" placeholder="Tell me about your project, requirements, and timeline..." required minlength="10"><?= e($_POST['message'] ?? '') ?></textarea>
                                <span class="error-text">Please enter your message (min. 10 characters)</span>
                            </div>
                            <button type="submit" class="btn-primary w-full justify-center"><i class="fas fa-paper-plane"></i> Send Message</button>
                        </form>
                    </div>
                </div>

                <div class="animate-on-scroll-right">
                    <div class="space-y-6 mb-8">
                        <h2 class="text-2xl font-bold mb-2">Contact Information</h2>
                        <p class="text-sm opacity-60 mb-6">Feel free to reach out through any of the channels below.</p>

                        <div class="card p-5 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-network-500/10 flex items-center justify-center text-network-400 flex-shrink-0"><i class="fas fa-envelope"></i></div>
                            <div>
                                <h3 class="font-semibold">Email</h3>
                                <p class="text-sm opacity-70"><?= e($profile['email'] ?? 'network.engineer@example.com') ?></p>
                            </div>
                        </div>
                        <div class="card p-5 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-network-500/10 flex items-center justify-center text-network-400 flex-shrink-0"><i class="fas fa-phone"></i></div>
                            <div>
                                <h3 class="font-semibold">Phone</h3>
                                <p class="text-sm opacity-70"><?= e($profile['phone'] ?? '+1 (555) 123-4567') ?></p>
                            </div>
                        </div>
                        <div class="card p-5 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-network-500/10 flex items-center justify-center text-network-400 flex-shrink-0"><i class="fas fa-map-marker-alt"></i></div>
                            <div>
                                <h3 class="font-semibold">Location</h3>
                                <p class="text-sm opacity-70"><?= e($profile['address'] ?? '[Your City, Your Country]') ?></p>
                                <p class="text-sm opacity-70">Available for remote & onsite engagements</p>
                            </div>
                        </div>
                        <div class="card p-5 flex items-start gap-4">
                            <div class="w-12 h-12 rounded-xl bg-network-500/10 flex items-center justify-center text-network-400 flex-shrink-0"><i class="fas fa-clock"></i></div>
                            <div>
                                <h3 class="font-semibold">Working Hours</h3>
                                <p class="text-sm opacity-70">Monday - Friday: 9:00 AM - 6:00 PM</p>
                                <p class="text-sm opacity-70">Saturday: 10:00 AM - 2:00 PM</p>
                            </div>
                        </div>
                    </div>

                    <div class="card p-6 mb-8">
                        <h3 class="font-semibold mb-4">Follow Me</h3>
                        <div class="flex flex-wrap gap-3">
                            <?php if (!empty($profile['linkedin'])): ?><a href="<?= e($profile['linkedin']) ?>" class="social-link !w-12 !h-12 !rounded-xl" aria-label="LinkedIn" target="_blank"><i class="fab fa-linkedin-in text-lg"></i></a><?php endif; ?>
                            <?php if (!empty($profile['github'])): ?><a href="<?= e($profile['github']) ?>" class="social-link !w-12 !h-12 !rounded-xl" aria-label="GitHub" target="_blank"><i class="fab fa-github text-lg"></i></a><?php endif; ?>
                            <?php if (!empty($profile['twitter'])): ?><a href="<?= e($profile['twitter']) ?>" class="social-link !w-12 !h-12 !rounded-xl" aria-label="Twitter/X" target="_blank"><i class="fab fa-x-twitter text-lg"></i></a><?php endif; ?>
                            <?php if (!empty($profile['facebook'])): ?><a href="<?= e($profile['facebook']) ?>" class="social-link !w-12 !h-12 !rounded-xl" aria-label="Facebook" target="_blank"><i class="fab fa-facebook-f text-lg"></i></a><?php endif; ?>
                            <a href="#" class="social-link !w-12 !h-12 !rounded-xl" aria-label="WhatsApp"><i class="fab fa-whatsapp text-lg"></i></a>
                            <a href="#" class="social-link !w-12 !h-12 !rounded-xl" aria-label="Telegram"><i class="fab fa-telegram-plane text-lg"></i></a>
                        </div>
                    </div>

                    <div class="card p-4">
                        <div class="w-full h-64 rounded-lg bg-gradient-to-br from-network-900/50 to-primary-900/50 flex items-center justify-center flex-col gap-3">
                            <i class="fas fa-map-marked-alt text-4xl text-network-400/60"></i>
                            <p class="text-sm opacity-60">Google Map Integration</p>
                            <p class="text-xs opacity-40"><?= e($profile['address'] ?? '[Your Location]') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>
<?php include 'includes/footer.php'; ?>
