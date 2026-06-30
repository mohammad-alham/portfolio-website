<?php
/**
 * Password Hash Utility
 * Run this script once to generate a bcrypt hash and update the admin password.
 * DELETE THIS FILE AFTER USE for security.
 *
 * Usage: Navigate to /admin/hash-password.php in your browser
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';

$message = '';
$hashOutput = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $email    = sanitize($_POST['email'] ?? 'admin@example.com');
    $action   = $_POST['action'] ?? '';

    if (empty($password)) {
        $message = '<div class="flash flash-error">Please enter a password.</div>';
    } else {
        $hash = password_hash($password, PASSWORD_DEFAULT);

        if ($action === 'hash_only') {
            $hashOutput = $hash;
            $message = '<div class="flash flash-success">Hash generated successfully. Copy the hash below.</div>';
        } elseif ($action === 'update_db') {
            $user = dbRow("SELECT id FROM users WHERE email = ?", [$email]);
            if ($user) {
                dbExecute("UPDATE users SET password = ? WHERE id = ?", [$hash, $user['id']]);
                $message = '<div class="flash flash-success">Password updated successfully for <strong>' . e($email) . '</strong>!</div>';
            } else {
                $message = '<div class="flash flash-error">User with email "' . e($email) . '" not found in database.</div>';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Hash Utility</title>
    <style>
        * { margin:0; padding:0; box-sizing:border-box; }
        body { font-family:system-ui,-apple-system,sans-serif; background:#0a0a0f; color:#e2e8f0; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:20px; }
        .card { background:#12121a; border:1px solid #2a2a3e; border-radius:16px; padding:32px; max-width:560px; width:100%; }
        h1 { font-size:1.5rem; margin-bottom:6px; }
        p { color:#94a3b8; font-size:0.9rem; margin-bottom:20px; }
        label { display:block; font-size:0.9rem; font-weight:600; margin-bottom:6px; }
        input, select { width:100%; padding:10px 14px; border-radius:8px; border:1px solid #2a2a3e; background:#1a1a2e; color:#e2e8f0; font-size:0.95rem; outline:none; margin-bottom:16px; }
        input:focus { border-color:#06b6d4; }
        .btn { padding:10px 20px; border-radius:8px; border:none; font-weight:600; cursor:pointer; transition:all 0.2s; font-size:0.9rem; }
        .btn-primary { background:linear-gradient(135deg,#06b6d4,#6366f1); color:#fff; }
        .btn-primary:hover { transform:translateY(-1px); box-shadow:0 4px 15px rgba(6,182,212,0.3); }
        .btn-danger { background:rgba(239,68,68,0.15); color:#ef4444; border:1px solid rgba(239,68,68,0.2); }
        .btn-group { display:flex; gap:10px; flex-wrap:wrap; }
        .hash-box { background:#1a1a2e; border:1px solid #2a2a3e; border-radius:8px; padding:14px; word-break:break-all; font-family:monospace; font-size:0.8rem; margin-top:12px; }
        .flash { padding:12px 16px; border-radius:8px; margin-bottom:16px; font-size:0.9rem; }
        .flash-success { background:rgba(16,185,129,0.1); border:1px solid rgba(16,185,129,0.2); color:#10b981; }
        .flash-error { background:rgba(239,68,68,0.1); border:1px solid rgba(239,68,68,0.2); color:#ef4444; }
        .warning { background:rgba(245,158,11,0.1); border:1px solid rgba(245,158,11,0.2); color:#f59e0b; padding:10px 14px; border-radius:8px; font-size:0.85rem; margin-bottom:20px; }
    </style>
</head>
<body>
    <div class="card">
        <h1><i class="fas fa-key"></i> Password Hash Utility</h1>
        <p>Generate a bcrypt hash or update the admin password in the database.</p>

        <div class="warning">
            <strong>&#9888; SECURITY:</strong> Delete this file (<code>admin/hash-password.php</code>) after use!
        </div>

        <?= $message ?>

        <form method="POST">
            <label for="password">Password</label>
            <input type="text" id="password" name="password" placeholder="Enter password to hash" required>

            <label for="email">Admin Email (for DB update)</label>
            <input type="email" id="email" name="email" value="admin@example.com">

            <div class="btn-group">
                <button type="submit" name="action" value="hash_only" class="btn btn-primary">Generate Hash Only</button>
                <button type="submit" name="action" value="update_db" class="btn btn-danger" onclick="return confirm('This will update the password in the database. Continue?')">Update Database</button>
            </div>
        </form>

        <?php if ($hashOutput): ?>
            <div class="hash-box"><?= e($hashOutput) ?></div>
            <p style="margin-top:8px;font-size:0.8rem;color:#64748b;">
                Copy this hash and use it in your SQL INSERT/UPDATE statement.
            </p>
        <?php endif; ?>

        <hr style="border-color:#2a2a3e;margin:20px 0;">
        <p style="font-size:0.8rem;color:#64748b;">
            <strong>Default credentials after schema import:</strong><br>
            Email: <code>admin@example.com</code><br>
            Password: <code>admin123</code>
        </p>
    </div>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</body>
</html>
