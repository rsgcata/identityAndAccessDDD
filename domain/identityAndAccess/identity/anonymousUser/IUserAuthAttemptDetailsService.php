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
interface IUserAuthAttemptDetailsService {
	/**
	 * @param string $userAgent
	 * @return array An array having this format [browserName, os]
	 */
	public function getUserAgentDetails($userAgent);
	
	/**
	 * @param IpAddress[] $ipAddresses
	 * @return array An array having this format [[IpAddress, countryCode, state, city, requiresRetry],]
	 */
	public function getIpDetailsInBulk(array $ipAddresses);
}

?>
