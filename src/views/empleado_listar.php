<h2>Listado de empleados</h2>

<?php if (!empty($_SESSION['flash'])): ?>
    <div style="color:green;">
        <?= $_SESSION['flash']; unset($_SESSION['flash']); ?>
    </div>
<?php endif; ?>

<a href="index.php?action=create">➕ Nuevo empleado</a>
<br><br>

<table border="1" cellpadding="5">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Email</th>
            <th>Sexo</th>
            <th>Área</th>
            <th>Boletín</th>
            <th>Descripción</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>
    <?php foreach ($empleados as $e): ?>
        <tr>
            <td><?= $e['id'] ?></td>
            <td><?= htmlspecialchars($e['nombre']) ?></td>
            <td><?= htmlspecialchars($e['email']) ?></td>
            <td><?= $e['sexo'] ?></td>
            <td><?= htmlspecialchars($e['area']) ?></td>
            <td><?= $e['boletin'] ? "Sí" : "No" ?></td>
            <td><?= htmlspecialchars($e['descripcion']) ?></td>
            <td>
                <a href="index.php?action=edit&id=<?= $e['id'] ?>">✏ Editar</a> |
                <a href="index.php?action=delete&id=<?= $e['id'] ?>"
                   onclick="return confirm('¿Seguro que deseas eliminar este empleado?')">🗑 Eliminar</a>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
