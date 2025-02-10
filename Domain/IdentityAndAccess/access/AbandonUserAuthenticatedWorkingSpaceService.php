<?php

namespace Domain\IdentityAndAccess\Access;

class AbandonUserAutheticatedWorkingSpaceService {
    /**
     * The working space service of the user
     *
     * @var IUserWorkingSpaceService
     * @access private
     */
    private $userWorkingSpaceService;

    public function __construct(IUserWorkingSpaceService $userWorkingSpaceService) {
        $this->userWorkingSpaceService = $userWorkingSpaceService;
    }

    /**
     * @param string $arg1 description
     *
     * @return void
     * @throws \DomainException If the user is not authenticated
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function exitFromAuthenticatedState() {
        if($this->userWorkingSpaceService->isUserAuthenticated() === FALSE) {
            throw new \DomainException('The user is not authenticated, could not log him out!');
        }
        $this->userWorkingSpaceService->closeAuthenticatedUserCurrentWorkingSpace();
    }
}

?>
