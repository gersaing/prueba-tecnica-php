<?php
class Empleado {
    private Database $db;
    private array $roles = [];
    private int $id;
    private string $nombre;
    private string $email;
    private string $sexo;
    private ?Area $area = null;
    private bool $boletin;
    private string $descripcion;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    // GETTERS
    public function getId(): int { return $this->id; }
    public function getNombre(): string { return $this->nombre; }
    public function getEmail(): string { return $this->email; }
    public function getSexo(): string { return $this->sexo; }
    public function getBoletin(): bool { return $this->boletin; }
    public function getDescripcion(): string { return $this->descripcion; }
    public function getArea(): ?Area {return $this->area;
    }

    // SETTERS
    public function setNombre(string $nombre): void { $this->nombre = $nombre;}
    public function setEmail(string $email): void { $this->email = $email; }
    public function setSexo(string $sexo): void { $this->sexo = $sexo; }
    public function setAreaId(int $area_id): void {
        $area = new Area($this->db);
        $areaData = $this->db->run("SELECT * FROM areas WHERE id = ?", [$area_id])->fetch();
        if ($areaData) {
            $area->load($areaData);
            $this->area = $area;
        } else {
            $this->area = null; // o lanzar excepción
        }
    }
    public function setBoletin(bool $boletin): void { $this->boletin = $boletin; }
    public function setDescripcion(string $descripcion): void { $this->descripcion = $descripcion; }
    public function setId(int $id): void { $this->id = $id; }

    // Carga de datos
    public function load(array $data): void {
        $this->id = $data['id'];
        $this->nombre = $data['nombre'];
        $this->email = $data['email'];
        $this->sexo = $data['sexo'];

         // Cargar el área asociada
        $areaData = $this->db->run("SELECT * FROM areas WHERE id = ?", [$data['area_id']])->fetch();
        if ($areaData) {
            $area = new Area($this->db);
            $area->load($areaData);
            $this->area = $area;
        } else {
            $this->area = null; // o Area vacío
        }

        $this->boletin = isset($data['boletin']) ? (bool)$data['boletin'] : false;
        $this->descripcion = $data['descripcion'] ?? '';
    }
    
    public function guardar() {
        if (isset($this->id)) {
            $sql = "UPDATE empleados 
                    SET nombre = ?, email = ?, sexo = ?, area_id = ?, boletin = ?, descripcion = ?
                    WHERE id = ?";
            $this->db->run($sql, [
                $this->nombre,
                $this->email,
                $this->sexo,
                $this->area->getId(),
                $this->boletin,
                $this->descripcion,
                $this->id
            ]);
        } else {
            $sql = "INSERT INTO empleados (nombre, email, sexo, area_id, boletin, descripcion)
                    VALUES (?, ?, ?, ?, ?, ?)";
            $this->db->run($sql, [
                $this->nombre,
                $this->email,
                $this->sexo,
                $this->area->getId(),
                $this->boletin,
                $this->descripcion
            ]);
            // Guardar ID generado
            $this->id = $this->db->lastInsertId();
        }
    }

    // Buscar empleado por ID
    public function find(int $id): self {
        $data = $this->db->run("SELECT * FROM empleados WHERE id = ?", [$id])->fetch();
        $this->load($data);
        return $this;
    }

    // Obtener roles 
    public function getRoles(): array {
        if (empty($this->roles)) {
            $rolesIds = (new EmpleadoRol($this->db))->obtenerRolesPorEmpleado($this->id);
            $this->roles = [];
            foreach ($rolesIds as $id) {
                $rol = new Rol($this->db);
                $this->roles[] = $rol->findById($id);
            }
        }
        return $this->roles;
    }

    // Asignar roles usando IDs
    public function asignarRoles(array $rolesIds): void {
        $empleadoRol = new EmpleadoRol($this->db);
        $empleadoRol->asignarRoles($this->id, $rolesIds);

        $this->roles = [];
        foreach ($rolesIds as $id) {
            $rol = new Rol($this->db);
            $this->roles[] = $rol->findById($id);
        }
    }
    
    public function editarEmpleado($id, $data) {
        $sql = "UPDATE empleados 
                SET nombre = ?, email = ?, sexo = ?, area_id = ?, boletin = ?, descripcion = ?
                WHERE id = ?";
        $this->db->run($sql, [
            $data['nombre'],
            $data['email'],
            $data['sexo'],
            $data['area_id'],
            isset($data['boletin']) ? 1 : 0,
            $data['descripcion'],
            $id
        ]);
        return $id;
    }
    public function delete($id) {
        if (! $this->find($id)) {
            return false;
        }
        $this->db->run("DELETE FROM empleado_rol WHERE empleado_id = ?", [$id]);
        $this->db->run("DELETE FROM empleados WHERE id = ?", [$id]);
        return true;
    }

    // Obtener todos los empleados
    public function all(): array {
        $sql = "SELECT e.*, a.nombre AS area 
                FROM empleados e 
                JOIN areas a ON e.area_id = a.id";
        return $this->db->run($sql)->fetchAll();
    }
}
