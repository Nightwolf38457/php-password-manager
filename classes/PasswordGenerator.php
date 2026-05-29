<?php

class PasswordGenerator {

    private string $upper   = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
    private string $lower   = 'abcdefghijklmnopqrstuvwxyz';
    private string $digits  = '0123456789';
    private string $special = '!@#$%^&*()_+-=[]{}|;:,.<>?';

    public function generate(
        int $countUpper,
        int $countLower,
        int $countDigits,
        int $countSpecial
    ): string {
        $chars = [];
        for ($i = 0; $i < $countUpper;   $i++) $chars[] = $this->randomChar($this->upper);
        for ($i = 0; $i < $countLower;   $i++) $chars[] = $this->randomChar($this->lower);
        for ($i = 0; $i < $countDigits;  $i++) $chars[] = $this->randomChar($this->digits);
        for ($i = 0; $i < $countSpecial; $i++) $chars[] = $this->randomChar($this->special);
        shuffle($chars);
        return implode('', $chars);
    }

    private function randomChar(string $pool): string {
        return $pool[random_int(0, strlen($pool) - 1)];
    }
}