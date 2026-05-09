<?php declare(strict_types=1); ?>
<?php $user = $user ?? []; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-0">Crear usuario</h1>
    <p class="text-muted mb-0">Agrega un nuevo usuario con rol de administrador u operador.</p>
  </div>
  <a class="btn btn-outline-secondary" href="/users">← Volver a usuarios</a>
</div>

<?php require __DIR__ . '/../components/flash.php'; ?>

<div class="card shadow-sm">
  <div class="card-body">
    <form method="post" action="/users/store">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nombre</label>
      <input class="form-control" name="nombre" value="<?= htmlspecialchars($user['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Email</label>
      <input class="form-control" type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Contraseña</label>
      <input class="form-control" type="password" name="password" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Rol</label>
      <select class="form-select" name="rol" required>
        <option value="">Selecciona un rol</option>
        <option value="admin" <?= (isset($user['rol']) && $user['rol'] === 'admin') ? 'selected' : '' ?>>Admin</option>
        <option value="operador" <?= (isset($user['rol']) && $user['rol'] === 'operador') ? 'selected' : '' ?>>Operador</option>
      </select>
    </div>
  </div>

  <div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Guardar</button>
    <a class="btn btn-outline-secondary" href="/users">Cancelar</a>
  </div>
    </form>
  </div>
</div>
