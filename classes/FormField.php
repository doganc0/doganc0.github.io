<?php

class FormField
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getByStep(int $stepId): array
    {
        $stmt = $this->db->prepare('SELECT * FROM tbl_form_fields WHERE step_id = :step_id ORDER BY field_order');
        $stmt->execute(['step_id' => $stepId]);
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM tbl_form_fields WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $field = $stmt->fetch();
        return $field ?: null;
    }

    public function create(array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO tbl_form_fields (step_id, label, field_type, placeholder, options, is_required, field_order) VALUES (:step_id, :label, :field_type, :placeholder, :options, :is_required, :field_order)');
        $stmt->execute($data);
        return (int)$this->db->lastInsertId();
    }

    public function update(int $id, array $data): void
    {
        $data['id'] = $id;
        $stmt = $this->db->prepare('UPDATE tbl_form_fields SET label = :label, field_type = :field_type, placeholder = :placeholder, options = :options, is_required = :is_required, field_order = :field_order WHERE id = :id');
        $stmt->execute($data);
    }

    public function delete(int $id): void
    {
        $stmt = $this->db->prepare('DELETE FROM tbl_form_fields WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }

    public function deleteByStep(int $stepId): void
    {
        $stmt = $this->db->prepare('DELETE FROM tbl_form_fields WHERE step_id = :step_id');
        $stmt->execute(['step_id' => $stepId]);
    }

}
