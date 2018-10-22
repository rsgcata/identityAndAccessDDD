<?php
namespace domain\identityAndAccess\identity\user;

use common\domain\AbstractDomainObject;
use domain\contact\PhoneNumber;

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
class UserPhoneNumber extends AbstractDomainObject {
	/**
	 * Short description
	 *
	 * @var PhoneNumber
	 * @access private
	 */
	private $phoneNumber;
	
	/**
	 * Short description
	 *
	 * @var bool
	 * @access private
	 */
	private $ownershipVerified;
	
	/**
	 * Short description
	 *
	 * @var bool
	 * @access private
	 */
	private $usedAsPrimaryPhoneNumber;
	
	private function __construct() {
		
	}
	
	/**
	 * Creates a new value object
	 * 
	 * @param PhoneNumber $phoneNumber
	 * @param bool $ownershipVerified
	 * @param bool $usedAsPrimaryPhoneNumber
	 *
	 * @return UserPhoneNumber
	 * @throws \DomainException
	 *
	 * @static
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public static function create(
			PhoneNumber $phoneNumber, 
			$ownershipVerified, 
			$usedAsPrimaryPhoneNumber) {
		$self = new self();
		$self->setOwnershipVerified($ownershipVerified);
		$self->setPhoneNumber($phoneNumber);
		$self->setUsedAsPrimaryPhoneNumber($usedAsPrimaryPhoneNumber);
		return $self;
	}
	
	/**
	 * Check if this object equals another object
	 * 
	 * @param UserPhoneNumber $userPhoneNumber
	 *
	 * @return boolean
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function equals(UserPhoneNumber $userPhoneNumber) {
		$equalObjects = FALSE;
		
		if(static::class === get_class($userPhoneNumber) 
				&& $this->phoneNumber->equals($userPhoneNumber->getPhoneNumber())
				&& $this->ownershipVerified === $userPhoneNumber->getOwnershipVerified()
				&& $this->usedAsPrimaryPhoneNumber === $userPhoneNumber->getUsedAsPrimaryPhoneNumber()) {
			$equalObjects = TRUE;
		}
		
		return $equalObjects;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function hasOwnershipVerified() {
		return $this->ownershipVerified;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function isUsedAsPrimaryPhoneNumber() {
		return $this->usedAsPrimaryPhoneNumber;
	}
	
	/**
	 * 
	 * @return PhoneNumber
	 */
	public function getPhoneNumber() {
		return $this->phoneNumber;
	}

	/**
	 * 
	 * @return bool
	 */
	public function getOwnershipVerified() {
		return $this->ownershipVerified;
	}

	/**
	 * 
	 * @return bool
	 */
	public function getUsedAsPrimaryPhoneNumber() {
		return $this->usedAsPrimaryPhoneNumber;
	}

	public function setPhoneNumber(PhoneNumber $phoneNumber) {
		$this->phoneNumber = $phoneNumber;
	}

	public function setOwnershipVerified($ownershipVerified) {
		if(!is_bool($ownershipVerified)) {
			throw new \DomainException('Could not set ownership verified to user phone number. Invalid'
					. ' format.');
		}
		
		$this->ownershipVerified = $ownershipVerified;
	}

	public function setUsedAsPrimaryPhoneNumber($usedAsPrimaryPhoneNumber) {
		if(!is_bool($usedAsPrimaryPhoneNumber)) {
			throw new \DomainException('Could not set used as primary phone number to user phone number.'
					. ' Invalid format.');
		}
		
		$this->usedAsPrimaryPhoneNumber = $usedAsPrimaryPhoneNumber;
	}
			
	/**
	 * 
	 * @return UserPhoneNumber
	 */
	public static function reconstitute(PhoneNumber $phoneNumber, $ownershipVerified, 
			$usedAsPrimaryPhoneNumber) {
		$self = new self();
		$self->phoneNumber = $phoneNumber;
		$self->ownershipVerified = $ownershipVerified;
		$self->usedAsPrimaryPhoneNumber = $usedAsPrimaryPhoneNumber;
		return $self;
	}

}

?>
