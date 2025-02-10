<?php

namespace Domain\IdentityAndAccess\Identity\User;

interface EncryptionService
{
    public function hashUserPassword(string $password): string;

    /**
     * Verify a plain user password against a hashed user password
     */
    public function plainUserPasswordMatchingHashedPassword(
        string $plainUserPassword,
        string $hashedUserPassword
    ): bool;
}
