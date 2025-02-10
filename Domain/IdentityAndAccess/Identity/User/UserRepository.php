<?php
namespace Domain\IdentityAndAccess\Identity\User;

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
interface IUserRepository {
    /**
     * 
     * @param User $user
     * @return void 
     */
    public function saveNew(User $user);
    
    /**
     * 
     * @param User $user
     * @return void
     */
    public function saveModificationsFor(User $user);
    
    /**
     * 
     * @param UserId $id
     * @return User
     */
    public function findByUserId(UserId $id);
    
    /**
     * 
     * @param UserEmailAddress $emailAddress
     * @return User
     */
    public function findByEmailAddress(UserEmailAddress $emailAddress);
    
    /**
     * @param UserArchivationStatus $userArchivationStatus
     * @param int $limit
     * @param int $offset
     * @return User[]
     */
    public function findAllByArchivationStatus(
            UserArchivationStatus $userArchivationStatus, $limit, $offset);
}

?>
