<?php
class EmpleadoRol{
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }
    
    public function asignarRoles(int $empleadoId, array $roles) {
        # eliminar roles actuales
        $this->db->run("DELETE FROM empleado_rol WHERE empleado_id = ?", [$empleadoId]);
        # asignar nuevos roles
        $sql = "INSERT INTO empleado_rol (empleado_id, rol_id) VALUES (?, ?)";
        foreach ($roles as $rolId) {
            $this->db->run($sql, [$empleadoId, $rolId]);
        }
    }
    public function obtenerRolesPorEmpleado(int $empleadoId) {
        return $this->db->run("SELECT rol_id FROM empleado_rol WHERE empleado_id = ?", [$empleadoId])->fetchAll(PDO::FETCH_COLUMN);
    }
}