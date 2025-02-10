<?php
namespace Domain\IdentityAndAccess\Identity\AnonymousUser;

use Domain\WebLabel\IpAddress;

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
