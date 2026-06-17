<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Twig;

use Setono\SyliusOutOfOfficePlugin\Dismissal\DismissalCookieKeyGeneratorInterface;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Provider\ActiveOutOfOfficePeriodProviderInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\Clock\ClockInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class OutOfOfficeExtension extends AbstractExtension
{
    public function __construct(
        private readonly ActiveOutOfOfficePeriodProviderInterface $activePeriodProvider,
        private readonly DismissalCookieKeyGeneratorInterface $dismissalCookieKeyGenerator,
        private readonly ClockInterface $clock,
        private readonly int $dismissalCookieMaxAge,
    ) {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('setono_sylius_out_of_office_active_period', $this->getActivePeriod(...)),
            new TwigFunction('setono_sylius_out_of_office_is_active', $this->isActive(...)),
            new TwigFunction('setono_sylius_out_of_office_period_is_active', $this->isPeriodActive(...)),
            new TwigFunction('setono_sylius_out_of_office_dismissal_cookie_key', $this->getDismissalCookieKey(...)),
            new TwigFunction('setono_sylius_out_of_office_dismissal_cookie_max_age', $this->getDismissalCookieMaxAge(...)),
        ];
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

    public function getDismissalCookieKey(OutOfOfficePeriodInterface $period): string
    {
        return $this->dismissalCookieKeyGenerator->generate($period);
    }

    public function getDismissalCookieMaxAge(): int
    {
        return $this->dismissalCookieMaxAge;
    }
}
