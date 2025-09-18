<?php
// $isEdit ya debe venir del controlador (true|false) y $areas, $roles, $empleado cuando aplique
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="container">
    <h2 class="text-center mb-3"><?= $isEdit ? "Editar empleado" : "Nuevo empleado" ?></h2>
    <?php $hasErrors = !empty($_SESSION['errors']); ?>
    <div id="campos-obligatorios"
        class="alert <?= $hasErrors ? 'alert-danger' : 'alert-info' ?>">
        <?php if ($hasErrors): ?>
            <ul class="mb-0">
                <?php foreach ($_SESSION['errors'] as $error): ?>
                    <li><?= htmlspecialchars($error) ?></li>
                <?php endforeach; ?>
            </ul>
            <?php unset($_SESSION['errors']); ?>
        <?php else: ?>
            Los campos con asteriscos (*) son obligatorios
        <?php endif; ?>
    </div>


    <div class="card">
        <form id="empleado-form" class="needs-validation" novalidate
            method="POST" action="index.php?action=<?= $isEdit ? "update" : "store" ?>">

            <?php if ($isEdit): ?>
                <input type="hidden" name="id" value="<?= $empleado->getId() ?>">
            <?php endif; ?>

            <!-- Nombre -->
            <div class="form-group">
                <label>Nombre *</label>
                <input
                    id="nombre"
                    type="text"
                    name="nombre"
                    required
                    minlength="3"
                    maxlength="100"
                    pattern="^[A-Za-zÁ-ÿ\s.'-]{3,100}$"
                    autocomplete="name"
                    value="<?= $isEdit ? htmlspecialchars($empleado->getNombre()) : "" ?>">
            </div>

            <!-- Email -->
            <div class="form-group">
                <label>Email *</label>
                <input
                    id="email"
                    type="email"
                    name="email"
                    required
                    maxlength="120"
                    autocomplete="email"
                    inputmode="email"
                    value="<?= $isEdit ? htmlspecialchars($empleado->getEmail()) : "" ?>">
            </div>

            <!-- Sexo -->
            <div class="form-group">
                <label>Sexo *</label>
                <div class="inline-group">
                    <label class="form-check">
                        <input type="radio" name="sexo" value="M" <?= $isEdit && $empleado->getSexo() === 'M' ? "checked" : "" ?> required>
                        Masculino
                    </label>
                    <label class="form-check">
                        <input type="radio" name="sexo" value="F" <?= $isEdit && $empleado->getSexo() === 'F' ? "checked" : "" ?>>
                        Femenino
                    </label>
                </div>
            </div>

            <!-- Área -->
            <div class="form-group">
                <label>Área *</label>
                <select name="area_id" required>
                    <option value="">Seleccione...</option>
                    <?php foreach ($areas as $area): ?>
                        <option value="<?= $area->getId() ?>"
                            <?= $isEdit && $empleado->getArea() && $empleado->getArea()->getId() == $area->getId() ? "selected" : "" ?>>
                            <?= htmlspecialchars($area->getNombre()) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <!-- Boletín -->
            <div class="form-group">
                <label class="form-check">
                    <input type="checkbox" name="boletin" value="1" <?= $isEdit && $empleado->getBoletin() ? "checked" : "" ?>>
                    Deseo recibir boletín informativo
                </label>
            </div>

            <!-- Descripción -->
            <div class="form-group">
                <label>Descripción *</label>
                <textarea
                    id="desc"
                    name="descripcion"
                    required
                    minlength="5"
                    maxlength="500"><?= $isEdit ? htmlspecialchars($empleado->getDescripcion()) : "" ?></textarea>
            </div>

            <!-- Roles -->
            <?php
            $empleadoRoleIds = [];
            if ($isEdit && $empleado) {
                foreach ($empleado->getRoles() as $r) {
                    $empleadoRoleIds[] = $r->getId();
                }
            }
            ?>
            <div class="form-group">
                <label>Roles *</label>
                <?php foreach ($roles as $rol): ?>
                    <label class="form-check">
                        <input type="checkbox" name="roles[]" value="<?= $rol->getId() ?>"
                            <?= $isEdit && in_array($rol->getId(), $empleadoRoleIds) ? "checked" : "" ?>>
                        <?= htmlspecialchars($rol->getNombre()) ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <div class="btn-row">
                <button type="submit" class="btn btn-primary"><?= $isEdit ? "Actualizar" : "Guardar" ?></button>
                <a href="index.php?action=listar" class="btn btn-outline">⬅ Volver al listado</a>
            </div>
        </form>
    </div>
</div>