<?php
class Area {
    private int $id;
    private string $nombre;
    private Database $db;

    public function __construct(Database $db) {
        $this->db = $db;
    }

    public function load(array $data): void {
        $this->id = $data['id'];
        $this->nombre = $data['nombre'];
    }

    public function getId(): int {
        return $this->id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function getAll(): array {
        $rows = $this->db->run("SELECT * FROM areas")->fetchAll();
        $areas = [];
        foreach ($rows as $row) {
            $area = new Area($this->db);
            $area->load($row);
            $areas[] = $area;
        }
        return $areas;
    }
}