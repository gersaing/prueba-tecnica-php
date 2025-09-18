<?php
class Rol {
    private Database $db;
    private int $id;
    private string $nombre;
    public function __construct(Database $db) {
        $this->db = $db;
    }


    public function load(array $data): void {
        $this->id = $data['id'];
        $this->nombre = $data['nombre'];
    }

    // Getters
    public function getId(): int {
        return $this->id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    // Buscar un rol por ID y devolver objeto Rol
    public function findById(int $id): Rol {
        $data = $this->db->run("SELECT * FROM roles WHERE id = ?", [$id])->fetch();
        if (!$data) {
            throw new Exception("Rol no encontrado con ID $id");
        }
        $rol = new Rol($this->db);
        $rol->load($data);
        return $rol;
    }

    // Obtener todos los roles
    public function getAll(): array {
        $rows = $this->db->run("SELECT * FROM roles")->fetchAll();
        $roles = [];
        foreach ($rows as $row) {
            $rol = new Rol($this->db);
            $rol->load($row);
            $roles[] = $rol;
        }
        return $roles;
    }
}