<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $title ?? 'Aplicación' ?></title>
  <?php $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/'; ?>
  <link rel="stylesheet" href="<?= $base ?>css/styles.css?v=3">

  <?php $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/\\') . '/'; ?>
  <script src="<?= $base ?>js/validaciones.js?v=1"></script>
</head>

</head>
<body>

  <!-- Aquí irá el contenido de cada vista -->
  <?= $content ?? '' ?>

</body>
</html>
