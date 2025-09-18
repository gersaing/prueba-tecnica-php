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
    include __DIR__ . '/../views/empleado_listar.php';
  }
  public function create()
  {
    $areaModel = new Area($this->db);
    $rolModel = new Rol($this->db);
    $areas = $areaModel->getAll();
    $roles = $rolModel->getAll();

    include __DIR__ . '/../views/empleado_formulario.php';
  }
  public function store($data)
  {

    $empleado = new Empleado($this->db);
    $empleado->setNombre($data['nombre']);
    $empleado->setEmail($data['email']);
    $empleado->setSexo($data['sexo']);
    $empleado->setAreaId($data['area_id']);
    $empleado->setBoletin(isset($data['boletin']) ? 1 : 0);
    $empleado->setDescripcion($data['descripcion']);

    $empleado->guardar(); // INSERT en BD

    // Asignar roles
    $roles = $data['roles'] ?? [];
    $empleado->asignarRoles($roles);
    $_SESSION['flash'] = "Empleado creado correctamente.";
    header("Location: index.php?action=listar");
  }
  public function edit($id)
  {
    $empleado = $this->model->find($id);

    if (!$empleado) {
      header("Location: index.php?action=listar");
      exit;
    }

    // Modelos relacionados
    $areaModel = new Area($this->db);
    $rolModel  = new Rol($this->db);
    $empleadoRolModel = new EmpleadoRol($this->db);

    // Obtener todas las áreas y roles
    $areas = $areaModel->getAll(); 
    $roles = $rolModel->getAll();

    // Obtener roles asignados y cargarlos en el objeto empleado
    $rolesAsignados = $empleadoRolModel->obtenerRolesPorEmpleado($id);
    $empleado->asignarRoles($rolesAsignados);

    // Indicador de edición para la vista
    $isEdit = true;

    // Mostrar formulario
    include __DIR__ . '/../views/empleado_formulario.php';
  }

  public function update($id, $data)
  {
    $empleado = $this->model->find($id);

    if (!$empleado) {
      $_SESSION['flash'] = "Empleado no encontrado.";
      header("Location: index.php?action=listar");
      exit;
    }

    // Actualizar propiedades usando setters
    $empleado->setNombre($data['nombre']);
    $empleado->setEmail($data['email']);
    $empleado->setSexo($data['sexo']);
    $empleado->setAreaId($data['area_id']);
    $empleado->setBoletin(isset($data['boletin']) ? 1 : 0);
    $empleado->setDescripcion($data['descripcion']);

    $empleado->guardar();

    // Asignar roles
    $roles = $data['roles'] ?? [];
    $empleado->asignarRoles($roles);

    $_SESSION['flash'] = "Empleado actualizado correctamente.";
    header("Location: index.php?action=listar");

    $_SESSION['flash'] = "Empleado actualizado correctamente.";
    header("Location: index.php?action=listar");
  }

  public function delete($id)
  {
    if ($this->model->delete($id)) {
      $_SESSION['flash'] = "Empleado eliminado correctamente.";
    } else {
      $_SESSION['flash'] = "Empleado no encontrado.";
    }
    header("Location: index.php?action=listar");
  }
  public function show()
  {
    $model = new Empleado($this->db);
    $empleados = $model->all();
    include __DIR__ . '/../views/empleado_listar.php';
  }
}
