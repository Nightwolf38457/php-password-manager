<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/KeyManager.php';

class PasswordVault {

    private PDO $db;
    private KeyManager $keyManager;

    public function __construct() {
        $this->db         = Database::getInstance();
        $this->keyManager = new KeyManager();
    }

    public function addPassword(
        int    $userId,
        string $encryptedKey,
        string $plainLoginPassword,
        string $siteName,
        string $plainSitePassword
    ): bool {
        $masterKey         = $this->keyManager->decryptKey($encryptedKey, $plainLoginPassword);
        $encryptedPassword = $this->keyManager->encryptKey($plainSitePassword, $masterKey);
        $stmt = $this->db->prepare(
            "INSERT INTO passwords (user_id, site_name, encrypted_password) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$userId, $siteName, $encryptedPassword]);
    }

    public function getPasswords(
        int    $userId,
        string $encryptedKey,
        string $plainLoginPassword
    ): array {
        $masterKey = $this->keyManager->decryptKey($encryptedKey, $plainLoginPassword);
        $stmt = $this->db->prepare(
            "SELECT id, site_name, encrypted_password, created_at
             FROM passwords WHERE user_id = ? ORDER BY created_at DESC"
        );
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        foreach ($rows as &$row) {
            $row['plain_password'] = $this->keyManager->decryptKey(
                $row['encrypted_password'], $masterKey
            );
        }
        return $rows;
    }

    public function deletePassword(int $id, int $userId): bool {
        $stmt = $this->db->prepare("DELETE FROM passwords WHERE id = ? AND user_id = ?");
        return $stmt->execute([$id, $userId]);
    }
}