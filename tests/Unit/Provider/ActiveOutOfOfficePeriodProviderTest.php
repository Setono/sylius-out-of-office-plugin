<?php

declare(strict_types=1);

namespace Setono\SyliusOutOfOfficePlugin\Tests\Unit\Provider;

use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Prophecy\PhpUnit\ProphecyTrait;
use Prophecy\Prophecy\ObjectProphecy;
use Setono\SyliusOutOfOfficePlugin\Model\OutOfOfficePeriodInterface;
use Setono\SyliusOutOfOfficePlugin\Provider\ActiveOutOfOfficePeriodProvider;
use Setono\SyliusOutOfOfficePlugin\Repository\OutOfOfficePeriodRepositoryInterface;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Model\ChannelInterface;
use Symfony\Component\Clock\MockClock;

final class ActiveOutOfOfficePeriodProviderTest extends TestCase
{
    use ProphecyTrait;

    /** @var ObjectProphecy<OutOfOfficePeriodRepositoryInterface> */
    private ObjectProphecy $repository;

    /** @var ObjectProphecy<ChannelContextInterface> */
    private ObjectProphecy $channelContext;

    private MockClock $clock;

    protected function setUp(): void
    {
        $this->repository = $this->prophesize(OutOfOfficePeriodRepositoryInterface::class);
        $this->channelContext = $this->prophesize(ChannelContextInterface::class);
        $this->clock = new MockClock(new \DateTimeImmutable('2026-06-17 12:00:00'));
    }

    private function createProvider(): ActiveOutOfOfficePeriodProvider
    {
        return new ActiveOutOfOfficePeriodProvider(
            $this->repository->reveal(),
            $this->channelContext->reveal(),
            $this->clock,
        );
    }

    private function channel(string $code = 'US_WEB'): ChannelInterface
    {
        $channel = $this->prophesize(ChannelInterface::class);
        $channel->getCode()->willReturn($code);

        return $channel->reveal();
    }

    /**
     * @test
     */
    public function it_returns_null_when_no_period_is_active(): void
    {
        $channel = $this->channel();
        $this->repository->findActive($channel, Argument::type(\DateTimeInterface::class))->willReturn([]);

        self::assertNull($this->createProvider()->getActivePeriod($channel));
    }

    /**
     * @test
     */
    public function it_resolves_the_channel_from_the_context_when_none_is_given(): void
    {
        $channel = $this->channel();
        $this->channelContext->getChannel()->willReturn($channel);

        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->getStartsAt()->willReturn(null);
        $this->repository->findActive($channel, Argument::type(\DateTimeInterface::class))->willReturn([$period->reveal()]);

        self::assertSame($period->reveal(), $this->createProvider()->getActivePeriod());
    }

    /**
     * @test
     */
    public function it_uses_the_given_channel_without_touching_the_context(): void
    {
        $channel = $this->channel();
        $this->channelContext->getChannel()->shouldNotBeCalled();

        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->getStartsAt()->willReturn(null);
        $this->repository->findActive($channel, Argument::type(\DateTimeInterface::class))->willReturn([$period->reveal()]);

        self::assertSame($period->reveal(), $this->createProvider()->getActivePeriod($channel));
    }

    /**
     * @test
     */
    public function it_prefers_the_period_with_the_latest_start(): void
    {
        $channel = $this->channel();

        $earlier = $this->prophesize(OutOfOfficePeriodInterface::class);
        $earlier->getStartsAt()->willReturn(new \DateTimeImmutable('2026-06-01 00:00:00'));

        $later = $this->prophesize(OutOfOfficePeriodInterface::class);
        $later->getStartsAt()->willReturn(new \DateTimeImmutable('2026-06-10 00:00:00'));

        $this->repository->findActive($channel, Argument::type(\DateTimeInterface::class))
            ->willReturn([$earlier->reveal(), $later->reveal()]);

        self::assertSame($later->reveal(), $this->createProvider()->getActivePeriod($channel));
    }

    /**
     * @test
     */
    public function it_prefers_a_bounded_period_over_an_open_ended_one(): void
    {
        $channel = $this->channel();

        $openEnded = $this->prophesize(OutOfOfficePeriodInterface::class);
        $openEnded->getStartsAt()->willReturn(null);

        $bounded = $this->prophesize(OutOfOfficePeriodInterface::class);
        $bounded->getStartsAt()->willReturn(new \DateTimeImmutable('2026-06-10 00:00:00'));

        $this->repository->findActive($channel, Argument::type(\DateTimeInterface::class))
            ->willReturn([$openEnded->reveal(), $bounded->reveal()]);

        self::assertSame($bounded->reveal(), $this->createProvider()->getActivePeriod($channel));
    }

    /**
     * @test
     */
    public function it_keeps_a_bounded_period_when_an_open_ended_one_follows(): void
    {
        $channel = $this->channel();

        $bounded = $this->prophesize(OutOfOfficePeriodInterface::class);
        $bounded->getStartsAt()->willReturn(new \DateTimeImmutable('2026-06-10 00:00:00'));

        $openEnded = $this->prophesize(OutOfOfficePeriodInterface::class);
        $openEnded->getStartsAt()->willReturn(null);

        $this->repository->findActive($channel, Argument::type(\DateTimeInterface::class))
            ->willReturn([$bounded->reveal(), $openEnded->reveal()]);

        self::assertSame($bounded->reveal(), $this->createProvider()->getActivePeriod($channel));
    }

    /**
     * @test
     */
    public function it_memoizes_the_result_per_channel(): void
    {
        $channel = $this->channel();

        $period = $this->prophesize(OutOfOfficePeriodInterface::class);
        $period->getStartsAt()->willReturn(null);

        $this->repository->findActive($channel, Argument::type(\DateTimeInterface::class))
            ->willReturn([$period->reveal()])
            ->shouldBeCalledOnce();

        $provider = $this->createProvider();

        self::assertSame($period->reveal(), $provider->getActivePeriod($channel));
        self::assertSame($period->reveal(), $provider->getActivePeriod($channel));
    }

    /**
     * @test
     */
    public function it_returns_null_when_resolution_fails(): void
    {
        $channel = $this->channel();
        $this->repository->findActive($channel, Argument::type(\DateTimeInterface::class))
            ->willThrow(new \RuntimeException('database is down'));

        self::assertNull($this->createProvider()->getActivePeriod($channel));
    }
}
