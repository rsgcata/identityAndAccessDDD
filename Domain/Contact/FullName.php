<?php
namespace domain\contact;

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
 * 
 */
class FullName extends AbstractValueObject{
    /**
     * The first name
     *
     * @var string|null
     * @access protected
     */
    protected $firstName;
    
    /**
     * The middle name
     *
     * @var string|null
     * @access protected
     */
    protected $middleName;
    
    /**
     * The last name
     *
     * @var string|null
     * @access protected
     */
    protected $lastName;
    
    protected function __construct() {
    }
    
    /**
     * Create new full name value object
     * 
     * @param string|null $firstName The first name
     * @param string|null $middleName The middle name
     * @param string|null $lastName The last name
     *
     * @return static
     * @throws \DomainException
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function create($firstName, $middleName, $lastName) {
        $self = new static();
        $self->setFirstName($firstName);
        $self->setLastName($lastName);
        $self->setMiddleName($middleName);
        return $self;
    }
    
    /**
     * Check if this object equals another object
     * 
     * @param FullName $fullName
     *
     * @return boolean
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function equals(FullName $fullName) {
        $equalObjects = FALSE;
        
        if(static::class === get_class($fullName) 
                && $this->firstName === $fullName->getFirstName()
                && $this->middleName === $fullName->getMiddleName() 
                && $this->lastName === $fullName->getLastName()) {
            $equalObjects = TRUE;
        }
        
        return $equalObjects;
    }
    
    /**
     * Check if all descriptors are null
     *
     * @return bool
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function hasAllDescriptorsNull() {
        if($this->firstName === NULL && $this->middleName === NULL && $this->lastName === NULL) {
            return TRUE;
        }
        else {
            return FALSE;
        }
    }
    
    /**
     * 
     * @return string|null
     */
    public function getFirstName() {
        return $this->firstName;
    }

    /**
     * 
     * @return string|null
     */
    public function getMiddleName() {
        return $this->middleName;
    }

    /**
     * 
     * @return string|null
     */
    public function getLastName() {
        return $this->lastName;
    }

    protected function setFirstName($firstName) {
        if($firstName !== NULL) {
            if(is_string($firstName)) {
                $firstName = ucfirst(trim($firstName));
            }
            
            if($firstName === '') {
                $firstName = NULL;
            }
            else {
                $this->assert()->intlPersonName($firstName, new \DomainException('Invalid generic first '
                    . ' name. Could not set first name in the full name.'));
            }
        }
        
        $this->firstName = $firstName;
    }

    protected function setMiddleName($middleName) {
        if($middleName !== NULL) {
            if(is_string($middleName)) {
                $middleName = ucfirst(trim($middleName));
            }
            
            if($middleName === '') {
                $middleName = NULL;
            }
            else {
                $this->assert()->intlPersonName($middleName, new \DomainException('Invalid generic middle '
                    . ' name. Could not set middle name in the full name.'));
            }
        }
        
        $this->middleName = $middleName;
    }

    protected function setLastName($lastName) {
        if($lastName !== NULL) {
            if(is_string($lastName)) {
                $lastName = ucfirst(trim($lastName));
            }
            
            if($lastName === '') {
                $lastName = NULL;
            }
            else {
                $this->assert()->intlPersonName($lastName, new \DomainException('Invalid generic last name.'
                    . ' Could not set last name in the full name.'));
            }
        }
        
        $this->lastName = $lastName;
    }

    /**
     * 
     * @return FullName
     */
    public static function reconstitute($firstName, $middleName, $lastName) {
        $self = new static();
        $self->firstName = $firstName;
        $self->middleName = $middleName;
        $self->lastName = $lastName;
        return $self;
    }

}

?>
