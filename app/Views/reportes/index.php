<?php declare(strict_types=1); ?>
<h1 class="mb-3">Reportes</h1>

<?php require __DIR__ . '/../components/flash.php'; ?>

<form method="get" action="/reportes/generate" class="card p-3">
  <div class="mb-3">
    <label class="form-label">Filtrar por Estado</label>
    <select name="estado" class="form-select">
      <option value="">Todos</option>
      <option value="activo">Activo</option>
      <option value="inactivo">Inactivo</option>
      <option value="en_mantenimiento">En Mantenimiento</option>
    </select>
  </div>
  <button type="submit" class="btn btn-primary">Generar Reporte CSV</button>
</form>