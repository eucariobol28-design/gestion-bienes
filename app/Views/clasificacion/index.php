<?php declare(strict_types=1); // Modo estricto. ?>
<?php
$categorias = $categorias ?? []; // Array de categorías.
$ubicaciones = $ubicaciones ?? []; // Array de ubicaciones.
?>
<h1 class="mb-3">Clasificación</h1> <!-- Título. -->

<?php require __DIR__ . '/../components/flash.php'; // Mensajes flash. ?>

<div class="row">
  <div class="col-md-6">
    <h3>Categorías</h3> <!-- Sección categorías. -->
    <a class="btn btn-success mb-3" href="/clasificacion/createCategoria">+ Crear Categoría</a>
    <ul class="list-group">
      <?php foreach ($categorias as $cat): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($cat['nombre'], ENT_QUOTES, 'UTF-8') ?>
          <div>
            <a href="/clasificacion/editCategoria?id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
            <form method="post" action="/clasificacion/deleteCategoria" class="d-inline">
              <input type="hidden" name="id" value="<?= $cat['id'] ?>">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_categoria_delete ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>

  <div class="col-md-6">
    <h3>Ubicaciones</h3> <!-- Sección ubicaciones. -->
    <a class="btn btn-success mb-3" href="/clasificacion/createUbicacion">+ Crear Ubicación</a>
    <ul class="list-group">
      <?php foreach ($ubicaciones as $ubi): ?>
        <li class="list-group-item d-flex justify-content-between align-items-center">
          <?= htmlspecialchars($ubi['nombre'], ENT_QUOTES, 'UTF-8') ?>
          <div>
            <a href="/clasificacion/editUbicacion?id=<?= $ubi['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
            <form method="post" action="/clasificacion/deleteUbicacion" class="d-inline">
              <input type="hidden" name="id" value="<?= $ubi['id'] ?>">
              <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_ubicacion_delete ?? '', ENT_QUOTES, 'UTF-8') ?>">
              <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('¿Eliminar?')">Eliminar</button>
            </form>
          </div>
        </li>
      <?php endforeach; ?>
    </ul>
  </div>
</div>