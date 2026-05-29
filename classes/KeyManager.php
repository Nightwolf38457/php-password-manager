<?php
require_once __DIR__ . '/../config.php';

class KeyManager {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance();
    }

    public function generateKey(): string {
        return bin2hex(random_bytes(32));
    }

    public function encryptKey(string $key, string $plainPassword): string {
        $iv = openssl_random_pseudo_bytes(16);
        $encrypted = openssl_encrypt($key, 'AES-256-CBC', $plainPassword, 0, $iv);
        return base64_encode($iv . '::' . $encrypted);
    }

    public function decryptKey(string $encryptedKey, string $plainPassword): string {
        $decoded = base64_decode($encryptedKey);
        list($iv, $encrypted) = explode('::', $decoded, 2);
        return openssl_decrypt($encrypted, 'AES-256-CBC', $plainPassword, 0, $iv);
    }
}