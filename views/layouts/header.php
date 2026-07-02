<?php
/** @var string|null $pageTitle set by the calling view before render() if needed */
$role = Auth::role();
$user = Auth::user();
$currentAction = $_GET['action'] ?? '';

/**
 * Nav definition per role. Each item's color is not decorative - it encodes
 * what kind of thing the section is: blue = overview, teal = stock/health,
 * amber = something awaiting action, brick = restricted/admin.
 */
$navByRole = [
    'super_admin' => [
        ['action' => 'dashboard',    'label' => 'Dashboard',   'icon' => '&#9673;', 'color' => '#6FC3E8'],
        ['action' => 'drugs',        'label' => 'Inventory',   'icon' => '&#9636;', 'color' => '#2FA88A'],
        ['action' => 'drugs_create', 'label' => 'Receive Stock','icon' => '&#43;',  'color' => '#2FA88A'],
        ['action' => 'users',        'label' => 'Staff Accounts','icon' => '&#9737;','color' => '#C1443C'],
    ],
    'admin' => [
        ['action' => 'dashboard', 'label' => 'Dashboard',      'icon' => '&#9673;', 'color' => '#6FC3E8'],
        ['action' => 'drugs',     'label' => 'Inventory',      'icon' => '&#9636;', 'color' => '#2FA88A'],
        ['action' => 'users',     'label' => 'Staff Accounts', 'icon' => '&#9737;', 'color' => '#C1443C'],
    ],
    'pharmacist' => [
        ['action' => 'dashboard',        'label' => 'Dashboard',         'icon' => '&#9673;', 'color' => '#6FC3E8'],
        ['action' => 'requests_create',  'label' => 'New Request',       'icon' => '&#43;',   'color' => '#D98E04'],
        ['action' => 'requests',         'label' => 'My Requests',       'icon' => '&#9776;', 'color' => '#D98E04'],
        ['action' => 'checkouts_ready',  'label' => 'Ready for Checkout','icon' => '&#10003;','color' => '#2FA88A'],
        ['action' => 'drugs',            'label' => 'Inventory',         'icon' => '&#9636;', 'color' => '#2FA88A'],
    ],
    'cashier' => [
        ['action' => 'dashboard',         'label' => 'Dashboard',          'icon' => '&#9673;', 'color' => '#6FC3E8'],
        ['action' => 'payments_pending',  'label' => 'Awaiting Payment',   'icon' => '&#8353;', 'color' => '#D98E04'],
    ],
];

$navItems = $navByRole[$role] ?? [];

$roleLabels = [
    'super_admin' => 'Super Admin',
    'admin'       => 'Admin',
    'pharmacist'  => 'Pharmacist',
    'cashier'     => 'Cashier',
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PharmaCare<?= isset($pageTitle) ? ' - ' . htmlspecialchars($pageTitle) : '' ?></title>
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>
<div class="app-shell">
    <aside class="sidebar">
        <div class="sidebar-brand">
            <span class="rx">&#8478;</span>
            <span class="name">PharmaCare</span>
        </div>
        <nav class="sidebar-nav">
            <div class="sidebar-section-label">Menu</div>
            <?php foreach ($navItems as $item): ?>
                <a class="nav-item <?= $currentAction === $item['action'] ? 'active' : '' ?>"
                   href="<?= BASE_URL ?>/index.php?action=<?= $item['action'] ?>">
                    <span class="tab-rule" style="background: <?= $item['color'] ?>;"></span>
                    <span class="icon"><?= $item['icon'] ?></span>
                    <span><?= htmlspecialchars($item['label']) ?></span>
                </a>
            <?php endforeach; ?>
        </nav>
        <div class="sidebar-user">
            <div class="who"><?= htmlspecialchars($user['full_name'] ?? '') ?></div>
            <div class="role-tag"><?= htmlspecialchars($roleLabels[$role] ?? $role) ?></div>
            <div><a class="logout-link" href="<?= BASE_URL ?>/index.php?action=logout">Log out</a></div>
        </div>
    </aside>

    <div class="main-col">
        <header class="topbar">
            <div>
                <div class="page-eyebrow"><?= htmlspecialchars($roleLabels[$role] ?? '') ?> &middot; PharmaCare</div>
                <div class="page-title"><?= isset($pageTitle) ? htmlspecialchars($pageTitle) : 'Dashboard' ?></div>
            </div>
        </header>
        <div class="content">
            <?php if (!empty($_SESSION['flash'])): ?>
                <?php foreach ($_SESSION['flash'] as $f): ?>
                    <div class="alert alert-<?= $f['type'] === 'error' ? 'error' : 'success' ?>">
                        <?= htmlspecialchars($f['message']) ?>
                    </div>
                <?php endforeach; ?>
                <?php unset($_SESSION['flash']); ?>
            <?php endif; ?>
