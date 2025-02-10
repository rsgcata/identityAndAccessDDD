<?php

namespace Domain\IdentityAndAccess\Identity\User;

interface UserRepository
{
    public function saveNew(User $user): void;

    public function saveModificationsFor(User $user): void;

    public function findByUserId(UserId $id): ?User;

    public function findByEmailAddress(UserEmailAddress $emailAddress): ?User;

    public function findAllByArchivationStatus(
        int $userArchivationStatus,
        int $limit,
        int $offset
    ): array;
}
