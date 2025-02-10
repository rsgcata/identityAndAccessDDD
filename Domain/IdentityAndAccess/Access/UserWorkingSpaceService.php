<?php

namespace Domain\IdentityAndAccess\Access;

use Domain\IdentityAndAccess\Identity\AnonymousUser\UserAuthenticationAttempt;
use Domain\IdentityAndAccess\Identity\AnonymousUser\UserAuthenticationAttemptId;
use Domain\IdentityAndAccess\Identity\User\User;
use Domain\IdentityAndAccess\Identity\User\UserId;

interface UserWorkingSpaceService
{
    /**
     * Create a new temporary working speace/session for thge current user
     */
    public function createNewTemporaryAuthenticatedWorkingSpaceFor(
        User                      $user,
        UserAuthenticationAttempt $userAuthenticationAttempt
    );

    /**
     * @param User $user
     * @param UserAuthenticationAttempt $userAuthenticationAttempt
     *
     * @return void
     */
    public function createNewTimelessAuthenticatedWorkingSpaceFor(
        User                      $user,
        UserAuthenticationAttempt $userAuthenticationAttempt
    ): void;

    public function closeAuthenticatedUserCurrentWorkingSpace(): void;

    public function getCurrentWorkingAuthenticatedUserId(): UserId;

    public function getUserAuthenticationAttemptId(): ?UserAuthenticationAttemptId;

    public function expectsTimelessWorkingSpace(): bool;

    public function isUserAuthenticated(): bool;
}
