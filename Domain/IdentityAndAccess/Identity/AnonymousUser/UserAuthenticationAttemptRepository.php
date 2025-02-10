<?php
namespace Domain\IdentityAndAccess\Identity\AnonymousUser;

use Domain\WebLabel\IpAddress;

interface UserAuthenticationAttemptRepository
{
    public function findById(UserAuthenticationAttemptId $id): ?UserAuthenticationAttempt;

    /**
     * @param UserAuthenticationAttemptId[] $ids
     *
     * @return UserAuthenticationAttempt[]
     */
    public function findAllByIdCollection(array $ids);

    /**
     * @return UserAuthenticationAttempt[]
     */
    public function findAllForDetailsFilling(
        AuthenticationDetailsStatus $detailsStatus,
        int                         $limit,
        int                         $offset
    );

    /**
     * @return UserAuthenticationAttempt[]
     */
    public function findForUserAuthentication(IpAddress $ipAddress, int $limit): array;

    public function findLastOneForTargetUserId(TargetUserId $targetUserId
    ): ?UserAuthenticationAttempt;

    /**
     * @return UserAuthenticationAttempt[]
     */
    public function findAllByUserIdCountryCodeDetailAndStartingDateTime(
        TargetUserId $targetUserId,
        string       $countryCodeDetail,
        \DateTime    $startingDateTime
    ): array;

    public function countNonEmptyCountryDetailAttemptsForTargetUserId(TargetUserId $targetUserId): int;

    public function saveNew(UserAuthenticationAttempt $userAuthenticationAttempt): void;

    public function saveModificationsFor(UserAuthenticationAttempt $userAuthenticationAttempt): void;

}
