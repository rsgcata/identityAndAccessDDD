<?php
namespace domain\identityAndAccess\access;

use domain\identityAndAccess\identity\user\User;
use domain\identityAndAccess\identity\anonymousUser\UserAuthenticationAttempt;
use domain\identityAndAccess\identity\anonymousUser\UserAuthenticationAttemptId;
use domain\identityAndAccess\identity\user\UserPhoneNumber;

/**
 *
 * Short description 
 *
 * Long description 
 *
 * @category   --
 * @package    --
 * @license    --
 * @version    1.0
 * @link       --
 * @since      Class available since Release 1.0
 */
interface IUserWorkingSpaceService {
    /**
     * Create a new temporary working speace/session for thge current user 
     * 
     * @param User $user
     * @param UserAuthenticationAttempt $userAuthenticationAttempt
     *
     * @return void
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function createNewTemporaryAuthenticatedWorkingSpaceFor(
            User $user, UserAuthenticationAttempt $userAuthenticationAttempt);
            
    /**
     * @param User $user
     * @param UserAuthenticationAttempt $userAuthenticationAttempt
     * @return void 
     */
    public function createNewTimelessAuthenticatedWorkingSpaceFor(
            User $user, UserAuthenticationAttempt $userAuthenticationAttempt);
    
    /**
     * @return void
     */
    public function closeAuthenticatedUserCurrentWorkingSpace();
    
    /**
     * @return UserId
     */
    public function getCurrentWorkingAuthenticatedUserId();
    
    /**
     * @return UserAuthenticationAttemptId|null
     */
    public function getUserAuthenticationAttemptId();
    
    /**
     * @return bool
     */
    public function expectsTimelessWorkingSpace();
    
    /**
     * @return boolean
     */
    public function isUserAuthenticated();
}

?>
