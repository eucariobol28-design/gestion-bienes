<?php declare(strict_types=1); ?>
<!doctype html>
<html lang="es">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title><?= htmlspecialchars($title ?? 'App', ENT_QUOTES, 'UTF-8') ?></title>

  <!-- Bootstrap CSS (local) -->
  <link href="/assets/css/bootstrap-local-min.css" rel="stylesheet">

  <!-- CSS custom para Signal-like (opcional, pero mantiene estilos del sistema) -->
  <link href="/assets/css/signal-style.css" rel="stylesheet">

  <style>
    /* Estilos mínimos para centrar el login sin depender del layout del dashboard */
    body {
      background: #f8f9fa;
    }
    .login-wrapper {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 24px 0;
    }
  </style>
</head>
<body>
  <div class="login-wrapper">
    <div>

