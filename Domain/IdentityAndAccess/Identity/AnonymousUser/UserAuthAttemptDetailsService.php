<?php
namespace Domain\IdentityAndAccess\Identity\AnonymousUser;

use Domain\WebLabel\IpAddress;

interface UserAuthAttemptDetailsService
{
    public function getUserAgentDetails(string $userAgent): array;

    /**
     * @param IpAddress[] $ipAddresses
     *
     * @return array An array having this format [[IpAddress, countryCode, state, city,
     *     requiresRetry],]
     */
    public function getIpDetailsInBulk(array $ipAddresses): array;
}
