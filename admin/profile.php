<?php
$pageTitle = 'Profile Settings';

// ─── Process ALL actions before any output ────────────────────
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

$user = getCurrentUser();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    checkCsrfToken();
    $full_name  = sanitize($_POST['full_name'] ?? '');
    $email      = sanitize($_POST['email'] ?? '');
    $headline   = sanitize($_POST['headline'] ?? '');
    $about_me   = sanitize($_POST['about_me'] ?? '');
    $phone      = sanitize($_POST['phone'] ?? '');
    $address    = sanitize($_POST['address'] ?? '');
    $website    = sanitize($_POST['website'] ?? '');
    $github     = sanitize($_POST['github'] ?? '');
    $linkedin   = sanitize($_POST['linkedin'] ?? '');
    $facebook   = sanitize($_POST['facebook'] ?? '');
    $twitter    = sanitize($_POST['twitter'] ?? '');

    $profileImage = null;
    if (!empty($_FILES['profile_image']['name'])) {
        $profileImage = uploadFile($_FILES['profile_image'], __DIR__ . '/../assets/uploads/profile');
    }
    $cvFile = null;
    if (!empty($_FILES['cv_file']['name'])) {
        $cvFile = uploadFile($_FILES['cv_file'], __DIR__ . '/../assets/uploads/profile', ['pdf', 'doc', 'docx']);
    }

    $userSql = "UPDATE users SET full_name=?, email=?";
    $userParams = [$full_name, $email];
    if ($profileImage) { $userSql .= ", profile_image=?"; $userParams[] = $profileImage; }
    $userSql .= " WHERE id=?";
    $userParams[] = $user['id'];
    dbExecute($userSql, $userParams);

    $profExists = dbRow("SELECT id FROM profile WHERE user_id=?", [$user['id']]);
    if ($profExists) {
        $sql = "UPDATE profile SET headline=?, about_me=?, phone=?, address=?, website=?, github=?, linkedin=?, facebook=?, twitter=?";
        $params = [$headline, $about_me, $phone, $address, $website, $github, $linkedin, $facebook, $twitter];
        if ($cvFile) { $sql .= ", cv_file=?"; $params[] = $cvFile; }
        $sql .= " WHERE user_id=?";
        $params[] = $user['id'];
        dbExecute($sql, $params);
    } else {
        dbInsert("INSERT INTO profile (user_id, headline, about_me, phone, address, website, github, linkedin, facebook, twitter) VALUES (?,?,?,?,?,?,?,?,?,?)",
            [$user['id'], $headline, $about_me, $phone, $address, $website, $github, $linkedin, $facebook, $twitter]);
    }

    $_SESSION['user_data']['full_name'] = $full_name;
    $_SESSION['user_data']['email'] = $email;
    if ($profileImage) $_SESSION['user_data']['profile_image'] = $profileImage;

    setFlash('success', 'Profile updated successfully.');
    header('Location: profile.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    checkCsrfToken();
    $current_password = $_POST['current_password'] ?? '';
    $new_password     = $_POST['new_password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $stored = dbRow("SELECT password FROM users WHERE id=?", [$user['id']]);

    if (!password_verify($current_password, $stored['password'])) {
        setFlash('error', 'Current password is incorrect.');
    } elseif (strlen($new_password) < 6) {
        setFlash('error', 'New password must be at least 6 characters.');
    } elseif ($new_password !== $confirm_password) {
        setFlash('error', 'Passwords do not match.');
    } else {
        dbExecute("UPDATE users SET password=? WHERE id=?", [password_hash($new_password, PASSWORD_DEFAULT), $user['id']]);
        setFlash('success', 'Password changed successfully.');
    }
    header('Location: profile.php'); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_settings'])) {
    checkCsrfToken();
    $site_name        = sanitize($_POST['site_name'] ?? '');
    $site_title       = sanitize($_POST['site_title'] ?? '');
    $site_description = sanitize($_POST['site_description'] ?? '');
    $settings = dbRow("SELECT id FROM site_settings LIMIT 1");
    if ($settings) {
        dbExecute("UPDATE site_settings SET site_name=?, site_title=?, site_description=? WHERE id=?", [$site_name, $site_title, $site_description, $settings['id']]);
    } else {
        dbInsert("INSERT INTO site_settings (site_name, site_title, site_description) VALUES (?,?,?)", [$site_name, $site_title, $site_description]);
    }
    setFlash('success', 'Site settings updated successfully.');
    header('Location: profile.php'); exit;
}

// ─── Now safe to include header (output starts) ───────────────
require_once __DIR__ . '/includes/header.php';

$flash = getFlash();
$profile = getProfile();
$siteSettings = getSiteSettings();
?>

<?php if ($flash): ?><div class="flash flash-<?= $flash['type'] ?>"><i class="fas fa-<?= $flash['type'] === 'success' ? 'check-circle' : 'exclamation-circle' ?> mr-2"></i><?= e($flash['message']) ?></div><?php endif; ?>

<div class="mb-8">
    <h1 class="text-2xl font-bold">Profile Settings</h1>
    <p class="text-gray-500 text-sm mt-1">Manage your profile, password, and site settings</p>
</div>

<div class="grid lg:grid-cols-3 gap-6">
    <div class="lg:col-span-2 stat-card p-6">
        <h2 class="text-lg font-bold mb-4"><i class="fas fa-user-cog mr-2 text-network-400"></i> Personal Information</h2>
        <form method="POST" action="" enctype="multipart/form-data">
            <?= csrfField() ?>
            <div class="grid grid-cols-2 gap-4 mb-4">
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-sm font-medium block mb-1.5">Full Name</label>
                    <input type="text" name="full_name" class="form-input" value="<?= e($profile['full_name'] ?? $user['full_name'] ?? '') ?>" required>
                </div>
                <div class="col-span-2 sm:col-span-1">
                    <label class="text-sm font-medium block mb-1.5">Email Address</label>
                    <input type="email" name="email" class="form-input" value="<?= e($profile['email'] ?? $user['email'] ?? '') ?>" required>
                </div>
                <div class="col-span-2">
                    <label class="text-sm font-medium block mb-1.5">Professional Headline</label>
                    <input type="text" name="headline" class="form-input" value="<?= e($profile['headline'] ?? '') ?>" placeholder="Network Engineer & IT Infrastructure Specialist">
                </div>
                <div class="col-span-2">
                    <label class="text-sm font-medium block mb-1.5">About Me</label>
                    <textarea name="about_me" class="form-input" rows="5" placeholder="Write a brief description about yourself..."><?= e($profile['about_me'] ?? '') ?></textarea>
                </div>
                <div><label class="text-sm font-medium block mb-1.5">Phone</label><input type="text" name="phone" class="form-input" value="<?= e($profile['phone'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Address</label><input type="text" name="address" class="form-input" value="<?= e($profile['address'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Website</label><input type="url" name="website" class="form-input" value="<?= e($profile['website'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">GitHub URL</label><input type="url" name="github" class="form-input" value="<?= e($profile['github'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">LinkedIn URL</label><input type="url" name="linkedin" class="form-input" value="<?= e($profile['linkedin'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Facebook URL</label><input type="url" name="facebook" class="form-input" value="<?= e($profile['facebook'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Twitter URL</label><input type="url" name="twitter" class="form-input" value="<?= e($profile['twitter'] ?? '') ?>"></div>
                <div><label class="text-sm font-medium block mb-1.5">Profile Image</label><input type="file" name="profile_image" class="form-input text-sm" accept="image/*"></div>
                <div><label class="text-sm font-medium block mb-1.5">CV/Resume</label><input type="file" name="cv_file" class="form-input text-sm" accept=".pdf,.doc,.docx"></div>
            </div>
            <button type="submit" name="update_profile" class="btn btn-primary"><i class="fas fa-save"></i> Save Profile</button>
        </form>
    </div>

    <div class="space-y-6">
        <div class="stat-card p-6">
            <h2 class="text-lg font-bold mb-4"><i class="fas fa-lock mr-2 text-network-400"></i> Change Password</h2>
            <form method="POST" action="">
                <?= csrfField() ?>
                <div class="space-y-3">
                    <div><label class="text-sm font-medium block mb-1.5">Current Password</label><input type="password" name="current_password" class="form-input" required></div>
                    <div><label class="text-sm font-medium block mb-1.5">New Password</label><input type="password" name="new_password" class="form-input" required minlength="6"></div>
                    <div><label class="text-sm font-medium block mb-1.5">Confirm New Password</label><input type="password" name="confirm_password" class="form-input" required minlength="6"></div>
                </div>
                <button type="submit" name="change_password" class="btn btn-primary w-full mt-4"><i class="fas fa-key"></i> Update Password</button>
            </form>
        </div>

        <div class="stat-card p-6">
            <h2 class="text-lg font-bold mb-4"><i class="fas fa-cog mr-2 text-network-400"></i> Site Settings</h2>
            <form method="POST" action="">
                <?= csrfField() ?>
                <div class="space-y-3">
                    <div><label class="text-sm font-medium block mb-1.5">Site Name</label><input type="text" name="site_name" class="form-input" value="<?= e($siteSettings['site_name'] ?? '') ?>"></div>
                    <div><label class="text-sm font-medium block mb-1.5">Site Title</label><input type="text" name="site_title" class="form-input" value="<?= e($siteSettings['site_title'] ?? '') ?>"></div>
                    <div><label class="text-sm font-medium block mb-1.5">Site Description</label><textarea name="site_description" class="form-input" rows="3"><?= e($siteSettings['site_description'] ?? '') ?></textarea></div>
                </div>
                <button type="submit" name="update_settings" class="btn btn-primary w-full mt-4"><i class="fas fa-save"></i> Save Settings</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
