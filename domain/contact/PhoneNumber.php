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
 */
class PhoneNumber extends AbstractValueObject{
	/**
	 * The phone number
	 *
	 * @var string|null
	 * @access protected
	 */
	protected $phoneNumber;
	
	protected function __construct() {
	}
	
	/**
	 * Create new phone number value object
	 * 
	 * @param string|null $phoneNumber The phone number
	 *
	 * @return static
	 * @throws \DomainException
	 *
	 * @static
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public static function create($phoneNumber) {
		$self = new static();
		$self->setPhoneNumber($phoneNumber);
		return $self;
	}
	
	/**
	 * Check if this object equals another object
	 * 
	 * @param PhoneNumber $phoneNumber
	 *
	 * @return boolean
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function equals(PhoneNumber $phoneNumber) {
		$equalObjects = FALSE;
		
		if(static::class === get_class($phoneNumber)
				&& $this->phoneNumber === $phoneNumber->getPhoneNumber()) {
			$equalObjects = TRUE;
		}
		
		return $equalObjects;
	}
	
	/**
	 * 
	 * @return string|null
	 */
	public function getPhoneNumber() {
		return $this->phoneNumber;
	}

	protected function setPhoneNumber($phoneNumber) {
		if($phoneNumber !== NULL) {
			if(is_numeric($phoneNumber) || is_string($phoneNumber)) {
				$phoneNumber = trim(str_replace(' ', '', $phoneNumber));
			}
			
			if($phoneNumber === '') {
				$phoneNumber = NULL;
			}
			else {
				$this->assert()->intlPhoneNumber($phoneNumber, new \DomainException('Invalid generic phone '
						. 'number. Could not set the phone number.'));
			}
			
		}
		
		$this->phoneNumber = $phoneNumber;
	}

	/**
	 * 
	 * @return static
	 */
	public static function reconstitute($phoneNumber) {
		$self = new static();
		$self->phoneNumber = $phoneNumber;
		return $self;
	}
	
	
}

?>
