<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Provider;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Repository\OutOfOfficePeriodRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\Clock\ClockInterface;

final class ActiveOutOfOfficePeriodProvider implements ActiveOutOfOfficePeriodProviderInterface
{
    private readonly LoggerInterface $logger;

    /**
     * Memoizes the resolved period per channel for the lifetime of the request, since the provider
     * is hit up to three times per page render (top bar, product page, checkout).
     *
     * @var array<string, OutOfOfficePeriodInterface|null>
     */
    private array $cache = [];

    public function __construct(
        private readonly OutOfOfficePeriodRepositoryInterface $repository,
        private readonly ChannelContextInterface $channelContext,
        private readonly ClockInterface $clock,
        ?LoggerInterface $logger = null,
    ) {
        $this->logger = $logger ?? new NullLogger();
    }

    public function getActivePeriod(?ChannelInterface $channel = null): ?OutOfOfficePeriodInterface
    {
        try {
            $channel ??= $this->channelContext->getChannel();

            $cacheKey = (string) $channel->getCode();
            if (array_key_exists($cacheKey, $this->cache)) {
                return $this->cache[$cacheKey];
            }

            $periods = $this->repository->findActive($channel, $this->clock->now());

            return $this->cache[$cacheKey] = $this->resolve($periods);
        } catch (\Throwable $e) {
            // A misconfiguration or DB blip must never white-screen the shop.
            $this->logger->error('Unable to resolve the active out of office period.', ['exception' => $e]);

            return null;
        }
    }

    /**
     * Tie-break for overlapping periods: prefer the one with the latest non-null startsAt;
     * periods with a null startsAt sort last.
     *
     * @param array<array-key, OutOfOfficePeriodInterface> $periods
     */
    private function resolve(array $periods): ?OutOfOfficePeriodInterface
    {
        $active = null;
        foreach ($periods as $period) {
            if (null === $active || $this->isPreferredOver($period, $active)) {
                $active = $period;
            }
        }

        return $active;
    }

    private function isPreferredOver(OutOfOfficePeriodInterface $candidate, OutOfOfficePeriodInterface $current): bool
    {
        $candidateStartsAt = $candidate->getStartsAt();
        $currentStartsAt = $current->getStartsAt();

        if (null === $candidateStartsAt) {
            return false;
        }

        if (null === $currentStartsAt) {
            return true;
        }

        return $candidateStartsAt > $currentStartsAt;
    }
}
