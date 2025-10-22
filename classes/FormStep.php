<?php

class FormStep
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getByType(int $insuranceTypeId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM tbl_form_steps WHERE insurance_type_id = :insurance_type_id ORDER BY step_order');
        $stmt->execute(['insurance_type_id' => $insuranceTypeId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tbl_form_steps WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $step = $stmt->fetch();
        return $step ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO tbl_form_steps (insurance_type_id, step_title, step_order) VALUES (:insurance_type_id, :step_title, :step_order)');
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare('UPDATE tbl_form_steps SET step_title = :step_title, step_order = :step_order WHERE id = :id');
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM tbl_form_steps WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
