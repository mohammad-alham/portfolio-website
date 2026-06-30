<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';

// Start session for theme cookie reading
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$siteSettings = getSiteSettings();
$profile      = getProfile();

$themeClass = '';
if (isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light') {
    $themeClass = 'bg-gray-50 text-gray-900';
} else {
    $themeClass = 'bg-[#0a0a0f] text-gray-100';
}
?>
<!DOCTYPE html>
<html lang="en" data-theme="<?= isset($_COOKIE['theme']) && $_COOKIE['theme'] === 'light' ? 'light' : 'dark' ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="<?= e($siteSettings['site_description'] ?? 'Professional Network Engineer & Network Administrator Portfolio') ?>">
    <meta name="keywords" content="Network Engineer, Network Administrator, Cisco, Mikrotik, IT Infrastructure, Network Security, Portfolio">
    <meta name="author" content="<?= e($profile['full_name'] ?? '[Your Name]') ?>">
    <meta name="robots" content="index, follow">

    <meta property="og:title" content="<?= e($pageTitle ?? 'Home') ?> | <?= e($siteSettings['site_name'] ?? 'Network Engineer Portfolio') ?>">
    <meta property="og:description" content="<?= e($siteSettings['site_description'] ?? 'Professional Network Engineer Portfolio') ?>">
    <meta property="og:type" content="website">
    <meta property="og:url" content="<?php echo 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']; ?>">

    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="<?= e($pageTitle ?? 'Home') ?> | <?= e($siteSettings['site_name'] ?? 'Network Engineer Portfolio') ?>">
    <meta name="twitter:description" content="<?= e($siteSettings['site_description'] ?? 'Professional Network Engineer Portfolio') ?>">

    <link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg">
    <link rel="apple-touch-icon" href="assets/icons/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <script src="https://cdn.tailwindcss.com"></script>

    <link rel="stylesheet" href="assets/css/style.css">

    <title><?= isset($pageTitle) ? e($pageTitle) . ' | ' : '' ?><?= e($siteSettings['site_name'] ?? 'Network Engineer Portfolio') ?></title>

    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    colors: {
                        primary: {
                            50: '#eef2ff', 100: '#e0e7ff', 200: '#c7d2fe', 300: '#a5b4fc',
                            400: '#818cf8', 500: '#6366f1', 600: '#4f46e5', 700: '#4338ca',
                            800: '#3730a3', 900: '#312e81', 950: '#1e1b4b',
                        },
                        network: {
                            50: '#ecfeff', 100: '#cffafe', 200: '#a5f3fc', 300: '#67e8f9',
                            400: '#22d3ee', 500: '#06b6d4', 600: '#0891b2', 700: '#0e7490',
                            800: '#155e75', 900: '#164e63', 950: '#083344',
                        }
                    },
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                        mono: ['JetBrains Mono', 'monospace'],
                    },
                }
            }
        }
    </script>
</head>
<body class="font-sans transition-colors duration-300 <?= $themeClass ?>">
    <div id="loader" class="fixed inset-0 z-[9999] flex items-center justify-center bg-[#0a0a0f] transition-opacity duration-500">
        <div class="text-center">
            <div class="loader-ring"></div>
            <p class="text-network-400 font-mono text-sm mt-4">Loading...</p>
        </div>
    </div>

    <a href="#main-content" class="skip-link">Skip to main content</a>
