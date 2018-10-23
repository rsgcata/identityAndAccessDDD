<?php
namespace domain\identityAndAccess\identity\user\events;

use common\domain\AbstractDomainEvent;
use domain\contact\FullName;
use domain\identityAndAccess\identity\user\actionAuthenticity\PasswordChangeCode;
use domain\identityAndAccess\identity\user\User;
use domain\identityAndAccess\identity\user\UserEmailAddress;
use domain\identityAndAccess\identity\user\UserId;
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
class NewPasswordChangeCodeRequested extends AbstractDomainEvent {
    /**
     * The user id
     *
     * @var UserId
     * @access private
     */
    private $userId;
    
    /**
     * Email Address
     *
     * @var UserEmailAddress
     * @access private
     */
    private $emailAddress;
    
    /**
     * Short description
     *
     * @var UserPhoneNumber
     * @access private
     */
    private $phoneNumber;
    
    /**
     * The full name of the user
     *
     * @var FullName
     * @access private
     */
    private $fullName;
    
    /**
     * Email address verification code
     *
     * @var PasswordChangeCode
     * @access private
     */
    private $passwordChangeCode;
    
    /**
     * Domain event object constructor
     * 
     * @param User $user The newly registered user
     *
     * @return void
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function __construct(User $user) {
        parent::__construct();
        $this->emailAddress = $user->getEmailAddress();
        $this->phoneNumber = $user->getPerson()->getPrimaryUserPhoneNumber();
        $this->passwordChangeCode = $user->getPasswordChangeCode();
        $this->fullName = $user->getPerson()->getFullName();
        $this->userId = $user->getId();
    }

    /**
     * 
     * @return UserEmailAddress
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }
    
    /**
     * 
     * @return UserPhoneNumber
     */
    public function getPhoneNumber() {
        return $this->phoneNumber;
    }

    /**
     * 
     * @return FullName
     */
    public function getFullName() {
        return $this->fullName;
    }

    /**
     * 
     * @return PasswordChangeCode
     */
    public function getPasswordChangeCode() {
        return $this->passwordChangeCode;
    }
    
    /**
     * 
     * @return UserId
     */
    public function getUserId() {
        return $this->userId;
    }
}

?>
