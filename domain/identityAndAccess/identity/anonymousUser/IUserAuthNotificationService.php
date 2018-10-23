<?php
namespace domain\identityAndAccess\identity\anonymousUser;

use domain\identityAndAccess\identity\user\UserId;
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
interface IUserAuthNotificationService {
    /**
     * @param UserId $userId
     * @param IpAddress $ipAddress
     * @param string|null $countryCode
     * @param string|null $state
     * @param string|null $city
     */
    public function notifyTargetUserOfSuspiciousAuthDetection(
            UserId $userId, IpAddress $ipAddress, $countryCode, $state, $city);
}

?>
