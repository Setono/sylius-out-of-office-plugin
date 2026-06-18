<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Twig;

use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Provider\ActiveOutOfOfficePeriodProviderInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\Clock\ClockInterface;
use Twig\Extension\RuntimeExtensionInterface;

final class OutOfOfficeRuntime implements RuntimeExtensionInterface
{
    public function __construct(
        private readonly ActiveOutOfOfficePeriodProviderInterface $activePeriodProvider,
        private readonly ClockInterface $clock,
    ) {
    }

    public function getActivePeriod(?ChannelInterface $channel = null): ?OutOfOfficePeriodInterface
    {
        return $this->activePeriodProvider->getActivePeriod($channel);
    }

    public function isActive(?ChannelInterface $channel = null): bool
    {
        return null !== $this->activePeriodProvider->getActivePeriod($channel);
    }

    /**
     * Whether a specific period is active right now. Used by the admin grid "Active now" badge.
     */
    public function isPeriodActive(OutOfOfficePeriodInterface $period): bool
    {
        try {
            return $period->isActiveAt($this->clock->now());
        } catch (\Throwable) {
            return false;
        }
    }
}
