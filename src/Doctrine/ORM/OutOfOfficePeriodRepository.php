<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Doctrine\ORM;

use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Repository\OutOfOfficePeriodRepositoryInterface;
use Sylius\Bundle\ResourceBundle\Doctrine\ORM\EntityRepository;
use Sylius\Component\Channel\Model\ChannelInterface;

class OutOfOfficePeriodRepository extends EntityRepository implements OutOfOfficePeriodRepositoryInterface
{
    public function findActive(ChannelInterface $channel, \DateTimeInterface $now): array
    {
        $qb = $this->createQueryBuilder('o');

        /** @var array<array-key, OutOfOfficePeriodInterface> $result */
        $result = $qb
            ->andWhere('o.enabled = :enabled')
            ->andWhere($qb->expr()->orX('o.startsAt IS NULL', 'o.startsAt <= :now'))
            ->andWhere($qb->expr()->orX('o.endsAt IS NULL', 'o.endsAt >= :now'))
            ->andWhere($qb->expr()->orX(':channel MEMBER OF o.channels', 'o.channels IS EMPTY'))
            ->setParameter('enabled', true)
            ->setParameter('now', $now)
            ->setParameter('channel', $channel)
            ->addOrderBy('o.startsAt', 'DESC')
            ->getQuery()
            ->getResult()
        ;

        return $result;
    }
}
