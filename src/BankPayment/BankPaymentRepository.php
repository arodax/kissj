<?php

declare(strict_types=1);

namespace kissj\BankPayment;

use kissj\Event\Event;
use kissj\Orm\Order;
use kissj\Orm\Repository;

/**
 * @method BankPayment[] findBy(mixed[] $criteria, Order[] $orders = [])
 * @method BankPayment|null findOneBy(mixed[] $criteria, mixed[] $orderBy = [])
 * @method BankPayment getOneBy(mixed[] $criteria)
 */
class BankPaymentRepository extends Repository
{
    /**
     * @param Event $event
     * @return BankPayment[]
     */
    public function getAllBankPaymentsOrdered(Event $event): array
    {
        return $this->findBy(
            ['event' => $event],
            [new Order('id', Order::DIRECTION_DESC)],
        );
    }

    /**
     * @param Event $event
     * @param string $status
     * @return BankPayment[]
     */
    public function getBankPaymentsOrderedWithStatus(Event $event, string $status): array
    {
        return $this->findBy(
            [
                'event' => $event,
                'status' => $status,
            ],
            [new Order('id', Order::DIRECTION_DESC)],
        );
    }

    public function getLastBankPaymentId(Event $event): ?string
    {
        $bankPayment = $this->findOneBy(
            ['event' => $event],
            ['bank_id' => false],
        );

        return $bankPayment?->bankId;
    }
}
