<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Provider;

use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Sylius\Component\Channel\Model\ChannelInterface;

/**
 * Memoizes the resolved period per channel for the lifetime of the request, since the provider is hit
 * up to three times per page render (top bar, product page, checkout).
 */
final class CachedActiveOutOfOfficePeriodProvider implements ActiveOutOfOfficePeriodProviderInterface
{
    /** @var array<string, OutOfOfficePeriodInterface|null> */
    private array $cache = [];

    public function __construct(
        private readonly ActiveOutOfOfficePeriodProviderInterface $provider,
    ) {
    }

    public function getActivePeriod(?ChannelInterface $channel = null): ?OutOfOfficePeriodInterface
    {
        $key = $channel?->getCode() ?? '';

        if (array_key_exists($key, $this->cache)) {
            return $this->cache[$key];
        }

        return $this->cache[$key] = $this->provider->getActivePeriod($channel);
    }
}
