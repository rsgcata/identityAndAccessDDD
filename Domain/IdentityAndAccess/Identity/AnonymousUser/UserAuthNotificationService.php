<?php
namespace Domain\IdentityAndAccess\Identity\AnonymousUser;

use Domain\IdentityAndAccess\Identity\User\UserId;
use domain\webLabel\IpAddress;

interface UserAuthNotificationService
{
    public function notifyTargetUserOfSuspiciousAuthDetection(
        UserId      $userId,
        IpAddress   $ipAddress,
        string|null $countryCode,
        string|null $state,
        string|null $city
    ): void;
}
