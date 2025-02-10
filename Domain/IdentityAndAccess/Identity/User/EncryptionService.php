<?php

namespace Fomain\IdentityAndAccess\Identity\User;

interface EncryptionService {
    /**
     * @param string $password The password to be hashed
     * @return string The hashed version of the password
     */
    public function hashUserPassword($password);

    /**
     * Verify a plain user password against a hashed user password
     *
     * @param string $plainUserPassword
     * @param string $hashedUserPassword
     *
     * @return boolean
     */
    public function plainUserPasswordMatchingHashedPassword($plainUserPassword, $hashedUserPassword);
}

?>
