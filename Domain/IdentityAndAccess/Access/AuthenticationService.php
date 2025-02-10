<?php

namespace Domain\IdentityAndAccess\Access;

use DateTime;
use Domain\IdentityAndAccess\Access\Representation\AuthenticationRep;
use Domain\IdentityAndAccess\Identity\AnonymousUser\TargetUserId;
use Domain\IdentityAndAccess\Identity\AnonymousUser\UserAuthenticationAttempt;
use Domain\IdentityAndAccess\Identity\AnonymousUser\UserAuthenticationAttemptRepository;
use Domain\IdentityAndAccess\Identity\User\EncryptionService;
use Domain\IdentityAndAccess\Identity\User\UserEmailAddress;
use Domain\IdentityAndAccess\Identity\User\UserRepository;
use domain\webLabel\IpAddress;
use DomainException;

class AuthenticationService
{
    private UserRepository $userRepository;
    private UserAuthenticationAttemptRepository $userAuthenticationAttemptRepository;
    private EncryptionService $encryptionService;
    private UserWorkingSpaceService $userWorkingSpaceService;

    public function __construct(
        UserRepository                      $userRepository,
        UserAuthenticationAttemptRepository $userAuthenticationAttemptRepository,
        EncryptionService                   $encryptionService,
        UserWorkingSpaceService             $userWorkingSpaceService
    )
    {
        $this->userRepository = $userRepository;
        $this->userAuthenticationAttemptRepository = $userAuthenticationAttemptRepository;
        $this->encryptionService = $encryptionService;
        $this->userWorkingSpaceService = $userWorkingSpaceService;
    }

    /**
     * @throws DomainException
     */
    public function authenticateUserWith(
        UserEmailAddress $emailAddress,
        string           $password,
        IpAddress        $ipAddress,
        bool             $keepLogged,
        string           $userAgent,
        string           $referer
    ): AuthenticationRep
    {
        if ($this->userWorkingSpaceService->isUserAuthenticated()) {
            throw new DomainException(
                'Could not authenticate user. The user is already authenticated.'
            );
        }

        if ($ipAddress->getIpAddress() === null) {
            throw new DomainException(
                'Cannot identify the anonymous user for user authentication using'
                . ' a null ip address.'
            );
        }

        $latestAttempts = $this->userAuthenticationAttemptRepository->findForUserAuthentication(
            $ipAddress,
            UserAuthenticationAttempt::AUTHENTICATION_LOCK_MAX_FAILED_ATTEMPTS
        );

        $totalFailedAttemptsDuringLockInterval = $this->countFailedAttemptsDuringLockInterval(
            $latestAttempts
        );

        $lastAttempt = null;
        foreach ($latestAttempts as $attempt) {
            if ($lastAttempt === null) {
                $lastAttempt = $attempt;
            } else if (
                $lastAttempt->getDateOfAttempt()->getTimestamp() <
                $attempt->getDateOfAttempt()->getTimestamp()
            ) {
                $lastAttempt = $attempt;
            }
        }

        if ($this->hasAnonymousUserAuthenticationLock($totalFailedAttemptsDuringLockInterval)) {
            return new AuthenticationRep(
                false, false, true,
                (new DateTime())->setTimestamp(
                    $lastAttempt->getDateOfAttempt()->getTimestamp() +
                    UserAuthenticationAttempt::AUTHENTICATION_LOCK_SECONDS_TO_LIVE
                )
            );
        }

        $user = $this->userRepository->findByEmailAddress($emailAddress);

        if (
            $user === null ||
            !$this->encryptionService->plainUserPasswordMatchingHashedPassword(
                $password,
                $user->getPassword()
            )
        ) {
            $userAuthenticationAttempt = UserAuthenticationAttempt::create(
                $ipAddress,
                false,
                TargetUserId::create($user?->getId()->getId()),
                $userAgent,
                $referer
            );

            $totalFailedAttemptsDuringLockInterval++;
            $this->userAuthenticationAttemptRepository->saveNew($userAuthenticationAttempt);
            $locked = $this->hasAnonymousUserAuthenticationLock(
                $totalFailedAttemptsDuringLockInterval
            );
            return new AuthenticationRep(
                false, true, $locked,
                (new DateTime())->setTimestamp(
                    $userAuthenticationAttempt->getDateOfAttempt()
                        ->getTimestamp()
                    + UserAuthenticationAttempt::AUTHENTICATION_LOCK_SECONDS_TO_LIVE
                )
            );
        }

        $userAuthenticationAttempt = UserAuthenticationAttempt::create(
            $ipAddress,
            true,
            TargetUserId::create($user->getId()->getId()),
            $userAgent,
            $referer
        );

        $this->userAuthenticationAttemptRepository->saveNew($userAuthenticationAttempt);

        if ($keepLogged) {
            $this->userWorkingSpaceService->createNewTimelessAuthenticatedWorkingSpaceFor(
                $user,
                $userAuthenticationAttempt
            );
        } else {
            $this->userWorkingSpaceService->createNewTemporaryAuthenticatedWorkingSpaceFor(
                $user,
                $userAuthenticationAttempt
            );
        }

        return new AuthenticationRep(true, false, false);
    }

    /**
     * Counts the total latest failed auth attempts during lock interval
     *
     * @param UserAuthenticationAttempt[] $latestAttempts
     */
    private function countFailedAttemptsDuringLockInterval(array $latestAttempts): int
    {
        $failedAttemptsDuringLockInterval = 0;

        foreach ($latestAttempts as $attempt) {
            if (
                $attempt->getDateOfAttempt()->getTimestamp() >
                time() - UserAuthenticationAttempt::AUTHENTICATION_LOCK_SECONDS_TO_LIVE
            ) {
                if (!$attempt->isSuccessful()) {
                    $failedAttemptsDuringLockInterval++;
                } else {
                    break;
                }
            }
        }

        return $failedAttemptsDuringLockInterval;
    }

    /**
     * Check if anonymous user has authentication lock
     */
    private function hasAnonymousUserAuthenticationLock(int $failedAttemptsDuringLockInterval): bool
    {
        if ($failedAttemptsDuringLockInterval
            >= UserAuthenticationAttempt::AUTHENTICATION_LOCK_MAX_FAILED_ATTEMPTS) {
            return true;
        } else {
            return false;
        }
    }
}
