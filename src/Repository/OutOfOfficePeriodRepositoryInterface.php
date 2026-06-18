<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Repository;

use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Resource\Doctrine\Persistence\RepositoryInterface;

/**
 * @template T of OutOfOfficePeriodInterface
 * @extends RepositoryInterface<T>
 */
interface OutOfOfficePeriodRepositoryInterface extends RepositoryInterface
{
    /**
     * Returns all enabled periods that match the given channel (or have no channel restriction)
     * and whose optional start/end bounds contain the given instant.
     *
     * @return array<array-key, OutOfOfficePeriodInterface>
     */
    public function findActive(ChannelInterface $channel, \DateTimeInterface $now): array;
}
