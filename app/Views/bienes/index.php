<?php declare(strict_types=1); // Modo estricto. ?>
<?php
$estado = $estado ?? null; // Estado filtrado, default null.
$bienes = $bienes ?? []; // Array de bienes, default vacío.
?>
<h1 class="mb-4 text-primary">📦 Bienes</h1> <!-- Título con icono local. -->

<?php require __DIR__ . '/../components/flash.php'; // Incluye mensajes flash. ?>

<div class="card mb-3"> <!-- Tarjeta para filtro. -->
  <div class="card-body d-flex gap-2 align-items-end flex-wrap"> <!-- Cuerpo flexible. -->
    <div>
      <label class="form-label">Filtrar por estado</label> <!-- Etiqueta. -->
      <select class="form-select" name="estado" form="filterForm"> <!-- Select ligado a form. -->
        <option value="" <?= $estado === null || $estado === '' ? 'selected' : '' ?>>Todos</option> <!-- Opción todos. -->
        <option value="activo" <?= $estado === 'activo' ? 'selected' : '' ?>>activo</option> <!-- Activo. -->
        <option value="inactivo" <?= $estado === 'inactivo' ? 'selected' : '' ?>>inactivo</option> <!-- Inactivo. -->
        <option value="en_mantenimiento" <?= $estado === 'en_mantenimiento' ? 'selected' : '' ?>>en mantenimiento</option> <!-- Mantenimiento. -->
      </select>
    </div>
    <div>
      <button class="btn btn-primary" type="submit" form="filterForm">Aplicar</button> <!-- Botón aplicar. -->
      <a class="btn btn-outline-secondary" href="/bienes">Limpiar</a> <!-- Enlace limpiar. -->
    </div>
  </div>
</div>

<form id="filterForm" method="get" action="/bienes" class="mb-3"></form> <!-- Form oculto para filtro. -->

<a class="btn btn-success mb-3" href="/bienes/create">➕ Registrar Bien</a> <!-- Botón con icono local. -->

<div class="table-responsive">
<table class="table table-hover align-middle"> <!-- Tabla con hover. -->
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Código</th>
      <th>Ubicación</th>
      <th>Estado</th>
      <th>Responsable</th>
      <th class="text-end">Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!$bienes): ?>
      <tr><td colspan="6" class="text-center text-muted py-4">No hay bienes registrados</td></tr>
    <?php else: ?>
      <?php foreach ($bienes as $b): ?>
        <tr>
          <td><?= htmlspecialchars($b['nombre'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($b['codigo'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($b['ubicacion'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($b['estado'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td><?= htmlspecialchars($b['responsable'] ?? '', ENT_QUOTES, 'UTF-8') ?></td>
          <td class="text-end">
            <a class="btn btn-sm btn-outline-primary" href="/bienes/edit?id=<?= (int)($b['id'] ?? 0) ?>">Editar</a>
            <form method="post" action="/bienes/delete" class="d-inline">
              <input type="hidden" name="id" value="<?= (int)$b['id'] ?>">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar este bien?')">Eliminar</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    <?php endif; ?>
  </tbody>
</table>
</div>

