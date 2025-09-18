<?php
require_once __DIR__ . '/../models/Empleado.php';
require_once __DIR__ . '/../models/Area.php';
require_once __DIR__ . '/../models/Rol.php';
require_once __DIR__ . '/../models/EmpleadoRol.php';

class EmpleadoController
{

  private $model;
  private $db;
  private $rol;

  public function __construct($db)
  {
    $this->db = $db;
    $this->model = new Empleado($db);
  }
  public function list()
  {
    $empleados = $this->model->all();

    ob_start();
    include __DIR__ . '/../views/empleado_listar.php';
    $content = ob_get_clean();

    $title = "Listado de empleados";
    include __DIR__ . '/../views/layout.php';
  }
  public function create()
  {
    $areaModel = new Area($this->db);
    $rolModel  = new Rol($this->db);
    $areas = $areaModel->getAll();
    $roles = $rolModel->getAll();
    $isEdit = false;

    ob_start();
    include __DIR__ . '/../views/empleado_formulario.php';
    $content = ob_get_clean();

    $title = 'Nuevo empleado';
    include __DIR__ . '/../views/vista.php';
  }
  public function store($data)
  { //Validaciones del lado servidor
    $nombre      = trim($data['nombre'] ?? '');
    $email       = trim($data['email'] ?? '');
    $sexo        = $data['sexo'] ?? '';
    $areaIdRaw   = $data['area_id'] ?? '';
    $descripcion = trim($data['descripcion'] ?? '');
    $boletin     = isset($data['boletin']) ? 1 : 0;
    $roles       = isset($data['roles']) ? (array)$data['roles'] : [];

    $errors = [];
    if (
      $nombre === '' || mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100 ||
      !preg_match('/^[A-Za-zÁ-ÿ\s.\'-]{3,100}$/u', $nombre)
    ) $errors[] = 'El nombre debe tene entre 3 y 100 caracteres.';
    if ($email === '' || mb_strlen($email) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electrónico inválido.';
    if (!in_array($sexo, ['M', 'F'], true)) $errors[] = 'Debes seleccionar el sexo.';
    if ($areaIdRaw === '' || !ctype_digit((string)$areaIdRaw)) $errors[] = 'Debes seleccionar un área.';
    if ($descripcion === '' || mb_strlen($descripcion) < 3 || mb_strlen($descripcion) > 500) $errors[] = 'La descripción es obligatoria.';

    if (!empty($errors)) {
      $_SESSION['errors'] = $errors;
      header('Location: index.php?action=create');
      exit;
    }

    $areaId = (int)$areaIdRaw;
    $roles  = array_map('intval', array_unique($roles));
    $descripcion = strip_tags($descripcion);

    // --- Manejo transacción ---
    $pdo = $this->db->pdo();

    try {
      $pdo->beginTransaction();

      $empleado = new Empleado($this->db);
      $empleado->setNombre($nombre);
      $empleado->setEmail($email);
      $empleado->setSexo($sexo);
      $empleado->setAreaId($areaId);
      $empleado->setBoletin($boletin);
      $empleado->setDescripcion($descripcion);
      $empleado->guardar();               

      $empleado->asignarRoles($roles);   

      $pdo->commit();

      $_SESSION['flash'] = 'Empleado creado correctamente.';
      header('Location: index.php?action=listar');
      exit;
    } catch (PDOException $e) {
      if ($pdo->inTransaction()) {
        $pdo->rollBack();
      }
      error_log("STORE empleado - PDOException: " . $e->getMessage());
      $msg = 'Ocurrió un error al guardar.';

      $_SESSION['errors'] = [$msg];
      $_SESSION['old']    = $data;
      header('Location: index.php?action=create');
      exit;
    } catch (Throwable $e) {
      if ($pdo->inTransaction()) {
        $pdo->rollBack();
      }
      error_log("STORE empleado  Throwable: " . $e->getMessage());
      $_SESSION['errors'] = ['Error inesperado al guardar.'];
      $_SESSION['old']    = $data;
      header('Location: index.php?action=create');
      exit;
    }
  }

  public function edit($id)
  {
    $empleado = $this->model->find($id);
    if (!$empleado) {
      $_SESSION['flash'] = "Empleado no encontrado.";
      header("Location: index.php?action=listar");
      exit;
    }
    $areaModel = new Area($this->db);
    $rolModel  = new Rol($this->db);
    $empleadoRolModel = new EmpleadoRol($this->db);
    $areas = $areaModel->getAll();
    $roles = $rolModel->getAll();
    $rolesAsignados = $empleadoRolModel->obtenerRolesPorEmpleado($id);
    $empleado->asignarRoles($rolesAsignados);
    $isEdit = true;

    ob_start();
    include __DIR__ . '/../views/empleado_formulario.php';
    $content = ob_get_clean();

    $title = 'Editar empleado';
    include __DIR__ . '/../views/vista.php';
  }

  public function update($id, $data)
  {
    $empleado = $this->model->find($id);
    if (!$empleado) {
      $_SESSION['flash'] = "Empleado no encontrado.";
      header("Location: index.php?action=listar");
      exit;
    }
    //Validaciones del lado servidor
    $nombre      = trim($data['nombre'] ?? '');
    $email       = trim($data['email'] ?? '');
    $sexo        = $data['sexo'] ?? '';
    $areaIdRaw   = $data['area_id'] ?? '';
    $descripcion = trim($data['descripcion'] ?? '');
    $boletin     = isset($data['boletin']) ? 1 : 0;
    $roles       = isset($data['roles']) ? (array)$data['roles'] : [];

    $errors = [];
    if (
      $nombre === '' || mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100 ||
      !preg_match('/^[A-Za-zÁ-ÿ\s.\'-]{3,100}$/u', $nombre)
    ) $errors[] = 'El nombre debe tene entre 3 y 100 caracteres.';
    if ($email === '' || mb_strlen($email) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Correo electrónico inválido.';
    if (!in_array($sexo, ['M', 'F'], true)) $errors[] = 'Debes seleccionar el sexo.';
    if ($areaIdRaw === '' || !ctype_digit((string)$areaIdRaw)) $errors[] = 'Debes seleccionar un área.';
    if ($descripcion === '' || mb_strlen($descripcion) < 3 || mb_strlen($descripcion) > 500) $errors[] = 'La descripción es obligatoria.';

    if (!empty($errors)) {
      $_SESSION['errors'] = $errors;
      header('Location: index.php?action=edit&id=' . (int)$id);
      exit;
    }

    //manejo de transacción 
    $pdo = $this->db->pdo();
    $roles = array_map('intval', array_unique($roles));

    try {
      $pdo->beginTransaction();

      $empleado->setNombre($nombre);
      $empleado->setEmail($email);
      $empleado->setSexo($sexo);
      $empleado->setAreaId((int)$areaIdRaw);
      $empleado->setBoletin($boletin);
      $empleado->setDescripcion(strip_tags($descripcion));

      $empleado->guardar();
      $empleado->asignarRoles($roles);

      $pdo->commit();

      $_SESSION['flash'] = "Empleado actualizado correctamente.";
      header("Location: index.php?action=listar");
      exit;
    } catch (PDOException $e) {
      $pdo->rollBack();
      error_log("UPDATE empleado {$id} - PDOException: " . $e->getMessage());

      $msg = 'Ocurrió un error al actualizar.';

      $_SESSION['errors'] = [$msg];
      $_SESSION['old']    = $data;
      header('Location: index.php?action=edit&id=' . (int)$id);
      exit;
    } catch (Throwable $e) {
      $pdo->rollBack();
      error_log("UPDATE empleado {$id} - Throwable: " . $e->getMessage());
      $_SESSION['errors'] = ['Error inesperado al actualizar.'];
      $_SESSION['old']    = $data;
      header('Location: index.php?action=edit&id=' . (int)$id);
      exit;
    }
  }

  public function delete($id)
  {
    // 0) Validar id
    if ($id === null || !ctype_digit((string)$id)) {
      $_SESSION['errors'] = ['ID inválido.'];
      header('Location: index.php?action=listar');
      exit;
    }

    // 1) Existencia
    $empleado = $this->model->find((int)$id);
    if (!$empleado) {
      $_SESSION['flash'] = "Empleado no encontrado.";
      header("Location: index.php?action=listar");
      exit;
    }

    $pdo = $this->db->pdo();

    try {
      $pdo->beginTransaction();

      // Si BD NO tiene ON DELETE CASCADE en empleado_rol,
      // quitar roles primero para evitar errores por FK:
      if (method_exists($empleado, 'asignarRoles')) {
        $empleado->asignarRoles([]); // quita todos los roles
      }

      $ok = $this->model->delete((int)$id);

      $pdo->commit();

      if ($ok) {
        $_SESSION['flash'] = "Empleado eliminado correctamente.";
      } else {
        $_SESSION['errors'] = ["No se pudo eliminar el empleado."];
      }
    } catch (PDOException $e) {
      $pdo->rollBack();
      error_log("DELETE empleado {$id} - PDOException: " . $e->getMessage());

      if ($e->getCode() === '23000') {
        $_SESSION['errors'] = ['No se puede eliminar: el empleado tiene registros relacionados.'];
      } else {
        $_SESSION['errors'] = ['Error al eliminar el empleado.'];
      }
    } catch (Throwable $e) {
      $pdo->rollBack();
      error_log("DELETE empleado {$id} - Throwable: " . $e->getMessage());
      $_SESSION['errors'] = ['Error inesperado al eliminar.'];
    }

    header('Location: index.php?action=listar');
    exit;
  }
  public function show()
  {
    $empleados = $this->model->all();

    ob_start();
    include __DIR__ . '/../views/empleado_listar.php';
    $content = ob_get_clean();

    $title = "Listado de empleados";
    include __DIR__ . '/../views/vista.php';
  }
}
