<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Provider;

use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

interface ActiveOutOfOfficePeriodProviderInterface
{
    /**
     * Returns the single active out of office period for the given channel (or the channel resolved
     * from the channel context when none is given), or null when no period is active.
     */
    public function getActivePeriod(?ChannelInterface $channel = null): ?OutOfOfficePeriodInterface;
}
