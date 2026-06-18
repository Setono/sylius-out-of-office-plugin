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
     * @param OutOfOfficePeriodRepositoryInterface<OutOfOfficePeriodInterface> $repository
     */
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

            return $this->resolve($this->repository->findActive($channel, $this->clock->now()));
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
