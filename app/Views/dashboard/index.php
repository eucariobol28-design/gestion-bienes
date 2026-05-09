<?php declare(strict_types=1); // Modo estricto para PHP embebido. ?>
<h1 class="mb-4 text-primary">📊 Dashboard</h1> <!-- Título con icono local. -->

<?php require __DIR__ . '/../components/flash.php'; // Incluye componente para mostrar mensajes flash (éxito/error). ?>

<div class="row g-3"> <!-- Contenedor Bootstrap con filas y gutters. -->
  <div class="col-md-4"> <!-- Columna para total de bienes (4/12 en md). -->
    <div class="card h-100"> <!-- Tarjeta con altura completa. -->
      <div class="card-body text-center"> <!-- Cuerpo centrado. -->
        <div class="display-3 text-primary mb-3">📦</div> <!-- Icono local de cajas. -->
        <div class="text-muted">Total Bienes</div> <!-- Etiqueta gris. -->
        <div class="fs-1 fw-bold text-primary"><?= (int)$total ?></div> <!-- Número grande en azul. -->
      </div>
    </div>
  </div>

  <div class="col-md-8"> <!-- Columna para bienes por estado (8/12 en md). -->
    <div class="card h-100"> <!-- Tarjeta. -->
      <div class="card-body">
        <div class="fs-2 text-success mb-2">📈</div> <!-- Icono local de gráfico. -->
        <div class="text-muted mb-3">Bienes por Estado</div> <!-- Etiqueta. -->
        <div class="row g-3"> <!-- Fila con gutters. -->
          <?php if (empty($porEstado)): // Si no hay datos por estado. ?>
            <div class="text-muted">Sin datos</div> <!-- Mensaje si vacío. -->
          <?php else: // Si hay datos. ?>
            <?php foreach ($porEstado as $row): // Itera sobre array de estados. ?>
              <div class="col-6 col-lg-4"> <!-- Columnas responsive. -->
                <div class="card border-0 shadow-sm h-100"> <!-- Sub-card moderna. -->
                  <div class="card-body text-center">
                    <div class="fw-semibold text-primary"><?= htmlspecialchars((string)($row['estado'] ?? ''), ENT_QUOTES, 'UTF-8') ?></div> <!-- Estado. -->
                    <div class="fs-2 fw-bold text-success"><?= (int)($row['c'] ?? 0) ?></div> <!-- Conteo. -->
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>
    </div>
  </div>
</div>

