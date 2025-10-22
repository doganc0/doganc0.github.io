<?php

class Settings
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getSettings(): array
    {
        $stmt = $this->db->query('SELECT * FROM tbl_settings LIMIT 1');
        $settings = $stmt->fetch();

        if (!$settings) {
            $this->db->exec("INSERT INTO tbl_settings (site_logo, site_phone, form_avatar, redirect_url, notification_email) VALUES ('', '', '', '', '')");
            $stmt = $this->db->query('SELECT * FROM tbl_settings LIMIT 1');
            $settings = $stmt->fetch();
        }

        return $settings;
    }

    public function update(array $data): void
    {
        $stmt = $this->db->prepare('UPDATE tbl_settings SET site_logo = :site_logo, site_phone = :site_phone, form_avatar = :form_avatar, redirect_url = :redirect_url, notification_email = :notification_email WHERE id = :id');
        $stmt->execute($data);
    }
}
