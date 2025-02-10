<?php

namespace Domain\IdentityAndAccess\Access\Representation;

use DateTime;

class AuthenticationRep
{
    private bool $successfulAuthentication;
    private bool $authenticatedWithInvalidCredentials;
    private bool $authenticationLocked;
    private DateTime $authenticationLockExpiration;

    public function __construct(
        bool $successfulAuthentication,
        bool $authenticatedWithInvalidCredentials,
        bool $authenticationLocked,
        DateTime $authenticationLockExpiration = null
    )
    {
        $this->successfulAuthentication = $successfulAuthentication;
        $this->authenticatedWithInvalidCredentials = $authenticatedWithInvalidCredentials;
        $this->authenticationLocked = $authenticationLocked;
        $this->authenticationLockExpiration = $authenticationLockExpiration !== null
            ? $authenticationLockExpiration : new DateTime();
    }

    public function getSuccessfulAuthentication(): bool
    {
        return $this->successfulAuthentication;
    }

    public function getAuthenticatedWithInvalidCredentials(): bool
    {
        return $this->authenticatedWithInvalidCredentials;
    }

    public function getAuthenticationLocked(): bool
    {
        return $this->authenticationLocked;
    }

    public function getAuthenticationLockExpiration(): DateTime
    {
        return $this->authenticationLockExpiration;
    }
}
