<?php

class Auth
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function attemptLogin(string $username, string $password): bool
    {
        $stmt = $this->db->prepare('SELECT id, password FROM tbl_admin WHERE username = :username LIMIT 1');
        $stmt->execute(['username' => $username]);
        $admin = $stmt->fetch();

        if ($admin && password_verify($password, $admin['password'])) {
            $_SESSION['admin_id'] = $admin['id'];
            return true;
        }

        return false;
    }

    public function logout(): void
    {
        unset($_SESSION['admin_id']);
    }

    public function check(): bool
    {
        return isset($_SESSION['admin_id']);
    }
}
