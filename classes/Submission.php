<?php

class Submission
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function create(int $insuranceTypeId, array $data): int
    {
        $stmt = $this->db->prepare('INSERT INTO tbl_submissions (insurance_type_id, submission_data, status, created_at) VALUES (:insurance_type_id, :submission_data, :status, NOW())');
        $stmt->execute([
            'insurance_type_id' => $insuranceTypeId,
            'submission_data' => json_encode($data, JSON_UNESCAPED_UNICODE),
            'status' => 'Yeni',
        ]);

        return (int)$this->db->lastInsertId();
    }

    public function all(): array
    {
        $stmt = $this->db->query('SELECT s.*, t.name AS insurance_name FROM tbl_submissions s INNER JOIN tbl_insurance_types t ON t.id = s.insurance_type_id ORDER BY s.created_at DESC');
        return $stmt->fetchAll();
    }

    public function find(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT s.*, t.name AS insurance_name FROM tbl_submissions s INNER JOIN tbl_insurance_types t ON t.id = s.insurance_type_id WHERE s.id = :id');
        $stmt->execute(['id' => $id]);
        $submission = $stmt->fetch();
        return $submission ?: null;
    }

    public function markAsRead(int $id): void
    {
        $stmt = $this->db->prepare('UPDATE tbl_submissions SET status = "Okundu" WHERE id = :id');
        $stmt->execute(['id' => $id]);
    }
}
