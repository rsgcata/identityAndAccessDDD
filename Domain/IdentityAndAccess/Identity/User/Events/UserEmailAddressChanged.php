<?php
namespace domain\identityAndAccess\identity\user\events;

use common\domain\AbstractDomainEvent;
use domain\contact\FullName;
use domain\identityAndAccess\identity\user\actionAuthenticity\EmailAddressVerificationCode;
use domain\identityAndAccess\identity\user\User;
use domain\identityAndAccess\identity\user\UserEmailAddress;
use domain\identityAndAccess\identity\user\UserId;

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
class UserEmailAddressChanged extends AbstractDomainEvent {
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
     * The full name of the user
     *
     * @var FullName
     * @access private
     */
    private $fullName;
    
    /**
     * Email address verification code
     *
     * @var EmailAddressVerificationCode
     * @access private
     */
    private $emailAddressVerificationCode;
    
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
        $this->emailAddressVerificationCode = $user->getEmailAddressVerificationCode();
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
     * @return FullName
     */
    public function getFullName() {
        return $this->fullName;
    }

    /**
     * 
     * @return EmailAddressVerificationCode
     */
    public function getEmailAddressVerificationCode() {
        return $this->emailAddressVerificationCode;
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
