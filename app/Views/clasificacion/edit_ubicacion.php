<?php declare(strict_types=1); ?>
<h1 class="mb-3">Editar Ubicación</h1>
<?php require __DIR__ . '/../components/flash.php'; ?>
<form method="post" action="/clasificacion/updateUbicacion" class="card p-4" style="max-width: 520px;">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <input type="hidden" name="id" value="<?= (int)($ubicacion['id'] ?? 0) ?>">
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input class="form-control" name="nombre" type="text" value="<?= htmlspecialchars($ubicacion['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
  </div>
  <button class="btn btn-primary" type="submit">Actualizar ubicación</button>
  <a class="btn btn-secondary ms-2" href="/clasificacion">Cancelar</a>
</form>
