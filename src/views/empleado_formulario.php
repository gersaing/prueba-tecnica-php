<?php
// Si venimos de editar, $empleado tendrá datos
require_once __DIR__ . '/../models/Rol.php';
$isEdit = isset($empleado);
?>

<h2><?= $isEdit ? "Editar empleado" : "Nuevo empleado" ?></h2>

<?php if (!empty($_SESSION['errors'])): ?>
    <div style="color:red;">
        <ul>
            <?php foreach ($_SESSION['errors'] as $error): ?>
                <li><?= htmlspecialchars($error) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
    <?php unset($_SESSION['errors']); ?>
<?php endif; ?>

<form method="POST" action="index.php?action=<?= $isEdit ? "update" : "store" ?>">
    <?php if ($isEdit): ?>
        <input type="hidden" name="id" value="<?= $empleado->getId() ?>">
    <?php endif; ?>

    <!-- Text -->
    <label>Nombre:</label><br>
    <input type="text" name="nombre" required
           value="<?= $isEdit ? htmlspecialchars($empleado->getNombre()) : "" ?>"><br><br>

    <!-- Text (email) -->
    <label>Email:</label><br>
    <input type="email" name="email" required
           value="<?= $isEdit ? htmlspecialchars($empleado->getEmail()) : "" ?>"><br><br>

    <!-- Radio -->
    <label>Sexo:</label><br>
    <input type="radio" name="sexo" value="M"
        <?= $isEdit && $empleado->getSexo() === 'M' ? "checked" : "" ?>> Masculino
    <input type="radio" name="sexo" value="F"
        <?= $isEdit && $empleado->getSexo() === 'F' ? "checked" : "" ?>> Femenino
    <br><br>

    <!-- Select -->
    <label>Área:</label><br>
    <select name="area_id" required>
        <option value="">Seleccione...</option>
        <?php foreach ($areas as $area): ?>
            <option value="<?= $area->getId() ?>"
                <?= $isEdit && $empleado->getArea() && $empleado->getArea()->getId() == $area->getId() ? "selected" : "" ?>>
                <?= htmlspecialchars($area->getNombre()) ?>
            </option>
        <?php endforeach; ?>
    </select>
    <br><br>


    <!-- Checkbox -->
    <label>
        <input type="checkbox" name="boletin" value="1"
            <?= $isEdit && $empleado->getBoletin() ? "checked" : "" ?>>
        Deseo recibir boletín informativo
    </label>
    <br><br>

    <!-- Textarea -->
    <label>Descripción:</label><br>
    <textarea name="descripcion" required><?= $isEdit ? htmlspecialchars($empleado->getDescripcion()) : "" ?></textarea>
    <br><br>

    <!--Roles-->
    <!-- Checkbox múltiple -->

    <?php
    $empleadoRoleIds = [];
    if ($isEdit && $empleado) {
        foreach ($empleado->getRoles() as $r) {
            $empleadoRoleIds[] = $r->getId();
        }
    }
    ?>

    <!--Roles-->
    <label>Roles:</label><br>
    <?php foreach ($roles as $rol): ?>
        <label>
            <input type="checkbox" name="roles[]" value="<?= $rol->getId() ?>"
                <?= $isEdit && in_array($rol->getId(), $empleadoRoleIds) ? "checked" : "" ?>>
            <?= htmlspecialchars($rol->getNombre()) ?>
        </label><br>
    <?php endforeach; ?>
    <br>



    <button type="submit"><?= $isEdit ? "Actualizar" : "Guardar" ?></button>
</form>

<a href="index.php?action=list">⬅ Volver al listado</a>
