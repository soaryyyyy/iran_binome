<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'Backoffice') ?> — Administration</title>
    <meta name="robots" content="noindex, nofollow">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            background: #f0f2f5;
            color: #222;
            display: flex;
            min-height: 100vh;
        }

        /* ---- Sidebar ---- */
        .sidebar {
            width: 240px;
            background: #1a1a2e;
            color: #fff;
            display: flex;
            flex-direction: column;
            position: fixed;
            top: 0; left: 0;
            height: 100vh;
            z-index: 100;
        }

        .sidebar-logo {
            padding: 24px 20px;
            border-bottom: 1px solid #2a2a4a;
        }

        .sidebar-logo h1 {
            font-size: 20px;
            letter-spacing: 1px;
        }

        .sidebar-logo h1 span { color: #e8a020; }

        .sidebar-logo p {
            font-size: 11px;
            color: #888;
            margin-top: 4px;
        }

        .sidebar nav { flex: 1; padding: 16px 0; }

        .sidebar nav ul { list-style: none; }

        .sidebar nav ul li a {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 20px;
            color: #bbb;
            text-decoration: none;
            font-size: 14px;
            transition: background 0.2s, color 0.2s;
            border-left: 3px solid transparent;
        }

        .sidebar nav ul li a:hover,
        .sidebar nav ul li a.active {
            background: #16213e;
            color: #fff;
            border-left-color: #e8a020;
        }

        .sidebar-footer {
            padding: 16px 20px;
            border-top: 1px solid #2a2a4a;
        }

        .sidebar-footer a {
            display: block;
            color: #e74c3c;
            text-decoration: none;
            font-size: 13px;
            padding: 8px 0;
        }

        .sidebar-footer a:hover { color: #ff6b6b; }

        /* ---- Main content ---- */
        .main-wrapper {
            margin-left: 240px;
            flex: 1;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        .topbar {
            background: #fff;
            padding: 16px 32px;
            border-bottom: 1px solid #e0e0e0;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar h2 {
            font-size: 20px;
            color: #1a1a2e;
        }

        .topbar .user {
            font-size: 13px;
            color: #888;
        }

        .topbar .user strong { color: #1a1a2e; }

        .content {
            padding: 32px;
            flex: 1;
        }

        /* ---- Composants réutilisables ---- */
        .btn {
            display: inline-block;
            padding: 8px 18px;
            border-radius: 5px;
            font-size: 14px;
            text-decoration: none;
            cursor: pointer;
            border: none;
            transition: background 0.2s;
        }

        .btn-primary { background: #4a90e2; color: #fff; }
        .btn-primary:hover { background: #357abd; }

        .btn-success { background: #27ae60; color: #fff; }
        .btn-success:hover { background: #1e8449; }

        .btn-danger { background: #e74c3c; color: #fff; }
        .btn-danger:hover { background: #c0392b; }

        .btn-secondary { background: #95a5a6; color: #fff; }
        .btn-secondary:hover { background: #7f8c8d; }

        .btn-sm { padding: 5px 12px; font-size: 12px; }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 6px rgba(0,0,0,0.08);
            padding: 24px;
            margin-bottom: 24px;
        }

        .card h3 {
            font-size: 16px;
            margin-bottom: 16px;
            color: #1a1a2e;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f2f5;
        }

        .alert {
            padding: 12px 16px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 14px;
        }

        .alert-success { background: #d5f5e3; color: #1e8449; border: 1px solid #27ae60; }
        .alert-error   { background: #fdecea; color: #c0392b; border: 1px solid #e74c3c; }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        table th {
            background: #f8f9fa;
            padding: 10px 14px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            color: #888;
            border-bottom: 2px solid #e0e0e0;
        }

        table td {
            padding: 12px 14px;
            border-bottom: 1px solid #f0f2f5;
            vertical-align: middle;
        }

        table tr:hover td { background: #fafafa; }

        .badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: bold;
        }

        .badge-green  { background: #d5f5e3; color: #1e8449; }
        .badge-grey   { background: #ecf0f1; color: #7f8c8d; }
    </style>
</head>
<body>

<aside class="sidebar">
    <div class="sidebar-logo">
        <h1>Iran <span>Admin</span></h1>
        <p>Backoffice</p>
    </div>
    <nav aria-label="Navigation backoffice">
        <ul>
            <li>
                <a href="/backoffice/dashboard"
                   <?= str_contains($_SERVER['REQUEST_URI'], 'dashboard') ? 'class="active"' : '' ?>>
                   Tableau de bord
                </a>
            </li>
            <li>
                <a href="/backoffice/articles"
                   <?= str_contains($_SERVER['REQUEST_URI'], 'articles') ? 'class="active"' : '' ?>>
                   Articles
                </a>
            </li>
            <li>
                <a href="/backoffice/article/nouveau">
                    + Nouvel article
                </a>
            </li>
        </ul>
    </nav>
    <div class="sidebar-footer">
        <a href="/backoffice/logout">Déconnexion</a>
    </div>
</aside>

<div class="main-wrapper">
    <div class="topbar">
        <h2><?= htmlspecialchars($pageTitle ?? '') ?></h2>
        <span class="user">Connecté : <strong><?= htmlspecialchars($_SESSION['username'] ?? '') ?></strong></span>
    </div>
    <div class="content">
