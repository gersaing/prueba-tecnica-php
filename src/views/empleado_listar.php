<div class="container">
  <h1 class="page-title">Lista de empleados</h1>

  <?php if (!empty($_SESSION['flash'])): ?>
    <div class="alert alert-success">
      <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
    </div>
  <?php endif; ?>

  <div class="list-toolbar">
    <a href="index.php?action=create" class="btn btn-primary">➕ Crear</a>
  </div>

  <div class="card">
    <div class="table-responsive">
      <table class="table table-compact">
        <thead>
          <tr>
            <th>Nombre</th>
            <th>Email</th>
            <th>Sexo</th>
            <th>Área</th>
            <th>Boletín</th>
            <th class="col-action">Modificar</th>
            <th class="col-action">Eliminar</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($empleados as $e): ?>
            <tr>
              <td><?= htmlspecialchars($e['nombre']) ?></td>
              <td><?= htmlspecialchars($e['email']) ?></td>
              <td><?= $e['sexo'] === 'F' ? 'Femenino' : 'Masculino' ?></td>
              <td><?= htmlspecialchars($e['area']) ?></td>
              <td><?= $e['boletin'] ? 'Sí' : 'No' ?></td>
              <td class="col-action">
                <a href="index.php?action=edit&id=<?= $e['id'] ?>" class="icon-btn" title="Modificar" aria-label="Modificar">
                  <svg viewBox="0 0 24 24" class="icon">
                    <path d="M3 17.25V21h3.75L17.81 9.94l-3.75-3.75L3 17.25zM20.71 7.04a1 1 0 0 0 0-1.41l-2.34-2.34a1 1 0 0 0-1.41 0l-1.83 1.83 3.75 3.75 1.83-1.83z"/>
                  </svg>
                </a>
              </td>
              <td class="col-action">
                <a href="index.php?action=delete&id=<?= $e['id'] ?>" class="icon-btn danger" title="Eliminar"
                   onclick="return confirm('¿Seguro que deseas eliminar este empleado?')">
                  <svg viewBox="0 0 24 24" class="icon">
                    <path d="M9 3h6l1 2h4v2H4V5h4l1-2zm2 7h2v8h-2v-8zM7 10h2v8H7v-8zm8 0h2v8h-2v-8z"/>
                  </svg>
                </a>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
