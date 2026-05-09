<?php declare(strict_types=1); ?>
<h1 class="mb-3">Iniciar sesión</h1>
<?php require __DIR__ . '/../components/flash.php'; ?>

<form method="post" action="/auth/doLogin" class="card p-3" style="max-width: 520px;">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <div class="mb-3">
    <label class="form-label">Correo</label>
    <input class="form-control" name="email" type="email" autocomplete="username" required>
  </div>

  <div class="mb-3">
    <label class="form-label">Contraseña</label>
    <input class="form-control" name="password" type="password" autocomplete="current-password" required>
  </div>

  <button class="btn btn-primary" type="submit">Entrar</button>
</form>

