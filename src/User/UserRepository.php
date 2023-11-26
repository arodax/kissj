<?php

declare(strict_types=1);

namespace kissj\User;

use kissj\Event\Event;
use kissj\Orm\Repository;

/**
 * @method User|null findOneBy(mixed[] $criteria)
 * @method User getOneBy(mixed[] $criteria)
 */
class UserRepository extends Repository
{
    public function getUserFromEmail(string $email, Event $event): User
    {
        return $this->getOneBy([
            'login_type' => UserLoginType::Email->value,
            'email' => $email,
            'event' => $event,
        ]);
    }

    public function findUserFromEmail(string $email, Event $event): ?User
    {
        return $this->findOneBy([
            'login_type' => UserLoginType::Email->value,
            'email' => $email,
            'event' => $event,
        ]);
    }

    public function isEmailUserExisting(string $email, Event $event): bool
    {
        return $this->isExisting([
            'login_type' => UserLoginType::Email->value,
            'email' => $email,
            'event' => $event,
        ]);
    }

    public function findSkautisUser(int $skautisId, Event $event): ?User
    {
        return $this->findOneBy([
            'login_type' => UserLoginType::Skautis->value,
            'skautis_id' => $skautisId,
            'event' => $event,
        ]);
    }
}
