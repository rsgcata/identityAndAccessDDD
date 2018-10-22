<?php
namespace domain\identityAndAccess\access;

use domain\identityAndAccess\access\representation\AuthenticationRep;
use domain\identityAndAccess\identity\anonymousUser\IUserAuthenticationAttemptRepository;
use domain\identityAndAccess\identity\anonymousUser\TargetUserId;
use domain\identityAndAccess\identity\anonymousUser\UserAuthenticationAttempt;
use domain\identityAndAccess\identity\user\IEncryptionService;
use domain\identityAndAccess\identity\user\IUserRepository;
use domain\identityAndAccess\identity\user\UserEmailAddress;
use domain\webLabel\IpAddress;

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
class AuthenticationService {
	/**
	 * User repository
	 *
	 * @var IUserRepository
	 * @access private
	 */
	private $userRepository;
	
	/**
	 * Short description
	 *
	 * @var IUserAuthenticationAttemptRepository
	 * @access private
	 */
	private $userAuthenticationAttemptRepository;	
	
	/**
	 * Password encryption service
	 *
	 * @var IEncryptionService
	 * @access private
	 */
	private $encryptionService;
	
	/**
	 * The working space service of the user
	 *
	 * @var IUserWorkingSpaceService
	 * @access private
	 */
	private $userWorkingSpaceService;
	
	public function __construct(
			IUserRepository $userRepository, 
			IUserAuthenticationAttemptRepository $userAuthenticationAttemptRepository,
			IEncryptionService $encryptionService, 
			IUserWorkingSpaceService $userWorkingSpaceService) {
		$this->userRepository = $userRepository;
		$this->userAuthenticationAttemptRepository = $userAuthenticationAttemptRepository;
		$this->encryptionService = $encryptionService;
		$this->userWorkingSpaceService = $userWorkingSpaceService;
	}
	
	/**
	 * Authenticate a user
	 * 
	 * @param UserEmailAddress $emailAddress
	 * @param string $password
	 * @param IpAddress $ipAddress
	 * @param bool $keepLogged
	 * @param string $userAgent
	 * @param string $referer
	 * 
	 * @return AuthenticationRep
	 * @throws \DomainException
	 * @access public
	 */
	public function authenticateUserWith(UserEmailAddress $emailAddress, $password, 
			IpAddress $ipAddress, $keepLogged, $userAgent, $referer) {
		if($this->userWorkingSpaceService->isUserAuthenticated()) {
			throw new \DomainException('Could not authenticate user. The user is already authenticated.');
		}
		
		if($ipAddress->getIpAddress() === NULL) {
			throw new \DomainException('Cannot identify the annonymous user for user authentication using'
					. ' a null ip address.');
		}
		
		$latestAttempts = $this->userAuthenticationAttemptRepository->findForUserAuthentication(
				$ipAddress,
				UserAuthenticationAttempt::AUTHENTICATION_LOCK_MAX_FAILED_ATTEMPTS);
		
		$totalFailedAttemptsDuringLockInterval = $this->countFailedAttemptsDuringLockInterval(
				$latestAttempts);
		
		$lastAttempt = NULL;
		
		foreach($latestAttempts as $attempt) {
			if($lastAttempt === NULL) {
				$lastAttempt = $attempt;
			}
			else {
				if($lastAttempt->getDateOfAttempt()->getTimestamp() 
						< $attempt->getDateOfAttempt()->getTimestamp()) {
					$lastAttempt = $attempt;
				}
			}
		}
		
		if($this->hasAnonymousUserAuthenticationLock($totalFailedAttemptsDuringLockInterval)) {
			return new AuthenticationRep(FALSE, FALSE, TRUE, 
					(new \DateTime())->setTimestamp($lastAttempt->getDateOfAttempt()->getTimestamp() 
							+ UserAuthenticationAttempt::AUTHENTICATION_LOCK_SECONDS_TO_LIVE));
		}
		
		$user = $this->userRepository->findByEmailAddress($emailAddress);
		
		if($user === NULL
				|| !$this->encryptionService->plainUserPasswordMatchingHashedPassword(
						$password, $user->getPassword())) {
			$userAuthenticationAttempt = UserAuthenticationAttempt::create(
					$ipAddress,
					FALSE,
					TargetUserId::create($user === NULL ? NULL : $user->getId()->getId()),
					$userAgent,
					$referer);
			
			$totalFailedAttemptsDuringLockInterval++;
			$this->userAuthenticationAttemptRepository->saveNew($userAuthenticationAttempt);
			$locked = $this->hasAnonymousUserAuthenticationLock($totalFailedAttemptsDuringLockInterval);
			return new AuthenticationRep(FALSE, TRUE, $locked, 
					(new \DateTime())->setTimestamp($userAuthenticationAttempt->getDateOfAttempt()
							->getTimestamp() 
							+ UserAuthenticationAttempt::AUTHENTICATION_LOCK_SECONDS_TO_LIVE));
		}
		
		$userAuthenticationAttempt = UserAuthenticationAttempt::create(
				$ipAddress,
				TRUE,
				TargetUserId::create($user->getId()->getId()),
				$userAgent,
				$referer);
		
		$this->userAuthenticationAttemptRepository->saveNew($userAuthenticationAttempt);
        
        if($keepLogged) {
            $this->userWorkingSpaceService->createNewTimelessAuthenticatedWorkingSpaceFor(
                    $user, $userAuthenticationAttempt);
        }
        else {
            $this->userWorkingSpaceService->createNewTemporaryAuthenticatedWorkingSpaceFor(
                    $user, $userAuthenticationAttempt);
        }

        return new AuthenticationRep(TRUE, FALSE, FALSE);
	}
	
	/**
	 * Counts the total latest failed auth attempts during lock interval
	 * 
	 * @param UserAuthenticationAttempt[] $latestAttempts
	 *
	 * @return int
	 * @throws --
	 *
	 * @access public
	 * @since Method/function available since Release 1.0
	 */
	private function countFailedAttemptsDuringLockInterval(array $latestAttempts) {
		$failedAttemptsDuringLockInterval = 0;
		
		foreach($latestAttempts as $attempt) {
			if($attempt->getDateOfAttempt()->getTimestamp() 
					> time() - UserAuthenticationAttempt::AUTHENTICATION_LOCK_SECONDS_TO_LIVE) {
				if(!$attempt->isSuccessful()) {
					$failedAttemptsDuringLockInterval++;
				}
				else if($attempt->isSuccessful()) {
					break;
				}
			}			
		}
		
		return $failedAttemptsDuringLockInterval;
	}
	
	/**
	 * Check if anonymous user has authentication lock
	 * 
	 * @param int $failedAttemptsDuringLockInterval
	 *
	 * @return bool
	 * @throws --
	 *
	 * @access private
	 * @since Method/function available since Release 1.0
	 */
	private function hasAnonymousUserAuthenticationLock($failedAttemptsDuringLockInterval) {
		if($failedAttemptsDuringLockInterval 
				>= UserAuthenticationAttempt::AUTHENTICATION_LOCK_MAX_FAILED_ATTEMPTS) {
			return TRUE;
		}
		else {
			return FALSE;
		}
	}
}

?>
