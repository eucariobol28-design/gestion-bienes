<?php declare(strict_types=1); ?>
<?php $bien = $bien ?? []; ?>
<h1 class="mb-3">Editar Bien</h1>
<?php require __DIR__ . '/../components/flash.php'; ?>

<form method="post" action="/bienes/update" class="card p-3">
  <input type="hidden" name="id" value="<?= (int)($bien['id'] ?? 0) ?>">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">

  <div class="row g-3">
    <div class="col-md-6">
      <label class="form-label">Nombre</label>
      <input class="form-control" name="nombre" value="<?= htmlspecialchars($bien['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Código</label>
      <input class="form-control" name="codigo" value="<?= htmlspecialchars($bien['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>
    <div class="col-md-6">
      <label class="form-label">Ubicación</label>
      <input class="form-control" name="ubicacion" value="<?= htmlspecialchars($bien['ubicacion'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Estado</label>
      <input class="form-control" name="estado" value="<?= htmlspecialchars($bien['estado'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>
    <div class="col-md-3">
      <label class="form-label">Responsable</label>
      <input class="form-control" name="responsable" value="<?= htmlspecialchars($bien['responsable'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
    </div>
  </div>

  <div class="mt-4 d-flex gap-2">
    <button class="btn btn-primary" type="submit">Actualizar</button>
    <a class="btn btn-outline-secondary" href="/bienes">Cancelar</a>
  </div>
</form>

