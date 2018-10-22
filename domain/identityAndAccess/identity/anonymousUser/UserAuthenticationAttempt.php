<?php
namespace domain\identityAndAccess\identity\anonymousUser;

use common\domain\AbstractDomainObject;
use domain\webLabel\IpAddress;
use common\domain\DomainEventPublisher;

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
class UserAuthenticationAttempt extends AbstractDomainObject {
	/**
	 * Short description
	 *
	 * @var UserAuthenticationAttemptId
	 * @access private
	 */
	private $id;
	
	/**
	 * Short description
	 *
	 * @var IpAddress
	 * @access private
	 */
	private $ipAddress;
	
	/**
	 * The date when the attempt was created
	 *
	 * @var \DateTime
	 * @access private
	 */
	private $dateOfAttempt;
	
	/**
	 * The user id on which the attempt was made
	 *
	 * @var TargetUserId
	 * @access private
	 */
	private $targetUserId;
	
	/**
	 * If attempt succeeded or not
	 *
	 * @var boolean
	 * @access private
	 */
	private $attemptSucceeded;
	
	/**
	 * Short description
	 *
	 * @var string
	 * @access private
	 */
	private $userAgent;
	
	/**
	 * Short description
	 *
	 * @var string
	 * @access private
	 */
	private $referer;
	
	/**
	 * Short description
	 *
	 * @var AuthenticationDetailsStatus
	 * @access private
	 */
	private $detailsStatus;
	
	/**
	 * Short description
	 *
	 * @var string
	 * @access private
	 */
	private $countryCode;
	
	/**
	 * Short description
	 *
	 * @var string
	 * @access private
	 */
	private $state;
	
	/**
	 * Short description
	 *
	 * @var string
	 * @access private
	 */
	private $city;
	
	/**
	 * Short description
	 *
	 * @var string
	 * @access private
	 */
	private $os;
	
	/**
	 * Short description
	 *
	 * @var string
	 * @access private
	 */
	private $browserName;
	
	/**
	 * How many times has been tried to attache the details
	 *
	 * @var int
	 * @access private
	 */
	private $numberOfDetailsAttachmentTries;
	
	/**
	 * Short description
	 *
	 * @var bool
	 * @access private
	 */
	private $suspiciousAuthentication;
	
	const AUTHENTICATION_LOCK_SECONDS_TO_LIVE = 120;
	const AUTHENTICATION_LOCK_MAX_FAILED_ATTEMPTS = 5;
	const MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES = 3;
	
	private function __construct() {
		
	}
	
	/**
	 * Create new authentication attempt
	 * 
	 * @param IpAddress $ipAddress
	 * @param boolean $succeeded
	 * @param TargetUserId $targetUserId
	 * @param string $userAgent
	 * @param string $referer
	 *
	 * @return UserAuthenticationAttempt
	 * @throws \DomainException
	 *
	 * @static
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public static function create(
			IpAddress $ipAddress, 
			$succeeded, 
			TargetUserId $targetUserId, 
			$userAgent, 
			$referer) {
		$self = new self();
		$self->setIpAddress($ipAddress);
		$self->setDetailsStatus(AuthenticationDetailsStatus::createNew(
				AuthenticationDetailsStatus::PENDING_DETAILS_ATTACHMENT));
		$self->setTargetUserId($targetUserId);
		$self->setAttemptSucceeded($succeeded);
		$self->setDateOfAttempt(new \DateTime());	
		$self->setReferer($referer);
		$self->setUserAgent($userAgent);
		$self->setCountryCode(NULL);
		$self->setCity(NULL);
		$self->setState(NULL);
		$self->setOs(NULL);
		$self->setBrowserName(NULL);
		$self->id = UserAuthenticationAttemptId::createNull();
		$self->setNumberOfDetailsAttachmentTries(0);
		$self->setSuspiciousAuthentication(FALSE);
		
		return $self;
	}
	
	/**
	 * Fill the auth details for the first time
	 *
	 * @param string $countryCode
	 * @param string $state
	 * @param string $city
	 * @param string $os
	 * @param string $browserName
	 * @param AuthenticationDetailsStatus $detailsStatus
	 * 
	 * @return void
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function fillAuthenticationDetails($countryCode, $state, $city, $os, $browserName,
			AuthenticationDetailsStatus $detailsStatus) {
		if($this->detailsStatus->getStatus() === AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED) {
			throw new \DomainException('Could not fill authentication details for user authentication'
					. ' attempt. The current details status is not right for this action.');
		}
		
		if($this->numberOfDetailsAttachmentTries >= self::MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES) {
			throw new \DomainException('Could not fill authentication details for user authentication'
					. ' attempt. The max number of attachment tries has already been reached.');
		}
		
		if($detailsStatus->getStatus() 
				!== AuthenticationDetailsStatus::PENDING_IP_DETAILS_ATTACHMENT_RETRY
				&& $detailsStatus->getStatus() 
				!== AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED) {
			throw new \DomainException('Could not fill authentication details for user authentication'
					. ' attempt. The details status provided is not right for this action.');
		}
		
		$this->setNumberOfDetailsAttachmentTries($this->numberOfDetailsAttachmentTries + 1);	
		
		$this->setBrowserName($browserName);
		$this->setOs($os);
		$this->setCountryCode($countryCode);
		$this->setCity($city);
		$this->setState($state);
		
		if($this->numberOfDetailsAttachmentTries === self::MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES
				|| ($this->countryCode !== NULL && $this->os !== NULL && $this->browserName !== NULL)) {
			$detailsStatus = AuthenticationDetailsStatus::createNew(
					AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED);
		}
		
		$this->setDetailsStatus($detailsStatus);
	}
	
	/**
	 * Retry tor efill the ip address details
	 * 
	 * @param string $countryCode
	 * @param string $state
	 * @param string $city
	 *
	 * @return void
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function tryRefillingIpDetails($countryCode, $state, $city, 
			AuthenticationDetailsStatus $detailsStatus) {
		if($this->detailsStatus->getStatus() === AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED) {
			throw new \DomainException('Could not refill ip details for user authentication'
					. ' attempt. The current details status is not right for this action.');
		}
		
		if($this->numberOfDetailsAttachmentTries >= self::MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES) {
			throw new \DomainException('Could not refill ip details for user authentication'
					. ' attempt. The max number of attachment tries has already been reached.');
		}
		
		if($detailsStatus->getStatus() 
				!== AuthenticationDetailsStatus::PENDING_IP_DETAILS_ATTACHMENT_RETRY
				&& $detailsStatus->getStatus() 
				!== AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED) {
			throw new \DomainException('Could not refill ip details for user authentication'
					. ' attempt. The details status provided is not right for this action.');
		}
		
		$this->setNumberOfDetailsAttachmentTries($this->numberOfDetailsAttachmentTries + 1);		
		
		$this->setCountryCode($countryCode);
		$this->setCity($city);
		$this->setState($state);
		
		if($this->numberOfDetailsAttachmentTries === self::MAX_NUM_OF_DETAILS_ATTACHMENT_TRIES
				|| $this->countryCode !== NULL) {
			$detailsStatus = AuthenticationDetailsStatus::createNew(
					AuthenticationDetailsStatus::DETAILS_ATTACHMENT_FINALIZED);
		}
		
		$this->setDetailsStatus($detailsStatus);
	}
	
	/**
	 * Checks if the attempt is a succesful one
	 *
	 * @return boolean
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function isSuccessful() {
		return $this->attemptSucceeded;
	}
	
	/**
	 * Shows if the auth is suspicious
	 *
	 * @return bool
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	public function isSuspiciousAuthentication() {
		return $this->suspiciousAuthentication;
	}
	
	/**
	 * 
	 * @return UserAuthenticationAttemptId
	 */
	public function getId() {
		return $this->id;
	}
	
	/**
	 * 
	 * @return IpAddress
	 */
	public function getIpAddress() {
		return $this->ipAddress;
	}

	/**
	 * 
	 * @return AuthenticationDetailsStatus
	 */
	public function getDetailsStatus() {
		return $this->detailsStatus;
	}
	
	/**
	 * @return \DateTime
	 */
	public function getDateOfAttempt() {
		return $this->dateOfAttempt;
	}

	/**
	 * @return TargetUserId
	 */
	public function getTargetUserId() {
		return $this->targetUserId;
	}

	/**
	 * @return boolean 
	 */
	public function getAttemptSucceeded() {
		return $this->attemptSucceeded;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getUserAgent() {
		return $this->userAgent;
	}

	/**
	 * 
	 * @return string
	 */
	public function getReferer() {
		return $this->referer;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getCountryCode() {
		return $this->countryCode;
	}

	/**
	 * 
	 * @return string
	 */
	public function getState() {
		return $this->state;
	}

	/**
	 * 
	 * @return string
	 */
	public function getCity() {
		return $this->city;
	}

	/**
	 * 
	 * @return string
	 */
	public function getOs() {
		return $this->os;
	}

	/**
	 * 
	 * @return string
	 */
	public function getBrowserName() {
		return $this->browserName;
	}
	
	/**
	 * 
	 * @return int
	 */
	public function getNumberOfDetailsAttachmentTries() {
		return $this->numberOfDetailsAttachmentTries;
	}
	
	/**
	 * 
	 * @return bool
	 */
	public function getSuspiciousAuthentication() {
		return $this->suspiciousAuthentication;
	}

	private function setIpAddress(IpAddress $ipAddress) {
		if($ipAddress->getIpAddress() === NULL) {
			throw new \DomainException('Could not set ip address to user authentication attempt.'
					. ' The ip cannot be null.');
		}
		
		$this->ipAddress = $ipAddress;
	}

	private function setDetailsStatus(AuthenticationDetailsStatus $detailsStatus) {
		$this->detailsStatus = $detailsStatus;
	}
	
	private function setCountryCode($countryCode) {
		if($countryCode !== NULL) {
			if(is_string($countryCode)) {
				$countryCode = strtoupper(trim($countryCode));
			}
			
			if($countryCode === '') {
				$countryCode = NULL;
			}
			else {
				$this->assert()->iso2LetterCountryCode($countryCode, new \DomainException('Invalid generic '
						. ' countryCode. Could not set countryCode in user authentication attempt.'));
			}
		}
		
		$this->countryCode = $countryCode;
	}

	private function setState($state) {
		if($state !== NULL) {
			if(is_string($state)) {
				$state = ucfirst(trim($state));
			}
			
			if($state === '') {
				$state = NULL;
			}
			else {
				$this->assert()->stringCharCount($state, 2, 64, new \DomainException('Invalid generic '
						. 'international state name. Could not set state in user auth attempt, '
						. ' character count missmatch.'));

				if (preg_match('/^([\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]+\s{0,1})+$/u', $state) !== 1) {
					throw new \DomainException('Invalid generic international state name. Could not set '
							. 'state in user auth attempt, only international alphabet letters, '
							. ' single quotes, numbers and spaces are allowed .');
				}
			}
		}
		
		$this->state = $state;
	}

	private function setCity($city) {
		if($city !== NULL) {
			if(is_string($city)) {
				$city = ucfirst(trim($city));
			}
			
			if($city === '') {
				$city = NULL;
			}
			else {
				$this->assert()->stringCharCount($city, 2, 64, new \DomainException('Invalid generic '
						. 'international city name. Could not set city in user auth attempt, character'
						. ' count missmatch.'));

				if (preg_match('/^([\p{L}\p{Mn}\p{Pd}\'\x{2019}0-9]+\s{0,1})+$/u', $city) !== 1) {
					throw new \DomainException('Invalid generic international city name. Could not set '
							. 'city in user auth attempt, only international alphabet letters, single'
							. ' quotes, numbers and spaces are allowed .');
				}
			}
		}
		
		$this->city = $city;
	}

	private function setOs($os) {
		if($os !== NULL) {
			if(is_string($os)) {
				$os = trim($os);
			}
			
			if($os === '') {
				$os = NULL;
			}
			else {
				$this->assert()->stringCharCount($os, 1, 128, new \DomainException('Invalid os.'
						. ' Could not set os in user auth attempt, character'
						. ' count missmatch.'));
			}
		}
		
		$this->os = $os;
	}

	private function setBrowserName($browserName) {
		if($browserName !== NULL) {
			if(is_string($browserName)) {
				$browserName = trim($browserName);
			}
			
			if($browserName === '') {
				$browserName = NULL;
			}
			else {
				$this->assert()->stringCharCount($browserName, 1, 128, new \DomainException('Invalid'
						. ' browser name. Could not set os in user auth attempt, character'
						. ' count missmatch.'));
			}
		}
		
		$this->browserName = $browserName;
	}
	
	private function setDateOfAttempt(\DateTime $dateOfAttempt) {
		$this->dateOfAttempt = $dateOfAttempt;
	}

	private function setTargetUserId(TargetUserId $targetUserId) {
		$this->targetUserId = $targetUserId;
	}

	private function setAttemptSucceeded($attemptSucceeded) {
		if(!is_bool($attemptSucceeded)) {
			throw new \DomainException('Could not set attempt succeeded to user authentication attempt.'
					. ' Invalid format.');
		}
		
		$this->attemptSucceeded = $attemptSucceeded;
	}
	
	private function setUserAgent($userAgent) {
		if($userAgent !== NULL) {
			if(is_string($userAgent)) {
				$userAgent = trim($userAgent);
			}
			else {
				throw new \DomainException('Invalid user agent. Could not set user agent to'
						. ' user athentication attempt.');
			}
			
			if($userAgent === '') {
				$userAgent = NULL;
			}
			else {
				$userAgent = mb_substr($userAgent, 0, 256);
			}
		}
		
		$this->userAgent = $userAgent;
	}

	private function setReferer($referer) {
		if($referer !== NULL) {
			if(is_string($referer)) {
				$referer = trim($referer);
			}
			else {
				throw new \DomainException('Invalid referer. Could not set referer to'
						. ' user athentication attempt.');
			}
			
			if($referer === '') {
				$referer = NULL;
			}
			else {
				$referer = mb_substr($referer, 0, 256);
			}
		}
		
		$this->referer = $referer;
	}
	
	private function setNumberOfDetailsAttachmentTries($numberOfDetailsAttachmentTries) {
		if(!is_int($numberOfDetailsAttachmentTries)) {
			throw new \DomainException('Could nto set number of details attachement tries. Invalid format.');
		}
		
		$this->numberOfDetailsAttachmentTries = $numberOfDetailsAttachmentTries;
	}
	
	public function setSuspiciousAuthentication($suspiciousAuthentication) {
		if(!is_bool($suspiciousAuthentication)) {
			throw new \DomainException('Could not set suspicious authentication. Invalid format.');
		}
		
		$this->suspiciousAuthentication = $suspiciousAuthentication;
	}

	/**
	 * @return UserAuthenticationAttempt
	 */
	public static function reconstitute(UserAuthenticationAttemptId $id, IpAddress $ipAddress,
			\DateTime $dateOfAttempt, TargetUserId $targetUserId, $succeeded,
			$userAgent, $referer, AuthenticationDetailsStatus $detailsStatus, 
			$countryCode, $state, $city, $os, $browserName, $numberOfDetailsAttachmentTries,
			$suspiciousAuthentication) {
		$self = new self();
		$self->id = $id;
		$self->ipAddress = $ipAddress;
		$self->detailsStatus = $detailsStatus;
		$self->targetUserId = $targetUserId;
		$self->attemptSucceeded = $succeeded;
		$self->dateOfAttempt = $dateOfAttempt;
		$self->userAgent = $userAgent;
		$self->referer = $referer;
		$self->countryCode = $countryCode;
		$self->state = $state;
		$self->city = $city;
		$self->os = $os;
		$self->browserName = $browserName;
		$self->numberOfDetailsAttachmentTries = $numberOfDetailsAttachmentTries;
		$self->suspiciousAuthentication = $suspiciousAuthentication;
		return $self;
	}
}

?>
