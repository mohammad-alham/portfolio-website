<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email    = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        $user = dbRow("SELECT * FROM users WHERE email = ?", [$email]);

        if ($user && password_verify($password, $user['password'])) {
            sessionRegenerate();
            $_SESSION['user_id']   = $user['id'];
            $_SESSION['user_data'] = [
                'id'         => $user['id'],
                'full_name'  => $user['full_name'],
                'email'      => $user['email'],
                'role'       => $user['role'],
                'profile_image' => $user['profile_image'],
            ];

            $redirect = $_SESSION['redirect_after_login'] ?? 'index.php';
            unset($_SESSION['redirect_after_login']);
            header('Location: ' . $redirect);
            exit;
        } else {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | Portfolio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = { darkMode: 'class', theme: { extend: { colors: { network: { 400: '#22d3ee', 500: '#06b6d4' }, primary: { 500: '#6366f1', 600: '#4f46e5' } }, fontFamily: { sans: ['Inter', 'sans-serif'] } } } }
    </script>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:'Inter',sans-serif; background:#0a0a0f; display:flex; align-items:center; justify-content:center; min-height:100vh; overflow:hidden; }
        .bg-orb { position:fixed; border-radius:50%; filter:blur(100px); opacity:0.1; }
        .bg-orb:nth-child(1) { width:500px;height:500px;background:#6366f1;top:-20%;left:-10%; }
        .bg-orb:nth-child(2) { width:400px;height:400px;background:#06b6d4;bottom:-20%;right:-10%; }
        .login-card { background:#12121a; border:1px solid #2a2a3e; border-radius:20px; padding:40px; width:100%; max-width:420px; position:relative; }
        .form-input { width:100%; padding:12px 16px; border-radius:10px; border:1px solid #2a2a3e; background:#1a1a2e; color:#e2e8f0; font-size:0.95rem; outline:none; transition:all 0.2s; }
        .form-input:focus { border-color:#06b6d4; box-shadow:0 0 0 3px rgba(6,182,212,0.1); }
        .btn-login { width:100%; padding:12px; border-radius:10px; background:linear-gradient(135deg,#06b6d4,#6366f1); color:#fff; border:none; font-size:1rem; font-weight:600; cursor:pointer; transition:all 0.2s; }
        .btn-login:hover { transform:translateY(-2px); box-shadow:0 8px 25px rgba(6,182,212,0.3); }
        .error-msg { background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); color:#ef4444; padding:12px 16px; border-radius:10px; font-size:0.9rem; margin-bottom:20px; }
    </style>
</head>
<body>
    <div class="bg-orb"></div>
    <div class="bg-orb"></div>

    <div class="login-card">
        <div class="text-center mb-8">
            <div class="w-14 h-14 mx-auto mb-4 rounded-xl bg-gradient-to-br from-network-500 to-primary-600 flex items-center justify-center text-white text-xl">
                <i class="fas fa-network-wired"></i>
            </div>
            <h1 class="text-2xl font-bold">Admin Login</h1>
            <p class="text-gray-500 text-sm mt-1">Sign in to manage your portfolio</p>
        </div>

        <?php if ($error): ?>
            <div class="error-msg"><i class="fas fa-exclamation-circle mr-2"></i><?= e($error) ?></div>
        <?php endif; ?>

        <form method="POST" action="">
            <div class="mb-4">
                <label class="text-sm font-medium block mb-1.5">Email Address</label>
                <input type="email" name="email" class="form-input" placeholder="admin@example.com" required autocomplete="email" autofocus>
            </div>
            <div class="mb-6">
                <label class="text-sm font-medium block mb-1.5">Password</label>
                <input type="password" name="password" class="form-input" placeholder="Enter your password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn-login">
                <i class="fas fa-lock-open mr-2"></i> Sign In
            </button>
        </form>

        <div class="text-center mt-6">
            <a href="../index.php" class="text-sm text-gray-500 hover:text-network-400 transition-colors">
                <i class="fas fa-arrow-left mr-1"></i> Back to Website
            </a>
        </div>

        <div class="text-center mt-4 text-xs text-gray-600">
            Default: admin@example.com / admin123
        </div>
    </div>
</body>
</html>
