<?php

namespace Domain\IdentityAndAccess\Access;

use DomainException;

class AbandonUserAuthenticatedWorkingSpaceService
{
    private UserWorkingSpaceService $userWorkingSpaceService;

    public function __construct(UserWorkingSpaceService $userWorkingSpaceService)
    {
        $this->userWorkingSpaceService = $userWorkingSpaceService;
    }

    /**
     * @throws DomainException If the user is not authenticated
     */
    public function exitFromAuthenticatedState(): void
    {
        if (!$this->userWorkingSpaceService->isUserAuthenticated()) {
            throw new DomainException('The user is not authenticated, could not log him out!');
        }
        $this->userWorkingSpaceService->closeAuthenticatedUserCurrentWorkingSpace();
    }
}
