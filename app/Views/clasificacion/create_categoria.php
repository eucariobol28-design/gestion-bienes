<?php declare(strict_types=1); ?>
<h1 class="mb-3">Crear Categoría</h1>
<?php require __DIR__ . '/../components/flash.php'; ?>
<form method="post" action="/clasificacion/storeCategoria" class="card p-4" style="max-width: 520px;">
  <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
  <div class="mb-3">
    <label class="form-label">Nombre</label>
    <input class="form-control" name="nombre" type="text" required>
  </div>
  <button class="btn btn-primary" type="submit">Guardar categoría</button>
  <a class="btn btn-secondary ms-2" href="/clasificacion">Cancelar</a>
</form>
