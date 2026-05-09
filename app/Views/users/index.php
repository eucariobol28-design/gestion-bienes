<?php declare(strict_types=1); ?>
<?php $users = $users ?? []; ?>
<div class="d-flex justify-content-between align-items-center mb-4">
  <div>
    <h1 class="mb-0">Usuarios</h1>
    <p class="text-muted mb-0">Administra cuentas de usuario y roles del sistema.</p>
  </div>
  <a class="btn btn-success" href="/users/create">+ Crear usuario</a>
</div>

<?php require __DIR__ . '/../components/flash.php'; ?>
<?php $search = $search ?? ''; ?>
<?php $role = $role ?? ''; ?>
<?php $page = $page ?? 1; ?>
<?php $pages = $pages ?? 1; ?>
<?php $total = $total ?? 0; ?>
<?php $perPage = $perPage ?? 10; ?>

<div class="card mb-4">
  <div class="card-body">
    <form method="get" action="/users" class="row g-3 align-items-end">
      <div class="col-md-4">
        <label class="form-label">Buscar</label>
        <input type="search" name="search" class="form-control" placeholder="Nombre o email" value="<?= htmlspecialchars($search, ENT_QUOTES, 'UTF-8') ?>">
      </div>
      <div class="col-md-3">
        <label class="form-label">Filtrar por rol</label>
        <select class="form-select" name="role">
          <option value="">Todos</option>
          <option value="admin" <?= $role === 'admin' ? 'selected' : '' ?>>Admin</option>
          <option value="operador" <?= $role === 'operador' ? 'selected' : '' ?>>Operador</option>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Resultados</label>
        <div class="form-control-plaintext text-muted"><?= $total ?> registros</div>
      </div>
      <div class="col-md-2 d-grid">
        <button type="submit" class="btn btn-primary">Aplicar</button>
      </div>
    </form>
  </div>
</div>
<div class="card shadow-sm">
  <div class="card-body">
    <div class="table-responsive">
      <table class="table table-striped align-middle mb-0">
  <thead>
    <tr>
      <th>Nombre</th>
      <th>Email</th>
      <th>Rol</th>
      <th>Acciones</th>
    </tr>
  </thead>
  <tbody>
    <?php foreach ($users as $user): ?>
      <tr>
        <td><?= htmlspecialchars($user['nombre'], ENT_QUOTES, 'UTF-8') ?></td>
        <td><?= htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8') ?></td>
        <td>
          <?php if ($user['rol'] === 'admin'): ?>
            <span class="badge bg-primary">Admin</span>
          <?php else: ?>
            <span class="badge bg-secondary">Operador</span>
          <?php endif; ?>
        </td>
        <td>
          <a href="/users/edit?id=<?= $user['id'] ?>" class="btn btn-sm btn-outline-primary">Editar</a>
          <form method="post" action="/users/delete" class="d-inline-block ms-1" onsubmit="return confirm('¿Eliminar este usuario?');">
            <input type="hidden" name="id" value="<?= htmlspecialchars((string)$user['id'], ENT_QUOTES, 'UTF-8') ?>">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf ?? '', ENT_QUOTES, 'UTF-8') ?>">
            <button type="submit" class="btn btn-sm btn-outline-danger">Eliminar</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
    </table>
    </div>
    <?php if (empty($users)): ?>
      <div class="text-center text-muted py-4">
        No se encontraron usuarios. Usa el botón "Crear usuario" para agregar uno.
      </div>
    <?php endif; ?>
    <?php if ($pages > 1): ?>
      <nav aria-label="Paginación de usuarios" class="mt-4">
        <ul class="pagination justify-content-center mb-0">
          <?php for ($i = 1; $i <= $pages; $i++): ?>
            <?php $params = http_build_query(['search' => $search, 'role' => $role, 'page' => $i]); ?>
            <li class="page-item <?= $i === $page ? 'active' : '' ?>">
              <a class="page-link" href="/users?<?= $params ?>"><?= $i ?></a>
            </li>
          <?php endfor; ?>
        </ul>
      </nav>
    <?php endif; ?>
  </div>
</div>