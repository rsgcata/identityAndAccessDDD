<?php
namespace domain\identityAndAccess\identity\user;

use domain\webLabel\EmailAddress;

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
class UserEmailAddress extends EmailAddress {
    protected function __construct() {
        
    }
    
    /**
     * Creates a new user email address value object
     * 
     * @param string $emailAddress The email address
     *
     * @return static
     * @throws \DomainException
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function create($emailAddress) {
        return parent::create($emailAddress);
    }

    protected function setEmailAddress($emailAddress) {
        $this->assert()->emailAddress($emailAddress, 
                new \DomainException('Invalid user email address. Could not set user email address.'));
        $this->emailAddress = $emailAddress;
    }

}

?>
