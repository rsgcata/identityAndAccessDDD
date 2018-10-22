<?php
namespace domain\contact;

use common\domain\AbstractValueObject;

/**
 *
 * Address value object
 *
 * @category   --
 * @package    --
 * @license    --
 * @version    1.0
 * @link       --
 * @since      Class available since Release 1.0
 */
class Address extends AbstractValueObject {
	/**
	 * The countryCode (as in US)
	 *
	 * @var string|null
	 * @access protected
	 */
	protected $countryCode;
	
	/**
	 * The state (as in California)
	 *
	 * @var string|null
	 * @access protected
	 */
	protected $state;
	
	/**
	 * The county / region
	 *
	 * @var string|null
	 * @access protected
	 */
	protected $county;
	
	/**
	 * The city (as in Los Angeles)
	 *
	 * @var string|null
	 * @access protected
	 */
	protected $city;
	
	/**
	 * The street address (as in Grand Boulevard)
	 *
	 * @var string|null
	 * @access protected
	 */
	protected $streetAddress;
	
	/**
	 * The zip code
	 *
	 * @var string|null
	 * @access protected
	 */
	protected $zipCode;
	
	/**
	 * The area code
	 *
	 * @var string|null
	 * @access protected
	 */
	protected $areaCode;
	
	protected function __construct() {
	}
	
	/**
	 * Create new Address value object
	 * 
	 * @param string|null $countryCode The countryCode name
	 * @param string|null $state Teh state name , ex: California
	 * @param string|null $county
	 * @param string|null $city The city name
	 * @param string|null $streetAddress The street address
	 * @param string|null $zipCode The zip code
	 * @param string|null $areaCode The area code
	 *
	 * @return static
	 * @throws \DomainException
	 *
	 * @static
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public static function create($countryCode, $state, $county, $city, $streetAddress, $zipCode,
			$areaCode) {
		$self = new static();
		$self->setCity($city);
		$self->setCountryCode($countryCode);
		$self->setCounty($county);
		$self->setState($state);
		$self->setStreetAddress($streetAddress);
		$self->setZipCode($zipCode);
		$self->setAreaCode($areaCode);
		return $self;
	}
	
	/**
	 * Check if this object equals another object
	 * 
	 * @param Address $address
	 *
	 * @return boolean
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function equals(Address $address) {
		$equalObjects = FALSE;
		
		if(static::class === get_class($address) 
				&& $this->city === $address->getCity() 
				&& $this->countryCode === $address->getCountryCode() 
				&& $this->county === $address->getCounty()
				&& $this->state === $address->getState() 
				&& $this->streetAddress === $address->getStreetAddress()
				&& $this->zipCode === $address->getZipCode()
				&& $this->areaCode === $address->getAreaCode()) {
			$equalObjects = TRUE;
		}
		
		return $equalObjects;
	}
	
	/**
	 * Check if it has all fields/descriptors null
	 *
	 * @return boolean
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function hasAllDescriptorsNull() {
		if($this->city !== NULL || $this->countryCode !== NULL || $this->county !== NULL 
				|| $this->state !== NULL || $this->streetAddress !== NULL || $this->zipCode !== NULL
				|| $this->areaCode !== NULL) {
			return FALSE;
		}
		else {
			return TRUE;
		}
	}
	
	/**
	 * 
	 * @return string|null
	 */
	public function getCountryCode() {
		return $this->countryCode;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getCounty() {
		return $this->county;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getStreetAddress() {
		return $this->streetAddress;
	}

	/**
	 * 
	 * @return string|null
	 */
	public function getZipCode() {
		return $this->zipCode;
	}
	
	/**
	 * 
	 * @return string|null
	 */
	public function getAreaCode() {
		return $this->areaCode;
	}

	protected function setCountryCode($countryCode) {
		if($countryCode !== NULL) {
			if(is_string($countryCode)) {
				$countryCode = strtoupper(trim($countryCode));
			}
			
			if($countryCode === '') {
				$countryCode = NULL;
			}
			else {
				$this->assert()->iso2LetterCountryCode($countryCode, new \DomainException('Invalid generic '
						. ' countryCode. Could not set countryCode in address.'));
			}
		}
		
		$this->countryCode = $countryCode;
	}

	protected function setState($state) {
		if($state !== NULL) {
			if(is_string($state)) {
				$state = trim($state);
			}
			
			if($state === '') {
				$state = NULL;
			}
			else {
				$this->assert()->stringCharCount($state, 2, 100, new \DomainException('Invalid generic '
						. 'international state name. Could not set state in address, character count '
						. 'missmatch.'));

				if (preg_match('/^([\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]*[\p{L}]+[\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]*\s{0,1})+$/u', $state) !== 1) {
					throw new \DomainException('Invalid generic international state name. Could not set '
							. 'state in address, only international alphabet letters, single quotes, '
							. 'numbers, spaces, hyphens and dots are allowed.');
				}
			}
		}
		
		$this->state = $state;
	}

	protected function setCounty($county) {
		if($county !== NULL) {
			if(is_string($county)) {
				$county = trim($county);
			}
			
			if($county === '') {
				$county = NULL;
			}
			else {
				$this->assert()->stringCharCount($county, 2, 100, new \DomainException('Invalid generic '
						. 'international county name. Could not set county in address, character count '
						. 'missmatch.'));

				if (preg_match('/^([\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]*[\p{L}]+[\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]*\s{0,1})+$/u', $county) !== 1) {
					throw new \DomainException('Invalid generic international county name. Could not set '
							. 'county in address, only international alphabet letters, single quotes, '
							. 'numbers, spaces, hyphens and dots are allowed .');
				}
			}
		}
		
		$this->county = $county;
	}

	protected function setCity($city) {
		if($city !== NULL) {
			if(is_string($city)) {
				$city = trim($city);
			}
			
			if($city === '') {
				$city = NULL;
			}
			else {
				$this->assert()->stringCharCount($city, 2, 100, new \DomainException('Invalid generic '
						. 'international city name. Could not set city in address, character count '
						. 'missmatch.'));

				if (preg_match('/^([\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]*[\p{L}]+[\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]*\s{0,1})+$/u', $city) !== 1) {
					throw new \DomainException('Invalid generic international city name. Could not set '
							. 'city in address, only international alphabet letters, single quotes, '
							. 'numbers, spaces, hyphens and dots are allowed.');
				}
			}
		}
		
		$this->city = $city;
	}

	protected function setStreetAddress($streetAddress) {
		if($streetAddress !== NULL) {
			if(is_string($streetAddress)) {
				$streetAddress = trim($streetAddress);
			}
			
			if($streetAddress === '') {
				$streetAddress = NULL;
			}
			else {
				$this->assert()->stringCharCount(
						$streetAddress, 2, 128, new \DomainException('Invalid generic '
						. ' international street address. Could not set street address in address, character'
						. ' count missmatch.'));

				if (preg_match('/^([\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9,\.\-;]+\s{0,1})+$/u', $streetAddress) !== 1) {
					throw new \DomainException('Invalid generic international street address. Could not set '
							. ' street address in address, only international alphabet letters, single'
							. ' quotes, commas, periods, hyphens, numbers, semicolons and spaces are'
							. ' allowed.');
				}
			}
		}
		
		$this->streetAddress = $streetAddress;
	}

	protected function setZipCode($zipCode) {
		if($zipCode !== NULL) {
			if(is_string($zipCode) || is_numeric($zipCode)) {
				$zipCode = trim(str_replace(' ', '', $zipCode));
			}
			
			if($zipCode === '') {
				$zipCode = NULL;
			}
			else {
				$this->assert()->stringCharCount($zipCode, 1, 16, new \DomainException('Invalid'
						. ' generic international zip code. Could not set zip code in address,'
						. ' character count missmatch.'));

				if (preg_match('/^([\p{L}\p{Mn}\p{Pd}0-9]+\s{0,1})+$/u', $zipCode) 
						!== 1) {
					throw new \DomainException('Invalid generic international zip code. Could not set '
							. 'zip code in address, only international alphabet letters, spaces '
							. 'and numbers are allowed.');
				}
			}
		}
		
		$this->zipCode = $zipCode;
	}
	
	protected function setAreaCode($areaCode) {
		if($areaCode !== NULL) {
			if(is_string($areaCode) || is_numeric($areaCode)) {
				$areaCode = trim($areaCode);
			}
			
			if($areaCode === '') {
				$areaCode = NULL;
			}
			else {
				$this->assert()->stringCharCount($areaCode, 1, 16, new \DomainException('Invalid '
						. 'generic international area code. Could not set area code in address, '
						. 'character count missmatch.'));

				if (preg_match('/^([\p{L}\p{Mn}\p{Pd}0-9]+\s{0,1})+$/u', $areaCode) 
						!== 1) {
					throw new \DomainException('Invalid generic international area code. Could not set '
							. 'area code in address, only international alphabet letters, spaces '
							. 'and numbers area allowed.');
				}
			}
		}
		
		$this->areaCode = $areaCode;
	}
		
	/**
	 * 
	 * @param string|null  $countryCode
	 * @param string|null  $state
	 * @param string|null  $county
	 * @param string|null  $city
	 * @param string|null  $streetAddress
	 * @param string|null  $zipCode
	 * @param string|null  $areaCode
	 * @return static
	 */
	public static function reconstitute($countryCode, $state, $county, $city, $streetAddress, $zipCode,
			$areaCode) {
		$self = new static();
		$self->countryCode = $countryCode;
		$self->state = $state;
		$self->county = $county;
		$self->city = $city;
		$self->streetAddress = $streetAddress;
		$self->zipCode = $zipCode;
		$self->areaCode = $areaCode;
		return $self;
	}
}

?>
