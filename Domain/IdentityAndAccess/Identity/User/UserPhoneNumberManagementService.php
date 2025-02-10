<?php
namespace Domain\IdentityAndAccess\Identity\User;

use Domain\Contact\PhoneNumber;
use Domain\IdentityAndAccess\Identity\User\actionAuthenticity\UserPhoneNumberVerificationCode;
use DomainException;

class UserPhoneNumberManagementService
{
    private UserRepository $userRepository;
    private UserPhoneNumberVerificationService $userPhoneNumberVerificationService;

    public function __construct(
        UserRepository                     $userRepository,
        UserPhoneNumberVerificationService $userPhoneNumberVerificationService
    )
    {
        $this->userRepository = $userRepository;
        $this->userPhoneNumberVerificationService = $userPhoneNumberVerificationService;
    }

    public function addNewUserPhoneNumber(
        User                            $user,
        PhoneNumber                     $phoneNumber,
        UserPhoneNumberVerificationCode $userPhoneNumberVerificationCode
    ): void
    {
        $ownershipVerificationPassed = $this->userPhoneNumberVerificationService
            ->doesVerificationCodePassOwnershipVerification(
                $phoneNumber,
                $userPhoneNumberVerificationCode);

        if (!$ownershipVerificationPassed) {
            throw new DomainException(
                'Could not add new user phone number. The ownership verification failed.'
            );
        }

        $user->addPhoneNumber($phoneNumber);
        $user->markPhoneNumberOwnershipAsVerified($phoneNumber);

        $this->userRepository->saveModificationsFor($user);
    }

    public function sendPhoneNumberVerificationCode(
        User        $user,
        PhoneNumber $phoneNumber
    ): void
    {
        $user->sendPhoneNumberVerificationCode();
        $this->userPhoneNumberVerificationService->sendPhoneNumberVerificationCode($phoneNumber);
    }
}


