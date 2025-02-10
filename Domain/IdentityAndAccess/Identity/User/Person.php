<?php
namespace domain\identityAndAccess\identity\user;

use common\domain\AbstractValueObject;
use domain\contact\FullName;
use domain\contact\PhoneNumber;
use DomainException;

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
class Person extends AbstractValueObject{
    /**
     * The full name of the person
     *
     * @var FullName
     * @access private
     */
    private $fullName;
    
    /**
     * The main address of the person
     *
     * @var UserAddress
     * @access private
     */
    private $address;
    
    /**
     * THe person's main phone number
     *
     * @var UserPhoneNumber[]
     * @access private
     */
    private $userPhoneNumbers;
    
    /**
     * @var int
     * @const
     */
    const MAX_PHONE_NUMBERS = 3;
    
    private function __construct() {
    }
    
    /**
     * Create new person value object
     * 
     * @param FullName $fullName The full name of the person
     *
     * @return Person
     * @throws DomainException
     *
     * @static
     * @access public
     * @since Method/function available since Release 1.0
     */
    public static function create(FullName $fullName) {
        $self = new self();
        $self->setFullName($fullName);
        $self->setAddress(UserAddress::create(NULL, NULL, NULL, NULL, NULL, NULL));
        $self->userPhoneNumbers = array();
        return $self;
    }
    
    /**
     * Change the full name
     * 
     * @param FullName $fullName
     *
     * @return void
     * @throws DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function changeFullName(FullName $fullName) {
        $this->setFullName($fullName);
    }
    
    /**
     * Change the address
     * 
     * @param UserAddress $address
     *
     * @return void
     * @throws DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function changeAddress(UserAddress $address) {
        $this->setAddress($address);
    }
    
    /**
     * @param PhoneNumber $phoneNumber
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function addNewUserPhoneNumber(PhoneNumber $phoneNumber) {
        $this->addPhoneNumber($phoneNumber);
    }
    
    /**
     * @param PhoneNumber $phoneNumber
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function removePhoneNumber(PhoneNumber $phoneNumber) {
        if($this->getPrimaryUserPhoneNumber()->getPhoneNumber()->equals($phoneNumber)
                && count($this->userPhoneNumbers) > 1) {
            throw new \DomainException('Could not remove user phone number. The phone is set as primary.'
                    . ' Another primary phone number should be selected first.');
        }
        
        $phoneRemoved = FALSE;
        
        foreach($this->userPhoneNumbers as $k => $userPhone) {
            if($userPhone->getPhoneNumber()->equals($phoneNumber)) {
                unset($this->userPhoneNumbers[$k]);
                $phoneRemoved = TRUE;
                break;
            }
        }
        
        if(!$phoneRemoved) {
            throw new \DomainException('Could not remove user phone number. Inexistent phone number.');
        }
    }
    
    /**
     * @param PhoneNumber $phoneNumber
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function markPhoneNumberOwnershipAsVerified(PhoneNumber $phoneNumber) {
        $phoneMarked = TRUE;
        
        foreach($this->userPhoneNumbers as $k => $userPhone) {
            if($userPhone->getPhoneNumber()->equals($phoneNumber)) {
                $this->userPhoneNumbers[$k] = UserPhoneNumber::create(
                        $userPhone->getPhoneNumber(),
                        TRUE,
                        $userPhone->getUsedAsPrimaryPhoneNumber());
                $phoneMarked = TRUE;
                break;
            }
        }
        
        if(!$phoneMarked) {
            throw new \DomainException('Could not verify user phone number. Inexistent phone number.');
        }
    }
    
    /**
     * @param PhoneNumber $phoneNumber
     *
     * @return void
     * @throws \DomainException
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function markPhoneNumberAsPrimary(PhoneNumber $phoneNumber) {
        $phoneMarked = FALSE;        
        $phonesBackup = $this->userPhoneNumbers;
        
        foreach($this->userPhoneNumbers as $k => $userPhone) {
            if($userPhone->getPhoneNumber()->equals($phoneNumber)) {
                if(!$userPhone->hasOwnershipVerified() ) {
                    throw new \DomainException('Could not mark phone number as primary.'
                            . ' Phone number not verified.');
                }
                
                $this->userPhoneNumbers[$k] = UserPhoneNumber::create(
                        $userPhone->getPhoneNumber(),
                        $userPhone->getOwnershipVerified(),
                        TRUE);
                $phoneMarked = TRUE;
            }
            else {
                $this->userPhoneNumbers[$k] = UserPhoneNumber::create(
                        $userPhone->getPhoneNumber(),
                        $userPhone->getOwnershipVerified(),
                        FALSE);
            }
        }
        
        if(!$phoneMarked) {
            $this->userPhoneNumbers = $phonesBackup;
            throw new \DomainException('Could not mark phone number as primary.'
                    . ' Inexistent phone number.');
        }
    }
    
    /**
     * @param PhoneNumber $phoneNumber
     * @return bool
     */
    public function hasPhoneNumber(PhoneNumber $phoneNumber) {
        foreach($this->userPhoneNumbers as $uph) {
            if($uph->getPhoneNumber()->equals($phoneNumber)) {
                return TRUE;
            }
        }
        
        return FALSE;
    }
    
    /**
     * @param PhoneNumber $phoneNumber
     * @return bool
     */
    public function hasPhoneNumberVerified(PhoneNumber $phoneNumber) {
        foreach($this->userPhoneNumbers as $uph) {
            if($uph->getPhoneNumber()->equals($phoneNumber)) {
                return $uph->hasOwnershipVerified();
            }
        }
        
        return FALSE;
    }
    
    /**
     * Check if this object equals another object
     * 
     * @param Person $person
     *
     * @return boolean
     * @throws --
     *
     * @access public
     * @since Method/function available since Release 1.0
     */
    public function equals(Person $person) {
        $equalObjects = FALSE;
        
        $phoneMatchedKeys = array();
        $userPhoneNumbersMatch = TRUE;
        
        if(count($this->userPhoneNumbers) === count($person->getUserPhoneNumbers())) {
            foreach($this->userPhoneNumbers as $userPhone) {
                $matchFound = FALSE;
                foreach($person->getUserPhoneNumbers() as $k => $uph) {
                    if(in_array($k, $phoneMatchedKeys)) {
                        continue;
                    }

                    if($userPhone->equals($uph)) {
                        $phoneMatchedKeys[] = $k;
                        $matchFound = TRUE;
                    }
                }

                if(!$matchFound) {
                    $userPhoneNumbersMatch = FALSE;
                    break;
                }
            }
        }            
        
        if(self::class === get_class($person)
                && $userPhoneNumbersMatch
                && $this->address->equals($person->getAddress()) 
                && $this->fullName->equals($person->getFullName())) {
            $equalObjects = TRUE;
        }
        
        return $equalObjects;
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
     * @return UserAddress
     */
    public function getAddress() {
        return $this->address;
    }
    
    /**
     * 
     * @return UserPhoneNumber[]
     */
    public function getUserPhoneNumbers() {
        return $this->userPhoneNumbers;
    }
    
    /**
     * 
     * @return UserPhoneNumber
     */
    public function getPrimaryUserPhoneNumber() {
        foreach ($this->userPhoneNumbers as $number) {
            if($number->isUsedAsPrimaryPhoneNumber()) {
                return $number;
            }
        }
    }

    private function setFullName(FullName $fullName) {
        if($fullName->getFirstName() === NULL || $fullName->getLastName() === NULL) {
            throw new DomainException('The full name of the person must contain at least a first name '
                    . 'and a last name. Middle name is optional.');
        }
        
        $this->fullName = $fullName;
    }
    
    private function setAddress(UserAddress $address) {
        $this->address = $address;
    }
    
    private function addPhoneNumber(PhoneNumber $phoneNumber) {
        foreach($this->userPhoneNumbers as $userPhone) {
            if($userPhone->getPhoneNumber()->equals($phoneNumber)) {
                throw new \DomainException('Could not add phone number for person. The phone number'
                        . ' already exists.');
            }
        }
        
        $primary = (empty($this->userPhoneNumbers)) ? TRUE : FALSE;
        
        $this->userPhoneNumbers[] = UserPhoneNumber::create(
                $phoneNumber, FALSE, $primary);
    }
    
    /**
     * @param FullName $fullName The full name
     * @param UserAddress $address
     * @param UserPhoneNumber[] $userPhoneNumbers
     * @return Person
     */
    public static function reconstitute(
            FullName $fullName, UserAddress $address, array $userPhoneNumbers) {
        $self = new self();
        $self->fullName = $fullName;
        $self->address = $address;
        $self->userPhoneNumbers = $userPhoneNumbers;
        return $self;
    }
}

?>
