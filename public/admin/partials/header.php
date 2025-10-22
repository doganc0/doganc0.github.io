<?php
if (!isset($pageTitle)) {
    $pageTitle = 'Yönetim';
}
$settingsRepo = new Settings($db);
$currentSettings = $settingsRepo->getSettings();
?>
<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?> - Magnus Admin</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body {
            background: #f3f4fb;
        }
        .admin-container {
            display: grid;
            grid-template-columns: 260px 1fr;
            min-height: 100vh;
        }
        .sidebar {
            background: #fff;
            box-shadow: 8px 0 24px rgba(26, 26, 67, 0.06);
            padding: 2rem 1.5rem;
            display: flex;
            flex-direction: column;
            gap: 2rem;
        }
        .sidebar__logo img {
            max-height: 48px;
        }
        .sidebar__logo span {
            font-size: 1.35rem;
            color: var(--primary);
            font-weight: 600;
        }
        .sidebar nav {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }
        .sidebar nav a {
            text-decoration: none;
            color: var(--muted);
            font-weight: 500;
            padding: 0.6rem 0.9rem;
            border-radius: 12px;
            transition: background 0.3s ease, color 0.3s ease;
        }
        .sidebar nav a.active,
        .sidebar nav a:hover {
            background: rgba(93, 63, 211, 0.12);
            color: var(--primary);
        }
        .admin-content {
            padding: 2.5rem 3rem;
        }
        .admin-content header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
        }
        .admin-content h1 {
            margin: 0;
            font-size: 1.75rem;
            color: var(--primary);
        }
        .logout-link {
            color: var(--muted);
            text-decoration: none;
            font-weight: 500;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--shadow);
        }
        table th, table td {
            padding: 0.9rem 1rem;
            border-bottom: 1px solid #ececff;
            font-size: 0.95rem;
        }
        table th {
            background: #f7f7fe;
            text-align: left;
        }
        table tr:last-child td {
            border-bottom: none;
        }
        .card-box {
            background: #fff;
            padding: 1.5rem;
            border-radius: 16px;
            box-shadow: var(--shadow);
            margin-bottom: 1.5rem;
        }
        .form-inline {
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
        }
        .form-inline .form-group {
            flex: 1;
            min-width: 220px;
        }
        .actions {
            display: flex;
            gap: 0.5rem;
        }
        .badge {
            display: inline-block;
            padding: 0.35rem 0.8rem;
            border-radius: 999px;
            background: rgba(93, 63, 211, 0.12);
            color: var(--primary);
            font-size: 0.85rem;
        }
        @media (max-width: 1024px) {
            .admin-container {
                grid-template-columns: 1fr;
            }
            .sidebar {
                flex-direction: row;
                justify-content: space-between;
                align-items: center;
            }
            .sidebar nav {
                flex-direction: row;
                flex-wrap: wrap;
            }
        }
    </style>
</head>
<body>
<div class="admin-container">
    <aside class="sidebar">
        <div class="sidebar__logo">
            <?php if (!empty($currentSettings['site_logo'])): ?>
                <img src="<?= htmlspecialchars($currentSettings['site_logo']) ?>" alt="Magnus">
            <?php else: ?>
                <span>Magnus Admin</span>
            <?php endif; ?>
        </div>
        <nav>
            <?php
            $menu = [
                'dashboard.php' => 'Genel Bakış',
                'settings.php' => 'Genel Ayarlar',
                'insurance_types.php' => 'Sigorta Türleri',
                'form_builder.php' => 'Dinamik Formlar',
                'submissions.php' => 'Gelen Teklifler',
            ];
            $current = basename($_SERVER['PHP_SELF']);
            foreach ($menu as $file => $label):
            ?>
                <a href="<?= $file ?>" class="<?= $current === $file ? 'active' : '' ?>"><?= $label ?></a>
            <?php endforeach; ?>
        </nav>
        <a class="logout-link" href="logout.php">Çıkış Yap</a>
    </aside>
    <main class="admin-content">
        <header>
            <h1><?= htmlspecialchars($pageTitle) ?></h1>
        </header>
