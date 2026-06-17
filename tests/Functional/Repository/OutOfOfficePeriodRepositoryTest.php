<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Functional\Repository;

use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriod;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Repository\OutOfOfficePeriodRepositoryInterface;
use Setono\SyliusOutOfOfficePlugin\Tests\Functional\DatabaseTestCase;
use Sylius\Component\Channel\Model\ChannelInterface;

final class OutOfOfficePeriodRepositoryTest extends DatabaseTestCase
{
    /**
     * @test
     */
    public function it_returns_only_enabled_channel_matching_periods_within_bounds(): void
    {
        $now = new \DateTimeImmutable('2026-06-17 12:00:00');

        $channelA = $this->createChannel('CHANNEL_A');
        $channelB = $this->createChannel('CHANNEL_B');

        $active = $this->createPeriod(true, null, null, [$channelA]);
        $future = $this->createPeriod(true, $now->modify('+10 days'), null, [$channelA]);
        $past = $this->createPeriod(true, null, $now->modify('-10 days'), [$channelA]);
        $disabled = $this->createPeriod(false, null, null, [$channelA]);
        $allChannels = $this->createPeriod(true, null, null, []);
        $forChannelB = $this->createPeriod(true, $now->modify('-1 day'), $now->modify('+1 day'), [$channelB]);

        $this->entityManager->flush();

        $idsForA = $this->ids($this->repository()->findActive($channelA, $now));
        self::assertContains($active->getId(), $idsForA);
        self::assertContains($allChannels->getId(), $idsForA);
        self::assertNotContains($future->getId(), $idsForA);
        self::assertNotContains($past->getId(), $idsForA);
        self::assertNotContains($disabled->getId(), $idsForA);
        self::assertNotContains($forChannelB->getId(), $idsForA);

        $idsForB = $this->ids($this->repository()->findActive($channelB, $now));
        self::assertContains($forChannelB->getId(), $idsForB);
        self::assertContains($allChannels->getId(), $idsForB);
        self::assertNotContains($active->getId(), $idsForB);
    }

    /**
     * @param list<ChannelInterface> $channels
     */
    private function createPeriod(
        bool $enabled,
        ?\DateTimeImmutable $startsAt,
        ?\DateTimeImmutable $endsAt,
        array $channels,
    ): OutOfOfficePeriodInterface {
        $period = new OutOfOfficePeriod();
        $period->setName('Period');
        $period->setEnabled($enabled);
        $period->setStartsAt($startsAt);
        $period->setEndsAt($endsAt);

        foreach ($channels as $channel) {
            $period->addChannel($channel);
        }

        $this->entityManager->persist($period);

        return $period;
    }

    /**
     * @param array<array-key, OutOfOfficePeriodInterface> $periods
     *
     * @return list<int|string|null>
     */
    private function ids(array $periods): array
    {
        return array_values(array_map(static fn (OutOfOfficePeriodInterface $period) => $period->getId(), $periods));
    }

    private function repository(): OutOfOfficePeriodRepositoryInterface
    {
        /** @var OutOfOfficePeriodRepositoryInterface $repository */
        $repository = self::getContainer()->get('setono_sylius_out_of_office.repository.out_of_office_period');

        return $repository;
    }
}
