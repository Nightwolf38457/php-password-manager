<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/Database.php';
require_once __DIR__ . '/KeyManager.php';

class User {
    private $db;
    private $keyManager;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->keyManager = new KeyManager();
    }

    public function register(string $username, string $password): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $key = $this->keyManager->generateKey();
        $encryptedKey = $this->keyManager->encryptKey($key, $password);

        $stmt = $this->db->prepare(
            "INSERT INTO users (username, password_hash, encrypted_key) VALUES (?, ?, ?)"
        );
        return $stmt->execute([$username, $hash, $encryptedKey]);
    }

    public function login(string $username, string $password): ?array {
        $stmt = $this->db->prepare(
            "SELECT * FROM users WHERE username = ?"
        );
        $stmt->execute([$username]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public function changePassword(int $userId, string $oldPassword, string $newPassword): bool {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user || !password_verify($oldPassword, $user['password_hash'])) {
            return false;
        }

        $key = $this->keyManager->decryptKey($user['encrypted_key'], $oldPassword);
        $newHash = password_hash($newPassword, PASSWORD_BCRYPT);
        $newEncryptedKey = $this->keyManager->encryptKey($key, $newPassword);

        $stmt = $this->db->prepare(
            "UPDATE users SET password_hash = ?, encrypted_key = ? WHERE id = ?"
        );
        return $stmt->execute([$newHash, $newEncryptedKey, $userId]);
    }
}