<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'App', ENT_QUOTES, 'UTF-8') ?></title>
  <!-- Bootstrap CSS (local) -->
  <link href="/assets/css/bootstrap-local-min.css" rel="stylesheet">

  <!-- CSS custom para Signal-like -->
  <link href="/assets/css/signal-style.css" rel="stylesheet">
</head>
<body>
  <?php if(isset($_SESSION['user'])): ?>
  <!-- Sidebar -->
  <nav id="sidebar" class="bg-dark text-white">
    <div class="sidebar-header p-3 text-center">
      <img src="/assets/images/mision%20sucre.jpeg" alt="Logo Misión Sucre" class="rounded-circle mb-2" style="width: 48px; height: 48px;">
      <h5 class="mb-0">Aldea Universitaria</h5>
      <small class="text-muted">Carlos Emiliano Salom</small>
    </div>
    <ul class="list-unstyled components p-2">
      <li><a href="/">📊 Dashboard</a></li>
      <li><a href="/bienes">📦 Bienes</a></li>
      <?php if (($_SESSION['user']['rol'] ?? '') === 'admin'): ?>
        <li><a href="/clasificacion">🏷️ Clasificación</a></li>
        <li><a href="/users">👥 Usuarios</a></li>
        <li><a href="/reportes">📈 Reportes</a></li>
      <?php endif; ?>
    </ul>
  </nav>
  <?php endif; ?>

  <!-- Contenido principal -->
  <div id="content">
    <!-- Header -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
      <div class="container-fluid">
        <?php if(isset($_SESSION['user'])): ?>
        <button type="button" id="sidebarCollapse" class="btn btn-outline-secondary">
          ☰
        </button>
        <?php endif; ?>
        <div class="d-flex align-items-center">
          <img src="/assets/images/mision%20sucre.jpeg" alt="Logo Misión Sucre" class="rounded-circle me-2" style="width: 32px; height: 32px;">
          <span class="fw-bold text-primary">Aldea Universitaria</span>
        </div>
        <div class="ms-auto d-flex align-items-center">
          <?php if(isset($_SESSION['user'])): ?>
          <span class="me-3 text-muted">Hola, <?= htmlspecialchars($_SESSION['user']['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></span>
          <a class="btn btn-outline-danger btn-sm" href="/auth/logout">🔒 Salir</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>

    <!-- Contenedor de página -->
    <div class="container-fluid p-4">

