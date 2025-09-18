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
  { 
   [$errors, $in] = $this->validateEmpleado($data, false, null);

    if (!empty($errors)) {
      $_SESSION['errors'] = $errors;
      header('Location: index.php?action=create');
      exit;
    }

    $areaId = (int)$in['areaIdRaw'];
    $roles  = array_map('intval', array_unique($in['roles']));
    $descripcion = strip_tags($in['descripcion']);

    // --- Manejo transacción ---
    $pdo = $this->db->pdo();

    try {
      $pdo->beginTransaction();

      $empleado = new Empleado($this->db);
      $empleado->setNombre($in['nombre']);
      $empleado->setEmail($in['email']);
      $empleado->setSexo($in['sexo']);
      $empleado->setAreaId((int)$in['area_id']);
      $empleado->setBoletin((int)$in['boletin']);
      $empleado->setDescripcion($in['descripcion']);
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
      $sqlState   = $e->getCode();            
        $driverCode = $e->errorInfo[1] ?? null; 
      if ($sqlState === '23000' && $driverCode === 1062) {
            $msg = 'El correo ya está registrado.';
        }
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
    $empleado = $this->model->find((int)$id);
    if (!$empleado) {
      $_SESSION['flash'] = "Empleado no encontrado.";
      header("Location: index.php?action=listar");
      exit;
    }

    // 👇 Modo UPDATE: valida solo lo que vino y excluye el propio ID para email único
    [$errors, $in] = $this->validateEmpleado($data, true, (int)$id);
    if (!empty($errors)) {
      $_SESSION['errors'] = $errors;
      $_SESSION['old']    = $data;
      header('Location: index.php?action=edit&id='.(int)$id);
      exit;
    }

    $pdo = $this->db->pdo();
    // ¿el form trajo roles? (si no, no tocamos roles)
    $rolesCambiados = array_key_exists('roles', $data);
    $roles = $rolesCambiados ? ($in['roles'] ?? []) : [];

    try {
      $pdo->beginTransaction();

      // Solo seteamos lo que vino (partial update). OJO: claves deben coincidir con validateEmpleado()
      if (array_key_exists('nombre', $in))      $empleado->setNombre($in['nombre']);
      if (array_key_exists('email', $in))       $empleado->setEmail($in['email']);
      if (array_key_exists('sexo', $in))        $empleado->setSexo($in['sexo']);
      if (array_key_exists('area_id', $in))     $empleado->setAreaId((int)$in['area_id']);
      if (array_key_exists('boletin', $in))     $empleado->setBoletin((int)$in['boletin']);
      if (array_key_exists('descripcion', $in)) $empleado->setDescripcion($in['descripcion']); // ya strip_tags en validador

      $empleado->guardar();

      if ($rolesCambiados) {
        $empleado->asignarRoles($roles);
      }

      $pdo->commit();
      $_SESSION['flash'] = "Empleado actualizado correctamente.";
      header("Location: index.php?action=listar");
      exit;

    } catch (PDOException $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      error_log("UPDATE empleado {$id} - PDOException: ".$e->getMessage().' | info='.json_encode($e->errorInfo));

      $msg = 'Ocurrió un error al actualizar.';
      $sqlState   = $e->getCode();
      $driverCode = $e->errorInfo[1] ?? null;
      if ($sqlState === '23000' && $driverCode === 1062) {
            $msg = 'El correo ya está registrado.';
        }

      $_SESSION['errors'] = [$msg];
      $_SESSION['old']    = $data;
      header('Location: index.php?action=edit&id='.(int)$id);
      exit;

    } catch (Throwable $e) {
      if ($pdo->inTransaction()) $pdo->rollBack();
      error_log("UPDATE empleado {$id} - Throwable: ".$e->getMessage());
      $_SESSION['errors'] = ['Error inesperado al actualizar: '.$e->getMessage()];
      $_SESSION['old']    = $data;
      header('Location: index.php?action=edit&id='.(int)$id);
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

  private function validateEmpleado(array $data, bool $isUpdate = false, ?int $empleadoId = null): array {
    $errors = [];

    $nombre      = trim($data['nombre'] ?? '');
    $email       = mb_strtolower(trim($data['email'] ?? ''));
    $sexo        = $data['sexo'] ?? '';
    $areaIdRaw   = $data['area_id'] ?? '';
    $descripcion = trim($data['descripcion'] ?? '');
    $boletin     = isset($data['boletin']) ? 1 : 0;
    $roles       = isset($data['roles']) ? (array)$data['roles'] : [];

    // Validaciones
    if ($nombre === '' || mb_strlen($nombre) < 3 || mb_strlen($nombre) > 100) {
        $errors[] = 'El nombre debe tener entre 3 y 100 caracteres.';
    }
    if ($email === '' || mb_strlen($email) > 120 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Correo electrónico inválido.';
    }
    if (!in_array($sexo, ['M','F'], true)) {
        $errors[] = 'Debes seleccionar el sexo.';
    }
    if ($areaIdRaw === '' || !ctype_digit((string)$areaIdRaw)) {
        $errors[] = 'Debes seleccionar un área válida.';
    }
    if ($descripcion === '' || mb_strlen($descripcion) < 3 || mb_strlen($descripcion) > 500) {
        $errors[] = 'La descripción es obligatoria.';
    }

    // Normalización final
    $normalized = [
        'nombre'      => $nombre,
        'email'       => $email,
        'sexo'        => $sexo,
        'area_id'     => (int)$areaIdRaw,
        'boletin'     => $boletin,
        'descripcion' => strip_tags($descripcion),
        'roles'       => array_map('intval', array_unique($roles)),
    ];

    return [$errors, $normalized];
}
}
