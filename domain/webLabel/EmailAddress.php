<?php
namespace domain\webLabel;

use common\domain\AbstractValueObject;

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
class EmailAddress extends AbstractValueObject{
    /**
     * The email address
     *
     * @var string|null
     * @access protected
     */
    protected $emailAddress;
    
    protected function __construct() {
    }
    
    /**
     * Creates a new email address value object
     * 
     * @param string|null $emailAddress The email address
     *
     * @return static
     * @throws \DomainException
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function create($emailAddress) {
        $self = new static();
        $self->setEmailAddress($emailAddress);
        return $self;
    }
    
    /**
     * Check if this object equals another object
     * 
     * @param EmailAddress $emailAddress
     *
     * @return boolean
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function equals(EmailAddress $emailAddress) {
        $equalObjects = FALSE;
        
        if(static::class === get_class($emailAddress)
                && $this->emailAddress === $emailAddress->getEmailAddress()) {
            $equalObjects = TRUE;
        }
        
        return $equalObjects;
    }
    
    /**
     * @return string|null
     */
    public function getEmailAddress() {
        return $this->emailAddress;
    }
    
    /**
     * @return string|null
     */
    public function getWebDomainNamePart() {
        if($this->emailAddress === NULL) {
            return NULL;
        }
        
        return explode('@', $this->emailAddress)[1];
    }

    /**
     * Set the email address
     * 
     * @param string|null $emailAddress
     *
     * @return void
     * @throws \DomainException
     *
     * @access protected
     * @since Method/function available since Release 1.0
     */
    protected function setEmailAddress($emailAddress) {
        if($emailAddress !== NULL) {
            if(is_string($emailAddress)) {
                $emailAddress = trim($emailAddress);
            }
            
            if($emailAddress === '') {
                $emailAddress = NULL;
            }
            else {
                $this->assert()->emailAddress($emailAddress, 
                    new \DomainException('Invalid generic email address. Could not set email address.'));
            }
            
        }
        
        $this->emailAddress = $emailAddress;
    }
    
    /**
     * 
     * @return static
     */
    public static function reconstitute($emailAddress) {
        $self = new static();
        $self->emailAddress = $emailAddress;
        return $self;
    }

}

?>
