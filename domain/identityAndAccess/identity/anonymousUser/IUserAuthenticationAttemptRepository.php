<?php
namespace domain\identityAndAccess\identity\anonymousUser;

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
interface IUserAuthenticationAttemptRepository {
	/**
	 * @param UserAuthenticationAttemptId $id
	 * @return UserAuthenticationAttempt|null
	 */
	public function findById(UserAuthenticationAttemptId $id);
	
	/**
	 * @param UserAuthenticationAttemptId[] $ids
	 * @return UserAuthenticationAttempt[]
	 */
	public function findAllByIdCollection(array $ids);
	
	/**
	 * 
	 * @param AuthenticationDetailsStatus $detailsStatus
	 * @param int $limit
	 * @param int $offset
	 * @return UserAuthenticationAttempt[]
	 */
	public function findAllForDetailsFilling(AuthenticationDetailsStatus $detailsStatus, $limit, $offset);
	
	/**
	 * @param IpAddress $ipAddress
	 * @param int $limit
	 * @return UserAuthenticationAttempt[]
	 */
	public function findForUserAuthentication(IpAddress $ipAddress, $limit);
	
	/**
	 * @param TargetUserId $targetUserId
	 * @return UserAuthenticationAttempt|null
	 */
	public function findLastOneForTargetUserId(TargetUserId $targetUserId);
	
	/**
	 * @param TargetUserId $targetUserId
	 * @param string $countryCodeDetail
	 * @param \DateTime $startingDateTime
	 * @return UserAuthenticationAttempt[]
	 */
	public function findAllByUserIdCountryCodeDetailAndStartingDateTime(
			TargetUserId $targetUserId, $countryCodeDetail, \DateTime $startingDateTime);
	
	/**
	 * @param TargetUserId $targetUserId
	 * @return int
	 */
	public function countNonEmptyCountryDetailAttemptsForTargetUserId(TargetUserId $targetUserId);
	
	/**
	 * @param UserAuthenticationAttempt $userAuthenticationAttempt
	 * @return void
	 */
	public function saveNew(UserAuthenticationAttempt $userAuthenticationAttempt);
	
	/**
	 * @param UserAuthenticationAttempt $userAuthenticationAttempt
	 * @return void
	 */
	public function saveModificationsFor(UserAuthenticationAttempt $userAuthenticationAttempt);
	
}

?>
