<?php

class InsuranceType
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function all(bool $onlyActive = false): array
    {
        if ($onlyActive) {
            $stmt = $this->db->query('SELECT * FROM tbl_insurance_types WHERE status = 1 ORDER BY name');
        } else {
            $stmt = $this->db->query('SELECT * FROM tbl_insurance_types ORDER BY name');
        }
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tbl_insurance_types WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $type = $stmt->fetch();
        return $type ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO tbl_insurance_types (name, description, image_path, status) VALUES (:name, :description, :image_path, :status)');
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare('UPDATE tbl_insurance_types SET name = :name, description = :description, image_path = :image_path, status = :status WHERE id = :id');
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM tbl_insurance_types WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
